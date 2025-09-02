@extends('layouts.admin')

@section('title', 'Create CMS Page')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="md:flex md:items-center md:justify-between mb-6">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                Create New Page
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Add a new page with SEO optimization and content management
            </p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4">
            <a href="{{ route('admin.cms.pages.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Back to Pages
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.cms.pages.store') }}" class="space-y-8">
        @csrf
        
        <div class="bg-white shadow sm:rounded-lg">
            <!-- Basic Information -->
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Basic Information</h3>
                
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <!-- Title -->
                    <div class="sm:col-span-2">
                        <label for="title" class="block text-sm font-medium text-gray-700">Page Title</label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('title') border-red-500 @enderror"
                               placeholder="Enter page title..." required>
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Slug -->
                    <div class="sm:col-span-2">
                        <label for="slug" class="block text-sm font-medium text-gray-700">URL Slug</label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                            <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                                {{ url('/') }}/
                            </span>
                            <input type="text" name="slug" id="slug" value="{{ old('slug') }}" 
                                   class="flex-1 block w-full rounded-none rounded-r-md border-gray-300 focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('slug') border-red-500 @enderror"
                                   placeholder="Auto-generated from title">
                        </div>
                        <p class="mt-1 text-sm text-gray-500">Leave empty to auto-generate from title</p>
                        @error('slug')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Content</h3>
                
                <div>
                    <label for="content" class="block text-sm font-medium text-gray-700">Page Content</label>
                    <div class="mt-1">
                        <textarea name="content" id="content" rows="20" 
                                  class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('content') border-red-500 @enderror"
                                  placeholder="Enter page content...">{{ old('content') }}</textarea>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">Use HTML or markdown for formatting</p>
                    @error('content')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- SEO Settings -->
        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">SEO Settings</h3>
                
                <div class="grid grid-cols-1 gap-6">
                    <!-- Meta Description -->
                    <div>
                        <div class="flex items-center justify-between">
                            <label for="meta_description" class="block text-sm font-medium text-gray-700">Meta Description</label>
                            <button type="button" 
                                    onclick="generateAiContent('meta_description')"
                                    class="inline-flex items-center px-2 py-1 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50">
                                <svg class="-ml-0.5 mr-1 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                AI Generate
                            </button>
                        </div>
                        <textarea name="meta_description" id="meta_description" rows="3" 
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('meta_description') border-red-500 @enderror"
                                  placeholder="Brief description for search engines..." maxlength="160">{{ old('meta_description') }}</textarea>
                        <p class="mt-1 text-sm text-gray-500">Recommended: 120-160 characters. <span id="meta-desc-count">0</span>/160</p>
                        @error('meta_description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Meta Keywords -->
                    <div>
                        <div class="flex items-center justify-between">
                            <label for="meta_keywords" class="block text-sm font-medium text-gray-700">Meta Keywords</label>
                            <button type="button" 
                                    onclick="generateAiContent('keywords')"
                                    class="inline-flex items-center px-2 py-1 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50">
                                <svg class="-ml-0.5 mr-1 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                AI Generate
                            </button>
                        </div>
                        <input type="text" name="meta_keywords" id="meta_keywords" value="{{ old('meta_keywords') }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('meta_keywords') border-red-500 @enderror"
                               placeholder="keyword1, keyword2, keyword3">
                        <p class="mt-1 text-sm text-gray-500">Separate keywords with commas</p>
                        @error('meta_keywords')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Publishing Options -->
        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Publishing Options</h3>
                
                <div class="flex items-center">
                    <input id="is_published" name="is_published" type="checkbox" value="1" {{ old('is_published') ? 'checked' : '' }}
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="is_published" class="ml-2 block text-sm text-gray-900">
                        Publish immediately
                    </label>
                </div>
                <p class="mt-1 text-sm text-gray-500">Uncheck to save as draft</p>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('admin.cms.pages.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Cancel
            </a>
            <button type="submit" 
                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Create Page
            </button>
        </div>
    </form>
</div>

<!-- Rich Text Editor (you can replace this with your preferred editor) -->
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-generate slug from title
    const titleInput = document.getElementById('title');
    const slugInput = document.getElementById('slug');
    
    titleInput.addEventListener('input', function() {
        if (!slugInput.value) {
            let slug = this.value
                .toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .trim('-');
            slugInput.value = slug;
        }
    });
    
    // Meta description character counter
    const metaDesc = document.getElementById('meta_description');
    const counter = document.getElementById('meta-desc-count');
    
    metaDesc.addEventListener('input', function() {
        counter.textContent = this.value.length;
        if (this.value.length > 160) {
            counter.style.color = 'red';
        } else if (this.value.length > 120) {
            counter.style.color = 'orange';
        } else {
            counter.style.color = 'green';
        }
    });
    
    // Initialize rich text editor
    tinymce.init({
        selector: '#content',
        height: 400,
        menubar: false,
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'help', 'wordcount'
        ],
        toolbar: 'undo redo | blocks | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
        content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }'
    });
});

// AI Content Generation
async function generateAiContent(contentType) {
    const titleField = document.getElementById('title');
    const title = titleField.value.trim();
    
    if (!title) {
        alert('Please enter a page title first');
        titleField.focus();
        return;
    }
    
    const button = event.target.closest('button');
    const originalText = button.innerHTML;
    
    // Show loading state
    button.innerHTML = '<svg class="animate-spin -ml-0.5 mr-1 h-3 w-3 text-gray-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Generating...';
    button.disabled = true;
    
    try {
        const response = await fetch('{{ route("admin.cms.ai.generate-seo") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                title: title,
                content_type: contentType
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Fill the appropriate field
            if (contentType === 'meta_description') {
                const field = document.getElementById('meta_description');
                field.value = result.content;
                // Trigger character counter update
                const counter = document.getElementById('meta-desc-count');
                counter.textContent = result.content.length;
                if (result.content.length > 160) {
                    counter.style.color = 'red';
                } else if (result.content.length > 120) {
                    counter.style.color = 'orange';
                } else {
                    counter.style.color = 'green';
                }
            } else if (contentType === 'keywords') {
                document.getElementById('meta_keywords').value = result.content;
            } else if (contentType === 'content_outline') {
                // For TinyMCE editor
                if (typeof tinymce !== 'undefined' && tinymce.get('content')) {
                    tinymce.get('content').setContent(result.content);
                } else {
                    document.getElementById('content').value = result.content;
                }
            }
            
            // Show success notification
            showNotification('AI content generated successfully!', 'success');
        } else {
            throw new Error(result.message || 'Failed to generate content');
        }
        
    } catch (error) {
        console.error('AI generation error:', error);
        showNotification('Failed to generate content: ' + error.message, 'error');
    } finally {
        // Restore button state
        button.innerHTML = originalText;
        button.disabled = false;
    }
}

function showNotification(message, type = 'info') {
    // Simple notification - you could integrate with a toast library
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-4 py-2 rounded-md shadow-lg z-50 text-white ${
        type === 'success' ? 'bg-green-500' : 
        type === 'error' ? 'bg-red-500' : 'bg-blue-500'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script>
@endsection