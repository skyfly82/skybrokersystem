@props([
    'type' => 'info',
    'title' => null,
    'dismissible' => false,
    'icon' => true,
    'border' => false,
    'size' => 'md'
])

@php
    $alertClasses = [
        'info' => [
            'bg' => 'bg-blue-50',
            'border' => 'border-blue-200',
            'text' => 'text-blue-800',
            'icon' => 'fas fa-info-circle text-blue-400',
            'title' => 'text-blue-800'
        ],
        'success' => [
            'bg' => 'bg-green-50',
            'border' => 'border-green-200', 
            'text' => 'text-green-800',
            'icon' => 'fas fa-check-circle text-green-400',
            'title' => 'text-green-800'
        ],
        'warning' => [
            'bg' => 'bg-yellow-50',
            'border' => 'border-yellow-200',
            'text' => 'text-yellow-800', 
            'icon' => 'fas fa-exclamation-triangle text-yellow-400',
            'title' => 'text-yellow-800'
        ],
        'error' => [
            'bg' => 'bg-red-50',
            'border' => 'border-red-200',
            'text' => 'text-red-800',
            'icon' => 'fas fa-exclamation-circle text-red-400',
            'title' => 'text-red-800'
        ]
    ];

    $classes = $alertClasses[$type];
    
    $sizeClasses = [
        'sm' => 'p-3 text-sm',
        'md' => 'p-4', 
        'lg' => 'p-6 text-lg'
    ];
@endphp

<div {{ $attributes->merge([
    'class' => implode(' ', [
        'rounded-md',
        $classes['bg'],
        $border ? $classes['border'] . ' border' : '',
        $sizeClasses[$size] ?? $sizeClasses['md'],
        $dismissible ? 'pr-12' : ''
    ])
]) }} 
     x-data="{ show: true }" 
     x-show="show"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform scale-95"
     x-transition:enter-end="opacity-100 transform scale-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 transform scale-100"
     x-transition:leave-end="opacity-0 transform scale-95">
    
    <div class="flex">
        @if($icon)
        <div class="flex-shrink-0">
            <i class="{{ $classes['icon'] }}"></i>
        </div>
        @endif

        <div class="{{ $icon ? 'ml-3' : '' }} flex-1">
            @if($title)
            <h3 class="text-sm font-medium {{ $classes['title'] }}">
                {{ $title }}
            </h3>
            @endif
            
            <div class="{{ $title ? 'mt-2' : '' }} text-sm {{ $classes['text'] }}">
                {{ $slot }}
            </div>
        </div>

        @if($dismissible)
        <div class="ml-auto pl-3">
            <div class="-mx-1.5 -my-1.5">
                <button @click="show = false" 
                        type="button" 
                        class="inline-flex rounded-md p-1.5 {{ $classes['text'] }} hover:{{ $classes['bg'] }} focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-{{ substr($classes['bg'], 3) }} focus:ring-{{ substr($classes['border'], 7) }}">
                    <span class="sr-only">Zamknij</span>
                    <i class="fas fa-times w-5 h-5" aria-hidden="true"></i>
                </button>
            </div>
        </div>
        @endif
    </div>
</div>