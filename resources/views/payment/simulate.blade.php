<!DOCTYPE html>
<html lang="pl" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>Symulacja płatności | SkyBrokerSystem</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;600;700;800&family=Mulish:wght@400;600;700;800&display=swap" rel="stylesheet">
    
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
                        'skywave': '#2F7DFF',
                        'black-coal': '#0C0212',
                        'pure-white': '#FFFFFF',
                    }
                }
            }
        }
    </script>
</head>
<body class="h-full font-body antialiased">
    <div class="min-h-full bg-gradient-to-br from-blue-50 via-white to-indigo-50">
        <div class="flex min-h-full flex-col justify-center py-12 sm:px-6 lg:px-8">
            <div class="sm:mx-auto sm:w-full sm:max-w-md">
                <!-- Logo -->
                <div class="flex justify-center">
                    <div class="flex items-center">
                        <img class="h-12 w-auto" src="{{ asset('images/logo_1.png') }}" alt="SkyBrokerSystem">
                        <div class="ml-3">
                            <h1 class="text-2xl font-bold text-gray-900">SkyBroker</h1>
                            <p class="text-sm text-gray-500">System</p>
                        </div>
                    </div>
                </div>
                
                <h2 class="mt-6 text-center text-3xl font-bold tracking-tight text-gray-900">
                    Symulacja płatności
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Środowisko testowe - symulacja bramki płatnej
                </p>
            </div>

            <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
                <div class="bg-white py-8 px-4 shadow-xl sm:rounded-lg sm:px-10 border border-gray-200">
                    <!-- Payment Details -->
                    <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <h3 class="text-lg font-semibold text-blue-900 mb-3 flex items-center">
                            <i class="fas fa-info-circle mr-2"></i>
                            Szczegóły płatności
                        </h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-blue-700">Kwota:</span>
                                <span class="font-bold text-blue-900">{{ number_format($payment->amount, 2) }} PLN</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-blue-700">Opis:</span>
                                <span class="text-blue-900">{{ $payment->description }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-blue-700">ID płatności:</span>
                                <span class="font-mono text-xs text-blue-800">{{ $payment->uuid }}</span>
                            </div>
                            @if($payment->expires_at)
                            <div class="flex justify-between">
                                <span class="text-blue-700">Ważne do:</span>
                                <span class="text-blue-900">{{ $payment->expires_at->format('d.m.Y H:i') }}</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Bank Simulation -->
                    <div class="mb-6">
                        <div class="flex items-center justify-center mb-4">
                            <div class="h-16 w-16 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center">
                                <i class="fas fa-university text-white text-2xl"></i>
                            </div>
                        </div>
                        <h3 class="text-center text-xl font-semibold text-gray-900 mb-2">
                            Symulacja Banku
                        </h3>
                        <p class="text-center text-sm text-gray-600 mb-6">
                            Wybierz wynik płatności do przetestowania systemu
                        </p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="space-y-4">
                        <form method="POST" action="{{ route('payment.simulate.process', $payment->uuid) }}">
                            @csrf
                            <input type="hidden" name="action" value="success">
                            
                            <button type="submit" 
                                    class="w-full flex justify-center items-center py-4 px-6 border border-transparent rounded-lg shadow-sm text-base font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200 transform hover:scale-105">
                                <i class="fas fa-check-circle mr-3 text-xl"></i>
                                <div class="text-left">
                                    <div class="font-bold">Symuluj SUKCES</div>
                                    <div class="text-sm opacity-90">Płatność zakończona pomyślnie</div>
                                </div>
                            </button>
                        </form>

                        <form method="POST" action="{{ route('payment.simulate.process', $payment->uuid) }}">
                            @csrf
                            <input type="hidden" name="action" value="fail">
                            
                            <button type="submit" 
                                    class="w-full flex justify-center items-center py-4 px-6 border border-transparent rounded-lg shadow-sm text-base font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200 transform hover:scale-105">
                                <i class="fas fa-times-circle mr-3 text-xl"></i>
                                <div class="text-left">
                                    <div class="font-bold">Symuluj BŁĄD</div>
                                    <div class="text-sm opacity-90">Płatność nieudana lub anulowana</div>
                                </div>
                            </button>
                        </form>
                    </div>

                    <!-- Test Notice -->
                    <div class="mt-6 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                        <div class="flex items-start">
                            <i class="fas fa-exclamation-triangle text-yellow-400 mt-0.5 mr-2"></i>
                            <div class="text-sm">
                                <p class="text-yellow-800 font-medium">Środowisko testowe</p>
                                <ul class="text-yellow-700 mt-1 space-y-1 text-xs">
                                    <li>• To jest symulacja - żadne prawdziwe pieniądze nie zostają pobrane</li>
                                    <li>• Wybierz "SUKCES" aby przetestować udaną płatność</li>
                                    <li>• Wybierz "BŁĄD" aby przetestować nieudaną płatność</li>
                                    <li>• Po wyborze zostaniesz przekierowany z powrotem do systemu</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="mt-6 text-center">
                        <p class="text-xs text-gray-500">
                            SkyBrokerSystem v6.0 - Środowisko testowe
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto countdown if expires_at is set
        @if($payment->expires_at)
        const expiresAt = new Date('{{ $payment->expires_at->toISOString() }}');
        const now = new Date();
        
        if (expiresAt > now) {
            const countdown = setInterval(() => {
                const timeLeft = expiresAt - new Date();
                
                if (timeLeft <= 0) {
                    clearInterval(countdown);
                    alert('Płatność wygasła. Zostaniesz przekierowany do systemu.');
                    window.location.href = '{{ route("customer.shipments.index") }}';
                }
            }, 1000);
        }
        @endif
        
        // Clear cart if instructed by server (for direct access to payment page)
        @if(session('clear_cart'))
        localStorage.removeItem('shipment_cart');
        console.log('Cart cleared from payment simulation page');
        @endif
    </script>
</body>
</html>