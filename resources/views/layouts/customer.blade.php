<!DOCTYPE html>
<html lang="pl" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Panel Klienta') | {{ config('app.name', 'SkyBrokerSystem') }}</title>
    
    <!-- Fonts - Brand Guidelines: Be Vietnam Pro (headings) + Mulish (content) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;600;700;800&family=Mulish:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Tailwind CSS - CDN for development only -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Suppress CDN warning in development
        if (typeof tailwind !== 'undefined') {
            tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'heading': ['Be Vietnam Pro', 'sans-serif'],
                        'body': ['Mulish', 'sans-serif'],
                        sans: ['Mulish', 'sans-serif'],
                    },
                    colors: {
                        // Brand colors according to brandbook
                        'skywave': '#2F7DFF',
                        'black-coal': '#0C0212',
                        'pure-white': '#FFFFFF',
                        // Additional brand colors (max 20% usage)
                        'bold-yellow': '#FFD700',
                        'bold-pink': '#FF69B4',
                        'purple-blue': '#6366F1',
                        primary: {
                            50: '#eff6ff',
                            500: '#2F7DFF',
                            600: '#1D5FD9',
                            700: '#1D4ED8',
                            900: '#0C0212',
                        }
                    }
                }
            }
        };
        }
    </script>
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Chart.js loaded conditionally via @stack('scripts') -->
    
    @stack('head')
