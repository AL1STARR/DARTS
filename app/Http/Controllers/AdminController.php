<?php

namespace App\Http\Controllers;

use App\Mail\AccountApprovedMail;
use App\Models\AuditLog;
use App\Models\Setting;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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
        $rolesWithMeta = Setting::where('group', 'roles')->get(['value', 'meta']);

        // Calculate sidebar stats
        $roleCounts = $allFilteredUsers->groupBy('role')->map->count();
        $deptCounts = $allFilteredUsers->groupBy('department')->map->count();

        return view('admin', compact('users', 'requests', 'settings', 'allFilteredUsers', 'roleCounts', 'deptCounts', 'departments', 'roles', 'rolesWithMeta'));
    }

    public function auditLogs(Request $request)
    {
        if (!auth()->user()->isFullAdmin()) abort(403);

        $query = AuditLog::with('user')->latest();

        if ($request->filled('event'))  $query->where('event', $request->event);
        if ($request->filled('type'))   $query->where('auditable_type', $request->type);

        $logs = $query->paginate(10);

        return response()->json([
            'data' => $logs->map(fn($l) => [
                'id'          => $l->id,
                'event'       => $l->event,
                'type'        => $l->auditable_type,
                'description' => $l->description,
                'user'        => $l->user ? $l->user->first_name . ' ' . $l->user->last_name : 'System',
                'timestamp'   => $l->created_at->format('M d, Y g:i A'),
                'metadata'    => $l->metadata,
            ]),
            'current_page' => $logs->currentPage(),
            'last_page'    => $logs->lastPage(),
            'total'        => $logs->total(),
        ]);
    }

    public function settingStore(Request $request, string $group)
    {
        if (!auth()->user()->isFullAdmin()) abort(403);

        $data = $request->validate([
            'value' => 'required|string|max:255',
            'meta'  => 'nullable|string|max:255',
        ]);

        if (Setting::where('group', $group)->where('value', $data['value'])->exists()) {
            return back()->with('error', 'That option already exists.');
        }

        Setting::create([
            'group' => $group,
            'value' => $data['value'],
            'meta'  => $data['meta'] ?? null,
        ]);

        return back()->with('success', "'{$data['value']}' added to {$group}.");
    }

    public function settingDestroy(Setting $setting)
    {
        if (!auth()->user()->isFullAdmin()) abort(403);

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

        $temporaryPassword = 'Temporary123';
        $user->update([
            'status' => 'active',
            'password' => bcrypt($temporaryPassword),
        ]);

        Mail::to($user->email)->send(new AccountApprovedMail($user, $temporaryPassword));

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
            'password'   => 'nullable|string|min:8',
        ]);

        $update = [
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'name'       => $data['first_name'] . ' ' . $data['last_name'],
            'email'      => $data['email'],
            'role'       => $data['role'],
            'department' => $data['department'],
            'status'     => $data['status'],
        ];

        if (!empty($data['password'])) {
            $update['password'] = bcrypt($data['password']);
        } elseif ($data['status'] === 'inactive') {
            $update['password'] = bcrypt('Temporary2000');
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

        AuditLog::record('user_removed', $user,
            "User {$user->first_name} {$user->last_name} ({$user->role}, {$user->department}) removed by " . auth()->user()->first_name . ' ' . auth()->user()->last_name
        );

        $user->delete();

        return back()->with('success', 'User removed successfully.');
    }
}
