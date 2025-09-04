<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class EmployeesController extends Controller
{
    public function index()
    {
        $user = auth('system_user')->user();

        if (! $user->canCreateEmployees()) {
            abort(403, 'Brak uprawnień do zarządzania pracownikami.');
        }

        $employees = SystemUser::where('role', 'employee')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.employees.index', compact('employees'));
    }

    public function create()
    {
        $user = auth('system_user')->user();

        if (! $user->canCreateEmployees()) {
            abort(403, 'Brak uprawnień do tworzenia pracowników.');
        }

        return view('admin.employees.create');
    }

    public function store(Request $request)
    {
        $user = auth('system_user')->user();

        if (! $user->canCreateEmployees()) {
            abort(403, 'Brak uprawnień do tworzenia pracowników.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:system_users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $employee = SystemUser::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'employee',
            'is_active' => true,
        ]);

        return redirect()
            ->route('admin.employees.index')
            ->with('success', 'Pracownik został utworzony pomyślnie.');
    }

    public function show(SystemUser $employee)
    {
        $user = auth('system_user')->user();

        if (! $user->canCreateEmployees()) {
            abort(403, 'Brak uprawnień do przeglądania pracowników.');
        }

        if ($employee->role !== 'employee') {
            abort(404, 'Nie znaleziono pracownika.');
        }

        return view('admin.employees.show', compact('employee'));
    }

    public function edit(SystemUser $employee)
    {
        $user = auth('system_user')->user();

        if (! $user->canCreateEmployees()) {
            abort(403, 'Brak uprawnień do edytowania pracowników.');
        }

        if ($employee->role !== 'employee') {
            abort(404, 'Nie znaleziono pracownika.');
        }

        return view('admin.employees.edit', compact('employee'));
    }

    public function update(Request $request, SystemUser $employee)
    {
        $user = auth('system_user')->user();

        if (! $user->canCreateEmployees()) {
            abort(403, 'Brak uprawnień do edytowania pracowników.');
        }

        if ($employee->role !== 'employee') {
            abort(404, 'Nie znaleziono pracownika.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('system_users')->ignore($employee->id),
            ],
            'is_active' => 'boolean',
        ]);

        $employee->update([
            'name' => $request->name,
            'email' => $request->email,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('admin.employees.index')
            ->with('success', 'Pracownik został zaktualizowany pomyślnie.');
    }

    public function destroy(SystemUser $employee)
    {
        $user = auth('system_user')->user();

        if (! $user->canCreateEmployees()) {
            abort(403, 'Brak uprawnień do usuwania pracowników.');
        }

        if ($employee->role !== 'employee') {
            abort(404, 'Nie znaleziono pracownika.');
        }

        // Prevent deleting self
        if ($employee->id === $user->id) {
            abort(403, 'Nie można usunąć siebie.');
        }

        $employee->delete();

        return redirect()
            ->route('admin.employees.index')
            ->with('success', 'Pracownik został usunięty pomyślnie.');
    }
}
