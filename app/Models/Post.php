<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class Post extends Model
{
    protected $fillable = [
        'title', 'slug', 'excerpt', 'content', 
        'featured_image', 'status', 'published_at',
        'meta_title', 'meta_description', 'meta_keywords',
        'og_image', 'views', 'reading_time', 'is_featured', 'preview_token'
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_featured' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($post) {
            if (empty($post->slug)) {
                $post->slug = Str::slug($post->title);
            }
            if (empty($post->preview_token)) {
                $post->preview_token = Str::random(32);
            }
            // Calculate reading time
            if (!empty($post->content)) {
                $post->reading_time = $post->calculateReadingTime($post->content);
            }
        });

        static::updating(function ($post) {
            if ($post->isDirty('content')) {
                $post->reading_time = $post->calculateReadingTime($post->content);
            }
        });

        static::saved(function ($post) {
            Cache::forget("post.{$post->id}");
            Cache::forget("post.slug.{$post->slug}");
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function revisions()
    {
        return $this->hasMany(PostRevision::class)->latest();
    }

    public function postViews()
    {
        return $this->hasMany(PostView::class);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopePopular($query, $days = 30)
    {
        return $query->where('status', 'published')
            ->where('published_at', '>=', now()->subDays($days))
            ->orderBy('views', 'desc');
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
              ->orWhere('excerpt', 'like', "%{$term}%")
              ->orWhere('content', 'like', "%{$term}%");
        });
    }

    public function incrementViews()
    {
        $this->increment('views');
        
        // Track detailed view
        PostView::create([
            'post_id' => $this->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'referer' => request()->header('referer'),
            'viewed_at' => now(),
        ]);
    }

    public function getRelatedPosts($limit = 5)
    {
        return static::published()
            ->where('id', '!=', $this->id)
            ->where(function ($q) {
                $q->whereHas('categories', function ($query) {
                    $query->whereIn('categories.id', $this->categories->pluck('id'));
                })
                ->orWhereHas('tags', function ($query) {
                    $query->whereIn('tags.id', $this->tags->pluck('id'));
                });
            })
            ->limit($limit)
            ->get();
    }

    public function calculateReadingTime($content)
    {
        $wordCount = str_word_count(strip_tags($content));
        $readingTime = ceil($wordCount / 200); // Average reading speed: 200 words per minute
        return max(1, $readingTime); // Minimum 1 minute
    }

    public function getMetaTitleAttribute($value)
    {
        return $value ?: $this->title;
    }

    public function getMetaDescriptionAttribute($value)
    {
        return $value ?: $this->excerpt;
    }

    public function getOgImageAttribute($value)
    {
        return $value ?: $this->featured_image;
    }

    public function canBeViewedBy($user = null)
    {
        if ($this->status === 'published' && $this->published_at <= now()) {
            return true;
        }

        if ($user && $user->id === $this->user_id) {
            return true;
        }

        return false;
    }

    public function getPreviewUrl()
    {
        return route('posts.preview', ['token' => $this->preview_token]);
    }
}
