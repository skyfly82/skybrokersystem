@props([
    'status',
    'type' => 'shipment', // shipment, payment, customer, order
    'size' => 'md',
    'dot' => true,
    'icon' => false
])

@php
    $statusConfig = [
        'shipment' => [
            'draft' => ['color' => 'gray', 'label' => 'Szkic', 'icon' => 'fa-file'],
            'created' => ['color' => 'blue', 'label' => 'Utworzona', 'icon' => 'fa-plus'],
            'printed' => ['color' => 'indigo', 'label' => 'Wydrukowana', 'icon' => 'fa-print'],
            'dispatched' => ['color' => 'yellow', 'label' => 'Nadana', 'icon' => 'fa-shipping-fast'],
            'in_transit' => ['color' => 'purple', 'label' => 'W transporcie', 'icon' => 'fa-truck'],
            'out_for_delivery' => ['color' => 'orange', 'label' => 'W doręczeniu', 'icon' => 'fa-route'],
            'delivered' => ['color' => 'green', 'label' => 'Dostarczona', 'icon' => 'fa-check'],
            'returned' => ['color' => 'red', 'label' => 'Zwrócona', 'icon' => 'fa-undo'],
            'cancelled' => ['color' => 'red', 'label' => 'Anulowana', 'icon' => 'fa-times'],
            'failed' => ['color' => 'red', 'label' => 'Błąd', 'icon' => 'fa-exclamation-triangle']
        ],
        'payment' => [
            'pending' => ['color' => 'yellow', 'label' => 'Oczekuje', 'icon' => 'fa-clock'],
            'processing' => ['color' => 'blue', 'label' => 'Przetwarzanie', 'icon' => 'fa-spinner'],
            'completed' => ['color' => 'green', 'label' => 'Zakończona', 'icon' => 'fa-check'],
            'failed' => ['color' => 'red', 'label' => 'Niepowodzenie', 'icon' => 'fa-times'],
            'cancelled' => ['color' => 'gray', 'label' => 'Anulowana', 'icon' => 'fa-ban'],
            'refunded' => ['color' => 'purple', 'label' => 'Zwrócona', 'icon' => 'fa-undo']
        ],
        'customer' => [
            'active' => ['color' => 'green', 'label' => 'Aktywny', 'icon' => 'fa-check'],
            'pending' => ['color' => 'yellow', 'label' => 'Oczekujący', 'icon' => 'fa-clock'],
            'suspended' => ['color' => 'red', 'label' => 'Zawieszony', 'icon' => 'fa-ban'],
            'inactive' => ['color' => 'gray', 'label' => 'Nieaktywny', 'icon' => 'fa-pause']
        ],
        'order' => [
            'new' => ['color' => 'blue', 'label' => 'Nowe', 'icon' => 'fa-plus'],
            'confirmed' => ['color' => 'green', 'label' => 'Potwierdzone', 'icon' => 'fa-check'],
            'processing' => ['color' => 'yellow', 'label' => 'W realizacji', 'icon' => 'fa-cog'],
            'completed' => ['color' => 'green', 'label' => 'Zakończone', 'icon' => 'fa-check-double'],
            'cancelled' => ['color' => 'red', 'label' => 'Anulowane', 'icon' => 'fa-times']
        ]
    ];
    
    $config = $statusConfig[$type][$status] ?? ['color' => 'gray', 'label' => ucfirst($status), 'icon' => 'fa-question'];
    $color = $config['color'];
    $label = $config['label'];
    $iconClass = $config['icon'];
    
    $colorClasses = [
        'gray' => 'bg-gray-100 text-gray-800',
        'red' => 'bg-red-100 text-red-800',
        'yellow' => 'bg-yellow-100 text-yellow-800',
        'green' => 'bg-green-100 text-green-800',
        'blue' => 'bg-blue-100 text-blue-800',
        'indigo' => 'bg-indigo-100 text-indigo-800',
        'purple' => 'bg-purple-100 text-purple-800',
        'pink' => 'bg-pink-100 text-pink-800',
        'orange' => 'bg-orange-100 text-orange-800'
    ];
    
    $dotClasses = [
        'gray' => 'bg-gray-400',
        'red' => 'bg-red-400',
        'yellow' => 'bg-yellow-400',
        'green' => 'bg-green-400',
        'blue' => 'bg-blue-400',
        'indigo' => 'bg-indigo-400',
        'purple' => 'bg-purple-400',
        'pink' => 'bg-pink-400',
        'orange' => 'bg-orange-400'
    ];
    
    $sizeClasses = [
        'sm' => 'px-2 py-0.5 text-xs',
        'md' => 'px-2.5 py-0.5 text-xs',
        'lg' => 'px-3 py-1 text-sm'
    ];
    
    $dotSizeClasses = [
        'sm' => 'w-1.5 h-1.5',
        'md' => 'w-2 h-2', 
        'lg' => 'w-2.5 h-2.5'
    ];
@endphp

<span {{ $attributes->merge([
    'class' => 'inline-flex items-center font-medium rounded-full ' . 
               ($colorClasses[$color] ?? $colorClasses['gray']) . ' ' .
               ($sizeClasses[$size] ?? $sizeClasses['md'])
]) }}>
    @if($dot && !$icon)
        <div class="rounded-full mr-1.5 {{ $dotClasses[$color] ?? $dotClasses['gray'] }} {{ $dotSizeClasses[$size] ?? $dotSizeClasses['md'] }}"></div>
    @endif
    
    @if($icon)
        <i class="fas {{ $iconClass }} mr-1.5 {{ $size === 'sm' ? 'text-xs' : ($size === 'lg' ? 'text-sm' : 'text-xs') }}"></i>
    @endif
    
    {{ $label }}
</span>