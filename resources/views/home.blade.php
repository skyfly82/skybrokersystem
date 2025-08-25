@extends('layouts.guest')

@section('title', 'SkyBrokerSystem - ' . __('common.professional_courier') . ' ' . __('common.courier') . ' ' . __('common.brokerage_platform'))

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50">
    <!-- Navigation Bar -->
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center">
                            <div class="bg-indigo-600 p-2 rounded-lg">
                                <i class="fas fa-shipping-fast text-white text-xl"></i>
                            </div>
                            <span class="ml-3 text-xl font-bold text-gray-900">SkyBrokerSystem</span>
                            <span class="ml-2 px-2 py-1 bg-indigo-100 text-indigo-600 text-xs font-medium rounded-full">v6</span>
                        </div>
                    </div>
                </div>

                <!-- Navigation Links -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#features" class="text-gray-600 hover:text-indigo-600 transition-colors duration-200 font-medium">{{ __('common.features') }}</a>
                    <a href="#services" class="text-gray-600 hover:text-indigo-600 transition-colors duration-200 font-medium">{{ __('common.services') }}</a>
                    <a href="#contact" class="text-gray-600 hover:text-indigo-600 transition-colors duration-200 font-medium">{{ __('common.contact') }}</a>
                    
                    <!-- Language Switcher -->
                    <x-language-switcher />
                    
                    <!-- Auth Buttons -->
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('customer.login') }}" class="text-indigo-600 hover:text-indigo-700 font-medium transition-colors duration-200">
                            {{ __('common.sign_in') }}
                        </a>
                        <a href="{{ route('customer.register') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium transition-all duration-200 shadow-sm hover:shadow-md">
                            {{ __('common.get_started') }}
                        </a>
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
                <a href="#features" class="block text-gray-600 hover:text-indigo-600 font-medium">{{ __('common.features') }}</a>
                <a href="#services" class="block text-gray-600 hover:text-indigo-600 font-medium">{{ __('common.services') }}</a>
                <a href="#contact" class="block text-gray-600 hover:text-indigo-600 font-medium">{{ __('common.contact') }}</a>
                <div class="border-t border-gray-200 pt-3">
                    <x-language-switcher class="mb-3" />
                    <a href="{{ route('customer.login') }}" class="block w-full text-center text-indigo-600 font-medium py-2">
                        {{ __('common.sign_in') }}
                    </a>
                    <a href="{{ route('customer.register') }}" class="block w-full text-center bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium mt-2">
                        {{ __('common.get_started') }}
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24">
            <div class="text-center fade-in">
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-gray-900 mb-6">
                    {{ __('common.professional_courier') }} <span class="text-indigo-600">{{ __('common.courier') }}</span><br>
                    {{ __('common.brokerage_platform') }}
                </h1>
                <p class="text-xl text-gray-600 mb-8 max-w-3xl mx-auto leading-relaxed">
                    {{ __('common.hero_description') }}
                </p>
                
                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                    <a href="{{ route('customer.register') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-4 rounded-xl font-semibold text-lg transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                        <i class="fas fa-rocket mr-2"></i>
                        {{ __('common.start_free_trial') }}
                    </a>
                    <a href="{{ route('customer.login') }}" class="bg-white hover:bg-gray-50 text-gray-900 border-2 border-gray-300 hover:border-indigo-300 px-8 py-4 rounded-xl font-semibold text-lg transition-all duration-200 shadow-lg hover:shadow-xl">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        {{ __('common.sign_in_to_dashboard') }}
                    </a>
                </div>

                <!-- Admin Access -->
                <div class="mt-8 text-sm text-gray-500">
                    <span>{{ __('common.administrator_question') }} </span>
                    <a href="{{ route('admin.login') }}" class="text-indigo-600 hover:text-indigo-700 font-medium">
                        {{ __('common.access_admin_panel') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Background Decoration -->
        <div class="absolute top-0 right-0 -translate-y-12 translate-x-12">
            <div class="w-72 h-72 bg-gradient-to-br from-indigo-200 to-blue-300 rounded-full mix-blend-multiply filter blur-xl opacity-70"></div>
        </div>
        <div class="absolute bottom-0 left-0 translate-y-12 -translate-x-12">
            <div class="w-72 h-72 bg-gradient-to-br from-purple-200 to-indigo-300 rounded-full mix-blend-multiply filter blur-xl opacity-70"></div>
        </div>
    </div>

    <!-- Features Section -->
    <section id="features" class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">
                    {{ __('common.why_choose_title') }}
                </h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    {{ __('common.why_choose_subtitle') }}
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-8 rounded-2xl hover:shadow-lg transition-shadow duration-300">
                    <div class="bg-indigo-600 w-12 h-12 rounded-lg flex items-center justify-center mb-6">
                        <i class="fas fa-shipping-fast text-white text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">{{ __('common.multi_carrier_integration') }}</h3>
                    <p class="text-gray-600">
                        {{ __('common.multi_carrier_description') }}
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-gradient-to-br from-green-50 to-emerald-50 p-8 rounded-2xl hover:shadow-lg transition-shadow duration-300">
                    <div class="bg-emerald-600 w-12 h-12 rounded-lg flex items-center justify-center mb-6">
                        <i class="fas fa-credit-card text-white text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">{{ __('common.payment_management') }}</h3>
                    <p class="text-gray-600">
                        {{ __('common.payment_management_description') }}
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-gradient-to-br from-purple-50 to-violet-50 p-8 rounded-2xl hover:shadow-lg transition-shadow duration-300">
                    <div class="bg-violet-600 w-12 h-12 rounded-lg flex items-center justify-center mb-6">
                        <i class="fas fa-chart-line text-white text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">{{ __('common.realtime_analytics') }}</h3>
                    <p class="text-gray-600">
                        {{ __('common.realtime_analytics_description') }}
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="bg-gradient-to-br from-orange-50 to-red-50 p-8 rounded-2xl hover:shadow-lg transition-shadow duration-300">
                    <div class="bg-orange-600 w-12 h-12 rounded-lg flex items-center justify-center mb-6">
                        <i class="fas fa-users text-white text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">{{ __('common.team_management') }}</h3>
                    <p class="text-gray-600">
                        {{ __('common.team_management_description') }}
                    </p>
                </div>

                <!-- Feature 5 -->
                <div class="bg-gradient-to-br from-cyan-50 to-blue-50 p-8 rounded-2xl hover:shadow-lg transition-shadow duration-300">
                    <div class="bg-cyan-600 w-12 h-12 rounded-lg flex items-center justify-center mb-6">
                        <i class="fas fa-shield-alt text-white text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">{{ __('common.enterprise_security') }}</h3>
                    <p class="text-gray-600">
                        {{ __('common.enterprise_security_description') }}
                    </p>
                </div>

                <!-- Feature 6 -->
                <div class="bg-gradient-to-br from-pink-50 to-rose-50 p-8 rounded-2xl hover:shadow-lg transition-shadow duration-300">
                    <div class="bg-rose-600 w-12 h-12 rounded-lg flex items-center justify-center mb-6">
                        <i class="fas fa-mobile-alt text-white text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">{{ __('common.api_integration') }}</h3>
                    <p class="text-gray-600">
                        {{ __('common.api_integration_description') }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">
                    {{ __('common.our_services') }}
                </h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    {{ __('common.our_services_subtitle') }}
                </p>
            </div>

            <div class="grid lg:grid-cols-2 gap-12">
                <!-- Service 1 -->
                <div class="bg-white p-8 rounded-2xl shadow-lg">
                    <div class="flex items-start space-x-4">
                        <div class="bg-indigo-600 p-3 rounded-lg flex-shrink-0">
                            <i class="fas fa-box text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-3">{{ __('common.shipment_management') }}</h3>
                            <p class="text-gray-600 mb-4">
                                {{ __('common.shipment_management_description') }}
                            </p>
                            <ul class="space-y-2 text-gray-600">
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
                        <div class="bg-emerald-600 p-3 rounded-lg flex-shrink-0">
                            <i class="fas fa-wallet text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-3">{{ __('common.financial_operations') }}</h3>
                            <p class="text-gray-600 mb-4">
                                {{ __('common.financial_operations_description') }}
                            </p>
                            <ul class="space-y-2 text-gray-600">
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
                    <div class="bg-indigo-600 p-2 rounded-lg">
                        <i class="fas fa-shipping-fast text-white text-xl"></i>
                    </div>
                    <span class="ml-3 text-xl font-bold">SkyBrokerSystem</span>
                    <span class="ml-2 px-2 py-1 bg-indigo-600 text-white text-xs font-medium rounded-full">v6</span>
                </div>
                
                <div class="flex flex-col sm:flex-row items-center space-y-4 sm:space-y-0 sm:space-x-6">
                    <div class="flex space-x-6">
                        <a href="{{ route('customer.login') }}" class="text-gray-300 hover:text-white transition-colors duration-200">
                            {{ __('common.customer_login') }}
                        </a>
                        <a href="{{ route('admin.login') }}" class="text-gray-300 hover:text-white transition-colors duration-200">
                            {{ __('common.admin_access') }}
                        </a>
                        <a href="{{ route('health') }}" class="text-gray-300 hover:text-white transition-colors duration-200">
                            {{ __('common.system_status') }}
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} SkyBrokerSystem v6. {{ __('common.copyright') }}</p>
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