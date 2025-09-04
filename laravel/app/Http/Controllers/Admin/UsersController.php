<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\SystemUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        $query = SystemUser::query()
            ->when($request->search, function ($query, $search) {
                return $query->where('email', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%");
            })
            ->when($request->role, function ($query, $role) {
                return $query->where('role', $role);
            })
            ->when($request->status !== null, function ($query) use ($request) {
                return $query->where('is_active', (bool) $request->status);
            });

        $users = $query->latest()->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(CreateUserRequest $request)
    {
        $validated = $request->validated();
        $validated['password'] = Hash::make($validated['password']);

        SystemUser::create($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully');
    }

    public function show(SystemUser $user)
    {
        return view('admin.users.show', compact('user'));
    }

    public function edit(SystemUser $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, SystemUser $user)
    {
        $validated = $request->validated();

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully');
    }

    public function destroy(SystemUser $user)
    {
        // Prevent deleting the last super admin
        if ($user->role === 'super_admin') {
            $superAdminCount = SystemUser::where('role', 'super_admin')
                ->where('is_active', true)
                ->count();

            if ($superAdminCount <= 1) {
                return redirect()->back()
                    ->with('error', 'Cannot delete the last super admin user');
            }
        }

        // Prevent self-deletion
        if ($user->id === auth()->id()) {
            return redirect()->back()
                ->with('error', 'You cannot delete your own account');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully');
    }

    public function updateStatus(Request $request, SystemUser $user)
    {
        $request->validate([
            'status' => 'required|boolean',
        ]);

        // Prevent self-deactivation
        if ($user->id === auth()->id() && ! $request->status) {
            return response()->json(['error' => 'You cannot deactivate your own account'], 400);
        }

        // Prevent deactivating the last super admin
        if ($user->role === 'super_admin' && ! $request->status) {
            $activeCount = SystemUser::where('role', 'super_admin')
                ->where('is_active', true)
                ->where('id', '!=', $user->id)
                ->count();

            if ($activeCount == 0) {
                return response()->json(['error' => 'Cannot deactivate the last super admin'], 400);
            }
        }

        $user->update(['is_active' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => $request->status ? 'User activated successfully' : 'User deactivated successfully',
        ]);
    }
}
