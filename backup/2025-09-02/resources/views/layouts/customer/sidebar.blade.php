<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Customer Panel') - SkyBrokerSystem</title>
    
    <!-- Fonts - Brand Guidelines: Be Vietnam Pro (headings) + Mulish (content) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;600;700;800&family=Mulish:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'heading': ['Be Vietnam Pro', 'sans-serif'],
                        'body': ['Mulish', 'sans-serif'],
                        sans: ['Mulish', 'sans-serif'],
                    },
                    colors: {
                        'skywave': '#2F7DFF',
                        'black-coal': '#0C0212',
                        'pure-white': '#FFFFFF',
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
        }
    </script>
    
    @stack('styles')
</head>
<body class="h-full font-body antialiased" x-data="{ sidebarOpen: false }">
    <div class="min-h-full">
        <!-- Mobile sidebar -->
        <div x-show="sidebarOpen" class="relative z-50 lg:hidden" x-cloak>
            <div x-show="sidebarOpen" 
                 x-transition:enter="transition-opacity ease-linear duration-300" 
                 x-transition:enter-start="opacity-0" 
                 x-transition:enter-end="opacity-100" 
                 x-transition:leave="transition-opacity ease-linear duration-300" 
                 x-transition:leave-start="opacity-100" 
                 x-transition:leave-end="opacity-0" 
                 class="fixed inset-0 bg-gray-900/80" 
                 @click="sidebarOpen = false"></div>
            
            <div x-show="sidebarOpen" 
                 x-transition:enter="transition ease-in-out duration-300 transform" 
                 x-transition:enter-start="-translate-x-full" 
                 x-transition:enter-end="translate-x-0" 
                 x-transition:leave="transition ease-in-out duration-300 transform" 
                 x-transition:leave-start="translate-x-0" 
                 x-transition:leave-end="-translate-x-full" 
                 class="relative mr-16 flex w-full max-w-xs flex-1">
                
                <div class="absolute left-full top-0 flex w-16 justify-center pt-5">
                    <button type="button" class="-m-2.5 p-2.5" @click="sidebarOpen = false">
                        <span class="sr-only">Close sidebar</span>
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                @include('layouts.customer.sidebar')
            </div>
        </div>
        
        <!-- Static sidebar for desktop -->
        <div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col">
            @include('layouts.customer.sidebar')
        </div>
        
        <div class="lg:pl-72">
            <!-- Top navigation -->
            <div class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-gray-200 bg-white px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8">
                <button type="button" class="-m-2.5 p-2.5 text-gray-700 lg:hidden" @click="sidebarOpen = true">
                    <span class="sr-only">Open sidebar</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
                
                <!-- Separator -->
                <div class="h-6 w-px bg-gray-200 lg:hidden"></div>
                
                <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
                    <!-- Balance Display -->
                    <div class="flex items-center">
                        <div class="text-sm">
                            <span class="font-medium text-gray-900">Balance:</span>
                            <span class="ml-1 font-bold text-primary-600">
                                {{ number_format(auth()->user()->customer->current_balance ?? 0, 2) }} PLN
                            </span>
                            @if((auth()->user()->customer->current_balance ?? 0) < 100)
                                <span class="ml-2 inline-flex items-center rounded-full bg-warning-100 px-2.5 py-0.5 text-xs font-medium text-warning-800">
                                    Low Balance
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="flex flex-1 justify-end">
                        <div class="flex items-center gap-x-4 lg:gap-x-6">
                            <!-- Notifications -->
                            <button type="button" class="-m-2.5 p-2.5 text-gray-400 hover:text-gray-500">
                                <span class="sr-only">View notifications</span>
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                                </svg>
                            </button>
                            
                            <!-- Separator -->
                            <div class="hidden lg:block lg:h-6 lg:w-px lg:bg-gray-200"></div>
                            
                            <!-- Profile dropdown -->
                            @include('layouts.customer.profile-dropdown')
                        </div>
                    </div>
                </div>
            </div>
            
            <main class="py-10">
                <div class="px-4 sm:px-6 lg:px-8">
                    <!-- Flash Messages -->
                    @include('components.flash-messages')
                    
                    <!-- Page Header -->
                    @hasSection('header')
                        <div class="md:flex md:items-center md:justify-between mb-8">
                            <div class="min-w-0 flex-1">
                                @yield('header')
                            </div>
                            @hasSection('actions')
                                <div class="mt-4 flex md:ml-4 md:mt-0">
                                    @yield('actions')
                                </div>
                            @endif
                        </div>
                    @endif
                    
                    <!-- Page Content -->
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
    
    <!-- Toast Notifications -->
    @include('components.toast')
    
    @stack('scripts')
</body>
</html>