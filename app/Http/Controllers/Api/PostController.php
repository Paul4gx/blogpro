<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $cacheKey = "api.posts.index.{$perPage}." . md5(json_encode($request->all()));

        $posts = Cache::remember($cacheKey, 3600, function () use ($request, $perPage) {
            $query = Post::published()
                ->select('id', 'title', 'excerpt', 'featured_image', 'slug', 'published_at', 'reading_time', 'views')
                ->with(['categories:id,name,slug', 'tags:id,name,slug', 'user:id,name']);

            // Search
            if ($request->filled('search')) {
                $query->search($request->search);
            }

            // Filter by category
            if ($request->filled('category')) {
                $query->whereHas('categories', function ($q) use ($request) {
                    $q->where('categories.slug', $request->category);
                });
            }

            // Filter by tag
            if ($request->filled('tag')) {
                $query->whereHas('tags', function ($q) use ($request) {
                    $q->where('tags.slug', $request->tag);
                });
            }

            return $query->latest('published_at')->paginate($perPage);
        });

        // Increment views for each post (async)
        foreach ($posts->items() as $post) {
            $post->incrementViews();
        }

        return response()->json([
            'data' => $posts->map(function ($post) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'excerpt' => $post->excerpt,
                    'featured_image' => $post->featured_image ? asset('storage/' . $post->featured_image) : null,
                    'slug' => $post->slug,
                    'reading_time' => $post->reading_time,
                    'views' => $post->views,
                    'published_at' => [
                        'raw' => $post->published_at,
                        'relative' => $post->published_at->diffForHumans(),
                        'formatted' => $post->published_at->format('M j, Y')
                    ],
                    'author' => [
                        'id' => $post->user->id,
                        'name' => $post->user->name,
                    ],
                    'categories' => $post->categories->map(function ($category) {
                        return [
                            'id' => $category->id,
                            'name' => $category->name,
                            'slug' => $category->slug
                        ];
                    }),
                    'tags' => $post->tags->map(function ($tag) {
                        return [
                            'id' => $tag->id,
                            'name' => $tag->name,
                            'slug' => $tag->slug
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
        $post = Cache::remember("api.post.{$slug}", 3600, function () use ($slug) {
            return Post::published()
                ->with(['categories', 'tags', 'user'])
                ->where('slug', $slug)
                ->firstOrFail();
        });

        $post->incrementViews();

        $relatedPosts = $post->getRelatedPosts(5);

        return response()->json([
            'data' => [
                'id' => $post->id,
                'title' => $post->title,
                'slug' => $post->slug,
                'excerpt' => $post->excerpt,
                'content' => $post->content,
                'featured_image' => $post->featured_image ? asset('storage/' . $post->featured_image) : null,
                'reading_time' => $post->reading_time,
                'views' => $post->views,
                'published_at' => [
                    'raw' => $post->published_at,
                    'relative' => $post->published_at->diffForHumans(),
                    'formatted' => $post->published_at->format('F j, Y')
                ],
                'meta' => [
                    'title' => $post->meta_title,
                    'description' => $post->meta_description,
                    'keywords' => $post->meta_keywords,
                    'og_image' => $post->og_image ? asset('storage/' . $post->og_image) : null,
                ],
                'author' => [
                    'id' => $post->user->id,
                    'name' => $post->user->name,
                ],
                'categories' => $post->categories,
                'tags' => $post->tags,
                'related_posts' => $relatedPosts->map(function ($related) {
                    return [
                        'id' => $related->id,
                        'title' => $related->title,
                        'slug' => $related->slug,
                        'excerpt' => $related->excerpt,
                        'featured_image' => $related->featured_image ? asset('storage/' . $related->featured_image) : null,
                    ];
                }),
            ]
        ]);
    }

    public function categories()
    {
        $categories = Cache::remember('api.categories', 3600, function () {
            return Category::has('posts')
                ->withCount('posts')
                ->orderBy('name')
                ->get();
        });

        return response()->json([
            'data' => $categories
        ]);
    }

    public function tags()
    {
        $tags = Cache::remember('api.tags', 3600, function () {
            return Tag::has('posts')
                ->withCount('posts')
                ->orderBy('name')
                ->get();
        });

        return response()->json([
            'data' => $tags
        ]);
    }

    public function postsByCategory($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        $posts = $category->posts()
            ->published()
            ->with(['categories', 'tags', 'user'])
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

    public function postsByTag($slug)
    {
        $tag = Tag::where('slug', $slug)->firstOrFail();

        $posts = $tag->posts()
            ->published()
            ->with(['categories', 'tags', 'user'])
            ->latest('published_at')
            ->paginate(10);

        return response()->json([
            'data' => $posts->items(),
            'meta' => [
                'tag' => $tag->only(['id', 'name', 'slug']),
                'current_page' => $posts->currentPage(),
                'total_pages' => $posts->lastPage(),
                'per_page' => $posts->perPage(),
                'total_items' => $posts->total(),
            ]
        ]);
    }

    public function search(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2',
        ]);

        $posts = Post::published()
            ->search($request->q)
            ->with(['categories', 'tags', 'user'])
            ->latest('published_at')
            ->paginate(10);

        return response()->json([
            'data' => $posts->items(),
            'meta' => [
                'query' => $request->q,
                'current_page' => $posts->currentPage(),
                'total_pages' => $posts->lastPage(),
                'per_page' => $posts->perPage(),
                'total_items' => $posts->total(),
            ]
        ]);
    }

    public function popular(Request $request)
    {
        $days = $request->get('days', 30);
        $limit = $request->get('limit', 10);

        $posts = Post::popular($days)
            ->with(['categories', 'tags'])
            ->limit($limit)
            ->get();

        return response()->json([
            'data' => $posts
        ]);
    }
}
