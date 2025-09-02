<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LabelDownloadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $availableFormats = config('skybrokersystem.couriers.available_formats', []);

        return [
            'format' => ['sometimes', 'string', Rule::in(array_keys($availableFormats))],
            'size' => ['sometimes', 'string', function ($attribute, $value, $fail) use ($availableFormats) {
                $format = $this->input('format', config('skybrokersystem.couriers.label_format', 'pdf'));
                $formatConfig = $availableFormats[$format] ?? [];

                if (! empty($formatConfig['sizes']) && ! in_array(strtoupper($value), $formatConfig['sizes'])) {
                    $fail('The selected size is not available for the chosen format.');
                }
            }],
        ];
    }

    /**
     * Get custom error messages for validation.
     */
    public function messages(): array
    {
        return [
            'format.in' => 'Wybrany format etykiety nie jest dostępny.',
        ];
    }
}
