<?php
// app/Http/Requests/Api/CreateShipmentRequest.php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateShipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorization handled by middleware
    }

    public function rules(): array
    {
        return [
            // Service selection
            'courier_code' => ['required', 'string', 'in:inpost,dhl,dpd,gls,meest,fedex,ambro,packeta'],
            'service_type' => ['required', 'string', 'max:100'],
            
            // Sender data
            'sender' => ['required', 'array'],
            'sender.name' => ['required', 'string', 'max:255'],
            'sender.company' => ['nullable', 'string', 'max:255'],
            'sender.address' => ['required', 'string', 'max:255'],
            'sender.city' => ['required', 'string', 'max:100'],
            'sender.postal_code' => ['required', 'string', 'max:10'],
            'sender.country' => ['required', 'string', 'size:2'],
            'sender.phone' => ['required', 'string', 'max:20'],
            'sender.email' => ['required', 'email', 'max:255'],
            
            // Recipient data
            'recipient' => ['required', 'array'],
            'recipient.name' => ['required', 'string', 'max:255'],
            'recipient.company' => ['nullable', 'string', 'max:255'],
            'recipient.phone' => ['required', 'string', 'max:20'],
            'recipient.email' => ['required', 'email', 'max:255'],
            'recipient.address' => ['required_without:recipient.pickup_point', 'string', 'max:255'],
            'recipient.city' => ['required_without:recipient.pickup_point', 'string', 'max:100'],
            'recipient.postal_code' => ['required_without:recipient.pickup_point', 'string', 'max:10'],
            'recipient.country' => ['required_without:recipient.pickup_point', 'string', 'size:2'],
            'recipient.pickup_point' => ['required_without:recipient.address', 'string', 'max:50'],
            
            // Package data
            'package' => ['required', 'array'],
            'package.weight' => ['required', 'numeric', 'min:0.01', 'max:70'],
            'package.length' => ['required', 'integer', 'min:1', 'max:200'],
            'package.width' => ['required', 'integer', 'min:1', 'max:200'],
            'package.height' => ['required', 'integer', 'min:1', 'max:200'],
            'package.description' => ['nullable', 'string', 'max:255'],
            
            // Optional data
            'cod_amount' => ['nullable', 'numeric', 'min:0.01', 'max:10000'],
            'insurance_amount' => ['nullable', 'numeric', 'min:0.01', 'max:50000'],
            'reference_number' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:500'],
            'additional_services' => ['sometimes', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'courier_code.required' => 'Courier code is required.',
            'courier_code.in' => 'Invalid courier code.',
            'service_type.required' => 'Service type is required.',
            'sender.required' => 'Sender information is required.',
            'sender.name.required' => 'Sender name is required.',
            'sender.address.required' => 'Sender address is required.',
            'sender.phone.required' => 'Sender phone is required.',
            'sender.email.required' => 'Sender email is required.',
            'recipient.required' => 'Recipient information is required.',
            'recipient.name.required' => 'Recipient name is required.',
            'recipient.phone.required' => 'Recipient phone is required.',
            'recipient.email.required' => 'Recipient email is required.',
            'recipient.address.required_without' => 'Recipient address or pickup point is required.',
            'recipient.pickup_point.required_without' => 'Pickup point or recipient address is required.',
            'package.required' => 'Package information is required.',
            'package.weight.required' => 'Package weight is required.',
            'package.weight.max' => 'Maximum weight is :max kg.',
            'package.length.required' => 'Package length is required.',
            'package.width.required' => 'Package width is required.',
            'package.height.required' => 'Package height is required.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 422)
        );
    }

    protected function prepareForValidation(): void
    {
        // Clean and format phone numbers
        if ($this->has('sender.phone')) {
            $this->merge([
                'sender' => array_merge($this->sender, [
                    'phone' => preg_replace('/[^0-9+]/', '', $this->input('sender.phone')),
                    'country' => strtoupper($this->input('sender.country', 'PL')),
                ])
            ]);
        }

        if ($this->has('recipient.phone')) {
            $this->merge([
                'recipient' => array_merge($this->recipient, [
                    'phone' => preg_replace('/[^0-9+]/', '', $this->input('recipient.phone')),
                    'country' => strtoupper($this->input('recipient.country', 'PL')),
                ])
            ]);
        }
    }
}
