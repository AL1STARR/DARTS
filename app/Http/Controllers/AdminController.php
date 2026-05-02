<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Setting;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $query = User::where('status', '!=', 'pending');

        if ($request->filled('role'))       $query->where('role',       $request->role);
        if ($request->filled('department')) $query->where('department', $request->department);
        if ($request->filled('status'))     $query->where('status',     $request->status);
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$s}%"])
                  ->orWhere('email', 'like', "%{$s}%");
            });
        }

        // Get all filtered users for sidebar stats (unordered for now, we'll order the paginated ones)
        $allFilteredUsers = (clone $query)->get();

        // Get paginated filtered users for table display
        $users    = $query->orderBy('created_at', 'desc')->paginate(7)->withQueryString();
        $requests = User::where('status', 'pending')->get();
        $settings = Setting::orderBy('group')->orderBy('value')->get()->groupBy('group');

        $departments = Setting::getGroup('departments');
        $roles       = Setting::getGroup('roles');

        // Calculate sidebar stats
        $roleCounts = $allFilteredUsers->groupBy('role')->map->count();
        $deptCounts = $allFilteredUsers->groupBy('department')->map->count();

        return view('admin', compact('users', 'requests', 'settings', 'allFilteredUsers', 'roleCounts', 'deptCounts', 'departments', 'roles'));
    }

    public function auditLogs(Request $request)
    {
        if (!auth()->user()->isAdmin()) abort(403);

        $query = AuditLog::with('user')->latest();

        if ($request->filled('event'))  $query->where('event', $request->event);
        if ($request->filled('type'))   $query->where('auditable_type', $request->type);
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where('description', 'like', "%{$s}%");
        }

        $logs = $query->paginate(20);

        return response()->json([
            'data' => $logs->map(fn($l) => [
                'id'          => $l->id,
                'event'       => $l->event,
                'type'        => $l->auditable_type,
                'description' => $l->description,
                'user'        => $l->user ? $l->user->first_name . ' ' . $l->user->last_name : 'System',
                'timestamp'   => $l->created_at->format('M d, Y g:i A'),
            ]),
            'current_page' => $logs->currentPage(),
            'last_page'    => $logs->lastPage(),
            'total'        => $logs->total(),
        ]);
    }

    public function downloadAuditLogs(Request $request)
    {
        if (!auth()->user()->isAdmin()) abort(403);

        $year = $request->query('year');
        $month = $request->query('month');

        if (!$year || !$month) {
            return response()->json(['error' => 'Invalid year or month'], 400);
        }

        $startDate = "{$year}-{$month}-01";
        $endDate = date('Y-m-t', strtotime($startDate));

        // Get logs for the selected month
        $logs = AuditLog::with('user')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();

        // Format the date range for display
        $startDateFormatted = \Carbon\Carbon::parse($startDate)->format('F d, Y');
        $endDateFormatted = \Carbon\Carbon::parse($endDate)->format('F d, Y');
        $monthYearFormatted = \Carbon\Carbon::parse($startDate)->format('F Y');

        // Generate PDF
        $pdf = \PDF::loadView('audit-log-report', [
            'logs' => $logs,
            'startDate' => $startDateFormatted,
            'endDate' => $endDateFormatted,
            'monthYear' => $monthYearFormatted,
            'generatedDate' => now()->format('F d, Y'),
            'generatedTime' => now()->format('g:i A'),
            'adminName' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
        ]);

        // Return PDF as download
        return $pdf->download('audit-log-' . $year . '-' . $month . '.pdf');
    }


    public function settingStore(Request $request, string $group)
    {
        if (!auth()->user()->isAdmin()) abort(403);

        $data = $request->validate(['value' => 'required|string|max:255']);

        if (Setting::where('group', $group)->where('value', $data['value'])->exists()) {
            return back()->with('error', 'That option already exists.');
        }

        Setting::create(['group' => $group, 'value' => $data['value']]);

        return back()->with('success', "'{$data['value']}' added to {$group}.");
    }

    public function settingDestroy(Setting $setting)
    {
        if (!auth()->user()->isAdmin()) abort(403);

        if ($setting->is_protected) {
            return back()->with('error', "'{$setting->value}' is a protected setting and cannot be removed.");
        }

        $setting->delete();

        return back()->with('success', "Option removed.");
    }

    public function toggleAdmin(User $user)
    {
        if (!auth()->user()->isAdmin()) abort(403);

        $user->update(['is_admin' => !$user->is_admin]);

        $msg = $user->is_admin
            ? "{$user->first_name} {$user->last_name} has been granted admin access."
            : "{$user->first_name} {$user->last_name} has been removed as admin.";

        return back()->with('success', $msg);
    }

    public function approve(User $user)
    {
        if (!auth()->user()->isAdmin()) abort(403);

        $user->update(['status' => 'active']);

        // Notify user of approval
        NotificationService::notifyUserApproved($user->id, $user->first_name . ' ' . $user->last_name);

        AuditLog::record('user_request_approved', $user,
            "Access request approved for {$user->first_name} {$user->last_name} ({$user->role}, {$user->department}) by " . auth()->user()->first_name . ' ' . auth()->user()->last_name
        );

        return back()->with('success', "Access approved for {$user->first_name} {$user->last_name}.");
    }

    public function reject(User $user)
    {
        if (!auth()->user()->isAdmin()) abort(403);

        $user->delete();

        return back()->with('success', "Access request rejected and removed.");
    }

    public function store(Request $request)
    {
        if (!auth()->user()->isAdmin()) abort(403);

        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'role'       => 'required|string',
            'department' => 'required|string',
            'status'     => 'required|in:active,inactive',
            'password'   => 'required|string|min:8',
        ]);

        $newUser = User::create(array_merge($data, [
            'name'     => $data['first_name'] . ' ' . $data['last_name'],
            'password' => bcrypt($data['password']),
            'is_admin' => false,
        ]));

        AuditLog::record('user_created', $newUser,
            "User {$newUser->first_name} {$newUser->last_name} ({$newUser->role}, {$newUser->department}) created by " . auth()->user()->first_name . ' ' . auth()->user()->last_name
        );

        if ($request->expectsJson()) {
            return response()->json(['message' => 'User added successfully.']);
        }

        return back()->with('success', 'User added successfully.');
    }

    public function update(Request $request, User $user)
    {
        if (!auth()->user()->isAdmin()) abort(403);

        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email,' . $user->id,
            'role'       => 'required|string',
            'department' => 'required|string',
            'status'     => 'required|in:active,inactive',
            'password'   => 'required|string|min:8',
        ]);

        $update = array_merge($data, ['name' => $data['first_name'] . ' ' . $data['last_name']]);
        unset($update['password']);
        if (!empty($data['password'])) {
            $update['password'] = bcrypt($data['password']);
        }

        $user->update($update);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'User updated successfully.']);
        }

        return back()->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if (!auth()->user()->isAdmin()) abort(403);

        $user->delete();

        return back()->with('success', 'User removed successfully.');
    }
}
