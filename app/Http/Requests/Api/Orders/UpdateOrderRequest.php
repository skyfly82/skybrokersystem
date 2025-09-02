<?php

/**
 * Cel: Request validation dla aktualizacji zamówień
 * Moduł: Orders
 * Odpowiedzialny: sky_fly82
 * Data: 2025-09-02
 */

namespace App\Http\Requests\Api\Orders;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Check if order can be updated (handled by OrderPolicy)
        return $this->user()->can('update', $this->route('order'));
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'shipping_data' => ['sometimes', 'array'],
            'shipping_data.recipient' => ['sometimes', 'array'],
            'shipping_data.recipient.name' => ['sometimes', 'string', 'max:255'],
            'shipping_data.recipient.phone' => ['sometimes', 'string', 'max:20'],
            'shipping_data.recipient.email' => ['nullable', 'email', 'max:255'],
            'shipping_data.recipient.address' => ['sometimes', 'string', 'max:255'],
            'shipping_data.recipient.city' => ['sometimes', 'string', 'max:100'],
            'shipping_data.recipient.postal_code' => ['sometimes', 'string', 'regex:/^\d{2}-\d{3}$/'],

            'shipping_data.package' => ['sometimes', 'array'],
            'shipping_data.package.description' => ['sometimes', 'string', 'max:500'],
            'shipping_data.package.value' => ['nullable', 'numeric', 'min:0'],

            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'shipping_data.recipient.postal_code.regex' => 'Recipient postal code must be in format XX-XXX',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $order = $this->route('order');

            // Cannot update order if it's already shipped
            if (in_array($order->status, ['shipped', 'delivered', 'cancelled'])) {
                $validator->errors()->add('order', 'Cannot update order in current status: '.$order->status);
            }
        });
    }
}
