@extends('layouts.admin')

@section('title', 'Edit Post')

@section('content')
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-12 flex flex-wrap">
    <div class="w-full md:w-9/12 px-2 mb-4 md:mb-0 bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-800">Edit Post</h2>
    </div>
    <form action="{{ route('admin.posts.update', $post) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="p-6 space-y-6">
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                <input type="text" name="title" id="title" required value="{{ old('title', $post->title) }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>

            <div>
                <label for="excerpt" class="block text-sm font-medium text-gray-700">Excerpt</label>
                <textarea name="excerpt" id="excerpt" rows="3"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('excerpt', $post->excerpt) }}</textarea>
            </div>

            <div>
                <label for="content" class="block text-sm font-medium text-gray-700">Content</label>
                <textarea name="content" id="editor" rows="10"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('content', $post->content) }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Featured Image</label>
                    <div class="mt-1 flex items-center space-x-4">
                        @if($post->featured_image)
                        <div class="relative">
                            <img src="{{ asset('storage/' . $post->featured_image) }}" alt="Current featured image" class="w-32 h-32 object-cover rounded">
                            <div class="absolute top-0 right-0 mt-1 mr-1">
                                <input type="checkbox" name="remove_image" id="remove_image" value="1" 
                                    class="rounded text-red-600 focus:ring-red-500">
                                <label for="remove_image" class="text-xs text-red-600">Remove</label>
                            </div>
                        </div>
                        @endif
                        <input type="file" name="featured_image" id="featured_image" 
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-[#011733] file:text-[#037396FF] hover:file:bg-[#011733]">
                    </div>
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" id="status" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="draft" {{ $post->status === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ $post->status === 'published' ? 'selected' : '' }}>Published</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 py-2">Categories</label>
                <hr>
                <div class="mt-2 space-y-2">
                    @foreach($categories as $category)
                    <div class="flex items-center">
                        <input type="checkbox" name="categories[]" id="category-{{ $category->id }}" 
                            value="{{ $category->id }}" 
                            {{ in_array($category->id, $selectedCategories) ? 'checked' : '' }}
                            class="rounded text-indigo-600 focus:ring-indigo-500">
                        <label for="category-{{ $category->id }}" class="ml-2 text-sm text-gray-700">
                            {{ $category->name }}
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="px-6 py-4 bg-gray-50 text-right">
            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-[#011733] hover:bg-[#011733] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Update Post
            </button>
        </div>
    </form>
</div>

<!-- Categories Card -->
<div class="w-full md:w-3/12 px-2 bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-800">Manage Categories</h2>
    </div>
    <div class="p-6">
        <form action="{{ route('admin.categories.store') }}" method="POST" class="flex">
            @csrf
            <input type="text" name="name" required placeholder="New category name"
                class="flex-1 rounded-l-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-r-md text-white bg-[#011733] hover:bg-[#011733] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Add
            </button>
        </form>

        <div class="mt-4 space-y-2">
            @foreach($categories as $category)
            <div class="flex items-center justify-between p-2 hover:bg-gray-50 rounded border-b border-gray {{$loop->first?'border-t':'';}}">
                <span>{{ $category->name }}</span>
                <div class="flex space-x-2">
                    <button onclick="editCategory({{ $category->id }}, '{{ $category->name }}')" class="text-indigo-600 hover:text-indigo-900">
                        <i class="fas fa-edit"></i>
                    </button>
                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
</div>
<!-- Edit Category Modal -->
<div id="editCategoryModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Edit Category</h3>
        <form id="editCategoryForm" method="POST">
            @csrf
            @method('PUT')
            <input type="text" name="name" id="editCategoryName" required
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            <div class="mt-4 flex justify-end space-x-3">
                <button type="button" onclick="document.getElementById('editCategoryModal').classList.add('hidden')" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-[#011733] hover:bg-[#011733]">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Initialize CKEditor
    ClassicEditor
        .create(document.querySelector('#editor'), {
            toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'insertTable', 'undo', 'redo']
        })
        .catch(error => {
            console.error(error);
        });

    // Category edit modal
    function editCategory(id, name) {
        const form = document.getElementById('editCategoryForm');
        form.action = `/admin/categories/${id}`;
        document.getElementById('editCategoryName').value = name;
        document.getElementById('editCategoryModal').classList.remove('hidden');
    }
</script>
@endsection