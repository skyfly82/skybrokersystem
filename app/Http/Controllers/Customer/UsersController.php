<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\CustomerUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UsersController extends Controller
{
    public function index()
    {
        $user = auth('customer_user')->user();
        
        if (!$user->canCreateUsers()) {
            abort(403, 'Brak uprawnień do zarządzania użytkownikami.');
        }

        $users = CustomerUser::where('customer_id', $user->customer_id)
            ->where('id', '!=', $user->id) // Exclude current user
            ->orderBy('created_at', 'desc')
            ->get();

        return view('customer.users.index', compact('users'));
    }

    public function create()
    {
        $user = auth('customer_user')->user();
        
        if (!$user->canCreateUsers()) {
            abort(403, 'Brak uprawnień do tworzenia użytkowników.');
        }

        $roles = [
            'user' => 'Standardowy użytkownik',
            'accountant' => 'Księgowy',
            'warehouse' => 'Magazynier',
            'viewer' => 'Tylko do odczytu'
        ];

        return view('customer.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $user = auth('customer_user')->user();
        
        if (!$user->canCreateUsers()) {
            abort(403, 'Brak uprawnień do tworzenia użytkowników.');
        }

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('customer_users')->where(function ($query) use ($user) {
                    return $query->where('customer_id', $user->customer_id);
                })
            ],
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:user,accountant,warehouse,viewer',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $customerUser = CustomerUser::create([
            'customer_id' => $user->customer_id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $request->role,
            'password' => Hash::make($request->password),
            'is_active' => true,
            'is_primary' => false,
        ]);

        return redirect()
            ->route('customer.users.index')
            ->with('success', 'Użytkownik został utworzony pomyślnie.');
    }

    public function show(CustomerUser $customerUser)
    {
        $user = auth('customer_user')->user();
        
        if (!$user->canCreateUsers() || $customerUser->customer_id !== $user->customer_id) {
            abort(403, 'Brak uprawnień do przeglądania tego użytkownika.');
        }

        return view('customer.users.show', compact('customerUser'));
    }

    public function edit(CustomerUser $customerUser)
    {
        $user = auth('customer_user')->user();
        
        if (!$user->canCreateUsers() || $customerUser->customer_id !== $user->customer_id) {
            abort(403, 'Brak uprawnień do edytowania tego użytkownika.');
        }

        // Prevent editing primary user
        if ($customerUser->is_primary) {
            abort(403, 'Nie można edytować głównego użytkownika.');
        }

        $roles = [
            'user' => 'Standardowy użytkownik',
            'accountant' => 'Księgowy',
            'warehouse' => 'Magazynier',
            'viewer' => 'Tylko do odczytu'
        ];

        return view('customer.users.edit', compact('customerUser', 'roles'));
    }

    public function update(Request $request, CustomerUser $customerUser)
    {
        $user = auth('customer_user')->user();
        
        if (!$user->canCreateUsers() || $customerUser->customer_id !== $user->customer_id) {
            abort(403, 'Brak uprawnień do edytowania tego użytkownika.');
        }

        // Prevent editing primary user
        if ($customerUser->is_primary) {
            abort(403, 'Nie można edytować głównego użytkownika.');
        }

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('customer_users')->where(function ($query) use ($user) {
                    return $query->where('customer_id', $user->customer_id);
                })->ignore($customerUser->id)
            ],
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:user,accountant,warehouse,viewer',
            'is_active' => 'boolean',
        ]);

        $customerUser->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $request->role,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('customer.users.index')
            ->with('success', 'Użytkownik został zaktualizowany pomyślnie.');
    }

    public function destroy(CustomerUser $customerUser)
    {
        $user = auth('customer_user')->user();
        
        if (!$user->canCreateUsers() || $customerUser->customer_id !== $user->customer_id) {
            abort(403, 'Brak uprawnień do usuwania tego użytkownika.');
        }

        // Prevent deleting primary user
        if ($customerUser->is_primary) {
            abort(403, 'Nie można usunąć głównego użytkownika.');
        }

        // Prevent deleting self
        if ($customerUser->id === $user->id) {
            abort(403, 'Nie można usunąć siebie.');
        }

        $customerUser->delete();

        return redirect()
            ->route('customer.users.index')
            ->with('success', 'Użytkownik został usunięty pomyślnie.');
    }

    public function transferAdmin(Request $request, CustomerUser $customerUser)
    {
        $currentUser = auth('customer_user')->user();
        
        if (!$currentUser->canTransferAdminRights()) {
            abort(403, 'Brak uprawnień do przeniesienia uprawnień administratora.');
        }

        if ($customerUser->customer_id !== $currentUser->customer_id) {
            abort(403, 'Nie można przenieść uprawnień na użytkownika z innej firmy.');
        }

        if ($customerUser->id === $currentUser->id) {
            return redirect()
                ->route('customer.users.index')
                ->withErrors(['error' => 'Nie można przenieść uprawnień na siebie.']);
        }

        if (!$customerUser->is_active) {
            return redirect()
                ->route('customer.users.index')
                ->withErrors(['error' => 'Nie można przenieść uprawnień na nieaktywnego użytkownika.']);
        }

        $success = $currentUser->transferAdminRightsTo($customerUser);

        if ($success) {
            return redirect()
                ->route('customer.users.index')
                ->with('success', 'Uprawnienia administratora zostały przeniesione pomyślnie. Zostałeś wylogowany.');
        }

        return redirect()
            ->route('customer.users.index')
            ->withErrors(['error' => 'Nie udało się przenieść uprawnień administratora.']);
    }
}
