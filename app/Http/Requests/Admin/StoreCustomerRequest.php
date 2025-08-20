<?php
// app/Http/Requests/Admin/StoreCustomerRequest.php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->canManageCustomers();
    }

    public function rules(): array
    {
        return [
            'company_name' => ['required', 'string', 'max:255'],
            'company_short_name' => ['nullable', 'string', 'max:100'],
            'nip' => ['nullable', 'string', 'size:10', 'unique:customers,nip'],
            'regon' => ['nullable', 'string', 'max:20'],
            'krs' => ['nullable', 'string', 'max:20'],
            'company_address' => ['required', 'string', 'max:500'],
            'city' => ['required', 'string', 'max:100'],
            'postal_code' => ['required', 'string', 'regex:/^\d{2}-\d{3}$/'],
            'country' => ['required', 'string', 'size:2'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:customers,email'],
            'website' => ['nullable', 'url', 'max:255'],
            'status' => ['required', 'in:active,inactive,suspended,pending'],
            'credit_limit' => ['required', 'numeric', 'min:0', 'max:1000000'],
            'current_balance' => ['required', 'numeric', 'min:0', 'max:1000000'],
        ];
    }

    public function messages(): array
    {
        return [
            'company_name.required' => 'Nazwa firmy jest wymagana.',
            'nip.size' => 'NIP musi mieć dokładnie 10 cyfr.',
            'nip.unique' => 'Firma z tym numerem NIP już istnieje.',
            'postal_code.regex' => 'Kod pocztowy musi mieć format XX-XXX.',
            'email.unique' => 'Firma z tym adresem email już istnieje.',
            'phone.required' => 'Numer telefonu jest wymagany.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'nip' => preg_replace('/[^0-9]/', '', $this->nip ?? ''),
            'phone' => preg_replace('/[^0-9+]/', '', $this->phone ?? ''),
            'country' => strtoupper($this->country ?? 'PL'),
        ]);
    }
}
