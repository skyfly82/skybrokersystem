<!-- Profile dropdown -->
<div class="relative" x-data="{ open: false }">
    <button type="button" 
            class="flex max-w-xs items-center rounded-full bg-white text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 lg:p-2 lg:hover:bg-gray-50" 
            @click="open = !open"
            :aria-expanded="open">
        <div class="flex items-center">
            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-primary-500 lg:h-10 lg:w-10">
                <span class="text-sm font-medium text-white lg:text-base">
                    {{ substr(auth()->user()->first_name ?? 'U', 0, 1) }}{{ substr(auth()->user()->last_name ?? 'U', 0, 1) }}
                </span>
            </div>
            <div class="ml-3 hidden lg:block">
                <p class="text-sm font-medium text-gray-700">{{ auth()->user()->full_name ?? 'User' }}</p>
                <p class="text-xs text-gray-500">{{ auth()->user()->email ?? 'user@example.com' }}</p>
            </div>
            <svg class="ml-2 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
            </svg>
        </div>
    </button>

    <!-- Dropdown menu -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         @click.away="open = false"
         class="absolute right-0 z-10 mt-2 w-56 origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
         x-cloak>
        
        <!-- User Info Section -->
        <div class="px-4 py-3">
            <p class="text-sm font-medium text-gray-900">{{ auth()->user()->full_name ?? 'User' }}</p>
            <p class="text-sm text-gray-500">{{ auth()->user()->email ?? 'user@example.com' }}</p>
            @if(auth()->user()->is_primary)
                <span class="mt-1 inline-flex items-center rounded-full bg-success-100 px-2 py-1 text-xs font-medium text-success-800">
                    Primary User
                </span>
            @endif
        </div>

        <!-- Quick Stats Section -->
        <div class="px-4 py-3 bg-gray-50">
            <div class="grid grid-cols-2 gap-3 text-xs">
                <div>
                    <p class="text-gray-500">Balance</p>
                    <p class="font-semibold text-gray-900">
                        {{ number_format(auth()->user()->customer->current_balance ?? 0, 2) }} PLN
                    </p>
                </div>
                <div>
                    <p class="text-gray-500">Shipments</p>
                    <p class="font-semibold text-gray-900">
                        {{ auth()->user()->customer->shipments()->count() ?? 0 }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Navigation Links -->
        <div class="py-1">
            <a href="{{ route('customer.profile.show') }}" 
               class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900">
                <svg class="mr-3 h-5 w-5 text-gray-400 group-hover:text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Your Profile
            </a>

            <a href="{{ route('customer.profile.notifications') }}" 
               class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900">
                <svg class="mr-3 h-5 w-5 text-gray-400 group-hover:text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                </svg>
                Notifications
            </a>

            @if(auth()->user()->is_primary)
                <a href="{{ route('customer.users.index') }}" 
                   class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900">
                    <svg class="mr-3 h-5 w-5 text-gray-400 group-hover:text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                    Manage Users
                </a>
            @endif

            <a href="{{ route('customer.api') }}" 
               class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900">
                <svg class="mr-3 h-5 w-5 text-gray-400 group-hover:text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75L22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3l-4.5 16.5" />
                </svg>
                API Keys
            </a>
        </div>

        <!-- Quick Actions -->
        <div class="py-1">
            <a href="{{ route('customer.shipments.create') }}" 
               class="group flex items-center px-4 py-2 text-sm text-primary-700 hover:bg-primary-50 hover:text-primary-900">
                <svg class="mr-3 h-5 w-5 text-primary-500 group-hover:text-primary-700" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Create Shipment
            </a>

            <a href="{{ route('customer.payments.topup') }}" 
               class="group flex items-center px-4 py-2 text-sm text-success-700 hover:bg-success-50 hover:text-success-900">
                <svg class="mr-3 h-5 w-5 text-success-500 group-hover:text-success-700" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Top Up Balance
            </a>
        </div>

        <!-- Support & Logout -->
        <div class="py-1">
            <a href="{{ route('customer.help') }}" 
               class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900">
                <svg class="mr-3 h-5 w-5 text-gray-400 group-hover:text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" />
                </svg>
                Help & Support
            </a>

            <form method="POST" action="{{ route('customer.logout') }}">
                @csrf
                <button type="submit" 
                        class="group flex w-full items-center px-4 py-2 text-left text-sm text-red-700 hover:bg-red-50 hover:text-red-900">
                    <svg class="mr-3 h-5 w-5 text-red-500 group-hover:text-red-700" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                    </svg>
                    Sign out
                </button>
            </form>
        </div>
    </div>
</div>