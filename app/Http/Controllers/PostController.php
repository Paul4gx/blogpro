<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use App\Models\PostRevision;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::with(['categories', 'tags', 'user'])->latest();

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->category);
            });
        }

        $posts = $query->paginate(10);

        $categories = Category::all();
        return view('admin.posts.index', compact('posts', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        $tags = Tag::all();
        return view('admin.posts.create', compact('categories', 'tags'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'slug' => 'nullable|unique:posts,slug',
            'excerpt' => 'nullable|max:500',
            'content' => 'required',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'featured_image' => 'nullable|image|max:2048',
            'og_image' => 'nullable|image|max:2048',
            'status' => 'required|in:draft,published',
            'meta_title' => 'nullable|max:255',
            'meta_description' => 'nullable|max:500',
            'meta_keywords' => 'nullable|max:255',
            'is_featured' => 'nullable|boolean',
        ]);

        $post = new Post();
        $post->title = $validated['title'];
        $post->slug = $validated['slug'] ?? Str::slug($validated['title']);
        $post->excerpt = $validated['excerpt'];
        $post->content = $validated['content'];
        $post->status = $validated['status'];
        $post->user_id = Auth::id();
        $post->meta_title = $validated['meta_title'] ?? null;
        $post->meta_description = $validated['meta_description'] ?? null;
        $post->meta_keywords = $validated['meta_keywords'] ?? null;
        $post->is_featured = $request->has('is_featured') ? (bool)$request->input('is_featured') : false;

        if ($request->hasFile('featured_image')) {
            $post->featured_image = $request->file('featured_image')->store('posts', 'public');
        }

        if ($request->hasFile('og_image')) {
            $post->og_image = $request->file('og_image')->store('posts/og', 'public');
        }

        if ($validated['status'] === 'published' && !$post->published_at) {
            $post->published_at = now();
        }

        $post->save();

        // Sync categories
        if (isset($validated['categories'])) {
            $post->categories()->sync($validated['categories']);
        }

        // Sync tags
        if (isset($validated['tags'])) {
            $post->tags()->sync($validated['tags']);
        }

        // Create initial revision
        $this->createRevision($post);

        return redirect()->route('admin.posts.index')->with('success', 'Post created successfully!');
    }

    public function edit(Post $post)
    {
        $categories = Category::all();
        $tags = Tag::all();
        $selectedCategories = $post->categories->pluck('id')->toArray();
        $selectedTags = $post->tags->pluck('id')->toArray();
        $revisions = $post->revisions()->with('user')->latest()->take(10)->get();

        return view('admin.posts.edit', compact(
            'post', 'categories', 'tags', 
            'selectedCategories', 'selectedTags', 'revisions'
        ));
    }

    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'slug' => 'nullable|unique:posts,slug,' . $post->id,
            'excerpt' => 'nullable|max:500',
            'content' => 'required',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'featured_image' => 'nullable|image|max:2048',
            'og_image' => 'nullable|image|max:2048',
            'status' => 'required|in:draft,published',
            'meta_title' => 'nullable|max:255',
            'meta_description' => 'nullable|max:500',
            'meta_keywords' => 'nullable|max:255',
            'is_featured' => 'nullable|boolean',
            'remove_image' => 'nullable|boolean',
            'remove_og_image' => 'nullable|boolean',
        ]);

        $post->title = $validated['title'];
        $post->slug = $validated['slug'] ?? Str::slug($validated['title']);
        $post->excerpt = $validated['excerpt'];
        $post->content = $validated['content'];
        $post->meta_title = $validated['meta_title'] ?? null;
        $post->meta_description = $validated['meta_description'] ?? null;
        $post->meta_keywords = $validated['meta_keywords'] ?? null;
        $post->is_featured = $request->has('is_featured') ? (bool)$request->input('is_featured') : false;

        if ($post->status !== $validated['status']) {
            $post->status = $validated['status'];
            if ($validated['status'] === 'published' && !$post->published_at) {
                $post->published_at = now();
            }
        }

        if ($request->hasFile('featured_image')) {
            if ($post->featured_image) {
                Storage::disk('public')->delete($post->featured_image);
            }
            $post->featured_image = $request->file('featured_image')->store('posts', 'public');
        } elseif ($request->input('remove_image')) {
            Storage::disk('public')->delete($post->featured_image);
            $post->featured_image = null;
        }

        if ($request->hasFile('og_image')) {
            if ($post->og_image) {
                Storage::disk('public')->delete($post->og_image);
            }
            $post->og_image = $request->file('og_image')->store('posts/og', 'public');
        } elseif ($request->input('remove_og_image')) {
            Storage::disk('public')->delete($post->og_image);
            $post->og_image = null;
        }

        $post->save();

        $post->categories()->sync($validated['categories'] ?? []);
        $post->tags()->sync($validated['tags'] ?? []);

        // Create revision if content changed
        if ($post->wasChanged(['title', 'excerpt', 'content', 'status'])) {
            $this->createRevision($post);
        }

        return redirect()->route('admin.posts.index')->with('success', 'Post updated successfully!');
    }

    public function destroy(Post $post)
    {
        if ($post->featured_image) {
            Storage::disk('public')->delete($post->featured_image);
        }
        if ($post->og_image) {
            Storage::disk('public')->delete($post->og_image);
        }
        $post->delete();
        return back()->with('success', 'Post deleted successfully!');
    }

    public function show(Post $post)
    {
        if (request()->wantsJson()) {
            return response()->json([
                'title' => $post->title,
                'content' => $post->content
            ]);
        }
        return view('admin.posts.show', compact('post'));
    }

    public function showApi(String $post_id)
    {
        $post = Post::findOrFail($post_id);
        return response()->json([
            'title' => $post->title,
            'content' => $post->content
        ]);
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,publish,draft',
            'posts' => 'required|array',
            'posts.*' => 'exists:posts,id',
        ]);

        $posts = Post::whereIn('id', $request->posts)->get();

        foreach ($posts as $post) {
            if ($request->action === 'delete') {
                $post->delete();
            } elseif ($request->action === 'publish') {
                $post->update([
                    'status' => 'published',
                    'published_at' => now(),
                ]);
            } elseif ($request->action === 'draft') {
                $post->update(['status' => 'draft']);
            }
        }

        return back()->with('success', 'Bulk action completed successfully!');
    }

    protected function createRevision(Post $post)
    {
        PostRevision::create([
            'post_id' => $post->id,
            'user_id' => Auth::id(),
            'title' => $post->title,
            'excerpt' => $post->excerpt,
            'content' => $post->content,
            'status' => $post->status,
        ]);
    }
}
