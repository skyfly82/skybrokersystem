@props(['items' => []])

<nav aria-label="{{ __('common.breadcrumbs', 'Breadcrumbs') }}" class="flex" x-data="{ items: @js($items) }">
    <ol class="flex items-center space-x-1 md:space-x-3">
        <!-- Home -->
        <li class="inline-flex items-center">
            <a href="{{ route('home') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 transition-colors duration-200">
                <i class="fas fa-home w-3 h-3 mr-2.5" aria-hidden="true"></i>
                {{ __('common.home') }}
            </a>
        </li>
        
        <!-- Dynamic breadcrumbs -->
        <template x-for="(item, index) in items" :key="index">
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right w-3 h-3 text-gray-400 mx-1" aria-hidden="true"></i>
                    <template x-if="item.url">
                        <a :href="item.url" 
                           class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2 transition-colors duration-200"
                           x-text="item.title">
                        </a>
                    </template>
                    <template x-if="!item.url">
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2" 
                              aria-current="page"
                              x-text="item.title">
                        </span>
                    </template>
                </div>
            </li>
        </template>
    </ol>
</nav>

<!-- Structured Data for Breadcrumbs -->
<script type="application/ld+json" x-data="{ items: @js($items) }">
{
    "@context": "https://schema.org",
    "@type": "BreadcrumbList",
    "itemListElement": [
        {
            "@type": "ListItem",
            "position": 1,
            "name": "{{ __('common.home') }}",
            "item": "{{ route('home') }}"
        }
        <template x-for="(item, index) in items" :key="index">
            ,{
                "@type": "ListItem",
                "position": <span x-text="index + 2"></span>,
                "name": <span x-text="JSON.stringify(item.title)"></span>
                <template x-if="item.url">
                    ,"item": <span x-text="JSON.stringify(item.url)"></span>
                </template>
            }
        </template>
    ]
}
</script>