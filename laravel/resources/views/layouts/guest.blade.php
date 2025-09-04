<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- SEO Meta Tags -->
    <title>@yield('title', 'SkyBrokerSystem - ' . __('common.professional_courier') . ' ' . __('common.brokerage_platform'))</title>
    <meta name="description" content="@yield('description', __('common.hero_description'))">
    <meta name="keywords" content="@yield('keywords', 'courier, brokerage, shipping, logistics, InPost, payments, tracking, przesyłki, kurierzy, logistyka')">
    <meta name="author" content="SkyBrokerSystem">
    <meta name="robots" content="@yield('robots', 'index, follow')">

    <!-- Language and Regional -->
    <meta name="language" content="{{ app()->getLocale() }}">
    <link rel="alternate" hreflang="pl" href="{{ url('/language/pl') }}">
    <link rel="alternate" hreflang="en" href="{{ url('/language/en') }}">
    <link rel="alternate" hreflang="x-default" href="{{ url('/') }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="{{ request()->url() }}">
    <meta property="og:title" content="@yield('og_title', 'SkyBrokerSystem - ' . __('common.professional_courier') . ' ' . __('common.brokerage_platform'))">
    <meta property="og:description" content="@yield('og_description', __('common.hero_description'))">
    <meta property="og:image" content="@yield('og_image', asset('images/og-image.jpg'))">
    <meta property="og:locale" content="{{ str_replace('-', '_', app()->getLocale()) }}">
    <meta property="og:site_name" content="SkyBrokerSystem">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ request()->url() }}">
    <meta property="twitter:title" content="@yield('twitter_title', 'SkyBrokerSystem - ' . __('common.professional_courier') . ' ' . __('common.brokerage_platform'))">
    <meta property="twitter:description" content="@yield('twitter_description', __('common.hero_description'))">
    <meta property="twitter:image" content="@yield('twitter_image', asset('images/twitter-image.jpg'))">

    <!-- Canonical URL -->
    <link rel="canonical" href="@yield('canonical', request()->url())">

    <!-- Preconnect for performance -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;600;700;800&family=Mulish:wght@400;600;700;800&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

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
                        // Brand colors according to brandbook
                        'skywave': '#2F7DFF',
                        'black-coal': '#0C0212',
                        'pure-white': '#FFFFFF',
                        // Additional brand colors (max 20% usage)
                        'bold-yellow': '#FFD700',
                        'bold-pink': '#FF69B4',
                        'purple-blue': '#6366F1',
                        brand: {
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

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Custom Styles -->
    <style>
        [x-cloak] { display: none !important; }

        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>

    @stack('styles')

    <!-- Structured Data (JSON-LD) -->
    @stack('structured-data')
</head>
<body class="font-body antialiased">
    <div class="min-h-screen bg-gray-50">
        @yield('content')
    </div>

    @stack('scripts')

    <!-- Google Analytics (placeholder) -->
    @if(config('services.google_analytics.id'))
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('services.google_analytics.id') }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ config('services.google_analytics.id') }}');
    </script>
    @endif
</body>
</html>
