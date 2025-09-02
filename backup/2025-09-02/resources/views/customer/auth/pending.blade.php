@extends('layouts.auth')

@section('title', 'Konto oczekuje zatwierdzenia')
@section('header', 'Konto oczekuje zatwierdzenia')
@section('description', 'Twoje konto zostało zweryfikowane i oczekuje na zatwierdzenie przez administratora')

@section('content')
<div class="text-center space-y-6">
    <!-- Status Icon -->
    <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-yellow-100">
        <i class="fas fa-hourglass-half text-3xl text-yellow-600"></i>
    </div>
    
    <!-- Status Message -->
    <div class="space-y-4">
        <h3 class="text-xl font-heading font-semibold text-black-coal">
            Konto zostało pomyślnie zweryfikowane!
        </h3>
        
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-yellow-400 mt-0.5"></i>
                </div>
                <div class="ml-3 text-left">
                    <p class="text-sm font-body text-yellow-800">
                        <strong>Status:</strong> Oczekuje na zatwierdzenie przez administratora
                    </p>
                    <p class="text-sm font-body text-yellow-700 mt-2">
                        Twoje dane zostały przesłane do weryfikacji. Administrator skontaktuje się z Tobą 
                        w ciągu <strong>1-2 dni roboczych</strong> w sprawie aktywacji konta.
                    </p>
                </div>
            </div>
        </div>
        
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-envelope text-blue-400 mt-0.5"></i>
                </div>
                <div class="ml-3 text-left">
                    <p class="text-sm font-body text-blue-800">
                        <strong>Email:</strong> {{ auth('customer_user')->user()->customer->email }}
                    </p>
                    <p class="text-sm font-body text-blue-700 mt-1">
                        Na ten adres otrzymasz powiadomienie o aktywacji konta.
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Actions -->
    <div class="space-y-4">
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('customer.logout') }}" 
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-body font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-skywave">
                <i class="fas fa-sign-out-alt mr-2"></i>
                Wyloguj się
            </a>
            
            <a href="mailto:support@skybrokersystem.com" 
               class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-body font-medium text-white bg-skywave hover:bg-skywave/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-skywave">
                <i class="fas fa-envelope mr-2"></i>
                Skontaktuj się z nami
            </a>
        </div>
        
        <form id="logout-form" action="{{ route('customer.logout') }}" method="POST" class="hidden">
            @csrf
        </form>
    </div>
</div>

<!-- FAQ Section -->
<div class="mt-8 bg-gray-50 rounded-lg p-6">
    <h4 class="text-lg font-heading font-medium text-black-coal mb-4">Często zadawane pytania</h4>
    
    <div class="space-y-4 text-left">
        <div>
            <h5 class="font-body font-medium text-gray-900">Jak długo trwa weryfikacja?</h5>
            <p class="text-sm font-body text-gray-600 mt-1">
                Proces weryfikacji zajmuje zazwyczaj 1-2 dni robocze. W szczególnych przypadkach może potrwać dłużej.
            </p>
        </div>
        
        <div>
            <h5 class="font-body font-medium text-gray-900">Co sprawdza administrator?</h5>
            <p class="text-sm font-body text-gray-600 mt-1">
                Administrator weryfikuje dane firmy, NIP oraz autentyczność podanych informacji kontaktowych.
            </p>
        </div>
        
        <div>
            <h5 class="font-body font-medium text-gray-900">Czy mogę przyspieszyć proces?</h5>
            <p class="text-sm font-body text-gray-600 mt-1">
                W pilnych przypadkach skontaktuj się z nami mailowo. Podaj w temacie "Pilna weryfikacja konta".
            </p>
        </div>
    </div>
</div>
@endsection

@section('footer')
<div class="text-center">
    <p class="text-sm font-body text-gray-600">
        Potrzebujesz pomocy?
    </p>
    <a href="mailto:support@skybrokersystem.com" class="text-skywave hover:text-skywave/80 font-body font-medium text-sm">
        support@skybrokersystem.com
    </a>
</div>
@endsection