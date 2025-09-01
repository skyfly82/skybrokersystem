<ul role="list" class="flex flex-1 flex-col gap-y-7">
    <li>
        <ul role="list" class="-mx-2 space-y-1">
            <!-- Dashboard -->
            <li>
                <a href="{{ route('admin.dashboard') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.dashboard') ? 'bg-admin-50 text-admin-600' : 'text-gray-700 hover:text-admin-600 hover:bg-gray-50' }}">
                    <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.dashboard') ? 'text-admin-600' : 'text-gray-400 group-hover:text-admin-600' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                    Dashboard
                </a>
            </li>

            <!-- Customers -->
            <li>
                <a href="{{ route('admin.customers.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.customers.*') ? 'bg-admin-50 text-admin-600' : 'text-gray-700 hover:text-admin-600 hover:bg-gray-50' }}">
                    <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.customers.*') ? 'text-admin-600' : 'text-gray-400 group-hover:text-admin-600' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                    Klienci
                    @if(isset($pendingCustomers) && $pendingCustomers > 0)
                    <span class="ml-auto inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                        {{ $pendingCustomers }}
                    </span>
                    @endif
                </a>
            </li>

            <!-- Shipments -->
            <li>
                <a href="{{ route('admin.shipments.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.shipments.*') ? 'bg-admin-50 text-admin-600' : 'text-gray-700 hover:text-admin-600 hover:bg-gray-50' }}">
                    <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.shipments.*') ? 'text-admin-600' : 'text-gray-400 group-hover:text-admin-600' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                    </svg>
                    Przesyłki
                </a>
            </li>

            <!-- Payments -->
            <li>
                <a href="{{ route('admin.payments.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.payments.*') ? 'bg-admin-50 text-admin-600' : 'text-gray-700 hover:text-admin-600 hover:bg-gray-50' }}">
                    <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.payments.*') ? 'text-admin-600' : 'text-gray-400 group-hover:text-admin-600' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                    </svg>
                    Płatności
                </a>
            </li>
        </ul>
    </li>

    <!-- Management Section -->
    <li>
        <div class="text-xs font-semibold leading-6 text-gray-400">Zarządzanie</div>
        <ul role="list" class="-mx-2 mt-2 space-y-1">
            <!-- Courier Services -->
            <li>
                <a href="{{ route('admin.couriers.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.couriers.*') ? 'bg-admin-50 text-admin-600' : 'text-gray-700 hover:text-admin-600 hover:bg-gray-50' }}">
                    <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.couriers.*') ? 'text-admin-600' : 'text-gray-400 group-hover:text-admin-600' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m0-3.75c0-.621.504-1.125 1.125-1.125h18m0 0a1.125 1.125 0 011.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-3.75m-3.75 0H9m1.5-12H6.375c-.621 0-1.125.504-1.125 1.125v3.75c0 .621.504 1.125 1.125 1.125h3.75m3.75-1.875V6.375a1.125 1.125 0 00-1.125-1.125H12m3.75 3.75h2.25c.621 0 1.125-.504 1.125-1.125V6.375a1.125 1.125 0 00-1.125-1.125H15.75" />
                    </svg>
                    Kurierzy
                </a>
            </li>

            <!-- Notifications -->
            <li>
                <a href="{{ route('admin.notifications.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.notifications.*') ? 'bg-admin-50 text-admin-600' : 'text-gray-700 hover:text-admin-600 hover:bg-gray-50' }}">
                    <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.notifications.*') ? 'text-admin-600' : 'text-gray-400 group-hover:text-admin-600' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                    </svg>
                    Powiadomienia
                </a>
            </li>

            <!-- Reports -->
            <li>
                <a href="{{ route('admin.reports.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.reports.*') ? 'bg-admin-50 text-admin-600' : 'text-gray-700 hover:text-admin-600 hover:bg-gray-50' }}">
                    <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.reports.*') ? 'text-admin-600' : 'text-gray-400 group-hover:text-admin-600' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                    </svg>
                    Raporty
                </a>
            </li>
        </ul>
    </li>

    <!-- Content Management Section -->
    @if(auth()->user()->canManageContent())
    <li>
        <div class="text-xs font-semibold leading-6 text-gray-400">Marketing & Content</div>
        <ul role="list" class="-mx-2 mt-2 space-y-1">
            <!-- Marketing Dashboard -->
            <li>
                <a href="{{ route('admin.cms.dashboard') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.cms.dashboard') ? 'bg-admin-50 text-admin-600' : 'text-gray-700 hover:text-admin-600 hover:bg-gray-50' }}">
                    <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.cms.dashboard') ? 'text-admin-600' : 'text-gray-400 group-hover:text-admin-600' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 0 0 6 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0 1 18 16.5h-2.25m-7.5 0h7.5m-7.5 0-1 3m8.5-3 1 3m0 0-1-3m1 3-3.5-2M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                    Marketing Hub
                </a>
            </li>

            <!-- CMS Pages -->
            <li>
                <a href="{{ route('admin.cms.pages.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.cms.pages.*') ? 'bg-admin-50 text-admin-600' : 'text-gray-700 hover:text-admin-600 hover:bg-gray-50' }}">
                    <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.cms.pages.*') ? 'text-admin-600' : 'text-gray-400 group-hover:text-admin-600' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                    Strony CMS
                </a>
            </li>

            <!-- Media Library -->
            <li>
                <a href="{{ route('admin.cms.media.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.cms.media.*') ? 'bg-admin-50 text-admin-600' : 'text-gray-700 hover:text-admin-600 hover:bg-gray-50' }}">
                    <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.cms.media.*') ? 'text-admin-600' : 'text-gray-400 group-hover:text-admin-600' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                    </svg>
                    Media
                </a>
            </li>

            <!-- Notification Banners -->
            <li>
                <a href="{{ route('admin.cms.banners.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.cms.banners.*') ? 'bg-admin-50 text-admin-600' : 'text-gray-700 hover:text-admin-600 hover:bg-gray-50' }}">
                    <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.cms.banners.*') ? 'text-admin-600' : 'text-gray-400 group-hover:text-admin-600' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 110-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38c-.551.318-1.26.117-1.527-.461a20.845 20.845 0 01-1.44-4.282m3.102.069a18.03 18.03 0 01-.59-4.59c0-1.586.205-3.124.59-4.59m0 9.18a23.848 23.848 0 018.835 2.535M10.34 6.66a23.847 23.847 0 008.835-2.535m0 0A23.74 23.74 0 0018.795 3m.38 1.125a23.91 23.91 0 011.014 5.395m-1.014 8.855c-.118.38-.245.754-.38 1.125m.38-1.125a23.91 23.91 0 001.014-5.395m0-3.46c.495.413.811 1.035.811 1.73 0 .695-.316 1.317-.811 1.73m0-3.46a24.347 24.347 0 010 3.46" />
                    </svg>
                    Banery & Awarie
                </a>
            </li>
        </ul>
    </li>
    @endif

    <!-- System Section -->
    @if(auth()->user()->isSuperAdmin())
    <li>
        <div class="text-xs font-semibold leading-6 text-gray-400">System</div>
        <ul role="list" class="-mx-2 mt-2 space-y-1">
            <!-- System Users -->
            <li>
                <a href="{{ route('admin.users.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.users.*') ? 'bg-admin-50 text-admin-600' : 'text-gray-700 hover:text-admin-600 hover:bg-gray-50' }}">
                    <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.users.*') ? 'text-admin-600' : 'text-gray-400 group-hover:text-admin-600' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                    Administratorzy
                </a>
            </li>

            <!-- System Settings -->
            <li>
                <a href="{{ route('admin.settings.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.settings.index') ? 'bg-admin-50 text-admin-600' : 'text-gray-700 hover:text-admin-600 hover:bg-gray-50' }}">
                    <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.settings.index') ? 'text-admin-600' : 'text-gray-400 group-hover:text-admin-600' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Ustawienia
                </a>
            </li>

            <!-- Verification Settings -->
            <li>
                <a href="{{ route('admin.settings.verification') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.settings.verification') ? 'bg-admin-50 text-admin-600' : 'text-gray-700 hover:text-admin-600 hover:bg-gray-50' }}">
                    <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.settings.verification') ? 'text-admin-600' : 'text-gray-400 group-hover:text-admin-600' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
                    </svg>
                    Weryfikacja
                </a>
            </li>

            <!-- System Logs -->
            <li>
                <a href="{{ route('admin.logs.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.logs.*') ? 'bg-admin-50 text-admin-600' : 'text-gray-700 hover:text-admin-600 hover:bg-gray-50' }}">
                    <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.logs.*') ? 'text-admin-600' : 'text-gray-400 group-hover:text-admin-600' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                    </svg>
                    Logi systemowe
                </a>
            </li>
        </ul>
    </li>
    @endif

    <!-- Quick Actions -->
    <li class="mt-auto">
        <div class="text-xs font-semibold leading-6 text-gray-400">Szybkie akcje</div>
        <ul role="list" class="-mx-2 mt-2 space-y-1">
            <li>
                <a href="{{ route('admin.customers.create') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold text-gray-700 hover:text-admin-600 hover:bg-gray-50">
                    <svg class="h-6 w-6 shrink-0 text-gray-400 group-hover:text-admin-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Dodaj klienta
                </a>
            </li>
            <li>
                <a href="{{ route('admin.notifications.test') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold text-gray-700 hover:text-admin-600 hover:bg-gray-50">
                    <svg class="h-6 w-6 shrink-0 text-gray-400 group-hover:text-admin-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                    </svg>
                    Test powiadomień
                </a>
            </li>
        </ul>
    </li>
</ul>