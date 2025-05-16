<?php

namespace App\Http\Controllers;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;


class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with('categories')->latest()->paginate(10);
        return view('admin.posts.index', compact('posts'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.posts.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'excerpt' => 'nullable|max:500',
            'content' => 'required',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'featured_image' => 'nullable|image|max:2048',
            'status' => 'required|in:draft,published',
        ]);

        $post = new Post();
        $post->title = $validated['title'];
        $post->slug = Str::slug($validated['title']);
        $post->excerpt = $validated['excerpt'];
        $post->content = $validated['content'];
        $post->status = $validated['status'];
        $post->user_id = Auth::user()->id;

        if ($request->hasFile('featured_image')) {
            $post->featured_image = $request->file('featured_image')->store('posts', 'public');
        }

        if ($validated['status'] === 'published') {
            $post->published_at = now();
        }

        $post->save();

        if (isset($validated['categories'])) {
            $post->categories()->sync($validated['categories']);
        }

        return redirect()->route('admin.posts.index')->with('success', 'Post created successfully!');
    }

    public function edit(Post $post)
    {
        $categories = Category::all();
        $selectedCategories = $post->categories->pluck('id')->toArray();
        return view('admin.posts.edit', compact('post', 'categories', 'selectedCategories'));
    }

    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'excerpt' => 'nullable|max:500',
            'content' => 'required',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'featured_image' => 'nullable|image|max:2048',
            'status' => 'required|in:draft,published',
            'remove_image' => 'nullable|boolean',
        ]);

        $post->title = $validated['title'];
        $post->slug = Str::slug($validated['title']);
        $post->excerpt = $validated['excerpt'];
        $post->content = $validated['content'];

        if ($post->status !== $validated['status']) {
            $post->status = $validated['status'];
            if ($validated['status'] === 'published' && !$post->published_at) {
                $post->published_at = now();
            }
        }

        if ($request->hasFile('featured_image')) {
            // Delete old image if exists
            if ($post->featured_image) {
                Storage::disk('public')->delete($post->featured_image);
            }
            $post->featured_image = $request->file('featured_image')->store('posts', 'public');
        } elseif ($request->input('remove_image')) {
            Storage::disk('public')->delete($post->featured_image);
            $post->featured_image = null;
        }

        $post->save();

        $post->categories()->sync($validated['categories'] ?? []);

        return redirect()->route('admin.posts.index')->with('success', 'Post updated successfully!');
    }

    public function destroy(Post $post)
    {
        if ($post->featured_image) {
            Storage::disk('public')->delete($post->featured_image);
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

}
