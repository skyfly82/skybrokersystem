<?php

// app/Http/Requests/Customer/UpdateProfileRequest.php

declare(strict_types=1);

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->user()->id;
        $customerId = $this->user()->customer_id;

        return [
            // User data
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('customer_users', 'email')->ignore($userId)],
            'phone' => ['nullable', 'string', 'max:20'],

            // Company data (only for primary users)
            'company_name' => ['required_if:is_primary,true', 'string', 'max:255'],
            'company_address' => ['required_if:is_primary,true', 'string', 'max:500'],
            'city' => ['required_if:is_primary,true', 'string', 'max:100'],
            'postal_code' => ['required_if:is_primary,true', 'string', 'regex:/^\d{2}-\d{3}$/'],
            'website' => ['nullable', 'url', 'max:255'],

            // Notification preferences
            'notification_preferences' => ['sometimes', 'array'],
            'notification_preferences.email' => ['sometimes', 'array'],
            'notification_preferences.sms' => ['sometimes', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'Imię jest wymagane.',
            'last_name.required' => 'Nazwisko jest wymagane.',
            'email.unique' => 'Ten adres email jest już używany.',
            'company_name.required_if' => 'Nazwa firmy jest wymagana.',
            'postal_code.regex' => 'Kod pocztowy musi mieć format XX-XXX.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'phone' => preg_replace('/[^0-9+]/', '', $this->phone ?? ''),
            'postal_code' => preg_replace('/[^0-9-]/', '', $this->postal_code ?? ''),
        ]);
    }
}