</head>
<body class="h-full font-body antialiased" x-data="{ sidebarOpen: false }">
    <div class="min-h-full">
        <!-- Sidebar -->
        <div class="fixed inset-y-0 z-50 flex w-64 flex-col" x-show="sidebarOpen" x-transition:enter="transition ease-in-out duration-300 transform" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in-out duration-300 transform" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" style="display: none;">
            <!-- Overlay -->
            <div class="fixed inset-0 bg-gray-900/80 lg:hidden" @click="sidebarOpen = false"></div>
            
            <!-- Sidebar panel -->
            <div class="relative flex w-full max-w-xs flex-1 flex-col bg-white">
                <div class="absolute top-0 right-0 -mr-12 pt-2">
                    <button type="button" class="ml-1 flex h-10 w-10 items-center justify-center rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white lg:hidden" @click="sidebarOpen = false">
                        <span class="sr-only">Zamknij sidebar</span>
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <div class="flex flex-1 flex-col overflow-y-auto pt-5 pb-4">
                    <!-- Logo -->
                    <div class="flex flex-shrink-0 items-center px-4">
                        <img class="h-8 w-auto" src="{{ asset('images/logo.svg') }}" alt="{{ config('app.name') }}">
                        <span class="ml-2 text-xl font-bold text-gray-900">SkyBroker</span>
                    </div>
                    
                    <!-- Navigation -->
                    <nav class="mt-8 flex-1 space-y-1 px-2">
                        <a href="{{ route('customer.dashboard') }}" class="group flex items-center rounded-md px-2 py-2 text-sm font-medium {{ request()->routeIs('customer.dashboard') ? 'bg-blue-100 text-blue-900' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                            <svg class="mr-3 h-6 w-6 {{ request()->routeIs('customer.dashboard') ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v3H8V5z" />
                            </svg>
                            Dashboard
                        </a>
                        
                        <a href="{{ route('customer.shipments.index') }}" class="group flex items-center rounded-md px-2 py-2 text-sm font-medium {{ request()->routeIs('customer.shipments.*') ? 'bg-blue-100 text-blue-900' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                            <svg class="mr-3 h-6 w-6 {{ request()->routeIs('customer.shipments.*') ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                            </svg>
                            Przesyłki
                        </a>
                        
                        <a href="{{ route('customer.payments.index') }}" class="group flex items-center rounded-md px-2 py-2 text-sm font-medium {{ request()->routeIs('customer.payments.*') ? 'bg-blue-100 text-blue-900' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                            <svg class="mr-3 h-6 w-6 {{ request()->routeIs('customer.payments.*') ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                            </svg>
                            Płatności
                        </a>
                        
                        <!-- Submenu dla zarządzania -->
                        <div x-data="{ open: {{ request()->routeIs('customer.profile.*') || request()->routeIs('customer.users.*') ? 'true' : 'false' }} }">
                            <button @click="open = !open" class="group flex w-full items-center rounded-md px-2 py-2 text-left text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900">
                                <svg class="mr-3 h-6 w-6 text-gray-400 group-hover:text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Zarządzanie
                                <svg class="ml-auto h-4 w-4 transform transition-transform" :class="{ 'rotate-90': open }" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                </svg>
                            </button>
                            
                            <div x-show="open" x-transition class="space-y-1 pl-11">
                                <a href="{{ route('customer.profile.show') }}" class="group flex items-center rounded-md px-2 py-2 text-sm font-medium {{ request()->routeIs('customer.profile.*') ? 'bg-blue-100 text-blue-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                                    Profil
                                </a>
                                
                                @if(auth()->user()->isAdmin())
                                <a href="{{ route('customer.users.index') }}" class="group flex items-center rounded-md px-2 py-2 text-sm font-medium {{ request()->routeIs('customer.users.*') ? 'bg-blue-100 text-blue-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                                    Użytkownicy
                                </a>
                                @endif
                            </div>
                        </div>
                        
                        <!-- API Documentation -->
                        <a href="#" class="group flex items-center rounded-md px-2 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900">
                            <svg class="mr-3 h-6 w-6 text-gray-400 group-hover:text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75L22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3l-4.5 16.5" />
                            </svg>
                            API
                        </a>
                    </nav>
                </div>
                
                <!-- Customer info -->
                <div class="flex flex-shrink-0 border-t border-gray-200 p-4">
                    <div class="group block w-full flex-shrink-0">
                        <div class="flex items-center">
                            <div class="inline-block h-9 w-9 rounded-full bg-gray-300 flex items-center justify-center">
                                <span class="text-sm font-medium text-gray-700">
                                    {{ substr(auth()->user()->customer->company_name, 0, 2) }}
                                </span>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-700 group-hover:text-gray-900">
                                    {{ auth()->user()->customer->company_name }}
                                </p>
                                <p class="text-xs font-medium text-gray-500 group-hover:text-gray-700">
                                    {{ number_format(auth()->user()->customer->current_balance, 2) }} PLN
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Desktop sidebar -->
        <div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-64 lg:flex-col">
            <div class="flex flex-1 flex-col overflow-y-auto bg-white border-r border-gray-200">
                <div class="flex flex-1 flex-col pt-5 pb-4">
                    <!-- Logo -->
                    <div class="flex flex-shrink-0 items-center px-4">
                        <img class="h-8 w-auto" src="{{ asset('images/logo.svg') }}" alt="{{ config('app.name') }}">
                        <span class="ml-2 text-xl font-bold text-gray-900">SkyBroker</span>
                    </div>
                    
                    <!-- Navigation - same as mobile -->
                    <nav class="mt-8 flex-1 space-y-1 px-2">
                        <a href="{{ route('customer.dashboard') }}" class="group flex items-center rounded-md px-2 py-2 text-sm font-medium {{ request()->routeIs('customer.dashboard') ? 'bg-blue-100 text-blue-900' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                            <svg class="mr-3 h-6 w-6 {{ request()->routeIs('customer.dashboard') ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v3H8V5z" />
                            </svg>
                            Dashboard
                        </a>
                        
                        <a href="{{ route('customer.shipments.index') }}" class="group flex items-center rounded-md px-2 py-2 text-sm font-medium {{ request()->routeIs('customer.shipments.*') ? 'bg-blue-100 text-blue-900' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                            <svg class="mr-3 h-6 w-6 {{ request()->routeIs('customer.shipments.*') ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                            </svg>
                            Przesyłki
                        </a>
                        
                        <a href="{{ route('customer.payments.index') }}" class="group flex items-center rounded-md px-2 py-2 text-sm font-medium {{ request()->routeIs('customer.payments.*') ? 'bg-blue-100 text-blue-900' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                            <svg class="mr-3 h-6 w-6 {{ request()->routeIs('customer.payments.*') ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                            </svg>
                            Płatności
                        </a>
                        
                        <!-- Submenu dla zarządzania -->
                        <div x-data="{ open: {{ request()->routeIs('customer.profile.*') || request()->routeIs('customer.users.*') ? 'true' : 'false' }} }">
                            <button @click="open = !open" class="group flex w-full items-center rounded-md px-2 py-2 text-left text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900">
                                <svg class="mr-3 h-6 w-6 text-gray-400 group-hover:text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Zarządzanie
                                <svg class="ml-auto h-4 w-4 transform transition-transform" :class="{ 'rotate-90': open }" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                </svg>
                            </button>
                            
                            <div x-show="open" x-transition class="space-y-1 pl-11">
                                <a href="{{ route('customer.profile.show') }}" class="group flex items-center rounded-md px-2 py-2 text-sm font-medium {{ request()->routeIs('customer.profile.*') ? 'bg-blue-100 text-blue-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                                    Profil
                                </a>
                                
                                @if(auth()->user()->isAdmin())
                                <a href="{{ route('customer.users.index') }}" class="group flex items-center rounded-md px-2 py-2 text-sm font-medium {{ request()->routeIs('customer.users.*') ? 'bg-blue-100 text-blue-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                                    Użytkownicy
                                </a>
                                @endif
                            </div>
                        </div>
                        
                        <!-- API Documentation -->
                        <a href="#" class="group flex items-center rounded-md px-2 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900">
                            <svg class="mr-3 h-6 w-6 text-gray-400 group-hover:text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75L22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3l-4.5 16.5" />
                            </svg>
                            API
                        </a>
                    </nav>
                </div>
                
                <!-- Customer info -->
                <div class="flex flex-shrink-0 border-t border-gray-200 p-4">
                    <div class="group block w-full flex-shrink-0">
                        <div class="flex items-center">
                            <div class="inline-block h-9 w-9 rounded-full bg-gray-300 flex items-center justify-center">
                                <span class="text-sm font-medium text-gray-700">
                                    {{ substr(auth()->user()->customer->company_name, 0, 2) }}
                                </span>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-700 group-hover:text-gray-900">
                                    {{ auth()->user()->customer->company_name }}
                                </p>
                                <p class="text-xs font-medium text-gray-500 group-hover:text-gray-700">
                                    {{ number_format(auth()->user()->customer->current_balance, 2) }} PLN
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main content -->
        <div class="lg:pl-64">
            <!-- Top bar -->
            <div class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-gray-200 bg-white px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8">
                <!-- Mobile menu button -->
                <button type="button" class="-m-2.5 p-2.5 text-gray-700 lg:hidden" @click="sidebarOpen = true">
                    <span class="sr-only">Otwórz sidebar</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
                
                <div class="h-6 w-px bg-gray-900/10 lg:hidden" aria-hidden="true"></div>
                
                <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
                    <div class="relative flex flex-1 items-center">
                        <!-- Breadcrumbs -->
                        <nav class="flex" aria-label="Breadcrumb">
                            @hasSection('breadcrumbs')
                                @yield('breadcrumbs')
                            @else
                                <ol class="flex items-center space-x-4">
                                    <li>
                                        <div class="flex items-center">
                                            <a href="{{ route('customer.dashboard') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700">Dashboard</a>
                                        </div>
                                    </li>
                                    @if(!request()->routeIs('customer.dashboard'))
                                    <li>
                                        <div class="flex items-center">
                                            <svg class="h-5 w-5 flex-shrink-0 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                                            </svg>
                                            <span class="ml-4 text-sm font-medium text-gray-900">
                                                @yield('page-title', 'Strona')
                                            </span>
                                        </div>
                                    </li>
                                    @endif
                                </ol>
                            @endif
                        </nav>
                    </div>
                    
                    <div class="flex items-center gap-x-4 lg:gap-x-6">
                        <!-- Balance indicator -->
                        <div class="hidden lg:flex lg:items-center lg:gap-x-2">
                            <div class="text-sm">
                                <span class="text-gray-500">Saldo:</span>
                                <span class="font-medium {{ auth()->user()->customer->current_balance > 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ number_format(auth()->user()->customer->current_balance, 2) }} PLN
                                </span>
                            </div>
                            @if(auth()->user()->customer->current_balance < 100)
                            <a href="{{ route('customer.payments.topup') }}" class="inline-flex items-center rounded-md bg-blue-600 px-2.5 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                                Doładuj
                            </a>
                            @endif
                        </div>
                        
                        <!-- Notifications -->
                        <div class="relative" x-data="{ open: false }">
                            <button type="button" class="relative rounded-full bg-white p-1 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2" @click="open = !open">
                                <span class="sr-only">Zobacz powiadomienia</span>
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                                </svg>
                                <!-- Notification badge -->
                                <span class="absolute -top-0.5 -right-0.5 block h-2 w-2 rounded-full bg-red-400 ring-2 ring-white"></span>
                            </button>
                            
                            <!-- Notifications dropdown -->
                            <div x-show="open" @click.outside="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute right-0 z-10 mt-2 w-80 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none" style="display: none;">
                                <div class="px-4 py-2 border-b border-gray-200">
                                    <p class="text-sm font-medium text-gray-900">Powiadomienia</p>
                                </div>
                                <div class="max-h-80 overflow-y-auto">
                                    <!-- Sample notification items -->
                                    <a href="#" class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-50">
                                        <div class="flex">
                                            <div class="flex-shrink-0">
                                                <div class="h-2 w-2 mt-2 bg-blue-600 rounded-full"></div>
                                            </div>
                                            <div class="ml-3">
                                                <p class="font-medium">Przesyłka została dostarczona</p>
                                                <p class="text-xs text-gray-500">Numer: 1234567890</p>
                                                <p class="text-xs text-gray-400">2 godziny temu</p>
                                            </div>
                                        </div>
                                    </a>
                                    <a href="#" class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-50">
                                        <div class="flex">
                                            <div class="flex-shrink-0">
                                                <div class="h-2 w-2 mt-2 bg-green-600 rounded-full"></div>
                                            </div>
                                            <div class="ml-3">
                                                <p class="font-medium">Płatność została zrealizowana</p>
                                                <p class="text-xs text-gray-500">Kwota: 150.00 PLN</p>
                                                <p class="text-xs text-gray-400">1 dzień temu</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <div class="border-t border-gray-200 px-4 py-2">
                                    <a href="#" class="text-sm font-medium text-blue-600 hover:text-blue-500">Zobacz wszystkie</a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- User menu -->
                        <div class="relative" x-data="{ open: false }">
                            <button type="button" class="flex items-center gap-x-2 text-sm font-semibold leading-6 text-gray-900" @click="open = !open">
                                <span class="sr-only">Otwórz menu użytkownika</span>
                                <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                                    <span class="text-sm font-medium text-gray-700">
                                        {{ substr(auth()->user()->first_name, 0, 1) }}{{ substr(auth()->user()->last_name, 0, 1) }}
                                    </span>
                                </div>
                                <span class="hidden lg:flex lg:items-center">
                                    <span class="text-sm font-semibold leading-6 text-gray-900">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</span>
                                    <svg class="ml-2 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                            </button>
                            
                            <!-- User dropdown -->
                            <div x-show="open" @click.outside="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none" style="display: none;">
                                <div class="px-4 py-2 border-b border-gray-200">
                                    <p class="text-sm font-medium text-gray-900">{{ auth()->user()->full_name }}</p>
                                    <p class="text-sm text-gray-500">{{ auth()->user()->email }}</p>
                                </div>
                                
                                <a href="{{ route('customer.profile.show') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    <div class="flex items-center">
                                        <svg class="mr-3 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                        </svg>
                                        Mój profil
                                    </div>
                                </a>
                                
                                <a href="{{ route('customer.profile.notifications') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    <div class="flex items-center">
                                        <svg class="mr-3 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                                        </svg>
                                        Powiadomienia
                                    </div>
                                </a>
                                
                                <div class="border-t border-gray-200 my-1"></div>
                                
                                <form method="POST" action="{{ route('customer.logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50">
                                        <div class="flex items-center">
                                            <svg class="mr-3 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                                            </svg>
                                            Wyloguj się
                                        </div>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Page content -->
            <main class="py-8">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <!-- Flash messages -->
                    @if(session('success'))
                    <div class="mb-6 rounded-md bg-green-50 p-4" x-data="{ show: true }" x-show="show" x-transition>
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-800">
                                    {{ session('success') }}
                                </p>
                            </div>
                            <div class="ml-auto pl-3">
                                <button type="button" class="-mx-1.5 -my-1.5 rounded-md bg-green-50 p-1.5 text-green-500 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-green-600 focus:ring-offset-2 focus:ring-offset-green-50" @click="show = false">
                                    <span class="sr-only">Dismiss</span>
                                    <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    @if(session('error') || $errors->any())
                    <div class="mb-6 rounded-md bg-red-50 p-4" x-data="{ show: true }" x-show="show" x-transition>
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <div class="text-sm text-red-700">
                                    @if(session('error'))
                                        <p>{{ session('error') }}</p>
                                    @endif
                                    @if($errors->any())
                                        @foreach($errors->all() as $error)
                                            <p>{{ $error }}</p>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                            <div class="ml-auto pl-3">
                                <button type="button" class="-mx-1.5 -my-1.5 rounded-md bg-red-50 p-1.5 text-red-500 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-600 focus:ring-offset-2 focus:ring-offset-red-50" @click="show = false">
                                    <span class="sr-only">Dismiss</span>
                                    <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    @if(session('warning'))
                    <div class="mb-6 rounded-md bg-yellow-50 p-4" x-data="{ show: true }" x-show="show" x-transition>
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-yellow-800">
                                    {{ session('warning') }}
                                </p>
                            </div>
                            <div class="ml-auto pl-3">
                                <button type="button" class="-mx-1.5 -my-1.5 rounded-md bg-yellow-50 p-1.5 text-yellow-500 hover:bg-yellow-100 focus:outline-none focus:ring-2 focus:ring-yellow-600 focus:ring-offset-2 focus:ring-offset-yellow-50" @click="show = false">
                                    <span class="sr-only">Dismiss</span>
                                    <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
    
    @stack('scripts')
    
    <!-- Global JavaScript -->
    <script>
        // Auto-hide flash messages after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const alerts = document.querySelectorAll('[x-data*="show: true"]');
                alerts.forEach(function(alert) {
                    if (alert.__x) {
                        alert.__x.$data.show = false;
                    }
                });
            }, 5000);
        });
        
        // CSRF token for AJAX requests
        window.Laravel = {
            csrfToken: '{{ csrf_token() }}'
        };
        
        // Set CSRF token for all AJAX requests
        const token = document.querySelector('meta[name="csrf-token"]');
        if (token && window.axios && window.axios.defaults) {
            window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.getAttribute('content');
        }
    </script>
</body>
</html>