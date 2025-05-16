@extends('layouts.admin')

@section('title', 'Posts')

@section('content')
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6 py-12">
    <!-- Header with controls -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <h1 class="text-2xl font-bold text-gray-800">Manage Posts</h1>
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="relative">
                <select id="statusFilter" onchange="filterPosts()"
                    class="appearance-none bg-white border border-gray-300 rounded-lg pl-4 pr-10 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#011733] focus:border-[#011733] w-full">
                    <option value="all">All Status</option>
                    <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-gray-700">
                    <i class="fas fa-chevron-down text-xs"></i>
                </div>
            </div>
            <a href="{{ route('admin.posts.create') }}" 
               class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-[#011733] hover:bg-[#011733] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#011733] whitespace-nowrap">
                <i class="fas fa-plus mr-2"></i> New Post
            </a>
        </div>
    </div>

    <!-- Grid Layout -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($posts as $post)
        <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
            <!-- Featured Image -->
            <div class="h-48 bg-gray-100 relative overflow-hidden">
                @if($post->featured_image)
                <img src="{{ asset('storage/' . $post->featured_image) }}" 
                     alt="{{ $post->title }}" 
                     class="w-full h-full object-cover">
                @else
                <div class="w-full h-full flex items-center justify-center text-gray-400">
                    <i class="fas fa-image text-4xl"></i>
                </div>
                @endif
                <!-- Status Badge -->
                <div class="absolute top-3 right-3">
                    <span class="px-3 py-1 rounded-full text-xs font-semibold 
                        {{ $post->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ ucfirst($post->status) }}
                    </span>
                </div>
            </div>

            <!-- Content -->
            <div class="p-5">
                <!-- Title and Date -->
                <h3 class="text-lg font-bold text-gray-800 mb-1 line-clamp-2">{{ $post->title }}</h3>
                <p class="text-sm text-gray-500 mb-3">
                    <i class="far fa-calendar-alt mr-1"></i>
                    {{ $post->created_at->format('M d, Y') }}
                </p>

                <!-- Categories -->
                @if($post->categories->count() > 0)
                <div class="flex flex-wrap gap-2 mb-4">
                    @foreach($post->categories as $category)
                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-blue-50 text-[#037396FF]">
                        {{ $category->name }}
                    </span>
                    @endforeach
                </div>
                @endif

                <!-- Excerpt -->
                @if($post->excerpt)
                <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ $post->excerpt }}</p>
                @endif

                <!-- Action Buttons -->
                <div class="flex justify-between items-center border-t border-gray-100 pt-4">
                    <a href="{{ route('admin.posts.show', $post) }}" 
                       class="text-sm font-medium text-[#037396FF] hover:text-[#011733] flex items-center">
                        <i class="far fa-eye mr-1"></i> Preview
                    </a>
                    <div class="flex space-x-3">
                        <a href="{{ route('admin.posts.edit', $post) }}" 
                           class="text-gray-500 hover:text-[#037396FF] transition-colors"
                           title="Edit">
                            <i class="fas fa-pencil-alt"></i>  <span class="text-xs">Edit</span>  
                        </a>
                        <span>|</span>
                        <form action="{{ route('admin.posts.destroy', $post) }}" method="POST" 
                              onsubmit="return confirm('Are you sure you want to delete this post?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="text-gray-500 hover:text-red-600 transition-colors"
                                    title="Delete">
                                <i class="fas fa-trash-alt"></i> <span class="text-xs">Delete</span> 
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $posts->links() }}
    </div>
</div>
@endsection

@section('scripts')
<script>
    function filterPosts() {
        const status = document.getElementById('statusFilter').value;
        window.location.href = "{{ route('admin.posts.index') }}?status=" + status;
    }

    // Set the initial filter value from URL
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const statusParam = urlParams.get('status');
        if (statusParam) {
            document.getElementById('statusFilter').value = statusParam;
        }
    });
</script>
@endsection