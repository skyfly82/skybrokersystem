<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CreateShipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sender' => 'required|array',
            'sender.name' => 'required|string|max:255',
            'sender.company_name' => 'nullable|string|max:255',
            'sender.postal_code' => 'required|string|max:10',
            'sender.city' => 'required|string|max:100',
            'sender.street' => 'required|string|max:255',
            'sender.house_number' => 'nullable|string|max:10',
            'sender.apartment_number' => 'nullable|string|max:10',
            'sender.phone' => 'required|string|max:20',
            'sender.email' => 'nullable|email|max:255',
            'sender.contact_person' => 'nullable|string|max:255',

            'recipient' => 'required|array',
            'recipient.name' => 'required|string|max:255',
            'recipient.company_name' => 'nullable|string|max:255',
            'recipient.postal_code' => 'required|string|max:10',
            'recipient.city' => 'required|string|max:100',
            'recipient.street' => 'required|string|max:255',
            'recipient.house_number' => 'nullable|string|max:10',
            'recipient.apartment_number' => 'nullable|string|max:10',
            'recipient.phone' => 'required|string|max:20',
            'recipient.email' => 'nullable|email|max:255',
            'recipient.contact_person' => 'nullable|string|max:255',
            'recipient.country_code' => 'nullable|string|max:2',

            'pieces' => 'required|array|min:1',
            'pieces.*.width' => 'required|numeric|min:1|max:300',
            'pieces.*.height' => 'required|numeric|min:1|max:300',
            'pieces.*.length' => 'required|numeric|min:1|max:300',
            'pieces.*.weight' => 'required|numeric|min:0.1|max:1000',
            'pieces.*.quantity' => 'required|integer|min:1|max:100',
            'pieces.*.type' => 'nullable|string|in:package,envelope,pallet',
            'pieces.*.non_standard' => 'nullable|boolean',

            'service_type' => 'nullable|string|max:50',
            'pickup_type' => 'nullable|string|in:REGULAR_PICKUP,REQUEST_PICKUP',
            'label_format' => 'nullable|string|in:pdf,zpl,a4',

            // Special services
            'cod_amount' => 'nullable|numeric|min:0|max:999999',
            'cod_payment_method' => 'nullable|string|in:BANK_TRANSFER,CASH',
            'insurance_amount' => 'nullable|numeric|min:0|max:999999',
            'saturday_delivery' => 'nullable|boolean',
            'evening_delivery' => 'nullable|boolean',

            // Additional info
            'content_description' => 'required|string|max:100',
            'comment' => 'nullable|string|max:255',
            'reference' => 'nullable|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'sender.required' => 'Sender information is required',
            'recipient.required' => 'Recipient information is required',
            'pieces.required' => 'At least one package is required',
            'content_description.required' => 'Content description is required',
            'pieces.*.weight.min' => 'Package weight must be at least 0.1 kg',
            'pieces.*.weight.max' => 'Package weight cannot exceed 1000 kg',
            'cod_amount.max' => 'COD amount cannot exceed 999,999',
            'insurance_amount.max' => 'Insurance amount cannot exceed 999,999',
        ];
    }
}
