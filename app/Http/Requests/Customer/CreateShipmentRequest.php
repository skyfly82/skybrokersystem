<?php
// app/Http/Requests/Customer/CreateShipmentRequest.php

declare(strict_types=1);

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class CreateShipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->canCreateShipments();
    }

    public function rules(): array
    {
        return [
            // Service selection
            'courier_code' => ['required', 'string', 'in:inpost,dhl,dpd,gls,meest,fedex,ambro,packeta'],
            'service_type' => ['required', 'string', 'max:100'],
            
            // Sender data
            'sender.name' => ['required', 'string', 'max:255'],
            'sender.company' => ['nullable', 'string', 'max:255'],
            'sender.address' => ['required', 'string', 'max:255'],
            'sender.building_number' => ['required', 'string', 'max:10'],
            'sender.apartment_number' => ['nullable', 'string', 'max:10'],
            'sender.city' => ['required', 'string', 'max:100'],
            'sender.postal_code' => ['required', 'string', 'regex:/^\d{2}-\d{3}$/'],
            'sender.country' => ['required', 'string', 'size:2'],
            'sender.phone' => ['required', 'string', 'max:20'],
            'sender.email' => ['required', 'email', 'max:255'],
            
            // Recipient data
            'recipient.name' => ['required', 'string', 'max:255'],
            'recipient.company' => ['nullable', 'string', 'max:255'],
            'recipient.phone' => ['required', 'string', 'max:20'],
            'recipient.email' => ['required', 'email', 'max:255'],
            
            // Address or pickup point
            'recipient.address' => ['required_without:pickup_point', 'string', 'max:255'],
            'recipient.building_number' => ['required_with:recipient.address', 'string', 'max:10'],
            'recipient.apartment_number' => ['nullable', 'string', 'max:10'],
            'recipient.city' => ['required_without:pickup_point', 'string', 'max:100'],
            'recipient.postal_code' => ['required_without:pickup_point', 'string', 'regex:/^\d{2}-\d{3}$/'],
            'recipient.country' => ['required_without:pickup_point', 'string', 'size:2'],
            'pickup_point' => ['required_without:recipient.address', 'string', 'max:50'],
            
            // Package data
            'package.weight' => ['required', 'numeric', 'min:0.01', 'max:70'],
            'package.length' => ['required', 'integer', 'min:1', 'max:200'],
            'package.width' => ['required', 'integer', 'min:1', 'max:200'],
            'package.height' => ['required', 'integer', 'min:1', 'max:200'],
            'package.description' => ['nullable', 'string', 'max:255'],
            
            // Optional services
            'cod_amount' => ['nullable', 'numeric', 'min:0.01', 'max:10000'],
            'insurance_amount' => ['nullable', 'numeric', 'min:0.01', 'max:50000'],
            'reference_number' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:500'],
            
            // Additional services
            'additional_services' => ['sometimes', 'array'],
            'additional_services.*' => ['string', 'in:saturday_delivery,evening_delivery,fragile,return_receipt'],
        ];
    }

    public function messages(): array
    {
        return [
            'courier_code.required' => 'Wybierz kuriera.',
            'service_type.required' => 'Wybierz rodzaj usługi.',
            'sender.name.required' => 'Podaj dane nadawcy.',
            'sender.address.required' => 'Adres nadawcy jest wymagany.',
            'sender.postal_code.regex' => 'Kod pocztowy musi mieć format XX-XXX.',
            'recipient.name.required' => 'Podaj dane odbiorcy.',
            'recipient.phone.required' => 'Numer telefonu odbiorcy jest wymagany.',
            'recipient.email.required' => 'Email odbiorcy jest wymagany.',
            'recipient.address.required_without' => 'Podaj adres odbiorcy lub wybierz punkt odbioru.',
            'pickup_point.required_without' => 'Wybierz punkt odbioru lub podaj adres odbiorcy.',
            'package.weight.required' => 'Waga przesyłki jest wymagana.',
            'package.weight.max' => 'Maksymalna waga to :max kg.',
            'package.length.required' => 'Długość przesyłki jest wymagana.',
            'package.width.required' => 'Szerokość przesyłki jest wymagana.',
            'package.height.required' => 'Wysokość przesyłki jest wymagana.',
            'cod_amount.max' => 'Maksymalna kwota pobrania to :max PLN.',
            'insurance_amount.max' => 'Maksymalna kwota ubezpieczenia to :max PLN.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'sender' => array_merge($this->sender ?? [], [
                'phone' => preg_replace('/[^0-9+]/', '', $this->input('sender.phone', '')),
                'postal_code' => preg_replace('/[^0-9-]/', '', $this->input('sender.postal_code', '')),
                'country' => strtoupper($this->input('sender.country', 'PL')),
            ]),
            'recipient' => array_merge($this->recipient ?? [], [
                'phone' => preg_replace('/[^0-9+]/', '', $this->input('recipient.phone', '')),
                'postal_code' => preg_replace('/[^0-9-]/', '', $this->input('recipient.postal_code', '')),
                'country' => strtoupper($this->input('recipient.country', 'PL')),
            ]),
        ]);
    }
}
