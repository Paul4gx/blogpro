@extends('layouts.admin')

@section('title', 'Posts')

@section('header')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Posts</h1>
        <p class="mt-1 text-xs sm:text-sm text-gray-500">Manage and organize your blog posts</p>
    </div>
    <a href="{{ route('admin.posts.create') }}" 
       class="inline-flex items-center justify-center px-3 sm:px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
        <i class="fas fa-plus mr-2"></i>
        New Post
    </a>
</div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Filters and Search -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
        <form method="GET" action="{{ route('admin.posts.index') }}" class="flex flex-col md:flex-row gap-3 sm:gap-4">
            <!-- Search -->
            <div class="flex-1">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Search posts..." 
                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
            </div>

            <!-- Status Filter -->
            <div class="w-full md:w-48">
                <select name="status" 
                        class="block w-full pl-3 pr-10 py-2 border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <option value="">All Status</option>
                    <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                </select>
            </div>

            <!-- Category Filter -->
            <div class="w-full md:w-48">
                <select name="category" 
                        class="block w-full pl-3 pr-10 py-2 border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Submit Button -->
            <button type="submit" 
                    class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="fas fa-filter mr-2"></i>
                Filter
            </button>

            @if(request()->has('search') || request()->has('status') || request()->has('category'))
            <a href="{{ route('admin.posts.index') }}" 
               class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50">
                <i class="fas fa-times mr-2"></i>
                Clear
            </a>
            @endif
        </form>
    </div>

    <!-- Bulk Actions -->
    <div x-data="{ selectedPosts: [], showBulkActions: false }" class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <input type="checkbox" 
                       @change="selectedPosts = $event.target.checked ? [].slice.call(document.querySelectorAll('input[type=checkbox][name=post_ids]')).map(cb => cb.value) : []; showBulkActions = selectedPosts.length > 0"
                       class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <span class="text-sm text-gray-700">
                    <span x-text="selectedPosts.length"></span> selected
                </span>
            </div>
            <div x-show="showBulkActions" class="flex items-center space-x-2">
                <form method="POST" action="{{ route('admin.posts.bulk-action') }}" class="inline">
                    @csrf
                    <input type="hidden" name="posts" :value="JSON.stringify(selectedPosts)">
                    <input type="hidden" name="action" value="publish">
                    <button type="submit" 
                            class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-green-700 bg-green-50 rounded-lg hover:bg-green-100">
                        <i class="fas fa-check mr-1"></i> Publish
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.posts.bulk-action') }}" class="inline">
                    @csrf
                    <input type="hidden" name="posts" :value="JSON.stringify(selectedPosts)">
                    <input type="hidden" name="action" value="draft">
                    <button type="submit" 
                            class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-yellow-700 bg-yellow-50 rounded-lg hover:bg-yellow-100">
                        <i class="fas fa-edit mr-1"></i> Draft
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.posts.bulk-action') }}" 
                      onsubmit="return confirm('Are you sure you want to delete selected posts?')" class="inline">
                    @csrf
                    <input type="hidden" name="posts" :value="JSON.stringify(selectedPosts)">
                    <input type="hidden" name="action" value="delete">
                    <button type="submit" 
                            class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-red-700 bg-red-50 rounded-lg hover:bg-red-100">
                        <i class="fas fa-trash mr-1"></i> Delete
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Posts Grid -->
    @if($posts->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($posts as $post)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-all duration-200">
            <!-- Featured Image -->
            <div class="h-48 bg-gradient-to-br from-gray-100 to-gray-200 relative overflow-hidden">
                @if($post->featured_image)
                <img src="{{ asset('storage/' . $post->featured_image) }}" 
                     alt="{{ $post->title }}" 
                     class="w-full h-full object-cover">
                @else
                <div class="w-full h-full flex items-center justify-center text-gray-400">
                    <i class="fas fa-image text-5xl"></i>
                </div>
                @endif
                <!-- Status Badge -->
                <div class="absolute top-3 right-3 flex flex-col gap-2 items-end">
                    <span class="px-3 py-1 rounded-full text-xs font-semibold shadow-sm
                        {{ $post->status === 'published' ? 'bg-green-500 text-white' : 'bg-yellow-500 text-white' }}">
                        {{ ucfirst($post->status) }}
                    </span>
                    @if($post->is_featured)
                    <span class="px-3 py-1 rounded-full text-xs font-semibold shadow-sm bg-indigo-600 text-white">
                        <i class="fas fa-star mr-1"></i> Featured
                    </span>
                    @endif
                </div>
                <!-- Checkbox for bulk actions -->
                <div class="absolute top-3 left-3">
                    <input type="checkbox" 
                           name="post_ids" 
                           value="{{ $post->id }}"
                           x-model="selectedPosts"
                           @change="showBulkActions = selectedPosts.length > 0"
                           class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 w-5 h-5">
                </div>
            </div>

            <!-- Content -->
            <div class="p-5">
                <!-- Title -->
                <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2 hover:text-indigo-600 transition-colors">
                    <a href="{{ route('admin.posts.edit', $post) }}">{{ $post->title }}</a>
                </h3>

                <!-- Meta Info -->
                <div class="flex items-center space-x-4 text-xs text-gray-500 mb-3">
                    <span class="flex items-center">
                        <i class="far fa-calendar-alt mr-1"></i>
                        {{ $post->created_at->format('M d, Y') }}
                    </span>
                    @if($post->reading_time)
                    <span class="flex items-center">
                        <i class="far fa-clock mr-1"></i>
                        {{ $post->reading_time }} min
                    </span>
                    @endif
                    @if($post->views > 0)
                    <span class="flex items-center">
                        <i class="far fa-eye mr-1"></i>
                        {{ $post->views }}
                    </span>
                    @endif
                </div>

                <!-- Categories -->
                @if($post->categories->count() > 0)
                <div class="flex flex-wrap gap-2 mb-3">
                    @foreach($post->categories->take(2) as $category)
                    <span class="px-2 py-1 bg-indigo-50 text-indigo-700 rounded text-xs font-medium">
                        {{ $category->name }}
                    </span>
                    @endforeach
                    @if($post->categories->count() > 2)
                    <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded text-xs">
                        +{{ $post->categories->count() - 2 }}
                    </span>
                    @endif
                </div>
                @endif

                <!-- Tags -->
                @if($post->tags->count() > 0)
                <div class="flex flex-wrap gap-1 mb-4">
                    @foreach($post->tags->take(3) as $tag)
                    <span class="px-2 py-0.5 bg-purple-50 text-purple-700 rounded text-xs">
                        #{{ $tag->name }}
                    </span>
                    @endforeach
                </div>
                @endif

                <!-- Excerpt -->
                @if($post->excerpt)
                <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ $post->excerpt }}</p>
                @endif

                <!-- Action Buttons -->
                <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('admin.posts.show', $post) }}" 
                           class="text-sm font-medium text-indigo-600 hover:text-indigo-800 flex items-center">
                            <i class="far fa-eye mr-1"></i> Preview
                        </a>
                        <a href="{{ route('admin.posts.edit', $post) }}" 
                           class="text-sm font-medium text-gray-600 hover:text-gray-800 flex items-center">
                            <i class="fas fa-edit mr-1"></i> Edit
                        </a>
                    </div>
                    <form action="{{ route('admin.posts.destroy', $post) }}" method="POST" 
                          onsubmit="return confirm('Are you sure you want to delete this post?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="text-red-600 hover:text-red-800 transition-colors">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        {{ $posts->links() }}
    </div>
    @else
    <!-- Empty State -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
        <i class="fas fa-newspaper text-gray-300 text-6xl mb-4"></i>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No posts found</h3>
        <p class="text-gray-500 mb-6">
            @if(request()->has('search') || request()->has('status') || request()->has('category'))
                Try adjusting your filters to see more results.
            @else
                Get started by creating your first blog post.
            @endif
        </p>
        <a href="{{ route('admin.posts.create') }}" 
           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
            <i class="fas fa-plus mr-2"></i>
            Create New Post
        </a>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    // Auto-submit on filter change (optional)
    document.querySelectorAll('select[name="status"], select[name="category"]').forEach(select => {
        select.addEventListener('change', function() {
            // Optionally auto-submit on change
        });
    });
</script>
@endsection
