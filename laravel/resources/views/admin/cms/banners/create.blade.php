@extends('layouts.admin')

@section('title', 'Create Notification Banner')

@section('content')
<div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8" x-data="bannerForm()">
    <div class="md:flex md:items-center md:justify-between mb-6">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                Create Notification Banner
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Create a new system-wide notification banner or outage alert
            </p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4">
            <a href="{{ route('admin.cms.banners.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Back to Banners
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.cms.banners.store') }}" class="space-y-8">
        @csrf
        
        <!-- Banner Content -->
        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Banner Content</h3>
                
                <div class="grid grid-cols-1 gap-6">
                    <!-- Title -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700">Banner Title</label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" 
                               x-model="title" @input="updatePreview"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('title') border-red-500 @enderror"
                               placeholder="Enter banner title..." required>
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Message -->
                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700">Message</label>
                        <textarea name="message" id="message" rows="4" 
                                  x-model="message" @input="updatePreview"
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('message') border-red-500 @enderror"
                                  placeholder="Enter the banner message..." required>{{ old('message') }}</textarea>
                        @error('message')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Type and Position -->
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700">Banner Type</label>
                            <select name="type" id="type" 
                                    x-model="type" @change="updatePreview"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('type') border-red-500 @enderror">
                                <option value="info" {{ old('type', 'info') === 'info' ? 'selected' : '' }}>Info (Blue)</option>
                                <option value="success" {{ old('type') === 'success' ? 'selected' : '' }}>Success (Green)</option>
                                <option value="warning" {{ old('type') === 'warning' ? 'selected' : '' }}>Warning (Yellow)</option>
                                <option value="error" {{ old('type') === 'error' ? 'selected' : '' }}>Error (Red)</option>
                            </select>
                            @error('type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="position" class="block text-sm font-medium text-gray-700">Position</label>
                            <select name="position" id="position" 
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('position') border-red-500 @enderror">
                                <option value="top" {{ old('position', 'top') === 'top' ? 'selected' : '' }}>Top of Page</option>
                                <option value="bottom" {{ old('position') === 'bottom' ? 'selected' : '' }}>Bottom of Page</option>
                            </select>
                            @error('position')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Priority -->
                    <div>
                        <label for="priority" class="block text-sm font-medium text-gray-700">
                            Priority 
                            <span class="text-sm text-gray-500">(0-100, higher numbers display first)</span>
                        </label>
                        <input type="number" name="priority" id="priority" value="{{ old('priority', 50) }}" 
                               min="0" max="100"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('priority') border-red-500 @enderror">
                        @error('priority')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Schedule Settings -->
        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Schedule Settings</h3>
                
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <!-- Start Date -->
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date & Time</label>
                        <input type="datetime-local" name="start_date" id="start_date" value="{{ old('start_date') }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('start_date') border-red-500 @enderror">
                        <p class="mt-1 text-sm text-gray-500">Leave empty to start immediately</p>
                        @error('start_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- End Date -->
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700">End Date & Time</label>
                        <input type="datetime-local" name="end_date" id="end_date" value="{{ old('end_date') }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('end_date') border-red-500 @enderror">
                        <p class="mt-1 text-sm text-gray-500">Leave empty for no end date</p>
                        @error('end_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Display Rules -->
        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Display Rules</h3>
                <p class="text-sm text-gray-500 mb-4">Configure where this banner should appear</p>
                
                <div class="space-y-4">
                    <!-- Show on specific pages -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Show on specific pages (optional)</label>
                        <input type="text" 
                               x-model="specificPages" 
                               @input="updateDisplayRules"
                               class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               placeholder="Enter page paths separated by commas, e.g., /admin/dashboard, /customer/login">
                        <p class="mt-1 text-sm text-gray-500">Leave empty to show on all pages</p>
                    </div>

                    <!-- Exclude specific pages -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Exclude from specific pages (optional)</label>
                        <input type="text" 
                               x-model="excludePages" 
                               @input="updateDisplayRules"
                               class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               placeholder="Enter page paths to exclude, e.g., /login, /register">
                        <p class="mt-1 text-sm text-gray-500">Pages where banner should NOT appear</p>
                    </div>
                </div>
                
                <input type="hidden" name="display_rules" x-model="displayRulesJson">
            </div>
        </div>

        <!-- Preview -->
        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Preview</h3>
                
                <div x-show="title || message" class="mb-4">
                    <div :class="getPreviewClass()" class="p-4 rounded-lg border">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg :class="getIconClass()" class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path x-show="type === 'info'" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"/>
                                    <path x-show="type === 'success'" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                                    <path x-show="type === 'warning'" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"/>
                                    <path x-show="type === 'error'" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/>
                                </svg>
                            </div>
                            <div class="ml-3 flex-1">
                                <h3 :class="getTitleClass()" class="text-sm font-medium" x-text="title || 'Banner Title'"></h3>
                                <p :class="getMessageClass()" class="mt-1 text-sm" x-text="message || 'Your banner message will appear here'"></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <p class="text-sm text-gray-500" x-show="!title && !message">Enter title and message to see preview</p>
            </div>
        </div>

        <!-- Activation -->
        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Activation</h3>
                
                <div class="flex items-center">
                    <input id="is_active" name="is_active" type="checkbox" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="is_active" class="ml-2 block text-sm text-gray-900">
                        Activate banner immediately
                    </label>
                </div>
                <p class="mt-1 text-sm text-gray-500">Uncheck to create as draft</p>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('admin.cms.banners.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Cancel
            </a>
            <button type="submit" 
                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Create Banner
            </button>
        </div>
    </form>
</div>

<script>
function bannerForm() {
    return {
        title: '{{ old("title", "") }}',
        message: '{{ old("message", "") }}',
        type: '{{ old("type", "info") }}',
        specificPages: '',
        excludePages: '',
        displayRulesJson: '{}',

        updatePreview() {
            // This will trigger Alpine's reactivity
        },

        updateDisplayRules() {
            const rules = {};
            
            if (this.specificPages.trim()) {
                rules.pages = this.specificPages.split(',').map(p => p.trim()).filter(p => p);
            }
            
            if (this.excludePages.trim()) {
                rules.exclude_pages = this.excludePages.split(',').map(p => p.trim()).filter(p => p);
            }
            
            this.displayRulesJson = JSON.stringify(rules);
        },

        getPreviewClass() {
            const baseClass = 'border-l-4 ';
            switch (this.type) {
                case 'success': return baseClass + 'bg-green-50 border-green-400';
                case 'warning': return baseClass + 'bg-yellow-50 border-yellow-400';
                case 'error': return baseClass + 'bg-red-50 border-red-400';
                default: return baseClass + 'bg-blue-50 border-blue-400';
            }
        },

        getIconClass() {
            switch (this.type) {
                case 'success': return 'text-green-400';
                case 'warning': return 'text-yellow-400';
                case 'error': return 'text-red-400';
                default: return 'text-blue-400';
            }
        },

        getTitleClass() {
            switch (this.type) {
                case 'success': return 'text-green-800';
                case 'warning': return 'text-yellow-800';
                case 'error': return 'text-red-800';
                default: return 'text-blue-800';
            }
        },

        getMessageClass() {
            switch (this.type) {
                case 'success': return 'text-green-700';
                case 'warning': return 'text-yellow-700';
                case 'error': return 'text-red-700';
                default: return 'text-blue-700';
            }
        }
    }
}
</script>

<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endsection