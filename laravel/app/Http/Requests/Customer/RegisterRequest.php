<?php

// app/Http/Requests/Customer/RegisterRequest.php

declare(strict_types=1);

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Company data
            'company_name' => ['required', 'string', 'max:255'],
            'nip' => ['nullable', 'string', 'size:10', 'unique:customers,nip'],
            'company_address' => ['required', 'string', 'max:500'],
            'city' => ['required', 'string', 'max:100'],
            'postal_code' => ['required', 'string', 'regex:/^\d{2}-\d{3}$/'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:customers,email'],

            // Primary user data
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],

            // Agreements
            'terms_accepted' => ['required', 'accepted'],
            'privacy_accepted' => ['required', 'accepted'],
            'marketing_accepted' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'company_name.required' => 'Nazwa firmy jest wymagana.',
            'nip.size' => 'NIP musi mieć dokładnie 10 cyfr.',
            'nip.unique' => 'Firma z tym numerem NIP już istnieje.',
            'postal_code.regex' => 'Kod pocztowy musi mieć format XX-XXX.',
            'email.unique' => 'Konto z tym adresem email już istnieje.',
            'password.min' => 'Hasło musi mieć co najmniej :min znaków.',
            'password.confirmed' => 'Potwierdzenie hasła nie zgadza się.',
            'terms_accepted.accepted' => 'Musisz zaakceptować regulamin.',
            'privacy_accepted.accepted' => 'Musisz zaakceptować politykę prywatności.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'nip' => preg_replace('/[^0-9]/', '', $this->nip ?? ''),
            'phone' => preg_replace('/[^0-9+]/', '', $this->phone ?? ''),
        ]);
    }
}
