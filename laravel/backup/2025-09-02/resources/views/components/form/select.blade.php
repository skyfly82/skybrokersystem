@props([
    'label' => null,
    'name',
    'value' => null,
    'placeholder' => null,
    'required' => false,
    'disabled' => false,
    'error' => null,
    'help' => null,
    'size' => 'md',
    'options' => [],
    'multiple' => false,
    'searchable' => false
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

    @if($searchable)
    <!-- Searchable Select with Alpine.js -->
    <div x-data="{
        open: false,
        search: '',
        selected: @js(old($name, $value)),
        options: @js($options),
        get filteredOptions() {
            if (!this.search) return this.options;
            return this.options.filter(option => 
                option.label.toLowerCase().includes(this.search.toLowerCase())
            );
        },
        selectOption(option) {
            this.selected = option.value;
            this.search = option.label;
            this.open = false;
            this.$refs.hiddenInput.value = option.value;
            this.$refs.hiddenInput.dispatchEvent(new Event('change'));
        },
        init() {
            if (this.selected) {
                const option = this.options.find(opt => opt.value == this.selected);
                if (option) this.search = option.label;
            }
        }
    }" class="relative">
        <input type="hidden" 
               name="{{ $name }}" 
               x-ref="hiddenInput"
               :value="selected"
               @if($required) required @endif>
        
        <div class="relative">
            <input type="text"
                   id="{{ $inputId }}"
                   x-model="search"
                   @click="open = !open"
                   @keydown.escape="open = false"
                   @keydown.arrow-down.prevent="open = true"
                   placeholder="{{ $placeholder ?: 'Wybierz opcję...' }}"
                   @if($disabled) disabled @endif
                   class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-50 disabled:text-gray-500 cursor-pointer
                          {{ $sizeClasses[$size] ?? $sizeClasses['md'] }}
                          {{ $hasError ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : '' }}">
            
            <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                <i class="fas fa-chevron-down text-gray-400" :class="{ 'rotate-180': open }"></i>
            </div>
        </div>

        <div x-show="open" 
             @click.away="open = false"
             x-transition:enter="transition ease-out duration-100"
             x-transition:enter-start="transform opacity-0 scale-95"
             x-transition:enter-end="transform opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-75"
             x-transition:leave-start="transform opacity-100 scale-100"
             x-transition:leave-end="transform opacity-0 scale-95"
             class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none">
            
            <template x-for="option in filteredOptions" :key="option.value">
                <div @click="selectOption(option)"
                     :class="{ 'bg-blue-600 text-white': selected == option.value, 'text-gray-900': selected != option.value }"
                     class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-50">
                    <span class="block truncate" :class="{ 'font-semibold': selected == option.value }">
                        <span x-text="option.label"></span>
                    </span>
                    <span x-show="selected == option.value" class="absolute inset-y-0 right-0 flex items-center pr-4">
                        <i class="fas fa-check"></i>
                    </span>
                </div>
            </template>
            
            <div x-show="filteredOptions.length === 0" class="py-2 px-3 text-gray-500 text-sm">
                Brak wyników
            </div>
        </div>
    </div>
    @else
    <!-- Standard Select -->
    <select 
        name="{{ $name }}{{ $multiple ? '[]' : '' }}"
        id="{{ $inputId }}"
        @if($required) required @endif
        @if($disabled) disabled @endif
        @if($multiple) multiple @endif
        {{ $attributes->except(['class', 'label', 'name', 'value', 'placeholder', 'required', 'disabled', 'error', 'help', 'size', 'options', 'multiple', 'searchable']) }}
        class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-50 disabled:text-gray-500
               {{ $sizeClasses[$size] ?? $sizeClasses['md'] }}
               {{ $hasError ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : '' }}"
    >
        @if($placeholder && !$multiple)
        <option value="">{{ $placeholder }}</option>
        @endif
        
        @if(is_array($options))
            @foreach($options as $optionValue => $optionLabel)
                @if(is_array($optionLabel))
                    <!-- Option Group -->
                    <optgroup label="{{ $optionValue }}">
                        @foreach($optionLabel as $groupValue => $groupLabel)
                            <option value="{{ $groupValue }}" 
                                    @if(old($name, $value) == $groupValue || (is_array(old($name, $value)) && in_array($groupValue, old($name, $value)))) selected @endif>
                                {{ $groupLabel }}
                            </option>
                        @endforeach
                    </optgroup>
                @else
                    <!-- Regular Option -->
                    <option value="{{ $optionValue }}" 
                            @if(old($name, $value) == $optionValue || (is_array(old($name, $value)) && in_array($optionValue, old($name, $value)))) selected @endif>
                        {{ $optionLabel }}
                    </option>
                @endif
            @endforeach
        @else
            {{ $slot }}
        @endif
    </select>
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