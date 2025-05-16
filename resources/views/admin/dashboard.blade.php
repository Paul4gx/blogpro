<!-- resources/views/admin/dashboard.blade.php -->
@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6 py-12">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-[#011733]-100 text-indigo-600">
                    <i class="fas fa-newspaper text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-gray-500 text-sm font-medium">Total Posts</h3>
                    <p class="text-2xl font-bold">{{ $postCount }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-gray-500 text-sm font-medium">Published</h3>
                    <p class="text-2xl font-bold">{{ $publishedCount }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fas fa-edit text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-gray-500 text-sm font-medium">Drafts</h3>
                    <p class="text-2xl font-bold">{{ $draftCount }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-tags text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-gray-500 text-sm font-medium">Categories</h3>
                    <p class="text-2xl font-bold">{{ $categoryCount }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Posts -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Recent Posts</h2>
        </div>
        <div class="divide-y divide-gray-200">
            @foreach($recentPosts as $post)
            <div class="px-6 py-4 hover:bg-gray-50">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-medium text-gray-800">{{ $post->title }}</h3>
                        <p class="text-sm text-gray-500 mt-1">
                            {{ $post->created_at->diffForHumans() }} • 
                            <span class="capitalize">{{ $post->status }}</span>
                        </p>
                    </div>
                    <div class="flex space-x-2">
                        <span class="px-2 py-1 text-xs rounded-full 
                            @if($post->status === 'published') bg-green-100 text-green-800 
                            @else bg-yellow-100 text-yellow-800 @endif">
                            {{ ucfirst($post->status) }}
                        </span>
                        <a href="{{ route('admin.posts.edit', $post) }}" class="text-indigo-600 hover:text-indigo-900">
                            <i class="fas fa-edit"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="px-6 py-4 bg-gray-50 text-right">
            <a href="{{ route('admin.posts.index') }}" class="text-indigo-600 hover:text-indigo-900 font-medium">
                View all posts
            </a>
        </div>
    </div>
</div>
@endsection