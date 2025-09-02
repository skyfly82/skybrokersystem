@extends('layouts.admin')

@section('title', 'Media Library')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8" x-data="mediaManager()">
    <!-- Header -->
    <div class="md:flex md:items-center md:justify-between mb-6">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                Media Library
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Manage images, documents and media files
            </p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4 space-x-2">
            <button @click="showUploadModal = true"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                Upload Files
            </button>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white shadow sm:rounded-lg mb-6">
        <div class="px-4 py-5 sm:p-6">
            <form method="GET" class="sm:flex sm:items-center sm:space-x-4">
                <!-- Search -->
                <div class="flex-1">
                    <label for="search" class="sr-only">Search media</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" 
                           class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                           placeholder="Search by filename, alt text, or description...">
                </div>
                
                <!-- Type Filter -->
                <div>
                    <label for="type" class="sr-only">File type</label>
                    <select name="type" id="type" 
                            class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">All types</option>
                        <option value="image" {{ request('type') === 'image' ? 'selected' : '' }}>Images</option>
                        <option value="application" {{ request('type') === 'application' ? 'selected' : '' }}>Documents</option>
                        <option value="video" {{ request('type') === 'video' ? 'selected' : '' }}>Videos</option>
                        <option value="audio" {{ request('type') === 'audio' ? 'selected' : '' }}>Audio</option>
                    </select>
                </div>
                
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gray-600 hover:bg-gray-700">
                    Filter
                </button>
                
                @if(request()->hasAny(['search', 'type']))
                    <a href="{{ route('admin.cms.media.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Clear
                    </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Media Grid -->
    @if($media->count() > 0)
        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Media Files ({{ $media->total() }})
                </h3>
            </div>
            
            <div class="p-4">
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    @foreach($media as $file)
                        <div class="group relative aspect-square bg-gray-100 rounded-lg overflow-hidden cursor-pointer hover:shadow-lg transition-shadow"
                             @click="selectMedia({{ $file->toJson() }})">
                            
                            <!-- Thumbnail -->
                            <div class="w-full h-full flex items-center justify-center">
                                @if($file->isImage())
                                    <img src="{{ $file->url }}" 
                                         alt="{{ $file->alt_text ?: $file->original_name }}"
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="text-center p-2">
                                        <svg class="mx-auto h-8 w-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <p class="text-xs text-gray-600 font-medium">{{ strtoupper(pathinfo($file->original_name, PATHINFO_EXTENSION)) }}</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Overlay -->
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all duration-200 flex items-center justify-center">
                                <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex space-x-2">
                                    <button @click.stop="editMedia({{ $file->toJson() }})"
                                            class="p-2 bg-white rounded-full text-gray-600 hover:text-blue-600">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <button @click.stop="copyUrl('{{ $file->url }}')"
                                            class="p-2 bg-white rounded-full text-gray-600 hover:text-green-600">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                    </button>
                                    <button @click.stop="deleteMedia({{ $file->id }})"
                                            class="p-2 bg-white rounded-full text-gray-600 hover:text-red-600">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- File info -->
                            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-2">
                                <p class="text-white text-xs font-medium truncate">{{ $file->original_name }}</p>
                                <p class="text-gray-300 text-xs">{{ $file->formatted_size }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Pagination -->
            @if($media->hasPages())
                <div class="px-4 py-3 border-t border-gray-200">
                    {{ $media->withQueryString()->links() }}
                </div>
            @endif
        </div>
    @else
        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6 6l-3.5 3.5a2 2 0 001.5 3.5H21l5-5m-4-4l-3-3m-4 4L12 22m4-4v4"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No media files</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by uploading your first file.</p>
                <div class="mt-6">
                    <button @click="showUploadModal = true"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        Upload Files
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Upload Modal -->
    <div x-show="showUploadModal" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                 @click="showUploadModal = false"></div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form @submit.prevent="uploadFiles">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                    Upload Files
                                </h3>
                                
                                <!-- File Upload Area -->
                                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition-colors"
                                     @drop.prevent="handleDrop($event)"
                                     @dragover.prevent
                                     @dragenter.prevent>
                                    <input type="file" 
                                           x-ref="fileInput"
                                           @change="handleFileSelect($event)"
                                           multiple
                                           accept="image/*,application/pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx"
                                           class="hidden">
                                    
                                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"/>
                                    </svg>
                                    
                                    <div>
                                        <p class="text-sm text-gray-600">
                                            Drop files here or 
                                            <button type="button" 
                                                    @click="$refs.fileInput.click()"
                                                    class="font-medium text-blue-600 hover:text-blue-500">
                                                browse
                                            </button>
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            PNG, JPG, GIF, PDF, DOC, XLS, PPT up to 10MB
                                        </p>
                                    </div>
                                </div>

                                <!-- Selected Files -->
                                <div x-show="selectedFiles.length > 0" class="mt-4">
                                    <h4 class="text-sm font-medium text-gray-900 mb-2">Selected Files:</h4>
                                    <div class="space-y-2 max-h-40 overflow-y-auto">
                                        <template x-for="(file, index) in selectedFiles" :key="index">
                                            <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                                                <span class="text-sm text-gray-700" x-text="file.name"></span>
                                                <button type="button" 
                                                        @click="removeFile(index)"
                                                        class="text-red-600 hover:text-red-800">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" 
                                :disabled="selectedFiles.length === 0 || uploading"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                            <span x-show="!uploading">Upload Files</span>
                            <span x-show="uploading" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Uploading...
                            </span>
                        </button>
                        <button type="button" 
                                @click="closeUploadModal()"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div x-show="showEditModal" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                 @click="showEditModal = false"></div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form @submit.prevent="updateMedia">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                            Edit Media
                        </h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Filename</label>
                                <p class="mt-1 text-sm text-gray-900" x-text="editingMedia?.original_name"></p>
                            </div>
                            
                            <div>
                                <label for="edit_alt_text" class="block text-sm font-medium text-gray-700">Alt Text</label>
                                <input type="text" 
                                       id="edit_alt_text"
                                       x-model="editingMedia.alt_text"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                       placeholder="Describe the image for accessibility">
                            </div>
                            
                            <div>
                                <label for="edit_description" class="block text-sm font-medium text-gray-700">Description</label>
                                <textarea id="edit_description"
                                          x-model="editingMedia.description"
                                          rows="3"
                                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                          placeholder="Additional description or notes"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" 
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Update
                        </button>
                        <button type="button" 
                                @click="showEditModal = false"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function mediaManager() {
    return {
        showUploadModal: false,
        showEditModal: false,
        selectedFiles: [],
        uploading: false,
        editingMedia: null,

        handleFileSelect(event) {
            const files = Array.from(event.target.files);
            this.selectedFiles = [...this.selectedFiles, ...files];
        },

        handleDrop(event) {
            const files = Array.from(event.dataTransfer.files);
            this.selectedFiles = [...this.selectedFiles, ...files];
        },

        removeFile(index) {
            this.selectedFiles.splice(index, 1);
        },

        closeUploadModal() {
            this.showUploadModal = false;
            this.selectedFiles = [];
            this.$refs.fileInput.value = '';
        },

        async uploadFiles() {
            if (this.selectedFiles.length === 0) return;

            this.uploading = true;
            
            try {
                const uploadPromises = this.selectedFiles.map(async (file) => {
                    const formData = new FormData();
                    formData.append('file', file);
                    
                    const response = await fetch('{{ route("admin.cms.media.upload") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });
                    
                    const result = await response.json();
                    if (!result.success) {
                        throw new Error(result.message);
                    }
                    
                    return result;
                });
                
                await Promise.all(uploadPromises);
                
                // Reload page to show new files
                window.location.reload();
                
            } catch (error) {
                alert('Upload failed: ' + error.message);
            } finally {
                this.uploading = false;
            }
        },

        editMedia(media) {
            this.editingMedia = { ...media };
            this.showEditModal = true;
        },

        async updateMedia() {
            try {
                const response = await fetch(`{{ url('admin/cms/media') }}/${this.editingMedia.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        alt_text: this.editingMedia.alt_text,
                        description: this.editingMedia.description
                    })
                });
                
                const result = await response.json();
                if (result.success) {
                    this.showEditModal = false;
                    window.location.reload();
                } else {
                    throw new Error(result.message);
                }
                
            } catch (error) {
                alert('Update failed: ' + error.message);
            }
        },

        async deleteMedia(mediaId) {
            if (!confirm('Are you sure you want to delete this file? This action cannot be undone.')) {
                return;
            }
            
            try {
                const response = await fetch(`{{ url('admin/cms/media') }}/${mediaId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                const result = await response.json();
                if (result.success) {
                    window.location.reload();
                } else {
                    throw new Error(result.message);
                }
                
            } catch (error) {
                alert('Delete failed: ' + error.message);
            }
        },

        copyUrl(url) {
            navigator.clipboard.writeText(url).then(() => {
                // You could show a toast notification here
                alert('URL copied to clipboard!');
            });
        },

        selectMedia(media) {
            // This could be used for selecting media in a modal/picker context
            console.log('Selected media:', media);
        }
    }
}
</script>
@endsection