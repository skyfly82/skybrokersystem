@extends('layouts.admin')

@section('title', 'Notification Banners')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="md:flex md:items-center md:justify-between mb-6">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                Notification Banners
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Manage system-wide notification banners and outage notifications
            </p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4">
            <a href="{{ route('admin.cms.banners.create') }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                New Banner
            </a>
        </div>
    </div>

    <!-- Banners List -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                All Banners ({{ $banners->total() }})
            </h3>
        </div>

        @if($banners->count() > 0)
            <ul class="divide-y divide-gray-200">
                @foreach($banners as $banner)
                    <li class="px-4 py-4 sm:px-6 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center">
                                    <h4 class="text-lg font-medium text-gray-900 truncate">
                                        {{ $banner->title }}
                                    </h4>
                                    
                                    <!-- Status Badge -->
                                    <div class="ml-2 flex-shrink-0">
                                        @if($banner->isCurrentlyActive())
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Active
                                            </span>
                                        @elseif($banner->is_active)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                Scheduled
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                Inactive
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <!-- Type Badge -->
                                    <div class="ml-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ match($banner->type) {
                                            'success' => 'bg-green-100 text-green-800',
                                            'warning' => 'bg-yellow-100 text-yellow-800',
                                            'error' => 'bg-red-100 text-red-800',
                                            default => 'bg-blue-100 text-blue-800'
                                        } }}">
                                            {{ ucfirst($banner->type) }}
                                        </span>
                                    </div>
                                    
                                    <!-- Priority Badge -->
                                    @if($banner->priority > 50)
                                        <div class="ml-2">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                High Priority
                                            </span>
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Banner Preview -->
                                <div class="mt-2">
                                    <div class="p-3 rounded-lg border {{ $banner->type_color }}">
                                        <p class="text-sm">{{ Str::limit($banner->message, 200) }}</p>
                                    </div>
                                </div>
                                
                                <div class="mt-2 flex items-center text-sm text-gray-500 space-x-4">
                                    <span>Priority: {{ $banner->priority }}</span>
                                    <span>Position: {{ ucfirst($banner->position) }}</span>
                                    <span>Created by {{ $banner->creator->name }}</span>
                                    <span>{{ $banner->created_at->format('M d, Y') }}</span>
                                    @if($banner->start_date)
                                        <span>Starts: {{ $banner->start_date->format('M d, Y H:i') }}</span>
                                    @endif
                                    @if($banner->end_date)
                                        <span>Ends: {{ $banner->end_date->format('M d, Y H:i') }}</span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="ml-4 flex-shrink-0 flex space-x-2">
                                <!-- Toggle Active -->
                                <button onclick="toggleBanner({{ $banner->id }}, {{ $banner->is_active ? 'false' : 'true' }})"
                                        class="inline-flex items-center px-3 py-1.5 border {{ $banner->is_active ? 'border-gray-300 text-gray-700 hover:bg-gray-50' : 'border-green-300 text-green-700 hover:bg-green-50' }} shadow-sm text-xs font-medium rounded bg-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    {{ $banner->is_active ? 'Deactivate' : 'Activate' }}
                                </button>

                                <a href="{{ route('admin.cms.banners.edit', $banner) }}" 
                                   class="inline-flex items-center px-3 py-1.5 border border-blue-300 shadow-sm text-xs font-medium rounded text-blue-700 bg-white hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Edit
                                </a>

                                <div class="relative" x-data="{ open: false }">
                                    <button @click="open = !open" 
                                            class="inline-flex items-center px-2 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50">
                                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                                        </svg>
                                    </button>
                                    <div x-show="open" 
                                         @click.away="open = false"
                                         class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10 border border-gray-200">
                                        <div class="py-1">
                                            <a href="{{ route('admin.cms.banners.show', $banner) }}" 
                                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                View Details
                                            </a>
                                            <form method="POST" action="{{ route('admin.cms.banners.destroy', $banner) }}" 
                                                  onsubmit="return confirm('Are you sure you want to delete this banner?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="block w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-red-50">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>

            <!-- Pagination -->
            @if($banners->hasPages())
                <div class="px-4 py-3 border-t border-gray-200">
                    {{ $banners->links() }}
                </div>
            @endif
        @else
            <div class="px-4 py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M34 40h10v-4a6 6 0 00-10.712-3.714M34 40H14m20 0v-4a9.971 9.971 0 00-.712-3.714M14 40H4v-4a6 6 0 0110.712-3.714M14 40v-4a9.971 9.971 0 01.712-3.714m0 0A9.973 9.973 0 0118 32a9.973 9.973 0 013.288 4.286m0 0A9.972 9.972 0 0118 36c-.896 0-1.75-.16-2.546-.457"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No banners</h3>
                <p class="mt-1 text-sm text-gray-500">
                    Get started by creating your first notification banner.
                </p>
                <div class="mt-6">
                    <a href="{{ route('admin.cms.banners.create') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        New Banner
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
async function toggleBanner(bannerId, newState) {
    try {
        const response = await fetch(`{{ url('admin/cms/banners') }}/${bannerId}/toggle`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const result = await response.json();
        if (result.success) {
            window.location.reload();
        } else {
            alert('Failed to toggle banner status');
        }
    } catch (error) {
        alert('Error: ' + error.message);
    }
}
</script>
@endsection