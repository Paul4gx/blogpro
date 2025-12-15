@extends('layouts.admin')

@section('title', 'Tags')

@section('header')
<div class="flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Tags</h1>
        <p class="mt-1 text-sm text-gray-500">Organize your content with tags</p>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Create Tag Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Create New Tag</h2>
        <form action="{{ route('admin.tags.store') }}" method="POST" class="flex gap-4">
            @csrf
            <div class="flex-1">
                <input type="text" 
                       name="name" 
                       required 
                       placeholder="Tag name"
                       class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>
            <div class="flex-1">
                <input type="text" 
                       name="description" 
                       placeholder="Description (optional)"
                       class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>
            <button type="submit" 
                    class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="fas fa-plus mr-2"></i>
                Add Tag
            </button>
        </form>
    </div>

    <!-- Tags Grid -->
    @if($tags->count() > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-900">All Tags ({{ $tags->total() }})</h2>
        </div>
        <div class="divide-y divide-gray-200">
            @foreach($tags as $tag)
            <div class="px-6 py-4 hover:bg-gray-50 transition-colors">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-3 mb-2">
                            <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm font-semibold">
                                #{{ $tag->name }}
                            </span>
                            <span class="text-sm text-gray-500">
                                {{ $tag->posts_count }} {{ Str::plural('post', $tag->posts_count) }}
                            </span>
                        </div>
                        @if($tag->description)
                        <p class="text-sm text-gray-600">{{ $tag->description }}</p>
                        @endif
                        <p class="text-xs text-gray-400 mt-1">Slug: {{ $tag->slug }}</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button onclick="editTag({{ $tag->id }}, '{{ $tag->name }}', '{{ $tag->description ?? '' }}')" 
                                class="p-2 text-gray-400 hover:text-indigo-600 transition-colors"
                                title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="{{ route('admin.tags.destroy', $tag) }}" method="POST" 
                              onsubmit="return confirm('Are you sure? This will remove the tag from all posts.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="p-2 text-gray-400 hover:text-red-600 transition-colors"
                                    title="Delete">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            {{ $tags->links() }}
        </div>
    </div>
    @else
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
        <i class="fas fa-tags text-gray-300 text-6xl mb-4"></i>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No tags yet</h3>
        <p class="text-gray-500">Create your first tag to get started.</p>
    </div>
    @endif
</div>

<!-- Edit Tag Modal -->
<div id="editTagModal" 
     x-data="{ open: false }" 
     x-show="open"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-md">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Edit Tag</h3>
        <form id="editTagForm" method="POST">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                    <input type="text" name="name" id="editTagName" required
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <input type="text" name="description" id="editTagDescription"
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
            </div>
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" 
                        @click="open = false"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function editTag(id, name, description) {
        const form = document.getElementById('editTagForm');
        form.action = `/admin/tags/${id}`;
        document.getElementById('editTagName').value = name;
        document.getElementById('editTagDescription').value = description || '';
        document.querySelector('#editTagModal').classList.remove('hidden');
        document.querySelector('#editTagModal').__x.$data.open = true;
    }
</script>
@endsection
