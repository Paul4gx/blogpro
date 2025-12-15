@extends('layouts.admin')

@section('title', 'Dashboard')

@section('header')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Dashboard</h1>
        <p class="mt-1 text-xs sm:text-sm text-gray-500">Welcome back, {{ Auth::user()->name }}!</p>
    </div>
    <a href="{{ route('admin.posts.create') }}" 
       class="inline-flex items-center justify-center px-3 sm:px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
        <i class="fas fa-plus mr-2"></i>
        New Post
    </a>
</div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 gap-4 sm:gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Total Posts -->
        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200 hover:shadow-md transition-shadow duration-200">
            <div class="p-4 sm:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-indigo-100 rounded-lg p-3">
                        <i class="fas fa-newspaper text-indigo-600 text-xl"></i>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-sm font-medium text-gray-500">Total Posts</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $postCount }}</p>
                        <p class="text-xs text-gray-500 mt-1">
                            <span class="text-green-600 font-medium">+{{ $publishedCount }}</span> published
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Published Posts -->
        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200 hover:shadow-md transition-shadow duration-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-100 rounded-lg p-3">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-sm font-medium text-gray-500">Published</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $publishedCount }}</p>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $postCount > 0 ? round(($publishedCount / $postCount) * 100) : 0 }}% of total
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Drafts -->
        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200 hover:shadow-md transition-shadow duration-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-yellow-100 rounded-lg p-3">
                        <i class="fas fa-edit text-yellow-600 text-xl"></i>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-sm font-medium text-gray-500">Drafts</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $draftCount }}</p>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $postCount > 0 ? round(($draftCount / $postCount) * 100) : 0 }}% of total
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Categories -->
        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200 hover:shadow-md transition-shadow duration-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-100 rounded-lg p-3">
                        <i class="fas fa-folder text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-sm font-medium text-gray-500">Categories</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $categoryCount }}</p>
                        <p class="text-xs text-gray-500 mt-1">Organized content</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Posts -->
        <div class="lg:col-span-2 bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Recent Posts</h2>
                    <a href="{{ route('admin.posts.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                        View all
                        <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($recentPosts as $post)
                <div class="px-6 py-4 hover:bg-gray-50 transition-colors duration-150">
                    <div class="flex items-start justify-between">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center space-x-2 mb-2">
                                <h3 class="text-base font-semibold text-gray-900 truncate">
                                    <a href="{{ route('admin.posts.edit', $post) }}" class="hover:text-indigo-600">
                                        {{ $post->title }}
                                    </a>
                                </h3>
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                    {{ $post->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ ucfirst($post->status) }}
                                </span>
                            </div>
                            <div class="flex items-center space-x-4 text-sm text-gray-500">
                                <span class="flex items-center">
                                    <i class="far fa-calendar-alt mr-1"></i>
                                    {{ $post->created_at->format('M d, Y') }}
                                </span>
                                @if($post->reading_time)
                                <span class="flex items-center">
                                    <i class="far fa-clock mr-1"></i>
                                    {{ $post->reading_time }} min read
                                </span>
                                @endif
                                @if($post->views > 0)
                                <span class="flex items-center">
                                    <i class="far fa-eye mr-1"></i>
                                    {{ $post->views }} views
                                </span>
                                @endif
                            </div>
                            @if($post->categories->count() > 0)
                            <div class="mt-2 flex flex-wrap gap-2">
                                @foreach($post->categories->take(3) as $category)
                                <span class="px-2 py-0.5 bg-blue-50 text-blue-700 rounded text-xs font-medium">
                                    {{ $category->name }}
                                </span>
                                @endforeach
                            </div>
                            @endif
                        </div>
                        <div class="ml-4 flex items-center space-x-2">
                            <a href="{{ route('admin.posts.edit', $post) }}" 
                               class="p-2 text-gray-400 hover:text-indigo-600 transition-colors"
                               title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="{{ route('admin.posts.show', $post) }}" 
                               class="p-2 text-gray-400 hover:text-gray-600 transition-colors"
                               title="View">
                                <i class="far fa-eye"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="px-6 py-12 text-center">
                    <i class="fas fa-newspaper text-gray-300 text-4xl mb-4"></i>
                    <p class="text-gray-500">No posts yet</p>
                    <a href="{{ route('admin.posts.create') }}" class="mt-4 inline-flex items-center text-indigo-600 hover:text-indigo-800">
                        Create your first post
                        <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Quick Actions & Stats -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-900">Quick Actions</h2>
                </div>
                <div class="p-6 space-y-3">
                    <a href="{{ route('admin.posts.create') }}" 
                       class="flex items-center justify-between p-3 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-colors group">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-indigo-600 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-plus text-white"></i>
                            </div>
                            <span class="font-medium text-gray-900">New Post</span>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400 group-hover:text-indigo-600"></i>
                    </a>
                    <a href="{{ route('admin.categories.index') }}" 
                       class="flex items-center justify-between p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors group">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-folder text-white"></i>
                            </div>
                            <span class="font-medium text-gray-900">Manage Categories</span>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400 group-hover:text-blue-600"></i>
                    </a>
                    <a href="{{ route('admin.tags.index') }}" 
                       class="flex items-center justify-between p-3 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors group">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-purple-600 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-tags text-white"></i>
                            </div>
                            <span class="font-medium text-gray-900">Manage Tags</span>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400 group-hover:text-purple-600"></i>
                    </a>
                    <a href="{{ route('admin.media.index') }}" 
                       class="flex items-center justify-between p-3 bg-green-50 rounded-lg hover:bg-green-100 transition-colors group">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-green-600 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-images text-white"></i>
                            </div>
                            <span class="font-medium text-gray-900">Media Library</span>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400 group-hover:text-green-600"></i>
                    </a>
                </div>
            </div>

            <!-- System Info -->
            <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl p-6 text-white shadow-lg">
                <h3 class="text-lg font-semibold mb-4">System Status</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-indigo-100">Last Updated</span>
                        <span class="font-medium">{{ now()->format('M d, Y') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-indigo-100">Total Content</span>
                        <span class="font-medium">{{ $postCount }} posts</span>
                    </div>
                    <div class="pt-3 border-t border-indigo-400">
                        <a href="{{ route('admin.posts.index') }}" 
                           class="block text-center py-2 bg-white bg-opacity-20 hover:bg-opacity-30 rounded-lg transition-all font-medium">
                            Manage Content
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
