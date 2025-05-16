<!-- resources/views/admin/posts/preview.blade.php -->
@extends('layouts.admin')

@section('title', 'Post Preview')

@section('content')
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6 py-12">
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Preview: {{ $post->title }}</h2>
            <div class="flex items-center mt-1 space-x-4">
                <span class="px-2 py-1 text-xs rounded-full 
                    @if($post->status === 'published') bg-green-100 text-green-800 
                    @else bg-yellow-100 text-yellow-800 @endif">
                    {{ ucfirst($post->status) }}
                </span>
                <span class="text-sm text-gray-500">
                    Last updated: {{ $post->updated_at->format('M d, Y \a\t h:i A') }}
                </span>
            </div>
        </div>
        <div>
            <a href="{{ route('admin.posts.edit', $post) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-[#011733]-600 hover:bg-[#011733]-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="fas fa-edit mr-2"></i> Edit Post
            </a>
        </div>
    </div>

    <div class="p-6">
        @if($post->featured_image)
        <div class="mb-6">
            <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}" class="h-64 rounded-lg shadow">
        </div>
        @endif

        <div class="prose max-w-none">
            <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $post->title }}</h1>
            
            @if($post->excerpt)
            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                <p class="text-blue-700">{{ $post->excerpt }}</p>
            </div>
            @endif

            <div class="text-gray-700">
                {!! $post->content !!}
            </div>
        </div>

        @if($post->categories->count() > 0)
        <div class="mt-8 pt-6 border-t border-gray-200">
            <h3 class="text-sm font-medium text-gray-500">Categories</h3>
            <div class="mt-2 flex flex-wrap gap-2">
                @foreach($post->categories as $category)
                <span class="px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                    {{ $category->name }}
                </span>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between items-center">
        <div>
            <p class="text-sm text-gray-500">
                Created by: {{ $post->user->name }} on {{ $post->created_at->format('M d, Y') }}
            </p>
            @if($post->published_at)
            <p class="text-sm text-gray-500 mt-1">
                Published on: {{ $post->published_at->format('M d, Y \a\t h:i A') }}
            </p>
            @endif
        </div>
        <div>
            <a href="{{ route('admin.posts.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="fas fa-arrow-left mr-2"></i> Back to Posts
            </a>
        </div>
    </div>
</div>
</div>
@endsection

@section('scripts')
<!-- Add any specific scripts needed for preview -->
@endsection