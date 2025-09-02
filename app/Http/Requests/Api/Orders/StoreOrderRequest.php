<?php

/**
 * Cel: Request validation dla tworzenia zamówień
 * Moduł: Orders
 * Odpowiedzialny: Claude-Code
 * Data: 2025-09-02
 */

namespace App\Http\Requests\Api\Orders;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Authorization is handled by middleware and policies
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'shipping_data' => ['required', 'array'],
            'shipping_data.sender' => ['required', 'array'],
            'shipping_data.sender.name' => ['required', 'string', 'max:255'],
            'shipping_data.sender.phone' => ['required', 'string', 'max:20'],
            'shipping_data.sender.email' => ['required', 'email', 'max:255'],
            'shipping_data.sender.address' => ['required', 'string', 'max:255'],
            'shipping_data.sender.city' => ['required', 'string', 'max:100'],
            'shipping_data.sender.postal_code' => ['required', 'string', 'regex:/^\d{2}-\d{3}$/'],
            
            'shipping_data.recipient' => ['required', 'array'],
            'shipping_data.recipient.name' => ['required', 'string', 'max:255'],
            'shipping_data.recipient.phone' => ['required', 'string', 'max:20'],
            'shipping_data.recipient.email' => ['nullable', 'email', 'max:255'],
            'shipping_data.recipient.address' => ['required', 'string', 'max:255'],
            'shipping_data.recipient.city' => ['required', 'string', 'max:100'],
            'shipping_data.recipient.postal_code' => ['required', 'string', 'regex:/^\d{2}-\d{3}$/'],
            
            'shipping_data.package' => ['required', 'array'],
            'shipping_data.package.weight' => ['required', 'numeric', 'min:0.1', 'max:50'],
            'shipping_data.package.dimensions' => ['required', 'array'],
            'shipping_data.package.dimensions.length' => ['required', 'numeric', 'min:1', 'max:150'],
            'shipping_data.package.dimensions.width' => ['required', 'numeric', 'min:1', 'max:150'],
            'shipping_data.package.dimensions.height' => ['required', 'numeric', 'min:1', 'max:150'],
            'shipping_data.package.description' => ['required', 'string', 'max:500'],
            'shipping_data.package.value' => ['nullable', 'numeric', 'min:0'],
            
            'shipping_data.service' => ['required', 'string', 'max:50'],
            'shipping_data.delivery_type' => ['required', 'in:courier,parcel_locker,pickup_point'],
            'shipping_data.pickup_point_id' => ['nullable', 'string', 'max:50'],
            
            'currency' => ['nullable', 'string', 'in:PLN,EUR,USD'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'shipping_data.required' => 'Shipping data is required',
            'shipping_data.sender.postal_code.regex' => 'Sender postal code must be in format XX-XXX',
            'shipping_data.recipient.postal_code.regex' => 'Recipient postal code must be in format XX-XXX',
            'shipping_data.package.weight.min' => 'Package weight must be at least 0.1 kg',
            'shipping_data.package.weight.max' => 'Package weight cannot exceed 50 kg',
        ];
    }

    /**
     * Prepare data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set defaults
        $this->merge([
            'currency' => $this->input('currency', 'PLN'),
        ]);
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Custom business logic validation
            $shippingData = $this->input('shipping_data', []);
            
            // Validate parcel locker delivery requires pickup_point_id
            if (($shippingData['delivery_type'] ?? '') === 'parcel_locker' && empty($shippingData['pickup_point_id'])) {
                $validator->errors()->add('shipping_data.pickup_point_id', 'Pickup point ID is required for parcel locker delivery');
            }
            
            // Validate package dimensions combination
            $dimensions = $shippingData['package']['dimensions'] ?? [];
            if (!empty($dimensions)) {
                $volume = ($dimensions['length'] ?? 0) * ($dimensions['width'] ?? 0) * ($dimensions['height'] ?? 0);
                if ($volume > 1000000) { // 100cm x 100cm x 100cm
                    $validator->errors()->add('shipping_data.package.dimensions', 'Package dimensions are too large');
                }
            }
        });
    }
}