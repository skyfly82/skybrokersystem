@extends('layouts.auth')

@section('title', 'Weryfikacja konta')
@section('header', 'Weryfikacja konta')
@section('description', 'Wprowadź kod weryfikacyjny wysłany na Twój email')

@section('content')
<div x-data="verificationForm" x-init="startCountdown()">
    <form method="POST" action="{{ route('customer.verify', $token) }}" class="space-y-6" @submit="loading = true">
        @csrf
        
        <!-- Email Display -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-envelope text-blue-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-800">
                        Kod weryfikacyjny został wysłany na adres:
                    </p>
                    <p class="font-medium text-blue-900">{{ $email }}</p>
                </div>
            </div>
        </div>

        <!-- Verification Code Input -->
        <div>
            <label for="verification_code" class="block text-sm font-medium leading-6 text-gray-900">
                Kod weryfikacyjny (6 cyfr)
            </label>
            <div class="mt-2">
                <input 
                    id="verification_code" 
                    name="verification_code" 
                    type="text" 
                    maxlength="6"
                    pattern="[0-9]{6}"
                    placeholder="123456"
                    autocomplete="one-time-code"
                    required
                    x-model="verificationCode"
                    x-on:input="formatCode"
                    class="block w-full rounded-md border-0 py-3 px-4 text-center text-2xl font-mono tracking-wider text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-xl sm:leading-6 @error('verification_code') ring-red-300 focus:ring-red-500 @enderror"
                >
            </div>
            @error('verification_code')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Countdown Timer -->
        <div x-show="timeLeft > 0" class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-clock text-yellow-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-800">
                        Kod wygaśnie za: <span class="font-mono font-bold" x-text="formatTime(timeLeft)"></span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Code Expired Notice -->
        <div x-show="timeLeft <= 0" class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-red-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-800">
                        Kod weryfikacyjny wygasł. Kliknij poniżej, aby otrzymać nowy kod.
                    </p>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div>
            <button 
                type="submit" 
                :disabled="verificationCode.length !== 6 || loading"
                class="flex w-full justify-center rounded-md bg-primary-600 px-3 py-3 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200"
            >
                <span x-show="!loading">Zweryfikuj konto</span>
                <span x-show="loading" class="flex items-center">
                    <i class="fas fa-spinner fa-spin mr-2"></i>
                    Weryfikowanie...
                </span>
            </button>
        </div>

        <!-- Resend Code -->
        <div class="text-center">
            <p class="text-sm text-gray-600 mb-3">
                Nie otrzymałeś kodu?
            </p>
            <button 
                type="button"
                @click="resendCode"
                :disabled="!canResend || resendLoading"
                class="text-primary-600 hover:text-primary-500 font-medium text-sm disabled:opacity-50 disabled:cursor-not-allowed"
            >
                <span x-show="!resendLoading">Wyślij kod ponownie</span>
                <span x-show="resendLoading" class="flex items-center justify-center">
                    <i class="fas fa-spinner fa-spin mr-2"></i>
                    Wysyłanie...
                </span>
            </button>
            
            <div x-show="!canResend && !resendLoading" class="text-xs text-gray-500 mt-1">
                Kolejny kod będzie dostępny za <span x-text="formatTime(resendCountdown)"></span>
            </div>
        </div>
    </form>
</div>
@endsection

@section('footer')
<div class="text-center">
    <p class="text-sm text-gray-600">
        Problemy z weryfikacją?
    </p>
    <a href="mailto:support@skybroker.pl" class="text-primary-600 hover:text-primary-500 font-medium text-sm">
        Skontaktuj się z pomocą techniczną
    </a>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('verificationForm', () => ({
        verificationCode: '',
        loading: false,
        resendLoading: false,
        canResend: {{ $canResend ? 'true' : 'false' }},
        timeLeft: {{ $codeExpiryMinutes }} * 60, // Convert minutes to seconds
        resendCountdown: 300, // 5 minutes in seconds
        
        init() {
            this.startCountdown();
            if (!this.canResend) {
                this.startResendCountdown();
            }
        },
        
        formatCode() {
            // Only allow digits
            this.verificationCode = this.verificationCode.replace(/[^0-9]/g, '').substring(0, 6);
        },
        
        formatTime(seconds) {
            const mins = Math.floor(seconds / 60);
            const secs = seconds % 60;
            return `${mins}:${secs.toString().padStart(2, '0')}`;
        },
        
        startCountdown() {
            const interval = setInterval(() => {
                this.timeLeft--;
                if (this.timeLeft <= 0) {
                    clearInterval(interval);
                    this.canResend = true;
                }
            }, 1000);
        },
        
        startResendCountdown() {
            const interval = setInterval(() => {
                this.resendCountdown--;
                if (this.resendCountdown <= 0) {
                    clearInterval(interval);
                    this.canResend = true;
                }
            }, 1000);
        },
        
        async resendCode() {
            if (!this.canResend || this.resendLoading) return;
            
            this.resendLoading = true;
            
            try {
                const response = await fetch(`/customer/verify/{{ $token }}/resend`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // Reset countdown with new expiry time
                    this.timeLeft = result.codeExpiryMinutes * 60;
                    this.canResend = false;
                    this.resendCountdown = 300; // 5 minutes
                    this.startCountdown();
                    this.startResendCountdown();
                    
                    // Show success message
                    this.showMessage(result.message, 'success');
                } else {
                    this.showMessage(result.message, 'error');
                }
            } catch (error) {
                console.error('Resend error:', error);
                this.showMessage('Wystąpił błąd podczas wysyłania kodu.', 'error');
            } finally {
                this.resendLoading = false;
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
                </div>
            `;
            
            document.body.appendChild(messageDiv);
            
            // Remove after 5 seconds
            setTimeout(() => {
                messageDiv.remove();
            }, 5000);
        }
    }));
});
</script>
@endpush