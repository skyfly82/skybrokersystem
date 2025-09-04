@extends('layouts.admin')

@section('title', 'Ustawienia Weryfikacji')

@section('content')
<div class="space-y-6" x-data="verificationSettings">
    <!-- Header -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="md:flex md:items-center md:justify-between">
            <div class="flex-1 min-w-0">
                <h2 class="text-2xl font-heading font-bold leading-7 text-black-coal sm:text-3xl sm:truncate">
                    Ustawienia Weryfikacji Konta
                </h2>
                <p class="mt-1 text-sm font-body text-gray-500">
                    Konfiguruj parametry weryfikacji emaili i czasów ważności kodów
                </p>
            </div>
        </div>
    </div>

    <!-- Current Settings Overview -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
        <div class="bg-white overflow-hidden shadow rounded-lg border-l-4 border-skywave">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-skywave rounded-full flex items-center justify-center">
                            <i class="fas fa-clock text-white text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-body font-medium text-gray-500 truncate">Ważność kodu</dt>
                            <dd class="text-lg font-heading font-medium text-black-coal">
                                {{ \App\Models\SystemSetting::get('verification_code_expiry_minutes', 60) }} minut
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg border-l-4 border-green-400">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-link text-white text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Ważność linku</dt>
                            <dd class="text-lg font-medium text-gray-900">
                                {{ \App\Models\SystemSetting::get('verification_link_expiry_hours', 24) }} godzin
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg border-l-4 border-yellow-400">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-trash text-white text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Auto-usuwanie</dt>
                            <dd class="text-lg font-medium text-gray-900">
                                {{ \App\Models\SystemSetting::get('auto_cleanup_unverified_accounts_hours', 72) }} godzin
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Settings Form -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-heading font-medium text-black-coal">
                Konfiguracja Weryfikacji
            </h3>
            <p class="mt-1 text-sm font-body text-gray-500">
                Ustaw czasy ważności kodów weryfikacyjnych i automatycznego czyszczenia
            </p>
        </div>

        <form method="POST" action="{{ route('admin.settings.verification.update') }}" class="p-6">
            @csrf
            
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <!-- Code Expiry -->
                <div>
                    <label for="verification_code_expiry_minutes" class="block text-sm font-medium text-gray-700">
                        Ważność kodu weryfikacyjnego (minuty)
                    </label>
                    <div class="mt-1 relative">
                        <input type="number" 
                               name="verification_code_expiry_minutes" 
                               id="verification_code_expiry_minutes"
                               min="1" 
                               max="1440"
                               value="{{ \App\Models\SystemSetting::get('verification_code_expiry_minutes', 60) }}"
                               class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md @error('verification_code_expiry_minutes') border-red-300 @enderror">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <span class="text-gray-500 sm:text-sm">min</span>
                        </div>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">
                        Czas przez jaki kod jest ważny (1-1440 minut = 24h)
                    </p>
                    @error('verification_code_expiry_minutes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Link Expiry -->
                <div>
                    <label for="verification_link_expiry_hours" class="block text-sm font-medium text-gray-700">
                        Ważność linku weryfikacyjnego (godziny)
                    </label>
                    <div class="mt-1 relative">
                        <input type="number" 
                               name="verification_link_expiry_hours" 
                               id="verification_link_expiry_hours"
                               min="1" 
                               max="168"
                               value="{{ \App\Models\SystemSetting::get('verification_link_expiry_hours', 24) }}"
                               class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md @error('verification_link_expiry_hours') border-red-300 @enderror">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <span class="text-gray-500 sm:text-sm">h</span>
                        </div>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">
                        Czas przez jaki link z emaila jest ważny (1-168 godzin = 7 dni)
                    </p>
                    @error('verification_link_expiry_hours')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Auto Cleanup -->
                <div>
                    <label for="auto_cleanup_unverified_accounts_hours" class="block text-sm font-medium text-gray-700">
                        Auto-usuwanie kont (godziny)
                    </label>
                    <div class="mt-1 relative">
                        <input type="number" 
                               name="auto_cleanup_unverified_accounts_hours" 
                               id="auto_cleanup_unverified_accounts_hours"
                               min="1" 
                               max="8760"
                               value="{{ \App\Models\SystemSetting::get('auto_cleanup_unverified_accounts_hours', 72) }}"
                               class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md @error('auto_cleanup_unverified_accounts_hours') border-red-300 @enderror">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <span class="text-gray-500 sm:text-sm">h</span>
                        </div>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">
                        Po jakim czasie usunąć niezweryfikowane konta (1-8760 godzin = 1 rok)
                    </p>
                    @error('auto_cleanup_unverified_accounts_hours')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="mt-6 flex items-center justify-between">
                <div class="flex items-center">
                    <span class="text-sm text-gray-500">
                        <i class="fas fa-info-circle text-blue-400 mr-1"></i>
                        Zmiany będą aktywne natychmiast dla nowych rejestracji
                    </span>
                </div>
                
                <div class="flex space-x-3">
                    <button type="button"
                            onclick="window.location.reload()"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-undo mr-2"></i>
                        Resetuj
                    </button>
                    
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-heading font-medium rounded-md shadow-sm text-white bg-skywave hover:bg-skywave/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-skywave">
                        <i class="fas fa-save mr-2"></i>
                        Zapisz ustawienia
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Email Testing -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-heading font-medium text-black-coal">
                Test Wysyłania Email
            </h3>
            <p class="mt-1 text-sm font-body text-gray-500">
                Sprawdź czy konfiguracja email działa poprawnie
            </p>
        </div>

        <div class="p-6">
            <div class="flex items-center space-x-4">
                <div class="flex-1">
                    <label for="test_email" class="block text-sm font-medium text-gray-700">
                        Adres testowy
                    </label>
                    <input type="email" 
                           x-model="testEmail"
                           id="test_email"
                           placeholder="test@example.com"
                           class="mt-1 shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                </div>
                
                <div class="flex-shrink-0">
                    <button type="button"
                            @click="sendTestEmail"
                            :disabled="!testEmail || loading"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span x-show="!loading">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Wyślij test
                        </span>
                        <span x-show="loading">
                            <i class="fas fa-spinner fa-spin mr-2"></i>
                            Wysyłanie...
                        </span>
                    </button>
                </div>
            </div>

            <div class="mt-4 bg-yellow-50 border-l-4 border-yellow-400 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-yellow-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            <strong>MailHog:</strong> Podczas developmentu wszystkie emaile trafiają do MailHog na porcie 1025. 
                            Sprawdź <a href="http://127.0.0.1:8025" target="_blank" class="underline">http://127.0.0.1:8025</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-heading font-medium text-black-coal">
                Statystyki Weryfikacji
            </h3>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-4">
                <div class="text-center">
                    <dt class="text-sm font-medium text-gray-500">Oczekujące weryfikacji</dt>
                    <dd class="mt-1 text-2xl font-semibold text-red-900">
                        {{ \App\Models\Customer::where('status', 'pending')->where('email_verified', false)->count() }}
                    </dd>
                </div>
                
                <div class="text-center">
                    <dt class="text-sm font-medium text-gray-500">Zweryfikowane (pending)</dt>
                    <dd class="mt-1 text-2xl font-semibold text-yellow-900">
                        {{ \App\Models\Customer::where('status', 'pending')->where('email_verified', true)->count() }}
                    </dd>
                </div>
                
                <div class="text-center">
                    <dt class="text-sm font-medium text-gray-500">Aktywne konta</dt>
                    <dd class="mt-1 text-2xl font-semibold text-green-900">
                        {{ \App\Models\Customer::where('status', 'active')->count() }}
                    </dd>
                </div>
                
                <div class="text-center">
                    <dt class="text-sm font-medium text-gray-500">Z ważnymi kodami</dt>
                    <dd class="mt-1 text-2xl font-semibold text-blue-900">
                        {{ \App\Models\Customer::whereNotNull('verification_code')
                            ->where('verification_code_expires_at', '>', now())
                            ->count() }}
                    </dd>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('verificationSettings', () => ({
        testEmail: '',
        loading: false,

        async sendTestEmail() {
            if (!this.testEmail || this.loading) return;
            
            this.loading = true;
            
            try {
                const response = await fetch('{{ route("admin.settings.test-email") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        test_email: this.testEmail
                    })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    this.showMessage(result.message, 'success');
                } else {
                    this.showMessage(result.message, 'error');
                }
            } catch (error) {
                console.error('Test email error:', error);
                this.showMessage('Wystąpił błąd podczas wysyłania emaila testowego.', 'error');
            } finally {
                this.loading = false;
            }
        },

        showMessage(message, type) {
            // Create a temporary message element
            const messageDiv = document.createElement('div');
            messageDiv.className = `fixed top-4 right-4 z-50 p-4 rounded-md shadow-lg ${type === 'success' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200'}`;
            messageDiv.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-${type === 'success' ? 'check' : 'exclamation-triangle'} mr-2"></i>
                    <span>${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            
            document.body.appendChild(messageDiv);
            
            // Remove after 8 seconds
            setTimeout(() => {
                if (messageDiv.parentNode) {
                    messageDiv.remove();
                }
            }, 8000);
        }
    }));
});
</script>
@endpush