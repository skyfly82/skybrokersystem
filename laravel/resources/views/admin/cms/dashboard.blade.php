@extends('layouts.admin')

@section('title', 'Marketing Dashboard')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Marketing Dashboard</h1>
        <p class="mt-2 text-gray-600">Manage content, media, and system notifications</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Pages Stats -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">CMS Pages</dt>
                            <dd class="text-2xl font-semibold text-gray-900">{{ $stats['pages']['total'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
                <div class="text-sm">
                    <div class="flex justify-between">
                        <span class="text-green-600 font-medium">{{ $stats['pages']['published'] }} published</span>
                        <span class="text-yellow-600 font-medium">{{ $stats['pages']['drafts'] }} drafts</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Media Stats -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6 6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Media Files</dt>
                            <dd class="text-2xl font-semibold text-gray-900">{{ $stats['media']['total'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
                <div class="text-sm">
                    <div class="flex justify-between">
                        <span class="text-blue-600 font-medium">{{ $stats['media']['images'] }} images</span>
                        <span class="text-gray-600 font-medium">{{ $stats['media']['size'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Banners Stats -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Notification Banners</dt>
                            <dd class="text-2xl font-semibold text-gray-900">{{ $stats['banners']['total'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
                <div class="text-sm">
                    <div class="flex justify-between">
                        <span class="text-green-600 font-medium">{{ $stats['banners']['active'] }} active</span>
                        <span class="text-blue-600 font-medium">{{ $stats['banners']['scheduled'] }} scheduled</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Pages -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Recent Pages</h3>
                    <a href="{{ route('admin.cms.pages.index') }}" 
                       class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                        View all →
                    </a>
                </div>
            </div>
            
            @if($recentPages->count() > 0)
                <ul class="divide-y divide-gray-200">
                    @foreach($recentPages as $page)
                        <li class="px-4 py-4 hover:bg-gray-50">
                            <div class="flex items-center justify-between">
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-medium text-gray-900 truncate">
                                        <a href="{{ route('admin.cms.pages.show', $page) }}" 
                                           class="hover:text-blue-600">
                                            {{ $page->title }}
                                        </a>
                                    </h4>
                                    <p class="text-sm text-gray-500">
                                        Updated {{ $page->updated_at->diffForHumans() }} by {{ $page->creator->name }}
                                    </p>
                                </div>
                                <div class="flex-shrink-0">
                                    @if($page->is_published)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Published
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Draft
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="px-4 py-8 text-center text-sm text-gray-500">
                    No pages created yet.
                    <a href="{{ route('admin.cms.pages.create') }}" class="text-blue-600 hover:text-blue-800">Create your first page</a>
                </div>
            @endif
        </div>

        <!-- Recent Media -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Recent Media</h3>
                    <a href="{{ route('admin.cms.media.index') }}" 
                       class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                        View all →
                    </a>
                </div>
            </div>
            
            @if($recentMedia->count() > 0)
                <div class="p-4">
                    <div class="grid grid-cols-3 gap-2">
                        @foreach($recentMedia as $media)
                            <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden">
                                @if($media->isImage())
                                    <img src="{{ $media->url }}" 
                                         alt="{{ $media->alt_text ?: $media->original_name }}"
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <div class="text-center">
                                            <svg class="mx-auto h-6 w-6 text-gray-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <p class="text-xs text-gray-600 font-medium">{{ strtoupper(pathinfo($media->original_name, PATHINFO_EXTENSION)) }}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="px-4 py-8 text-center text-sm text-gray-500">
                    No media files uploaded yet.
                    <a href="{{ route('admin.cms.media.index') }}" class="text-blue-600 hover:text-blue-800">Upload your first file</a>
                </div>
            @endif
        </div>
    </div>

    <!-- Active Banners -->
    @if($activeBanners->count() > 0)
        <div class="mt-8">
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Active Banners</h3>
                        <a href="{{ route('admin.cms.banners.index') }}" 
                           class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                            Manage banners →
                        </a>
                    </div>
                </div>
                
                <div class="p-4 space-y-3">
                    @foreach($activeBanners as $banner)
                        <div class="p-3 rounded-lg {{ $banner->type_color }}">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 {{ match($banner->type) {
                                        'success' => 'text-green-400',
                                        'warning' => 'text-yellow-400',
                                        'error' => 'text-red-400',
                                        default => 'text-blue-400'
                                    } }}" fill="currentColor" viewBox="0 0 20 20">
                                        @if($banner->type === 'success')
                                            <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                                        @elseif($banner->type === 'warning')
                                            <path d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"/>
                                        @elseif($banner->type === 'error')
                                            <path d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/>
                                        @else
                                            <path d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"/>
                                        @endif
                                    </svg>
                                </div>
                                <div class="ml-3 flex-1">
                                    <h4 class="text-sm font-medium {{ match($banner->type) {
                                        'success' => 'text-green-800',
                                        'warning' => 'text-yellow-800',
                                        'error' => 'text-red-800',
                                        default => 'text-blue-800'
                                    } }}">
                                        {{ $banner->title }}
                                    </h4>
                                    <p class="mt-1 text-sm {{ match($banner->type) {
                                        'success' => 'text-green-700',
                                        'warning' => 'text-yellow-700',
                                        'error' => 'text-red-700',
                                        default => 'text-blue-700'
                                    } }}">
                                        {{ Str::limit($banner->message, 120) }}
                                    </p>
                                    <p class="mt-1 text-xs text-gray-600">
                                        Priority {{ $banner->priority }} • {{ ucfirst($banner->position) }}
                                        @if($banner->end_date)
                                            • Ends {{ $banner->end_date->format('M d, Y H:i') }}
                                        @endif
                                    </p>
                                </div>
                                <div class="ml-3 flex-shrink-0">
                                    <a href="{{ route('admin.cms.banners.edit', $banner) }}" 
                                       class="text-gray-400 hover:text-gray-600">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Quick Actions -->
    <div class="mt-8">
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Quick Actions</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <a href="{{ route('admin.cms.pages.create') }}" 
                       class="inline-flex items-center p-4 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        <svg class="h-6 w-6 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <span class="text-sm font-medium text-gray-900">New Page</span>
                    </a>

                    <a href="{{ route('admin.cms.media.index') }}" 
                       class="inline-flex items-center p-4 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        <svg class="h-6 w-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <span class="text-sm font-medium text-gray-900">Upload Media</span>
                    </a>

                    <a href="{{ route('admin.cms.banners.create') }}" 
                       class="inline-flex items-center p-4 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        <svg class="h-6 w-6 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L1.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        <span class="text-sm font-medium text-gray-900">New Banner</span>
                    </a>

                    <a href="{{ route('admin.cms.pages.index') }}?status=draft" 
                       class="inline-flex items-center p-4 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        <svg class="h-6 w-6 text-yellow-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                        </svg>
                        <span class="text-sm font-medium text-gray-900">Review Drafts</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection