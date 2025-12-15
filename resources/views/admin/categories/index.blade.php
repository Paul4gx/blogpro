@extends('layouts.admin')

@section('title', 'Categories')

@section('header')
<div class="flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Categories</h1>
        <p class="mt-1 text-sm text-gray-500">Organize your content with categories</p>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Create Category Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Create New Category</h2>
        <form action="{{ route('admin.categories.store') }}" method="POST" class="flex gap-4">
            @csrf
            <div class="flex-1">
                <input type="text" 
                       name="name" 
                       required 
                       placeholder="Category name"
                       class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('name') border-red-300 @enderror"
                       value="{{ old('name') }}">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit" 
                    class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                <i class="fas fa-plus mr-2"></i>
                Add Category
            </button>
        </form>
    </div>

    <!-- Categories List -->
    @if($categories->count() > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">All Categories ({{ $categories->total() }})</h2>
                @if($categories->hasPages())
                <span class="text-sm text-gray-500">Page {{ $categories->currentPage() }} of {{ $categories->lastPage() }}</span>
                @endif
            </div>
        </div>
        <div class="divide-y divide-gray-200">
            @foreach($categories as $category)
            <div class="px-6 py-4 hover:bg-gray-50 transition-colors">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-3 mb-2">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-folder text-blue-600"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-semibold text-gray-900">{{ $category->name }}</h3>
                                <p class="text-xs text-gray-500 mt-0.5">Slug: {{ $category->slug }}</p>
                            </div>
                        </div>
                        <div class="ml-13">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                                <i class="fas fa-newspaper mr-1"></i>
                                {{ $category->posts_count }} {{ Str::plural('post', $category->posts_count) }}
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button onclick="editCategory({{ $category->id }}, '{{ $category->name }}')" 
                                class="p-2 text-gray-400 hover:text-indigo-600 transition-colors rounded-lg hover:bg-indigo-50"
                                title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" 
                              onsubmit="return confirm('Are you sure? This will remove the category from all posts.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="p-2 text-gray-400 hover:text-red-600 transition-colors rounded-lg hover:bg-red-50"
                                    title="Delete">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @if($categories->hasPages())
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            {{ $categories->links() }}
        </div>
        @endif
    </div>
    @else
    <!-- Empty State -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
        <i class="fas fa-folder-open text-gray-300 text-6xl mb-4"></i>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No categories yet</h3>
        <p class="text-gray-500 mb-6">Create your first category to organize your content.</p>
    </div>
    @endif
</div>

<!-- Edit Category Modal -->
<div id="editCategoryModal" 
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
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Edit Category</h3>
        <form id="editCategoryForm" method="POST">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Category Name</label>
                <input type="text" 
                       name="name" 
                       id="editCategoryName" 
                       required
                       class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('name') border-red-300 @enderror">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" 
                        @click="open = false"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 transition-colors">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function editCategory(id, name) {
        const form = document.getElementById('editCategoryForm');
        form.action = `/admin/categories/${id}`;
        document.getElementById('editCategoryName').value = name;
        const modal = document.querySelector('#editCategoryModal');
        modal.classList.remove('hidden');
        // Trigger Alpine.js reactivity
        if (modal.__x) {
            modal.__x.$data.open = true;
        }
    }

    // Close modal when clicking outside
    document.getElementById('editCategoryModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
        }
    });
</script>
@endsection
