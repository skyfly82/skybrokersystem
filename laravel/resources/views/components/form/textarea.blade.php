@props([
    'label' => null,
    'name',
    'value' => null,
    'placeholder' => null,
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'error' => null,
    'help' => null,
    'rows' => 3,
    'maxlength' => null,
    'showCounter' => false,
    'autoResize' => false
])

@php
    $inputId = $name . '_' . rand(1000, 9999);
    $hasError = $error || $errors->has($name);
    $errorMessage = $error ?: $errors->first($name);
    $currentValue = old($name, $value);
@endphp

<div {{ $attributes->only('class') }}>
    @if($label)
    <label for="{{ $inputId }}" class="block text-sm font-medium text-gray-700 mb-1">
        {{ $label }}
        @if($required)
            <span class="text-red-500 ml-1">*</span>
        @endif
    </label>
    @endif

    @if($autoResize)
    <div x-data="{ 
        resize() {
            $el.style.height = 'auto';
            $el.style.height = $el.scrollHeight + 'px';
        }
    }">
        <textarea 
            name="{{ $name }}"
            id="{{ $inputId }}"
            placeholder="{{ $placeholder }}"
            @if($required) required @endif
            @if($disabled) disabled @endif
            @if($readonly) readonly @endif
            @if($maxlength) maxlength="{{ $maxlength }}" @endif
            {{ $attributes->except(['class', 'label', 'name', 'value', 'placeholder', 'required', 'disabled', 'readonly', 'error', 'help', 'rows', 'maxlength', 'showCounter', 'autoResize']) }}
            x-ref="textarea"
            @input="resize()"
            x-init="resize()"
            class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-50 disabled:text-gray-500 resize-none
                   {{ $hasError ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : '' }}"
            style="min-height: {{ $rows * 1.5 }}rem;"
        >{{ $currentValue }}</textarea>
    </div>
    @else
    <textarea 
        name="{{ $name }}"
        id="{{ $inputId }}"
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        @if($required) required @endif
        @if($disabled) disabled @endif
        @if($readonly) readonly @endif
        @if($maxlength) maxlength="{{ $maxlength }}" @endif
        {{ $attributes->except(['class', 'label', 'name', 'value', 'placeholder', 'required', 'disabled', 'readonly', 'error', 'help', 'rows', 'maxlength', 'showCounter', 'autoResize']) }}
        class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-50 disabled:text-gray-500
               {{ $hasError ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : '' }}"
    >{{ $currentValue }}</textarea>
    @endif

    @if($showCounter && $maxlength)
    <div class="mt-1 flex justify-between text-sm">
        <div></div>
        <div class="text-gray-500" 
             x-data="{ 
                 count: {{ strlen($currentValue) }},
                 max: {{ $maxlength }}
             }"
             x-init="$watch('count', value => {
                 if (value > max * 0.9) {
                     $el.classList.add('text-yellow-600');
                 } else {
                     $el.classList.remove('text-yellow-600');
                 }
                 if (value >= max) {
                     $el.classList.add('text-red-600');
                 } else {
                     $el.classList.remove('text-red-600');
                 }
             })">
            <span x-text="count"></span>/<span x-text="max"></span>
        </div>
    </div>
    <script>
        document.getElementById('{{ $inputId }}').addEventListener('input', function(e) {
            const counter = e.target.parentNode.querySelector('[x-data]');
            if (counter) {
                Alpine.$data(counter).count = e.target.value.length;
            }
        });
    </script>
    @endif

    @if($hasError)
    <p class="mt-1 text-sm text-red-600">
        <i class="fas fa-exclamation-circle mr-1"></i>
        {{ $errorMessage }}
    </p>
    @endif

    @if($help && !$hasError)
    <p class="mt-1 text-sm text-gray-500">
        {{ $help }}
    </p>
    @endif
</div>