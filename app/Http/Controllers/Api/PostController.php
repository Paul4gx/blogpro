<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Category;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::published()
            ->select('id', 'title', 'excerpt', 'featured_image', 'slug')
            ->with(['categories:id,name,slug', 'user:id,name'])
            ->latest('published_at')
            ->paginate(10);

        return response()->json([
            'data' => $posts->map(function ($post) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'excerpt' => $post->excerpt,
                    'featured_image' => $post->featured_image_url,
                    'slug' => $post->slug,
                    'categories' => $post->categories->map(function ($category) {
                        return [
                            'id' => $category->id,
                            'name' => $category->name,
                            'slug' => $category->slug
                        ];
                    })
                ];
            }),
            'meta' => [
                'current_page' => $posts->currentPage(),
                'total_pages' => $posts->lastPage(),
                'per_page' => $posts->perPage(),
                'total_items' => $posts->total(),
            ]
        ]);
    }
    public function show($slug)
    {
        $post = Post::published()
            ->with(['categories', 'user'])
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json([
            'data' => $post
        ]);
    }

    public function categories()
    {
        $categories = Category::has('posts')
            ->withCount('posts')
            ->orderBy('name')
            ->get();

        return response()->json([
            'data' => $categories
        ]);
    }

    public function postsByCategory($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        $posts = $category->posts()
            ->published()
            ->with(['categories', 'user'])
            ->latest('published_at')
            ->paginate(10);

        return response()->json([
            'data' => $posts->items(),
            'meta' => [
                'category' => $category->only(['id', 'name', 'slug']),
                'current_page' => $posts->currentPage(),
                'total_pages' => $posts->lastPage(),
                'per_page' => $posts->perPage(),
                'total_items' => $posts->total(),
            ]
        ]);
    }
}