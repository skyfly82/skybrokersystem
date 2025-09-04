@extends('layouts.customer')

@section('title', 'Książka adresowa')

@section('content')
<div class="max-w-7xl mx-auto space-y-6" x-data="addressBookManager">
    <!-- Header -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-black-coal">Książka adresowa</h2>
                <p class="mt-1 text-sm font-body text-gray-500">
                    Zarządzaj zapisanymi adresami nadawców i odbiorców
                </p>
            </div>
            <div class="flex space-x-3">
                <button @click="showAddForm = true"
                        class="bg-skywave hover:bg-skywave/90 text-white px-4 py-2 rounded-lg font-body font-medium transition inline-flex items-center">
                    <i class="fas fa-plus mr-2"></i>
                    Dodaj adres
                </button>
                <a href="{{ route('customer.shipments.create') }}" 
                   class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-body font-medium transition inline-flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Wróć do formularza
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-skywave/10 rounded-lg flex items-center justify-center">
                    <i class="fas fa-address-book text-skywave text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Łączna liczba</p>
                    <p class="text-2xl font-bold text-black-coal" x-text="addresses.length"></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-paper-plane text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Nadawcy</p>
                    <p class="text-2xl font-bold text-black-coal" x-text="senderCount"></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-inbox text-purple-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Odbiorcy</p>
                    <p class="text-2xl font-bold text-black-coal" x-text="recipientCount"></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    <input type="text" 
                           x-model="searchTerm"
                           placeholder="Szukaj po nazwie, firmie, mieście..."
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-skywave focus:border-skywave">
                </div>
            </div>
            <div>
                <select x-model="filterType" 
                        class="w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-skywave focus:border-skywave">
                    <option value="">Wszystkie typy</option>
                    <option value="sender">Tylko nadawcy</option>
                    <option value="recipient">Tylko odbiorcy</option>
                </select>
            </div>
            <div>
                <select x-model="sortBy" 
                        class="w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-skywave focus:border-skywave">
                    <option value="name">Sortuj po nazwie</option>
                    <option value="city">Sortuj po mieście</option>
                    <option value="recent">Najnowsze</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Address Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <template x-for="address in filteredAddresses" :key="address.id">
            <div class="bg-white rounded-lg shadow hover:shadow-md transition-shadow p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center"
                             :class="address.type === 'sender' ? 'bg-green-100 text-green-600' : 'bg-purple-100 text-purple-600'">
                            <i class="fas" :class="address.type === 'sender' ? 'fa-paper-plane' : 'fa-inbox'"></i>
                        </div>
                        <span class="text-xs font-medium px-2 py-1 rounded-full"
                              :class="address.type === 'sender' ? 'bg-green-100 text-green-700' : 'bg-purple-100 text-purple-700'"
                              x-text="address.type === 'sender' ? 'Nadawca' : 'Odbiorca'">
                        </span>
                    </div>
                    <div class="flex space-x-1">
                        <button @click="editAddress(address)" 
                                class="text-gray-400 hover:text-skywave text-sm">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button @click="deleteAddress(address.id)" 
                                class="text-gray-400 hover:text-red-600 text-sm">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                
                <div class="space-y-2">
                    <h3 class="font-heading font-bold text-black-coal" x-text="address.name"></h3>
                    <p class="text-sm text-gray-600" x-text="address.company" x-show="address.company"></p>
                    <div class="text-sm text-gray-500">
                        <p x-text="address.address"></p>
                        <p><span x-text="address.postal_code"></span> <span x-text="address.city"></span></p>
                    </div>
                    <div class="text-xs text-gray-400 space-y-1">
                        <p><i class="fas fa-phone mr-1"></i><span x-text="address.phone"></span></p>
                        <p x-show="address.email"><i class="fas fa-envelope mr-1"></i><span x-text="address.email"></span></p>
                    </div>
                </div>
                
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <button @click="useInForm(address)" 
                            class="w-full bg-skywave hover:bg-skywave/90 text-white py-2 px-4 rounded-lg text-sm font-medium transition">
                        <i class="fas fa-plus mr-1"></i>
                        Użyj w formularzu
                    </button>
                </div>
            </div>
        </template>
    </div>

    <!-- Empty State -->
    <div x-show="filteredAddresses.length === 0" class="bg-white rounded-lg shadow p-12 text-center">
        <div class="text-gray-400">
            <i class="fas fa-address-book text-6xl mb-4"></i>
            <h3 class="text-lg font-heading font-medium text-gray-600 mb-2">
                <span x-show="!searchTerm && !filterType">Brak zapisanych adresów</span>
                <span x-show="searchTerm || filterType">Brak wyników</span>
            </h3>
            <p class="text-sm mb-6">
                <span x-show="!searchTerm && !filterType">Dodaj pierwszy adres do książki adresowej</span>
                <span x-show="searchTerm || filterType">Spróbuj zmienić kryteria wyszukiwania</span>
            </p>
            <button @click="showAddForm = true"
                    class="bg-skywave hover:bg-skywave/90 text-white px-6 py-2 rounded-lg font-medium">
                Dodaj pierwszy adres
            </button>
        </div>
    </div>

    <!-- Add/Edit Form Modal -->
    <div x-show="showAddForm || editingAddress" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-500 bg-opacity-50 flex items-center justify-center z-50">
        
        <div class="bg-white rounded-lg shadow-lg max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-heading font-medium text-black-coal">
                        <span x-show="!editingAddress">Dodaj nowy adres</span>
                        <span x-show="editingAddress">Edytuj adres</span>
                    </h3>
                    <button @click="closeForm()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            
            <div class="p-6">
                <form @submit.prevent="saveAddress()">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Typ adresu</label>
                            <div class="flex space-x-4">
                                <label class="flex items-center">
                                    <input type="radio" x-model="formData.type" value="sender" 
                                           class="h-4 w-4 text-skywave focus:ring-skywave border-gray-300">
                                    <span class="ml-2 text-sm text-gray-700">Nadawca</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" x-model="formData.type" value="recipient" 
                                           class="h-4 w-4 text-skywave focus:ring-skywave border-gray-300">
                                    <span class="ml-2 text-sm text-gray-700">Odbiorca</span>
                                </label>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nazwa / Imię i nazwisko *</label>
                            <input type="text" x-model="formData.name" required
                                   class="w-full border-gray-300 rounded-md focus:ring-skywave focus:border-skywave">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Firma</label>
                            <input type="text" x-model="formData.company"
                                   class="w-full border-gray-300 rounded-md focus:ring-skywave focus:border-skywave">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Telefon *</label>
                            <input type="tel" x-model="formData.phone" required
                                   class="w-full border-gray-300 rounded-md focus:ring-skywave focus:border-skywave">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" x-model="formData.email"
                                   class="w-full border-gray-300 rounded-md focus:ring-skywave focus:border-skywave">
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Adres *</label>
                            <input type="text" x-model="formData.address" required
                                   class="w-full border-gray-300 rounded-md focus:ring-skywave focus:border-skywave">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Miasto *</label>
                            <input type="text" x-model="formData.city" required
                                   class="w-full border-gray-300 rounded-md focus:ring-skywave focus:border-skywave">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kod pocztowy *</label>
                            <input type="text" x-model="formData.postal_code" required
                                   pattern="[0-9]{2}-[0-9]{3}" placeholder="00-000"
                                   class="w-full border-gray-300 rounded-md focus:ring-skywave focus:border-skywave">
                        </div>
                    </div>
                    
                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" @click="closeForm()" 
                                class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-md font-medium">
                            Anuluj
                        </button>
                        <button type="submit" 
                                class="bg-skywave hover:bg-skywave/90 text-white px-4 py-2 rounded-md font-medium">
                            <span x-show="!editingAddress">Dodaj adres</span>
                            <span x-show="editingAddress">Zapisz zmiany</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('addressBookManager', () => ({
        addresses: [],
        searchTerm: '',
        filterType: '',
        sortBy: 'name',
        showAddForm: false,
        editingAddress: null,
        formData: {
            id: null,
            type: 'recipient',
            name: '',
            company: '',
            phone: '',
            email: '',
            address: '',
            city: '',
            postal_code: ''
        },
        
        init() {
            this.loadAddresses();
        },
        
        get senderCount() {
            return this.addresses.filter(addr => addr.type === 'sender').length;
        },
        
        get recipientCount() {
            return this.addresses.filter(addr => addr.type === 'recipient').length;
        },
        
        get filteredAddresses() {
            let filtered = [...this.addresses];
            
            // Apply search filter
            if (this.searchTerm) {
                const search = this.searchTerm.toLowerCase();
                filtered = filtered.filter(addr => 
                    addr.name.toLowerCase().includes(search) ||
                    addr.company.toLowerCase().includes(search) ||
                    addr.city.toLowerCase().includes(search) ||
                    addr.address.toLowerCase().includes(search)
                );
            }
            
            // Apply type filter
            if (this.filterType) {
                filtered = filtered.filter(addr => addr.type === this.filterType);
            }
            
            // Apply sorting
            filtered.sort((a, b) => {
                switch (this.sortBy) {
                    case 'name':
                        return a.name.localeCompare(b.name);
                    case 'city':
                        return a.city.localeCompare(b.city);
                    case 'recent':
                        return (b.id || 0) - (a.id || 0);
                    default:
                        return 0;
                }
            });
            
            return filtered;
        },
        
        loadAddresses() {
            try {
                const saved = localStorage.getItem('address_book');
                if (saved) {
                    this.addresses = JSON.parse(saved);
                }
            } catch (error) {
                console.error('Error loading addresses:', error);
                this.addresses = [];
            }
        },
        
        saveAddresses() {
            localStorage.setItem('address_book', JSON.stringify(this.addresses));
        },
        
        saveAddress() {
            if (!this.formData.name || !this.formData.phone || !this.formData.address || !this.formData.city || !this.formData.postal_code) {
                alert('Proszę wypełnić wszystkie wymagane pola (*)');
                return;
            }
            
            if (this.editingAddress) {
                // Update existing
                const index = this.addresses.findIndex(addr => addr.id === this.editingAddress.id);
                if (index >= 0) {
                    this.addresses[index] = { ...this.formData };
                }
            } else {
                // Add new
                this.addresses.push({
                    ...this.formData,
                    id: Date.now()
                });
            }
            
            this.saveAddresses();
            this.closeForm();
        },
        
        editAddress(address) {
            this.editingAddress = address;
            this.formData = { ...address };
            this.showAddForm = false;
        },
        
        deleteAddress(addressId) {
            if (confirm('Czy na pewno chcesz usunąć ten adres?')) {
                this.addresses = this.addresses.filter(addr => addr.id !== addressId);
                this.saveAddresses();
            }
        },
        
        closeForm() {
            this.showAddForm = false;
            this.editingAddress = null;
            this.formData = {
                id: null,
                type: 'recipient',
                name: '',
                company: '',
                phone: '',
                email: '',
                address: '',
                city: '',
                postal_code: ''
            };
        },
        
        useInForm(address) {
            // Store selected address for form usage
            localStorage.setItem('selected_address', JSON.stringify(address));
            
            // Redirect to shipment form with address parameter
            window.location.href = '{{ route("customer.shipments.create") }}?address_type=' + address.type;
        }
    }));
});
</script>
@endpush
@endsection