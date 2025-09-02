<?php

declare(strict_types=1);

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->guard('customer_user')->check() && 
               auth()->guard('customer_user')->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:customer_users'],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['required', 'string', 'in:user,magazynier,ksiegowa,admin'],
            'is_active' => ['boolean'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'Imię jest wymagane',
            'last_name.required' => 'Nazwisko jest wymagane',
            'email.required' => 'Adres email jest wymagany',
            'email.email' => 'Proszę wprowadzić poprawny adres email',
            'email.unique' => 'Ten adres email jest już zajęty',
            'role.required' => 'Rola użytkownika jest wymagana',
            'role.in' => 'Proszę wybrać poprawną rolę',
            'password.required' => 'Hasło jest wymagane',
            'password.min' => 'Hasło musi mieć co najmniej 8 znaków',
            'password.confirmed' => 'Potwierdzenie hasła nie pasuje',
        ];
    }
}