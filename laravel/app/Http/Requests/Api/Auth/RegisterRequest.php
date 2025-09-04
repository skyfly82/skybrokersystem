<?php

/**
 * Cel: Request validation dla rejestracji
 * Moduł: Auth
 * Odpowiedzialny: sky_fly82
 * Data: 2025-09-02
 */

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('customer_users', 'email'),
            ],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
            ],
            'phone' => ['nullable', 'string', 'regex:/^(\+48|0048|48)?[1-9]\d{8}$/'],
            'role' => ['nullable', 'string', 'in:user,admin'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'customer_id.required' => 'Customer ID is required',
            'customer_id.exists' => 'Invalid customer ID',
            'name.required' => 'Full name is required',
            'name.min' => 'Name must be at least 2 characters long',
            'email.required' => 'Email address is required',
            'email.unique' => 'This email address is already registered',
            'password.confirmed' => 'Password confirmation does not match',
            'phone.regex' => 'Please provide a valid Polish phone number',
            'role.in' => 'Invalid role selected',
        ];
    }

    /**
     * Prepare data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set default role
        $this->merge([
            'role' => $this->input('role', 'user'),
        ]);
    }
}
