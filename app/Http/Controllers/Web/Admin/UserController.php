<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Outlet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $users = User::with('roles')->paginate(10);
        
        // If tab is set to roles, also fetch roles
        $roles = null;
        if ($request->get('tab') === 'roles') {
            $roles = Role::with('permissions')->paginate(10);
        }
        
        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $outlets = Outlet::active()->orderBy('name')->get();
        return view('admin.users.create', compact('outlets'));
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'outlets' => 'nullable|array',
            'outlets.*' => 'exists:outlets,id',
        ]);

        $user = User::create([
            ...$validated,
            'password' => Hash::make($validated['password']),
            'active' => true,
        ]);

        if ($request->has('roles')) {
            $user->assignRole($request->roles);
        }

        // Attach user to selected outlets
        if ($request->has('outlets') && !empty($request->outlets)) {
            $outletRoles = [];
            foreach ($request->outlets as $outletId) {
                $outletRoles[$outletId] = ['role_at_outlet' => 'staff'];
            }
            $user->outlets()->attach($outletRoles);
        }

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    /**
     * Show the form for editing the user.
     */
    public function edit(User $user)
    {
        $outlets = Outlet::active()->orderBy('name')->get();
        $userOutlets = $user->outlets()->pluck('outlets.id')->toArray();
        return view('admin.users.edit', compact('user', 'outlets', 'userOutlets'));
    }

    /**
     * Update the user.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'outlets' => 'nullable|array',
            'outlets.*' => 'exists:outlets,id',
        ]);

        $user->update($validated);

        if ($request->has('password') && $request->password) {
            $request->validate(['password' => 'string|min:8|confirmed']);
            $user->update(['password' => Hash::make($request->password)]);
        }

        $user->syncRoles($request->roles ?? []);

        // Sync outlet assignments
        $outlets = $request->input('outlets', []);
        if (!empty($outlets)) {
            $outletRoles = [];
            foreach ($outlets as $outletId) {
                // Check if there's an existing pivot record to preserve role
                $existingRole = $user->outlets()->where('outlets.id', $outletId)->first();
                $outletRoles[$outletId] = [
                    'role_at_outlet' => $existingRole?->pivot?->role_at_outlet ?? 'staff'
                ];
            }
            $user->outlets()->sync($outletRoles);
        } else {
            $user->outlets()->detach();
        }

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the user.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }

    /**
     * Activate a user.
     */
    public function activate(User $user)
    {
        $user->update(['active' => true]);
        return back()->with('success', 'User activated successfully.');
    }

    /**
     * Deactivate a user.
     */
    public function deactivate(User $user)
    {
        $user->update(['active' => false]);
        return back()->with('success', 'User deactivated successfully.');
    }
}

