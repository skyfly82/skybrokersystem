@extends('layouts.guest')

@section('title', 'SkyBrokerSystem - ' . __('common.professional_courier') . ' ' . __('common.courier') . ' ' . __('common.brokerage_platform'))
@section('description', __('common.hero_description'))
@section('keywords', 'courier brokerage, shipping management, InPost integration, payment processing, logistics platform, przesyłki kurierskie, zarządzanie wysyłką, integracja InPost, płatności online')
@section('og_type', 'website')
@section('og_title', 'SkyBrokerSystem - ' . __('common.professional_courier') . ' ' . __('common.brokerage_platform'))
@section('robots', 'index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1')

@push('structured-data')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Organization",
  "name": "SkyBrokerSystem",
  "url": "{{ url('/') }}",
  "logo": "{{ asset('images/logo_1.png') }}",
  "description": "{{ __('common.hero_description') }}",
  "founder": {
    "@type": "Organization",
    "name": "SkyBrokerSystem"
  },
  "foundingDate": "2024",
  "industry": "Logistics and Courier Services",
  "services": [
    "{{ __('common.shipment_management') }}",
    "{{ __('common.payment_management') }}",
    "{{ __('common.multi_carrier_integration') }}",
    "{{ __('common.realtime_analytics') }}"
  ],
  "address": {
    "@type": "PostalAddress",
    "addressCountry": "PL"
  },
  "contactPoint": {
    "@type": "ContactPoint",
    "contactType": "Customer Service",
    "availableLanguage": ["Polish", "English"]
  },
  "sameAs": [
    "{{ url('/') }}"
  ]
}
</script>

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebSite",
  "name": "SkyBrokerSystem",
  "url": "{{ url('/') }}",
  "description": "{{ __('common.hero_description') }}",
  "inLanguage": ["pl", "en"],
  "potentialAction": {
    "@type": "SearchAction",
    "target": {
      "@type": "EntryPoint",
      "urlTemplate": "{{ url('/') }}?q={search_term_string}"
    },
    "query-input": "required name=search_term_string"
  }
}
</script>

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "SoftwareApplication",
  "name": "SkyBrokerSystem",
  "applicationCategory": "BusinessApplication",
  "operatingSystem": "Web Browser",
  "description": "{{ __('common.hero_description') }}",
  "offers": {
    "@type": "Offer",
    "price": "0",
    "priceCurrency": "PLN",
    "description": "{{ __('common.start_free_trial') }}"
  },
  "featureList": [
    "{{ __('common.multi_carrier_integration') }}",
    "{{ __('common.payment_management') }}",
    "{{ __('common.realtime_analytics') }}",
    "{{ __('common.team_management') }}",
    "{{ __('common.enterprise_security') }}",
    "{{ __('common.api_integration') }}"
  ]
}
</script>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-sky-50 via-pure-white to-skywave/5">
    <!-- Flash Messages -->
    @if(session('info'))
        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 fixed top-4 right-4 z-50 max-w-sm shadow-lg rounded-md" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-700">
                        {{ session('info') }}
                    </p>
                </div>
                <div class="ml-auto pl-3">
                    <button @click="show = false" class="text-blue-400 hover:text-blue-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-400 p-4 fixed top-4 right-4 z-50 max-w-sm shadow-lg rounded-md" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700">
                        {{ session('success') }}
                    </p>
                </div>
                <div class="ml-auto pl-3">
                    <button @click="show = false" class="text-green-400 hover:text-green-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Navigation Bar -->
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center">
                            <img class="h-8 w-auto" src="{{ asset('images/logo_1.png') }}" alt="SkyBrokerSystem">
                            <span class="ml-3 text-xl font-heading font-bold text-black-coal">SkyBrokerSystem</span>
                            <span class="ml-2 px-2 py-1 bg-skywave/10 text-skywave text-xs font-medium rounded-full">V</span>
                        </div>
                    </div>
                </div>

                <!-- Navigation Links -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#features" class="text-gray-600 hover:text-skywave transition-colors duration-200 font-medium">{{ __('common.features') }}</a>
                    <a href="#services" class="text-gray-600 hover:text-skywave transition-colors duration-200 font-medium">{{ __('common.services') }}</a>
                    <a href="#contact" class="text-gray-600 hover:text-skywave transition-colors duration-200 font-medium">{{ __('common.contact') }}</a>
                    
                    <!-- Language Switcher -->
                    <x-language-switcher />
                    
                    <!-- Auth Buttons -->
                    <div class="flex items-center space-x-3">
                        @auth('system_user')
                            <!-- Admin Panel Button -->
                            <a href="{{ route('admin.dashboard') }}" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-all duration-200 shadow-sm hover:shadow-md flex items-center space-x-2">
                                <i class="fas fa-cogs text-sm"></i>
                                <span>Panel Admina</span>
                            </a>
                        @endauth
                        
                        @auth('customer_user')
                            <!-- Customer Panel Button -->
                            <a href="{{ route('customer.dashboard') }}" class="bg-skywave hover:bg-skywave/90 text-white px-4 py-2 rounded-lg font-medium transition-all duration-200 shadow-sm hover:shadow-md flex items-center space-x-2">
                                <i class="fas fa-user text-sm"></i>
                                <span>Panel Klienta</span>
                            </a>
                        @endauth
                        
                        @guest('system_user')
                        @guest('customer_user')
                            <!-- Guest Buttons -->
                            <a href="{{ route('customer.login') }}" class="text-skywave hover:text-skywave/80 font-medium transition-colors duration-200">
                                {{ __('common.sign_in') }}
                            </a>
                            <a href="{{ route('customer.register') }}" class="bg-skywave hover:bg-skywave/90 text-white px-4 py-2 rounded-lg font-medium transition-all duration-200 shadow-sm hover:shadow-md">
                                {{ __('common.get_started') }}
                            </a>
                        @endguest
                        @endguest
                    </div>
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-gray-600 hover:text-gray-900">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation Menu -->
        <div x-data="{ mobileMenuOpen: false }" x-show="mobileMenuOpen" x-cloak class="md:hidden bg-white border-t border-gray-200">
            <div class="px-4 py-3 space-y-3">
                <a href="#features" class="block font-body text-gray-600 hover:text-skywave font-medium">{{ __('common.features') }}</a>
                <a href="#services" class="block font-body text-gray-600 hover:text-skywave font-medium">{{ __('common.services') }}</a>
                <a href="#contact" class="block font-body text-gray-600 hover:text-skywave font-medium">{{ __('common.contact') }}</a>
                <div class="border-t border-gray-200 pt-3">
                    <x-language-switcher class="mb-3" />
                    @auth('system_user')
                        <!-- Admin Panel Button - Mobile -->
                        <a href="{{ route('admin.dashboard') }}" class="block w-full text-center bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium">
                            <i class="fas fa-cogs text-sm mr-2"></i>Panel Admina
                        </a>
                    @endauth
                    
                    @auth('customer_user')
                        <!-- Customer Panel Button - Mobile -->
                        <a href="{{ route('customer.dashboard') }}" class="block w-full text-center bg-skywave hover:bg-skywave/90 text-white px-4 py-2 rounded-lg font-medium">
                            <i class="fas fa-user text-sm mr-2"></i>Panel Klienta
                        </a>
                    @endauth
                    
                    @guest('system_user')
                    @guest('customer_user')
                        <!-- Guest Buttons - Mobile -->
                        <a href="{{ route('customer.login') }}" class="block w-full text-center text-skywave font-medium py-2">
                            {{ __('common.sign_in') }}
                        </a>
                        <a href="{{ route('customer.register') }}" class="block w-full text-center bg-skywave hover:bg-skywave/90 text-white px-4 py-2 rounded-lg font-medium mt-2">
                            {{ __('common.get_started') }}
                        </a>
                    @endguest
                    @endguest
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24">
            <div class="text-center fade-in">
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-heading font-bold text-black-coal mb-6">
                    {{ __('common.professional_courier') }} <span class="text-skywave">{{ __('common.courier') }}</span><br>
                    <span class="text-black-coal">{{ __('common.brokerage_platform') }}</span>
                </h1>
                <p class="text-xl font-body text-gray-600 mb-8 max-w-3xl mx-auto leading-relaxed">
                    {{ __('common.hero_description') }}
                </p>
                
                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                    <a href="{{ route('customer.register') }}" class="bg-skywave hover:bg-skywave/90 text-white px-8 py-4 rounded-xl font-heading font-semibold text-lg transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1" aria-label="{{ __('common.start_free_trial') }} - {{ __('auth.customer_register') }}">
                        <i class="fas fa-rocket mr-2" aria-hidden="true"></i>
                        {{ __('common.start_free_trial') }}
                    </a>
                    <a href="{{ route('customer.login') }}" class="bg-transparent hover:bg-skywave/5 text-skywave border-2 border-skywave hover:bg-skywave hover:text-white px-8 py-4 rounded-xl font-heading font-semibold text-lg transition-all duration-200 shadow-lg hover:shadow-xl" aria-label="{{ __('common.sign_in_to_dashboard') }} - {{ __('auth.customer_login') }}">
                        <i class="fas fa-sign-in-alt mr-2" aria-hidden="true"></i>
                        {{ __('common.sign_in_to_dashboard') }}
                    </a>
                </div>

                <!-- Admin Access -->
                <div class="mt-8 text-sm font-body text-gray-500">
                    <span>{{ __('common.administrator_question') }} </span>
                    <a href="{{ route('admin.login') }}" class="text-skywave hover:text-skywave/80 font-medium">
                        {{ __('common.access_admin_panel') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Background Decoration -->
        <div class="absolute top-0 right-0 -translate-y-12 translate-x-12">
            <div class="w-72 h-72 bg-gradient-to-br from-skywave/20 to-skywave/30 rounded-full mix-blend-multiply filter blur-xl opacity-70"></div>
        </div>
        <div class="absolute bottom-0 left-0 translate-y-12 -translate-x-12">
            <div class="w-72 h-72 bg-gradient-to-br from-skywave/10 to-skywave/20 rounded-full mix-blend-multiply filter blur-xl opacity-70"></div>
        </div>
    </div>

    <!-- Features Section -->
    <section id="features" class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl lg:text-4xl font-heading font-bold text-black-coal mb-4">
                    {{ __('common.why_choose_title') }}
                </h2>
                <p class="text-xl font-body text-gray-600 max-w-2xl mx-auto">
                    {{ __('common.why_choose_subtitle') }}
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <article class="bg-gradient-to-br from-skywave/5 to-skywave/10 p-8 rounded-2xl hover:shadow-lg transition-shadow duration-300">
                    <div class="bg-skywave w-12 h-12 rounded-lg flex items-center justify-center mb-6" role="img" aria-label="{{ __('common.multi_carrier_integration') }}">
                        <i class="fas fa-shipping-fast text-white text-xl" aria-hidden="true"></i>
                    </div>
                    <h3 class="text-xl font-heading font-bold text-black-coal mb-4">{{ __('common.multi_carrier_integration') }}</h3>
                    <p class="font-body text-gray-600">
                        {{ __('common.multi_carrier_description') }}
                    </p>
                </article>

                <!-- Feature 2 -->
                <article class="bg-gradient-to-br from-skywave/5 to-skywave/10 p-8 rounded-2xl hover:shadow-lg transition-shadow duration-300">
                    <div class="bg-emerald-600 w-12 h-12 rounded-lg flex items-center justify-center mb-6" role="img" aria-label="{{ __('common.payment_management') }}">
                        <i class="fas fa-credit-card text-white text-xl" aria-hidden="true"></i>
                    </div>
                    <h3 class="text-xl font-heading font-bold text-black-coal mb-4">{{ __('common.payment_management') }}</h3>
                    <p class="font-body text-gray-600">
                        {{ __('common.payment_management_description') }}
                    </p>
                </article>

                <!-- Feature 3 -->
                <article class="bg-gradient-to-br from-skywave/5 to-skywave/10 p-8 rounded-2xl hover:shadow-lg transition-shadow duration-300">
                    <div class="bg-violet-600 w-12 h-12 rounded-lg flex items-center justify-center mb-6" role="img" aria-label="{{ __('common.realtime_analytics') }}">
                        <i class="fas fa-chart-line text-white text-xl" aria-hidden="true"></i>
                    </div>
                    <h3 class="text-xl font-heading font-bold text-black-coal mb-4">{{ __('common.realtime_analytics') }}</h3>
                    <p class="font-body text-gray-600">
                        {{ __('common.realtime_analytics_description') }}
                    </p>
                </article>

                <!-- Feature 4 -->
                <article class="bg-gradient-to-br from-skywave/5 to-skywave/10 p-8 rounded-2xl hover:shadow-lg transition-shadow duration-300">
                    <div class="bg-orange-600 w-12 h-12 rounded-lg flex items-center justify-center mb-6" role="img" aria-label="{{ __('common.team_management') }}">
                        <i class="fas fa-users text-white text-xl" aria-hidden="true"></i>
                    </div>
                    <h3 class="text-xl font-heading font-bold text-black-coal mb-4">{{ __('common.team_management') }}</h3>
                    <p class="font-body text-gray-600">
                        {{ __('common.team_management_description') }}
                    </p>
                </article>

                <!-- Feature 5 -->
                <article class="bg-gradient-to-br from-skywave/5 to-skywave/10 p-8 rounded-2xl hover:shadow-lg transition-shadow duration-300">
                    <div class="bg-cyan-600 w-12 h-12 rounded-lg flex items-center justify-center mb-6" role="img" aria-label="{{ __('common.enterprise_security') }}">
                        <i class="fas fa-shield-alt text-white text-xl" aria-hidden="true"></i>
                    </div>
                    <h3 class="text-xl font-heading font-bold text-black-coal mb-4">{{ __('common.enterprise_security') }}</h3>
                    <p class="font-body text-gray-600">
                        {{ __('common.enterprise_security_description') }}
                    </p>
                </article>

                <!-- Feature 6 -->
                <article class="bg-gradient-to-br from-skywave/5 to-skywave/10 p-8 rounded-2xl hover:shadow-lg transition-shadow duration-300">
                    <div class="bg-rose-600 w-12 h-12 rounded-lg flex items-center justify-center mb-6" role="img" aria-label="{{ __('common.api_integration') }}">
                        <i class="fas fa-mobile-alt text-white text-xl" aria-hidden="true"></i>
                    </div>
                    <h3 class="text-xl font-heading font-bold text-black-coal mb-4">{{ __('common.api_integration') }}</h3>
                    <p class="font-body text-gray-600">
                        {{ __('common.api_integration_description') }}
                    </p>
                </article>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h3 class="text-3xl lg:text-4xl font-heading font-bold text-black-coal mb-4">
                    {{ __('common.our_services') }}
                </h3>
                <p class="text-xl font-body text-gray-600 max-w-2xl mx-auto">
                    {{ __('common.our_services_subtitle') }}
                </p>
            </div>

            <div class="grid lg:grid-cols-2 gap-12">
                <!-- Service 1 -->
                <div class="bg-white p-8 rounded-2xl shadow-lg">
                    <div class="flex items-start space-x-4">
                        <div class="bg-skywave p-3 rounded-lg flex-shrink-0" role="img" aria-label="{{ __('common.shipment_management') }}">
                            <i class="fas fa-box text-white text-xl" aria-hidden="true"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-heading font-bold text-black-coal mb-3">{{ __('common.shipment_management') }}</h3>
                            <p class="font-body text-gray-600 mb-4">
                                {{ __('common.shipment_management_description') }}
                            </p>
                            <ul class="space-y-2 font-body text-gray-600">
                                <li class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    {{ __('common.label_generation') }}
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    {{ __('common.pickup_point_selection') }}
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    {{ __('common.realtime_tracking') }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Service 2 -->
                <div class="bg-white p-8 rounded-2xl shadow-lg">
                    <div class="flex items-start space-x-4">
                        <div class="bg-skywave p-3 rounded-lg flex-shrink-0" role="img" aria-label="{{ __('common.financial_operations') }}">
                            <i class="fas fa-wallet text-white text-xl" aria-hidden="true"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-heading font-bold text-black-coal mb-3">{{ __('common.financial_operations') }}</h3>
                            <p class="font-body text-gray-600 mb-4">
                                {{ __('common.financial_operations_description') }}
                            </p>
                            <ul class="space-y-2 font-body text-gray-600">
                                <li class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    {{ __('common.credit_limit_management') }}
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    {{ __('common.automated_billing') }}
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    {{ __('common.financial_reporting') }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row justify-between items-center">
                <div class="flex items-center mb-4 lg:mb-0">
                    <div class="bg-skywave p-2 rounded-lg" role="img" aria-label="{{ __('common.courier') }}">
                        <i class="fas fa-shipping-fast text-white text-xl" aria-hidden="true"></i>
                    </div>
                    <span class="ml-3 text-xl font-heading font-bold">SkyBrokerSystem</span>
                    <span class="ml-2 px-2 py-1 bg-skywave text-white text-xs font-medium rounded-full">v1</span>
                </div>
                
                <div class="flex flex-col sm:flex-row items-center space-y-4 sm:space-y-0 sm:space-x-6">
                    <div class="flex space-x-6">
                        <a href="{{ route('customer.login') }}" class="font-body text-gray-300 hover:text-white transition-colors duration-200">
                            {{ __('common.customer_login') }}
                        </a>
                        <a href="{{ route('admin.login') }}" class="font-body text-gray-300 hover:text-white transition-colors duration-200">
                            {{ __('common.admin_access') }}
                        </a>
                        <a href="{{ route('health') }}" class="font-body text-gray-300 hover:text-white transition-colors duration-200">
                            {{ __('common.system_status') }}
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p class="font-body">&copy; {{ date('Y') }} SkyBrokerSystem. {{ __('common.copyright') }}</p>
            </div>
        </div>
    </footer>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});
</script>
@endsection