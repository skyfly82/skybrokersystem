@extends('layouts.customer')

@section('title', 'Koszyk przesyłek')

@section('content')
<div class="max-w-7xl mx-auto space-y-6" x-data="shipmentCart">
    <!-- Header -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-black-coal">Koszyk przesyłek</h2>
                <p class="mt-1 text-sm font-body text-gray-500">
                    Zarządzaj swoimi przesyłkami przed finalizacją zamówienia
                </p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('customer.shipments.create') }}" 
                   class="bg-skywave hover:bg-skywave/90 text-white px-4 py-2 rounded-lg font-body font-medium transition inline-flex items-center">
                    <i class="fas fa-plus mr-2"></i>
                    Dodaj przesyłkę
                </a>
            </div>
        </div>
    </div>

    <!-- Cart Items -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-heading font-medium text-black-coal">
                    Przesyłki w koszyku (<span x-text="cartItems.length"></span>)
                </h3>
                <div class="flex items-center space-x-4">
                    <button @click="selectAll()" 
                            class="text-sm text-skywave hover:text-skywave/80 font-medium">
                        <span x-show="!allSelected">Zaznacz wszystkie</span>
                        <span x-show="allSelected">Odznacz wszystkie</span>
                    </button>
                    <button @click="removeSelected()" 
                            x-show="selectedItems.length > 0"
                            class="text-sm text-red-600 hover:text-red-800 font-medium">
                        Usuń zaznaczone
                    </button>
                </div>
            </div>
        </div>

        <!-- Cart Items List -->
        <div class="divide-y divide-gray-200">
            <template x-for="(item, index) in cartItems" :key="item.id">
                <div class="p-6">
                    <div class="flex items-start space-x-4">
                        <!-- Checkbox -->
                        <div class="flex items-center pt-2">
                            <input type="checkbox" 
                                   :id="'item-' + item.id"
                                   x-model="selectedItems"
                                   :value="item.id"
                                   class="h-4 w-4 text-skywave focus:ring-skywave border-gray-300 rounded">
                        </div>

                        <!-- Shipment Info -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <!-- Courier Logo -->
                                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-truck text-gray-400"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-heading font-medium text-black-coal" x-text="item.courier_name"></h4>
                                        <p class="text-sm text-gray-500" x-text="item.service_name"></p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-bold text-black-coal" x-text="item.price_display"></p>
                                    <p class="text-sm text-gray-500">brutto</p>
                                </div>
                            </div>

                            <!-- Shipment Details -->
                            <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                <div>
                                    <p class="font-medium text-gray-700">Nadawca:</p>
                                    <p class="text-gray-600" x-text="item.sender_name"></p>
                                    <p class="text-gray-500" x-text="item.sender_city"></p>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-700">Odbiorca:</p>
                                    <p class="text-gray-600" x-text="item.recipient_name"></p>
                                    <p class="text-gray-500" x-text="item.recipient_city"></p>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-700">Parametry:</p>
                                    <p class="text-gray-600" x-text="item.weight + ' kg'"></p>
                                    <p class="text-gray-500" x-text="item.dimensions"></p>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="mt-4 flex items-center justify-between">
                                <div class="flex space-x-3">
                                    <button @click="duplicateItem(index)" 
                                            class="text-sm text-skywave hover:text-skywave/80 font-medium">
                                        <i class="fas fa-copy mr-1"></i>
                                        Duplikuj
                                    </button>
                                    <button @click="editItem(index)" 
                                            class="text-sm text-gray-600 hover:text-gray-800 font-medium">
                                        <i class="fas fa-edit mr-1"></i>
                                        Edytuj
                                    </button>
                                </div>
                                <button @click="removeItem(index)" 
                                        class="text-sm text-red-600 hover:text-red-800 font-medium">
                                    <i class="fas fa-trash mr-1"></i>
                                    Usuń
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Empty State -->
        <div x-show="cartItems.length === 0" class="text-center py-12">
            <div class="mx-auto h-12 w-12 text-gray-400">
                <i class="fas fa-shopping-cart text-4xl"></i>
            </div>
            <h3 class="mt-2 text-sm font-heading font-medium text-gray-900">Koszyk jest pusty</h3>
            <p class="mt-1 text-sm font-body text-gray-500">
                Dodaj przesyłki do koszyka, aby móc je opłacić jednocześnie.
            </p>
            <div class="mt-6">
                <a href="{{ route('customer.shipments.create') }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-skywave hover:bg-skywave/90">
                    <i class="fas fa-plus mr-2"></i>
                    Utwórz pierwszą przesyłkę
                </a>
            </div>
        </div>
    </div>

    <!-- Summary & Actions -->
    <div x-show="cartItems.length > 0" class="bg-white shadow rounded-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-body text-gray-600">
                    Łączny koszt <span x-text="selectedItems.length"></span> wybranych przesyłek:
                </p>
                <p class="text-2xl font-heading font-bold text-black-coal" x-text="totalPrice"></p>
            </div>
            <div class="flex space-x-3">
                <button @click="saveForLater()" 
                        class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded-lg font-body font-medium transition">
                    Zapisz na później
                </button>
                <button @click="proceedToPayment()" 
                        x-bind:disabled="selectedItems.length === 0"
                        class="bg-skywave hover:bg-skywave/90 disabled:bg-gray-300 text-white px-6 py-2 rounded-lg font-body font-medium transition">
                    Przejdź do płatności
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('shipmentCart', () => ({
        cartItems: [],
        selectedItems: [],
        
        init() {
            // Load cart items from localStorage
            const savedCart = localStorage.getItem('shipment_cart');
            if (savedCart) {
                try {
                    this.cartItems = JSON.parse(savedCart);
                    console.log('Loaded cart items:', this.cartItems.length);
                } catch (error) {
                    console.error('Error loading cart from localStorage:', error);
                    this.cartItems = [];
                }
            }
        },
        
        get allSelected() {
            return this.cartItems.length > 0 && this.selectedItems.length === this.cartItems.length;
        },
        
        get totalPrice() {
            const total = this.cartItems
                .filter(item => this.selectedItems.includes(item.id))
                .reduce((sum, item) => sum + item.price_gross, 0);
            return total.toFixed(2) + ' PLN';
        },
        
        selectAll() {
            if (this.allSelected) {
                this.selectedItems = [];
            } else {
                this.selectedItems = this.cartItems.map(item => item.id);
            }
        },
        
        removeSelected() {
            if (confirm(`Czy na pewno chcesz usunąć ${this.selectedItems.length} przesyłek?`)) {
                this.cartItems = this.cartItems.filter(item => !this.selectedItems.includes(item.id));
                this.selectedItems = [];
            }
        },
        
        removeItem(index) {
            if (confirm('Czy na pewno chcesz usunąć tę przesyłkę?')) {
                const removedId = this.cartItems[index].id;
                this.cartItems.splice(index, 1);
                this.selectedItems = this.selectedItems.filter(id => id !== removedId);
            }
        },
        
        duplicateItem(index) {
            const item = { ...this.cartItems[index] };
            item.id = Date.now();
            this.cartItems.push(item);
        },
        
        editItem(index) {
            // Redirect to edit form
            window.location.href = '/customer/shipments/create?edit=' + this.cartItems[index].id;
        },
        
        saveForLater() {
            // Save cart to localStorage or session
            localStorage.setItem('shipment_cart', JSON.stringify(this.cartItems));
            alert('Koszyk został zapisany. Możesz wrócić do niego później.');
        },
        
        async proceedToPayment() {
            if (this.selectedItems.length === 0) {
                alert('Wybierz co najmniej jedną przesyłkę do opłacenia.');
                return;
            }
            
            // Get selected cart items
            const selectedCartItems = this.cartItems.filter(item => 
                this.selectedItems.includes(item.id)
            );
            
            // Prepare data for backend processing
            const cartData = {
                items: selectedCartItems.map(item => ({
                    courier_code: item.courier_code || 'inpost',
                    service_type: item.service_type || 'inpost_locker_standard',
                    sender: {
                        name: item.sender_name || 'Default Sender',
                        phone: '123456789',
                        email: 'sender@example.com',
                        address: 'Default Address',
                        city: item.sender_city || 'Warsaw',
                        postal_code: '00-001'
                    },
                    recipient: {
                        name: item.recipient_name,
                        phone: item.recipient_phone || '123456789',
                        email: item.recipient_email || '',
                        address: item.recipient_address || item.pickup_point || 'Default Address',
                        city: item.recipient_city || 'Default City',
                        postal_code: item.recipient_postal_code || '00-001'
                    },
                    package: {
                        weight: parseFloat(item.weight) || 1.0,
                        length: parseInt(item.dimensions?.split('x')[0]) || 20,
                        width: parseInt(item.dimensions?.split('x')[1]) || 15,
                        height: parseInt(item.dimensions?.split('x')[2]) || 10
                    },
                    options: {
                        reference_number: item.reference_number || '',
                        notes: item.notes || ''
                    }
                }))
            };
            
            try {
                // Submit to backend
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("customer.shipments.cart.process") }}';
                
                // Add CSRF token
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = document.querySelector('meta[name="csrf-token"]').content;
                form.appendChild(csrfToken);
                
                // Add cart data
                const cartInput = document.createElement('input');
                cartInput.type = 'hidden';
                cartInput.name = 'items';
                cartInput.value = JSON.stringify(cartData.items);
                form.appendChild(cartInput);
                
                document.body.appendChild(form);
                form.submit();
                
                // Remove selected items from cart after successful submission
                this.cartItems = this.cartItems.filter(item => 
                    !this.selectedItems.includes(item.id)
                );
                this.selectedItems = [];
                localStorage.setItem('shipment_cart', JSON.stringify(this.cartItems));
                
            } catch (error) {
                console.error('Error processing cart:', error);
                alert('Wystąpił błąd podczas przetwarzania koszyka. Spróbuj ponownie.');
            }
        }
    }));
});
</script>
@endpush
@endsection