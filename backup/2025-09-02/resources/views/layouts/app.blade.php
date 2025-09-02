<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'SkyBrokerSystem')</title>
    
    <!-- Fonts - Brand Guidelines: Be Vietnam Pro (headings) + Mulish (content) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;600;700;800&family=Mulish:wght@400;600;700;800&display=swap" rel="stylesheet" />
    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @stack('styles')
</head>
<body class="h-full font-body antialiased">
    <div class="min-h-full">
        <!-- Navigation -->
        @include('layouts.navigation')
        
        <!-- Page Header -->
        @hasSection('header')
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    @yield('header')
                </div>
            </header>
        @endif
        
        <!-- Main Content -->
        <main class="flex-1">
            <!-- Flash Messages -->
            @include('components.flash-messages')
            
            <!-- Page Content -->
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                @yield('content')
            </div>
        </main>
        
        <!-- Footer -->
        @include('layouts.footer')
    </div>
    
    <!-- Toast Notifications -->
    @include('components.toast')
    
    @stack('scripts')
</body>
</html>