<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\User;
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

        User::create(array_merge($data, [
            'name'     => $data['first_name'] . ' ' . $data['last_name'],
            'password' => bcrypt($data['password']),
            'is_admin' => $data['role'] === 'Admin',
        ]));

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

        $update = array_merge($data, [
            'name'     => $data['first_name'] . ' ' . $data['last_name'],
            'is_admin' => $data['role'] === 'Admin' ? true : ($data['role'] !== $user->role ? false : $user->is_admin),
        ]);
        unset($update['password']);
        if (!empty($data['password'])) {
            $update['password'] = bcrypt($data['password']);
        }

        $user->update($update);

        // Re-authenticate session if the updated user is the currently logged-in user
        if (auth()->id() === $user->id) {
            auth()->setUser($user->fresh());
        }

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
