@extends('layouts.admin')

@section('title', 'Create Post')

@section('header')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Create New Post</h1>
        <p class="mt-1 text-xs sm:text-sm text-gray-500">Write and publish your blog post</p>
    </div>
    <a href="{{ route('admin.posts.index') }}" 
       class="inline-flex items-center justify-center px-3 sm:px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50">
        <i class="fas fa-arrow-left mr-2"></i>
        Back to Posts
    </a>
</div>
@endsection

@section('content')
<div x-data="{ activeTab: 'content' }" class="max-w-7xl mx-auto">
    <form action="{{ route('admin.posts.store') }}" method="POST" enctype="multipart/form-data" id="post-form">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content Area -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Post Content Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-4 sm:px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h2 class="text-base sm:text-lg font-semibold text-gray-900">Post Content</h2>
                    </div>
                    <div class="p-4 sm:p-6 space-y-4 sm:space-y-6">
                        <!-- Title -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                Post Title <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="title" 
                                   id="title" 
                                   required
                                   placeholder="Enter a compelling title..."
                                   class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                   value="{{ old('title') }}">
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Slug -->
                        <div>
                            <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">
                                URL Slug
                            </label>
                            <input type="text" 
                                   name="slug" 
                                   id="slug"
                                   readonly
                                   placeholder="Auto-generated from title"
                                   class="block w-full rounded-lg border-gray-300 shadow-sm bg-gray-100 text-gray-600 cursor-not-allowed sm:text-sm"
                                   value="{{ old('slug') }}">
                            <p class="mt-1 text-xs text-gray-500">Auto-generated from title</p>
                        </div>

                        <!-- Excerpt -->
                        <div>
                            <label for="excerpt" class="block text-sm font-medium text-gray-700 mb-2">
                                Excerpt
                            </label>
                            <textarea name="excerpt" 
                                      id="excerpt" 
                                      rows="3"
                                      placeholder="A brief summary of your post..."
                                      class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('excerpt') }}</textarea>
                            <p class="mt-1 text-xs text-gray-500">This will be used in post listings and meta descriptions</p>
                        </div>

                        <!-- Content Editor -->
                        <div>
                            <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                                Content <span class="text-red-500">*</span>
                            </label>
                            <textarea name="content" 
                                      id="editor" 
                                      rows="20"
                                      required
                                      class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('content') }}</textarea>
                            @error('content')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- SEO Settings Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-4 sm:px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h2 class="text-base sm:text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-search mr-2 text-indigo-600"></i>
                            SEO Settings
                        </h2>
                    </div>
                    <div class="p-4 sm:p-6 space-y-4">
                        <!-- Use Title/Excerpt Checkbox -->
                        <div class="flex items-center mb-4 pb-4 border-b border-gray-200">
                            <input type="checkbox" 
                                   id="use_title_excerpt" 
                                   checked
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="use_title_excerpt" class="ml-3 block text-sm font-medium text-gray-700">
                                Use title and excerpt as SEO meta details
                            </label>
                        </div>

                        <div>
                            <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-2">
                                Meta Title
                            </label>
                            <input type="text" 
                                   name="meta_title" 
                                   id="meta_title"
                                   readonly
                                   placeholder="Auto-generated from title"
                                   class="block w-full rounded-lg border-gray-300 shadow-sm bg-gray-100 text-gray-600 cursor-not-allowed sm:text-sm"
                                   value="{{ old('meta_title') }}">
                            <p class="mt-1 text-xs text-gray-500">Recommended: 50-60 characters</p>
                        </div>

                        <div>
                            <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-2">
                                Meta Description
                            </label>
                            <textarea name="meta_description" 
                                      id="meta_description" 
                                      rows="2"
                                      readonly
                                      placeholder="Auto-generated from excerpt"
                                      class="block w-full rounded-lg border-gray-300 shadow-sm bg-gray-100 text-gray-600 cursor-not-allowed sm:text-sm">{{ old('meta_description') }}</textarea>
                            <p class="mt-1 text-xs text-gray-500">Recommended: 150-160 characters</p>
                        </div>

                        <div>
                            <label for="meta_keywords" class="block text-sm font-medium text-gray-700 mb-2">
                                Meta Keywords
                            </label>
                            <input type="text" 
                                   name="meta_keywords" 
                                   id="meta_keywords"
                                   placeholder="keyword1, keyword2, keyword3"
                                   class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                   value="{{ old('meta_keywords') }}">
                        </div>

                        <div>
                            <label for="og_image" class="block text-sm font-medium text-gray-700 mb-2">
                                Open Graph Image
                            </label>
                            <input type="file" 
                                   name="og_image" 
                                   id="og_image"
                                   accept="image/*"
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            <p class="mt-1 text-xs text-gray-500">Recommended: 1200x630px for social media sharing</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Publish Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-4 sm:px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h2 class="text-base sm:text-lg font-semibold text-gray-900">Publish</h2>
                    </div>
                    <div class="p-4 sm:p-6 space-y-4">
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                Status
                            </label>
                            <select name="status" 
                                    id="status" 
                                    required
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="draft" {{ old('status', 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Published</option>
                            </select>
                        </div>

                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="is_featured" 
                                       id="is_featured"
                                       value="1"
                                       {{ old('is_featured') ? 'checked' : '' }}
                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                <span class="ml-3 block text-sm font-medium text-gray-700">
                                    Mark as Featured Post
                                </span>
                            </label>
                            <p class="mt-1 text-xs text-gray-500">Featured posts will be highlighted in listings</p>
                        </div>

                        <div class="pt-4 border-t border-gray-200 space-y-2">
                            <button type="submit" 
                                    class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <i class="fas fa-save mr-2"></i>
                                Save Post
                            </button>
                            <a href="{{ route('admin.posts.index') }}" 
                               class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50">
                                Cancel
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Featured Image Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-4 sm:px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h2 class="text-base sm:text-lg font-semibold text-gray-900">Featured Image</h2>
                    </div>
                    <div class="p-4 sm:p-6">
                        <div class="mb-4">
                            <input type="file" 
                                   name="featured_image" 
                                   id="featured_image"
                                   accept="image/*"
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        </div>
                        <div id="image-preview" class="hidden mt-4">
                            <img id="preview-img" src="" alt="Preview" class="w-full rounded-lg border border-gray-200">
                        </div>
                    </div>
                </div>

                <!-- Categories Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-4 sm:px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h2 class="text-base sm:text-lg font-semibold text-gray-900">Categories</h2>
                    </div>
                    <div class="p-4 sm:p-6">
                        <div class="space-y-2 max-h-64 overflow-y-auto">
                            @foreach($categories as $category)
                            <label class="flex items-center p-2 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" 
                                       name="categories[]" 
                                       value="{{ $category->id }}"
                                       class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="ml-3 text-sm text-gray-700">{{ $category->name }}</span>
                            </label>
                            @endforeach
                        </div>
                        <a href="{{ route('admin.categories.index') }}" 
                           class="mt-4 inline-flex items-center text-sm text-indigo-600 hover:text-indigo-800">
                            <i class="fas fa-plus mr-1"></i>
                            Add Category
                        </a>
                    </div>
                </div>

                <!-- Tags Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-4 sm:px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h2 class="text-base sm:text-lg font-semibold text-gray-900">Tags</h2>
                    </div>
                    <div class="p-4 sm:p-6">
                        <div id="tags-container" class="flex flex-wrap gap-2 mb-3 min-h-[40px] p-2 border border-gray-200 rounded-lg bg-gray-50"></div>
                        <input type="text" 
                               id="tag-input" 
                               placeholder="Type and press Enter"
                               class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <input type="hidden" name="tags" id="tags-input" value="">
                        <div id="tag-suggestions" class="mt-2 hidden bg-white border rounded-lg shadow-lg max-h-40 overflow-y-auto absolute z-10 w-full max-w-xs"></div>
                        <div class="mt-3">
                            <p class="text-xs text-gray-500 mb-2">Existing tags:</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach($tags as $tag)
                                <button type="button" 
                                        onclick="addExistingTag({{ $tag->id }}, '{{ $tag->name }}')" 
                                        class="text-xs px-2 py-1 bg-purple-50 text-purple-700 rounded hover:bg-purple-100 transition-colors">
                                    {{ $tag->name }}
                                </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    // Tags functionality
    const tagInput = document.getElementById('tag-input');
    const tagsContainer = document.getElementById('tags-container');
    const tagsInput = document.getElementById('tags-input');
    const tagSuggestions = document.getElementById('tag-suggestions');
    let selectedTags = [];

    // Initialize tags display
    if (tagsContainer.children.length === 0) {
        tagsContainer.innerHTML = '<p class="text-gray-400 text-sm w-full text-center">No tags added</p>';
    }

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
            tagsContainer.innerHTML = '<p class="text-gray-400 text-sm w-full text-center">No tags added</p>';
        } else {
            selectedTags.forEach(tag => {
                const tagEl = document.createElement('span');
                tagEl.className = 'inline-flex items-center px-3 py-1 rounded-full text-sm bg-purple-100 text-purple-800';
                tagEl.innerHTML = `${tag.name} <button type="button" onclick="removeTag('${tag.name}')" class="ml-2 text-purple-600 hover:text-purple-800">&times;</button>`;
                tagsContainer.appendChild(tagEl);
            });
        }
    }

    function updateTagsInput() {
        const tagIds = selectedTags.filter(t => t.id !== null).map(t => t.id);
        tagsInput.value = JSON.stringify(tagIds);
    }

    // Image preview
    document.getElementById('featured_image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview-img').src = e.target.result;
                document.getElementById('image-preview').classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }
    });

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
    titleInput.addEventListener('input', function(e) {
        const slug = generateSlug(e.target.value);
        slugInput.value = slug;
        
        // Update meta title if checkbox is checked
        if (useTitleExcerptCheckbox.checked) {
            metaTitleInput.value = e.target.value;
        }
    });

    // Update meta description when excerpt changes (if checkbox is checked)
    excerptInput.addEventListener('input', function(e) {
        if (useTitleExcerptCheckbox.checked) {
            metaDescriptionInput.value = e.target.value;
        }
    });

    // Toggle SEO fields based on checkbox
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
        metaTitleInput.value = titleInput.value;
        metaDescriptionInput.value = excerptInput.value;
    }

    // Initialize CKEditor
    ClassicEditor
        .create(document.querySelector('#editor'), {
            toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'insertTable', 'imageUpload', 'undo', 'redo']
        })
        .catch(error => {
            console.error(error);
        });
</script>
@endsection
