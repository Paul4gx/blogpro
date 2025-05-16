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
            ->with(['categories', 'user'])
            ->latest('published_at')
            ->paginate(10);

        return response()->json([
            'data' => $posts->items(),
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