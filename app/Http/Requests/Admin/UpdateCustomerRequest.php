<?php
// app/Http/Requests/Admin/UpdateCustomerRequest.php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->canManageCustomers();
    }

    public function rules(): array
    {
        $customerId = $this->route('customer')->id;

        return [
            'company_name' => ['required', 'string', 'max:255'],
            'company_short_name' => ['nullable', 'string', 'max:100'],
            'nip' => ['nullable', 'string', 'size:10', Rule::unique('customers', 'nip')->ignore($customerId)],
            'regon' => ['nullable', 'string', 'max:20'],
            'krs' => ['nullable', 'string', 'max:20'],
            'company_address' => ['required', 'string', 'max:500'],
            'city' => ['required', 'string', 'max:100'],
            'postal_code' => ['required', 'string', 'regex:/^\d{2}-\d{3}$/'],
            'country' => ['required', 'string', 'size:2'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('customers', 'email')->ignore($customerId)],
            'website' => ['nullable', 'url', 'max:255'],
            'status' => ['required', 'in:active,inactive,suspended,pending'],
            'credit_limit' => ['required', 'numeric', 'min:0', 'max:1000000'],
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