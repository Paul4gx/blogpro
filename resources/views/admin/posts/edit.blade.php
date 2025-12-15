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
                <label for="slug" class="block text-sm font-medium text-gray-700">URL Slug</label>
                <input type="text" 
                       name="slug" 
                       id="slug"
                       readonly
                       value="{{ old('slug', $post->slug) }}"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-100 text-gray-600 cursor-not-allowed sm:text-sm">
                <p class="mt-1 text-xs text-gray-500">Auto-generated from title</p>
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
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-[#011733] file:text-white hover:file:bg-[#011733]">
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

            <!-- Tags -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tags</label>
                <div id="tags-container" class="flex flex-wrap gap-2 mb-2 min-h-[40px] p-2 border border-gray-300 rounded-md"></div>
                <input type="text" id="tag-input" 
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    placeholder="Type tag name and press Enter to add">
                <input type="hidden" name="tags" id="tags-input" value="">
                <div id="tag-suggestions" class="mt-2 hidden bg-white border rounded-md shadow-lg max-h-40 overflow-y-auto absolute z-10 w-full"></div>
                <div class="mt-2">
                    <p class="text-xs text-gray-500">Existing tags:</p>
                    <div class="flex flex-wrap gap-2 mt-1">
                        @foreach($tags as $tag)
                        <button type="button" onclick="addExistingTag({{ $tag->id }}, '{{ $tag->name }}')" 
                            class="text-xs px-2 py-1 bg-gray-100 text-gray-700 rounded hover:bg-gray-200">
                            {{ $tag->name }}
                        </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- SEO Fields -->
            <div class="border-t pt-6 mt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">SEO Settings</h3>
                
                <div class="space-y-4">
                    <!-- Use Title/Excerpt Checkbox -->
                    <div class="flex items-center mb-4 pb-4 border-b border-gray-200">
                        @php
                            $metaTitle = old('meta_title', $post->meta_title);
                            $metaDescription = old('meta_description', $post->meta_description);
                            $useDefault = (!$metaTitle || $metaTitle == $post->title) && (!$metaDescription || $metaDescription == $post->excerpt);
                        @endphp
                        <input type="checkbox" 
                               id="use_title_excerpt" 
                               {{ $useDefault ? 'checked' : '' }}
                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="use_title_excerpt" class="ml-3 block text-sm font-medium text-gray-700">
                            Use title and excerpt as SEO meta details
                        </label>
                    </div>

                    <div>
                        <label for="meta_title" class="block text-sm font-medium text-gray-700">Meta Title</label>
                        <input type="text" 
                               name="meta_title" 
                               id="meta_title" 
                               value="{{ old('meta_title', $post->meta_title ?: $post->title) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                               placeholder="Leave empty to use post title">
                    </div>

                    <div>
                        <label for="meta_description" class="block text-sm font-medium text-gray-700">Meta Description</label>
                        <textarea name="meta_description" 
                                  id="meta_description" 
                                  rows="2"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                  placeholder="Leave empty to use excerpt">{{ old('meta_description', $post->meta_description ?: $post->excerpt) }}</textarea>
                    </div>

                    <div>
                        <label for="meta_keywords" class="block text-sm font-medium text-gray-700">Meta Keywords</label>
                        <input type="text" name="meta_keywords" id="meta_keywords" value="{{ old('meta_keywords', $post->meta_keywords) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            placeholder="keyword1, keyword2, keyword3">
                    </div>

                    <div>
                        <label for="og_image" class="block text-sm font-medium text-gray-700">Open Graph Image</label>
                        @if($post->og_image)
                        <div class="mt-1 mb-2">
                            <img src="{{ asset('storage/' . $post->og_image) }}" alt="OG Image" class="w-32 h-32 object-cover rounded">
                            <div class="mt-1">
                                <input type="checkbox" name="remove_og_image" id="remove_og_image" value="1" 
                                    class="rounded text-red-600 focus:ring-red-500">
                                <label for="remove_og_image" class="text-xs text-red-600 ml-1">Remove</label>
                            </div>
                        </div>
                        @endif
                        <input type="file" name="og_image" id="og_image" 
                            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-[#011733] file:text-white">
                        <p class="mt-1 text-xs text-gray-500">Recommended: 1200x630px</p>
                    </div>
                </div>
            </div>

            <!-- Featured Post -->
            <div class="border-t pt-6 mt-6">
                <label class="flex items-center">
                    <input type="checkbox" 
                           name="is_featured" 
                           id="is_featured"
                           value="1"
                           {{ old('is_featured', $post->is_featured) ? 'checked' : '' }}
                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <span class="ml-3 block text-sm font-medium text-gray-700">
                        Mark as Featured Post
                    </span>
                </label>
                <p class="mt-1 text-sm text-gray-500">Featured posts will be highlighted in listings</p>
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
    // Tags functionality
    const tagInput = document.getElementById('tag-input');
    const tagsContainer = document.getElementById('tags-container');
    const tagsInput = document.getElementById('tags-input');
    const tagSuggestions = document.getElementById('tag-suggestions');
    let selectedTags = @json($post->tags->map(function($tag) { return ['id' => $tag->id, 'name' => $tag->name]; }));

    tagInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const tagName = tagInput.value.trim();
            if (tagName && !selectedTags.find(t => t.name === tagName)) {
                addTag({id: null, name: tagName});
                tagInput.value = '';
                tagSuggestions.classList.add('hidden');
            }
        }
    });

    tagInput.addEventListener('input', function() {
        const query = this.value.trim();
        if (query.length > 1) {
            fetch(`/admin/tags/search?q=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(tags => {
                    if (tags.length > 0) {
                        tagSuggestions.innerHTML = tags.map(tag => 
                            `<div class="p-2 hover:bg-gray-100 cursor-pointer" onclick="addExistingTag(${tag.id}, '${tag.name}')">${tag.name}</div>`
                        ).join('');
                        tagSuggestions.classList.remove('hidden');
                    } else {
                        tagSuggestions.classList.add('hidden');
                    }
                });
        } else {
            tagSuggestions.classList.add('hidden');
        }
    });

    function addTag(tag) {
        if (!selectedTags.find(t => t.id === tag.id && t.name === tag.name)) {
            selectedTags.push(tag);
            renderTags();
            updateTagsInput();
        }
    }

    function addExistingTag(id, name) {
        addTag({id: id, name: name});
        tagInput.value = '';
        tagSuggestions.classList.add('hidden');
    }

    function removeTag(tagName) {
        selectedTags = selectedTags.filter(t => t.name !== tagName);
        renderTags();
        updateTagsInput();
    }

    function renderTags() {
        tagsContainer.innerHTML = '';
        if (selectedTags.length === 0) {
            tagsContainer.innerHTML = '<p class="text-gray-400 text-sm">No tags added</p>';
        } else {
            selectedTags.forEach(tag => {
                const tagEl = document.createElement('span');
                tagEl.className = 'inline-flex items-center px-3 py-1 rounded-full text-sm bg-indigo-100 text-indigo-800';
                tagEl.innerHTML = `${tag.name} <button type="button" onclick="removeTag('${tag.name}')" class="ml-2 text-indigo-600 hover:text-indigo-800">&times;</button>`;
                tagsContainer.appendChild(tagEl);
            });
        }
    }

    function updateTagsInput() {
        const tagIds = selectedTags.filter(t => t.id !== null).map(t => t.id);
        tagsInput.value = JSON.stringify(tagIds);
    }

    // Initialize tags display
    renderTags();

    // Auto-generate slug from title (always update)
    function generateSlug(text) {
        return text.toLowerCase()
            .replace(/[^\w\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .trim();
    }

    const titleInput = document.getElementById('title');
    const slugInput = document.getElementById('slug');
    const excerptInput = document.getElementById('excerpt');
    const metaTitleInput = document.getElementById('meta_title');
    const metaDescriptionInput = document.getElementById('meta_description');
    const useTitleExcerptCheckbox = document.getElementById('use_title_excerpt');

    // Update slug as user types in title
    if (titleInput && slugInput) {
        titleInput.addEventListener('input', function(e) {
            const slug = generateSlug(e.target.value);
            slugInput.value = slug;
            
            // Update meta title if checkbox is checked
            if (useTitleExcerptCheckbox && useTitleExcerptCheckbox.checked) {
                metaTitleInput.value = e.target.value;
            }
        });
    }

    // Update meta description when excerpt changes (if checkbox is checked)
    if (excerptInput && useTitleExcerptCheckbox) {
        excerptInput.addEventListener('input', function(e) {
            if (useTitleExcerptCheckbox.checked) {
                metaDescriptionInput.value = e.target.value;
            }
        });
    }

    // Toggle SEO fields based on checkbox
    if (useTitleExcerptCheckbox) {
        useTitleExcerptCheckbox.addEventListener('change', function(e) {
            const isChecked = e.target.checked;
            
            if (isChecked) {
                // Make fields readonly and auto-populate
                metaTitleInput.readOnly = true;
                metaDescriptionInput.readOnly = true;
                metaTitleInput.classList.add('bg-gray-100', 'text-gray-600', 'cursor-not-allowed');
                metaDescriptionInput.classList.add('bg-gray-100', 'text-gray-600', 'cursor-not-allowed');
                metaTitleInput.classList.remove('bg-white', 'text-gray-900');
                metaDescriptionInput.classList.remove('bg-white', 'text-gray-900');
                
                // Populate with current values
                metaTitleInput.value = titleInput.value;
                metaDescriptionInput.value = excerptInput.value;
            } else {
                // Make fields editable
                metaTitleInput.readOnly = false;
                metaDescriptionInput.readOnly = false;
                metaTitleInput.classList.remove('bg-gray-100', 'text-gray-600', 'cursor-not-allowed');
                metaDescriptionInput.classList.remove('bg-gray-100', 'text-gray-600', 'cursor-not-allowed');
                metaTitleInput.classList.add('bg-white', 'text-gray-900');
                metaDescriptionInput.classList.add('bg-white', 'text-gray-900');
            }
        });

        // Initialize on page load
        if (useTitleExcerptCheckbox.checked) {
            metaTitleInput.readOnly = true;
            metaDescriptionInput.readOnly = true;
            metaTitleInput.classList.add('bg-gray-100', 'text-gray-600', 'cursor-not-allowed');
            metaDescriptionInput.classList.add('bg-gray-100', 'text-gray-600', 'cursor-not-allowed');
            metaTitleInput.value = titleInput.value;
            metaDescriptionInput.value = excerptInput.value;
        }
    }

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