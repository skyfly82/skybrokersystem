@extends('layouts.auth')

@section('title', 'Wybierz Typ Konta')
@section('header', 'Załóż konto w SkyBroker')
@section('description', 'Wybierz typ konta odpowiedni dla Twoich potrzeb')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-4xl mx-auto">
    <!-- Company Account -->
    <div class="bg-white rounded-xl border-2 border-gray-200 hover:border-primary-500 p-8 text-center transition-all duration-300 cursor-pointer group" onclick="selectAccountType('company')">
        <div class="mb-6">
            <div class="w-20 h-20 mx-auto bg-primary-100 rounded-full flex items-center justify-center mb-4 group-hover:bg-primary-200 transition-colors">
                <svg class="h-10 w-10 text-primary-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m2.25-18v18m13.5-18v18m2.25-18v18M6.75 9h.75m-.75 3h.75M6.75 15h.75m-.75 3h.75M10.5 9h.75m-.75 3h.75m-.75 3h.75m-.75 3h.75M14.25 9h.75m-.75 3h.75m-.75 3h.75m-.75 3h.75" />
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-2">Konto Firmowe</h3>
            <p class="text-gray-600 text-lg">Dla firm i przedsiębiorców</p>
        </div>

        <div class="text-left space-y-3 mb-8">
            <div class="flex items-center text-gray-700">
                <svg class="h-5 w-5 text-green-500 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Wyższy limit kredytowy (1000 zł)</span>
            </div>
            <div class="flex items-center text-gray-700">
                <svg class="h-5 w-5 text-green-500 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Zarządzanie wieloma użytkownikami</span>
            </div>
            <div class="flex items-center text-gray-700">
                <svg class="h-5 w-5 text-green-500 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Zaawansowane raporty i analizy</span>
            </div>
            <div class="flex items-center text-gray-700">
                <svg class="h-5 w-5 text-green-500 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>API dla integracji</span>
            </div>
            <div class="flex items-center text-gray-700">
                <svg class="h-5 w-5 text-green-500 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Faktury VAT</span>
            </div>
        </div>

        <button type="button" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors duration-200">
            Wybierz Konto Firmowe
        </button>
    </div>

    <!-- Individual Account -->
    <div class="bg-white rounded-xl border-2 border-gray-200 hover:border-primary-500 p-8 text-center transition-all duration-300 cursor-pointer group" onclick="selectAccountType('individual')">
        <div class="mb-6">
            <div class="w-20 h-20 mx-auto bg-blue-100 rounded-full flex items-center justify-center mb-4 group-hover:bg-blue-200 transition-colors">
                <svg class="h-10 w-10 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-2">Konto Indywidualne</h3>
            <p class="text-gray-600 text-lg">Dla klientów prywatnych</p>
        </div>

        <div class="text-left space-y-3 mb-8">
            <div class="flex items-center text-gray-700">
                <svg class="h-5 w-5 text-green-500 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Szybka rejestracja</span>
            </div>
            <div class="flex items-center text-gray-700">
                <svg class="h-5 w-5 text-green-500 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Prostsze zarządzanie</span>
            </div>
            <div class="flex items-center text-gray-700">
                <svg class="h-5 w-5 text-green-500 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Wszystkie kurierzy dostępni</span>
            </div>
            <div class="flex items-center text-gray-700">
                <svg class="h-5 w-5 text-green-500 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Logowanie przez social media</span>
            </div>
            <div class="flex items-center text-gray-700">
                <svg class="h-5 w-5 text-green-500 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Podstawowe raporty</span>
            </div>
        </div>

        <button type="button" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors duration-200">
            Wybierz Konto Indywidualne
        </button>
    </div>
</div>

<div class="mt-12 text-center">
    <div class="mb-6">
        <h4 class="text-lg font-semibold text-gray-900 mb-4">Lub zarejestruj się przez:</h4>
        <div class="flex justify-center space-x-4">
            <!-- Google -->
            <a href="{{ route('customer.auth.social', 'google') }}?type=company" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50" id="google-auth">
                <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                Google
            </a>

            <!-- Facebook -->
            <a href="{{ route('customer.auth.social', 'facebook') }}?type=company" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50" id="facebook-auth">
                <svg class="w-5 h-5 mr-2" fill="#1877F2" viewBox="0 0 24 24">
                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                </svg>
                Facebook
            </a>

            <!-- LinkedIn -->
            <a href="{{ route('customer.auth.social', 'linkedin-openid') }}?type=company" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50" id="linkedin-auth">
                <svg class="w-5 h-5 mr-2" fill="#0A66C2" viewBox="0 0 24 24">
                    <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                </svg>
                LinkedIn
            </a>
        </div>
        <p class="text-sm text-gray-500 mt-3">Automatycznie utworzysz konto firmowe przy logowaniu przez social media</p>
    </div>
</div>
@endsection

@section('footer')
<div class="text-center">
    <p class="text-sm text-gray-600">
        Masz już konto?
    </p>
    <a href="{{ route('customer.login') }}" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-all duration-200 mt-2">
        <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
        </svg>
        Zaloguj się
    </a>
</div>
@endsection

@push('scripts')
<script>
function selectAccountType(type) {
    // Update social auth links to include selected type
    document.getElementById('google-auth').href = "{{ route('customer.auth.social', 'google') }}?type=" + type;
    document.getElementById('facebook-auth').href = "{{ route('customer.auth.social', 'facebook') }}?type=" + type;
    document.getElementById('linkedin-auth').href = "{{ route('customer.auth.social', 'linkedin-openid') }}?type=" + type;
    
    // Redirect to registration form with type
    window.location.href = "{{ route('customer.register') }}?type=" + type;
}
</script>
@endpush