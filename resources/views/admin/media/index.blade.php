@extends('layouts.admin')

@section('title', 'Media Library')

@section('header')
<div class="flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Media Library</h1>
        <p class="mt-1 text-sm text-gray-500">Manage your uploaded media files</p>
    </div>
    <label for="media-upload" 
           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 cursor-pointer">
        <i class="fas fa-upload mr-2"></i>
        Upload Media
    </label>
    <input type="file" id="media-upload" class="hidden" multiple accept="image/*,video/*,application/pdf">
</div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Upload Area -->
    <div id="upload-area" 
         class="border-2 border-dashed border-gray-300 rounded-xl p-12 text-center hover:border-indigo-400 transition-colors cursor-pointer">
        <i class="fas fa-cloud-upload-alt text-gray-400 text-5xl mb-4"></i>
        <p class="text-lg font-medium text-gray-700 mb-2">Drop files here or click to upload</p>
        <p class="text-sm text-gray-500">Supports images, videos, and PDFs (Max 10MB)</p>
    </div>

    <!-- Media Grid -->
    @if($media->count() > 0)
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
        @foreach($media as $item)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow group">
            <div class="aspect-square bg-gray-100 relative overflow-hidden">
                @if($item->isImage())
                <img src="{{ $item->url }}" 
                     alt="{{ $item->name }}" 
                     class="w-full h-full object-cover">
                @else
                <div class="w-full h-full flex items-center justify-center">
                    <i class="fas fa-file text-gray-400 text-4xl"></i>
                </div>
                @endif
                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all flex items-center justify-center opacity-0 group-hover:opacity-100">
                    <div class="flex space-x-2">
                        <a href="{{ $item->url }}" target="_blank" 
                           class="p-2 bg-white rounded-full text-gray-700 hover:text-indigo-600">
                            <i class="fas fa-external-link-alt"></i>
                        </a>
                        <form action="{{ route('admin.media.destroy', $item) }}" method="POST" 
                              onsubmit="return confirm('Are you sure?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="p-2 bg-white rounded-full text-gray-700 hover:text-red-600">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="p-2">
                <p class="text-xs font-medium text-gray-900 truncate" title="{{ $item->name }}">
                    {{ $item->name }}
                </p>
                <p class="text-xs text-gray-500">
                    {{ number_format($item->size / 1024, 2) }} KB
                </p>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        {{ $media->links() }}
    </div>
    @else
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
        <i class="fas fa-images text-gray-300 text-6xl mb-4"></i>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No media files yet</h3>
        <p class="text-gray-500">Upload your first file to get started.</p>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    // Upload functionality
    const uploadArea = document.getElementById('upload-area');
    const fileInput = document.getElementById('media-upload');

    uploadArea.addEventListener('click', () => fileInput.click());
    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.classList.add('border-indigo-500', 'bg-indigo-50');
    });
    uploadArea.addEventListener('dragleave', () => {
        uploadArea.classList.remove('border-indigo-500', 'bg-indigo-50');
    });
    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('border-indigo-500', 'bg-indigo-50');
        fileInput.files = e.dataTransfer.files;
        uploadFiles();
    });

    fileInput.addEventListener('change', uploadFiles);

    function uploadFiles() {
        const files = fileInput.files;
        if (files.length === 0) return;

        Array.from(files).forEach(file => {
            const formData = new FormData();
            formData.append('file', file);

            fetch('{{ route("admin.media.store") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Upload failed. Please try again.');
            });
        });
    }
</script>
@endsection
