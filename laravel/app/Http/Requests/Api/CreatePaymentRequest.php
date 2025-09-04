<?php

// app/Http/Requests/Api/CreatePaymentRequest.php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreatePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorization handled by middleware
    }

    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'min:0.01', 'max:100000'],
            'currency' => ['sometimes', 'string', 'in:PLN,EUR,USD'],
            'type' => ['required', 'in:topup,shipment,subscription'],
            'method' => ['required', 'in:card,bank_transfer,blik,paypal,simulation'],
            'description' => ['nullable', 'string', 'max:255'],
            'metadata' => ['sometimes', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'amount.required' => 'Amount is required.',
            'amount.min' => 'Minimum amount is :min.',
            'amount.max' => 'Maximum amount is :max.',
            'type.required' => 'Payment type is required.',
            'type.in' => 'Invalid payment type.',
            'method.required' => 'Payment method is required.',
            'method.in' => 'Invalid payment method.',
            'currency.in' => 'Invalid currency. Supported: PLN, EUR, USD.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422)
        );
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'currency' => strtoupper($this->currency ?? 'PLN'),
        ]);
    }
}
