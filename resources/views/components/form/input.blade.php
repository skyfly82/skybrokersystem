@props([
    'label' => null,
    'name',
    'type' => 'text',
    'value' => null,
    'placeholder' => null,
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'error' => null,
    'help' => null,
    'icon' => null,
    'iconPosition' => 'left', // left, right
    'size' => 'md', // sm, md, lg
    'addon' => null,
    'addonPosition' => 'right' // left, right
])

@php
    $inputId = $name . '_' . rand(1000, 9999);
    $hasError = $error || $errors->has($name);
    $errorMessage = $error ?: $errors->first($name);
    
    $sizeClasses = [
        'sm' => 'px-3 py-1.5 text-sm',
        'md' => 'px-3 py-2 text-sm',
        'lg' => 'px-4 py-3 text-base'
    ];
    
    $iconSizeClasses = [
        'sm' => 'w-4 h-4',
        'md' => 'w-5 h-5', 
        'lg' => 'w-6 h-6'
    ];
    
    $paddingClasses = [
        'left' => [
            'sm' => 'pl-9',
            'md' => 'pl-10',
            'lg' => 'pl-12'
        ],
        'right' => [
            'sm' => 'pr-9',
            'md' => 'pr-10', 
            'lg' => 'pr-12'
        ]
    ];
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

    <div class="relative">
        @if($addon && $addonPosition === 'left')
        <div class="absolute inset-y-0 left-0 flex items-center">
            <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                {{ $addon }}
            </span>
        </div>
        @endif

        @if($icon && $iconPosition === 'left')
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            @if(is_string($icon))
                <i class="fas {{ $icon }} text-gray-400 {{ $iconSizeClasses[$size] ?? $iconSizeClasses['md'] }}"></i>
            @else
                {{ $icon }}
            @endif
        </div>
        @endif

        <input 
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $inputId }}"
            value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}"
            @if($required) required @endif
            @if($disabled) disabled @endif
            @if($readonly) readonly @endif
            {{ $attributes->except(['class', 'label', 'name', 'type', 'value', 'placeholder', 'required', 'disabled', 'readonly', 'error', 'help', 'icon', 'iconPosition', 'size', 'addon', 'addonPosition']) }}
            class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-50 disabled:text-gray-500 
                   {{ $sizeClasses[$size] ?? $sizeClasses['md'] }}
                   {{ $icon && $iconPosition === 'left' ? $paddingClasses['left'][$size] ?? $paddingClasses['left']['md'] : '' }}
                   {{ $icon && $iconPosition === 'right' ? $paddingClasses['right'][$size] ?? $paddingClasses['right']['md'] : '' }}
                   {{ $addon && $addonPosition === 'left' ? 'rounded-l-none' : '' }}
                   {{ $addon && $addonPosition === 'right' ? 'rounded-r-none' : '' }}
                   {{ $hasError ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : '' }}"
        >

        @if($icon && $iconPosition === 'right')
        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
            @if(is_string($icon))
                <i class="fas {{ $icon }} text-gray-400 {{ $iconSizeClasses[$size] ?? $iconSizeClasses['md'] }}"></i>
            @else
                {{ $icon }}
            @endif
        </div>
        @endif

        @if($addon && $addonPosition === 'right')
        <div class="absolute inset-y-0 right-0 flex items-center">
            <span class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                {{ $addon }}
            </span>
        </div>
        @endif
    </div>

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