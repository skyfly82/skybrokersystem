@props(['class' => ''])

<div x-data="{ 
    open: false, 
    currentLocale: '{{ app()->getLocale() }}',
    locales: @js(config('app.supported_locales'))
}" class="relative {{ $class }}">
    <button @click="open = !open" 
            @click.away="open = false"
            class="flex items-center space-x-2 px-3 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-lg transition-colors duration-200">
        <i class="fas fa-globe text-gray-500"></i>
        <span x-text="locales[currentLocale]"></span>
        <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
    </button>

    <div x-show="open" 
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
        
        <template x-for="(name, locale) in locales" :key="locale">
            <a :href="`{{ url('/language') }}/${locale}`"
               class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-colors duration-200"
               :class="{ 'bg-indigo-50 text-indigo-600 font-medium': locale === currentLocale }">
                <span class="w-6 h-4 mr-3 rounded overflow-hidden">
                    <template x-if="locale === 'pl'">
                        <div class="w-full h-full bg-gradient-to-b from-white via-white to-red-500"></div>
                    </template>
                    <template x-if="locale === 'en'">
                        <div class="w-full h-full bg-gradient-to-b from-blue-500 via-white to-red-500"></div>
                    </template>
                </span>
                <span x-text="name"></span>
                <template x-if="locale === currentLocale">
                    <i class="fas fa-check ml-auto text-indigo-600"></i>
                </template>
            </a>
        </template>
    </div>
</div>