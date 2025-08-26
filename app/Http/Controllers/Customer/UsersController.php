<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\CustomerUser;
use App\Http\Requests\Customer\CreateUserRequest;
use App\Http\Requests\Customer\UpdateUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersController extends Controller
{
    public function index()
    {
        $customer = auth()->guard('customer_user')->user()->customer;
        $users = $customer->users()->latest()->get();

        return view('customer.users.index', compact('users'));
    }

    public function create()
    {
        return view('customer.users.create');
    }

    public function store(CreateUserRequest $request)
    {
        $validated = $request->validated();
        $validated['customer_id'] = auth()->guard('customer_user')->user()->customer_id;
        $validated['password'] = Hash::make($validated['password']);
        $validated['verification_token'] = Str::uuid()->toString();
        
        $user = CustomerUser::create($validated);

        // Send verification email (implement if needed)
        // $user->sendEmailVerificationNotification();

        return redirect()->route('customer.users.index')
            ->with('success', 'User created successfully. Verification email sent.');
    }

    public function show(CustomerUser $user)
    {
        // Check if user belongs to the same customer
        if ($user->customer_id !== auth()->guard('customer_user')->user()->customer_id) {
            abort(403);
        }
        
        return view('customer.users.show', compact('user'));
    }

    public function edit(CustomerUser $user)
    {
        // Check if user belongs to the same customer
        if ($user->customer_id !== auth()->guard('customer_user')->user()->customer_id) {
            abort(403);
        }
        
        return view('customer.users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, CustomerUser $user)
    {
        // Check if user belongs to the same customer
        if ($user->customer_id !== auth()->guard('customer_user')->user()->customer_id) {
            abort(403);
        }
        
        $validated = $request->validated();
        
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('customer.users.index')
            ->with('success', 'User updated successfully');
    }

    public function destroy(CustomerUser $user)
    {
        // Check if user belongs to the same customer
        if ($user->customer_id !== auth()->guard('customer_user')->user()->customer_id) {
            abort(403);
        }
        
        // Prevent deleting primary user
        if ($user->is_primary) {
            return redirect()->back()
                ->with('error', 'Cannot delete the primary user account');
        }

        // Prevent self-deletion
        if ($user->id === auth()->guard('customer_user')->id()) {
            return redirect()->back()
                ->with('error', 'You cannot delete your own account');
        }

        $user->delete();

        return redirect()->route('customer.users.index')
            ->with('success', 'User deleted successfully');
    }

    public function updateStatus(Request $request, CustomerUser $user)
    {
        // Check if user belongs to the same customer
        if ($user->customer_id !== auth()->guard('customer_user')->user()->customer_id) {
            abort(403);
        }
        
        $request->validate([
            'status' => 'required|boolean'
        ]);

        // Prevent self-deactivation
        if ($user->id === auth()->guard('customer_user')->id() && !$request->status) {
            return response()->json(['error' => 'You cannot deactivate your own account'], 400);
        }

        // Prevent deactivating primary user
        if ($user->is_primary && !$request->status) {
            return response()->json(['error' => 'Cannot deactivate the primary user'], 400);
        }

        $user->update(['is_active' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => $request->status ? 'User activated successfully' : 'User deactivated successfully'
        ]);
    }
}