@extends('layouts.guest')

@push('structured-data')
    @if (app()->isLocale('pl'))
        @php
            $ld = [
                '@context' => 'https://schema.org',
                '@type'    => 'Organization',
                'name'     => 'SkyBrokerSystem',
                'url'      => url('/'),
            ];
        @endphp
        <script type="application/ld+json">
            {!! json_encode($ld, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
        </script>
    @endif
@endpush


@section('content')
<div class="min-h-screen bg-gradient-to-br from-sky-50 via-pure-white to-skywave/5">

    {{-- FLASH: info --}}
    @if (session('info'))
        <div
            class="bg-blue-50 border-l-4 border-blue-400 p-4 fixed top-4 right-4 z-50 max-w-sm shadow-lg rounded-md"
            x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-700">{{ session('info') }}</p>
                </div>
                <div class="ml-auto pl-3">
                    <button @click="show = false" class="text-blue-400 hover:text-blue-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- FLASH: success --}}
    @if (session('success'))
        <div
            class="bg-green-50 border-l-4 border-green-400 p-4 fixed top-4 right-4 z-50 max-w-sm shadow-lg rounded-md"
            x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
                <div class="ml-auto pl-3">
                    <button @click="show = false" class="text-green-400 hover:text-green-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- NAVBAR (prosto i bezpiecznie) --}}
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <img class="h-8 w-auto" src="{{ asset('images/logo_1.png') }}" alt="SkyBrokerSystem">
                    <span class="ml-3 text-xl font-heading font-bold text-black-coal">SkyBrokerSystem</span>
                </div>

                @php
                    $isAdmin  = auth('system_user')->check();
                    $isClient = auth('customer_user')->check();
                @endphp

                <div class="hidden md:flex items-center space-x-4">
                    <a href="#features" class="text-gray-600 hover:text-skywave font-medium">{{ __('common.features') }}</a>
                    <a href="#services" class="text-gray-600 hover:text-skywave font-medium">{{ __('common.services') }}</a>
                    <a href="#contact" class="text-gray-600 hover:text-skywave font-medium">{{ __('common.contact') }}</a>

                    {{-- Jeden egzemplarz przełącznika języka (opcjonalnie) --}}
                    <x-language-switcher />

                    {{-- AKCJE (jeden łańcuch warunków) --}}
                    @if ($isAdmin)
                        <a href="{{ route('admin.dashboard') }}"
                           class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium">
                            <i class="fas fa-cogs text-sm mr-2"></i> Panel Admina
                        </a>
                    @elseif ($isClient)
                        <a href="{{ route('customer.dashboard') }}"
                           class="bg-skywave hover:bg-skywave/90 text-white px-4 py-2 rounded-lg font-medium">
                            <i class="fas fa-user text-sm mr-2"></i> Panel Klienta
                        </a>
                    @else
                        <a href="{{ route('customer.login') }}" class="text-skywave font-medium">
                            {{ __('common.sign_in') }}
                        </a>
                        <a href="{{ route('customer.register') }}"
                           class="bg-skywave hover:bg-skywave/90 text-white px-4 py-2 rounded-lg font-medium">
                            {{ __('common.get_started') }}
                        </a>
                    @endif
                </div>

                {{-- Mobile toggle --}}
                <div class="md:hidden" x-data="{ open:false }">
                    <button @click="open = !open" class="text-gray-600 hover:text-gray-900">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <div x-show="open" x-cloak class="absolute right-4 mt-2 w-56 bg-white shadow-lg rounded-lg p-3 border">
                        <div class="flex flex-col space-y-2">
                            <a href="#features" class="text-gray-700 hover:text-skywave">{{ __('common.features') }}</a>
                            <a href="#services" class="text-gray-700 hover:text-skywave">{{ __('common.services') }}</a>
                            <a href="#contact" class="text-gray-700 hover:text-skywave">{{ __('common.contact') }}</a>

                            <div class="border-t my-2"></div>

                            {{-- Ten sam łańcuch warunków co wyżej --}}
                            @if ($isAdmin)
                                <a href="{{ route('admin.dashboard') }}"
                                   class="block w-full text-center bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium">
                                    <i class="fas fa-cogs text-sm mr-1"></i> Panel Admina
                                </a>
                            @elseif ($isClient)
                                <a href="{{ route('customer.dashboard') }}"
                                   class="block w-full text-center bg-skywave hover:bg-skywave/90 text-white px-4 py-2 rounded-lg font-medium">
                                    <i class="fas fa-user text-sm mr-1"></i> Panel Klienta
                                </a>
                            @else
                                <a href="{{ route('customer.login') }}" class="text-skywave font-medium text-center py-2">
                                    {{ __('common.sign_in') }}
                                </a>
                                <a href="{{ route('customer.register') }}"
                                   class="block w-full text-center bg-skywave hover:bg-skywave/90 text-white px-4 py-2 rounded-lg font-medium">
                                    {{ __('common.get_started') }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    {{-- HERO --}}
    <header class="relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24">
            <div class="text-center">
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-heading font-bold text-black-coal mb-6">
                    {{ __('common.professional_courier') }} <span class="text-skywave">{{ __('common.courier') }}</span><br>
                    <span class="text-black-coal">{{ __('common.brokerage_platform') }}</span>
                </h1>
                <p class="text-xl font-body text-gray-600 mb-8 max-w-3xl mx-auto leading-relaxed">
                    {{ __('common.hero_description') }}
                </p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                    <a href="{{ route('customer.register') }}"
                       class="bg-skywave hover:bg-skywave/90 text-white px-8 py-4 rounded-xl font-heading font-semibold text-lg transition-all duration-200 shadow-lg">
                        <i class="fas fa-rocket mr-2"></i> {{ __('common.start_free_trial') }}
                    </a>
                    <a href="{{ route('customer.login') }}"
                       class="bg-transparent text-skywave border-2 border-skywave hover:bg-skywave hover:text-white px-8 py-4 rounded-xl font-heading font-semibold text-lg transition-all duration-200 shadow-lg">
                        <i class="fas fa-sign-in-alt mr-2"></i> {{ __('common.sign_in_to_dashboard') }}
                    </a>
                </div>

                <div class="mt-8 text-sm font-body text-gray-500">
                    <span>{{ __('common.administrator_question') }} </span>
                    <a href="{{ route('admin.login') }}" class="text-skywave hover:text-skywave/80 font-medium">
                        {{ __('common.access_admin_panel') }}
                    </a>
                </div>
            </div>
        </div>
    </header>

    {{-- FEATURES --}}
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
                <article class="bg-gradient-to-br from-skywave/5 to-skywave/10 p-8 rounded-2xl hover:shadow-lg">
                    <div class="bg-skywave w-12 h-12 rounded-lg flex items-center justify-center mb-6">
                        <i class="fas fa-shipping-fast text-white text-xl"></i>
                    </div>
                    <h3 class="text-xl font-heading font-bold text-black-coal mb-4">{{ __('common.multi_carrier_integration') }}</h3>
                    <p class="font-body text-gray-600">{{ __('common.multi_carrier_description') }}</p>
                </article>

                <article class="bg-gradient-to-br from-skywave/5 to-skywave/10 p-8 rounded-2xl hover:shadow-lg">
                    <div class="bg-emerald-600 w-12 h-12 rounded-lg flex items-center justify-center mb-6">
                        <i class="fas fa-credit-card text-white text-xl"></i>
                    </div>
                    <h3 class="text-xl font-heading font-bold text-black-coal mb-4">{{ __('common.payment_management') }}</h3>
                    <p class="font-body text-gray-600">{{ __('common.payment_management_description') }}</p>
                </article>

                <article class="bg-gradient-to-br from-skywave/5 to-skywave/10 p-8 rounded-2xl hover:shadow-lg">
                    <div class="bg-violet-600 w-12 h-12 rounded-lg flex items-center justify-center mb-6">
                        <i class="fas fa-chart-line text-white text-xl"></i>
                    </div>
                    <h3 class="text-xl font-heading font-bold text-black-coal mb-4">{{ __('common.realtime_analytics') }}</h3>
                    <p class="font-body text-gray-600">{{ __('common.realtime_analytics_description') }}</p>
                </article>

                <article class="bg-gradient-to-br from-skywave/5 to-skywave/10 p-8 rounded-2xl hover:shadow-lg">
                    <div class="bg-orange-600 w-12 h-12 rounded-lg flex items-center justify-center mb-6">
                        <i class="fas fa-users text-white text-xl"></i>
                    </div>
                    <h3 class="text-xl font-heading font-bold text-black-coal mb-4">{{ __('common.team_management') }}</h3>
                    <p class="font-body text-gray-600">{{ __('common.team_management_description') }}</p>
                </article>

                <article class="bg-gradient-to-br from-skywave/5 to-skywave/10 p-8 rounded-2xl hover:shadow-lg">
                    <div class="bg-cyan-600 w-12 h-12 rounded-lg flex items-center justify-center mb-6">
                        <i class="fas fa-shield-alt text-white text-xl"></i>
                    </div>
                    <h3 class="text-xl font-heading font-bold text-black-coal mb-4">{{ __('common.enterprise_security') }}</h3>
                    <p class="font-body text-gray-600">{{ __('common.enterprise_security_description') }}</p>
                </article>

                <article class="bg-gradient-to-br from-skywave/5 to-skywave/10 p-8 rounded-2xl hover:shadow-lg">
                    <div class="bg-rose-600 w-12 h-12 rounded-lg flex items-center justify-center mb-6">
                        <i class="fas fa-mobile-alt text-white text-xl"></i>
                    </div>
                    <h3 class="text-xl font-heading font-bold text-black-coal mb-4">{{ __('common.api_integration') }}</h3>
                    <p class="font-body text-gray-600">{{ __('common.api_integration_description') }}</p>
                </article>
            </div>
        </div>
    </section>

    {{-- SERVICES --}}
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
                <div class="bg-white p-8 rounded-2xl shadow-lg">
                    <div class="flex items-start space-x-4">
                        <div class="bg-skywave p-3 rounded-lg flex-shrink-0">
                            <i class="fas fa-box text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-heading font-bold text-black-coal mb-3">{{ __('common.shipment_management') }}</h3>
                            <p class="font-body text-gray-600 mb-4">{{ __('common.shipment_management_description') }}</p>
                            <ul class="space-y-2 font-body text-gray-600">
                                <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2"></i>{{ __('common.label_generation') }}</li>
                                <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2"></i>{{ __('common.pickup_point_selection') }}</li>
                                <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2"></i>{{ __('common.realtime_tracking') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-8 rounded-2xl shadow-lg">
                    <div class="flex items-start space-x-4">
                        <div class="bg-skywave p-3 rounded-lg flex-shrink-0">
                            <i class="fas fa-wallet text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-heading font-bold text-black-coal mb-3">{{ __('common.financial_operations') }}</h3>
                            <p class="font-body text-gray-600 mb-4">{{ __('common.financial_operations_description') }}</p>
                            <ul class="space-y-2 font-body text-gray-600">
                                <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2"></i>{{ __('common.credit_limit_management') }}</li>
                                <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2"></i>{{ __('common.automated_billing') }}</li>
                                <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2"></i>{{ __('common.financial_reporting') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- FOOTER --}}
    <footer id="contact" class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row justify-between items-center">
                <div class="flex items-center mb-4 lg:mb-0">
                    <div class="bg-skywave p-2 rounded-lg">
                        <i class="fas fa-shipping-fast text-white text-xl"></i>
                    </div>
                    <span class="ml-3 text-xl font-heading font-bold">SkyBrokerSystem</span>
                    <span class="ml-2 px-2 py-1 bg-skywave text-white text-xs font-medium rounded-full">v1</span>
                </div>

                <div class="flex space-x-6">
                    <a href="{{ route('customer.login') }}" class="font-body text-gray-300 hover:text-white">
                        {{ __('common.customer_login') }}
                    </a>
                    <a href="{{ route('admin.login') }}" class="font-body text-gray-300 hover:text-white">
                        {{ __('common.admin_access') }}
                    </a>
                    <a href="{{ route('health') }}" class="font-body text-gray-300 hover:text-white">
                        {{ __('common.system_status') }}
                    </a>
                </div>
            </div>

            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p class="font-body">&copy; {{ date('Y') }} SkyBrokerSystem. {{ __('common.copyright') }}</p>
            </div>
        </div>
    </footer>
</div>

{{-- Smooth scroll dla anchorów (prosty, bezpieczny) --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            var target = document.querySelector(this.getAttribute('href'));
            if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });
});
</script>
@endsection
