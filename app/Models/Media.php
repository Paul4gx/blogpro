<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    protected $fillable = [
        'user_id', 'name', 'file_name', 'mime_type', 'path',
        'disk', 'size', 'alt_text', 'caption', 'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getUrlAttribute()
    {
        return Storage::disk($this->disk)->url($this->path);
    }

    public function getThumbnailUrlAttribute()
    {
        if (str_starts_with($this->mime_type, 'image/')) {
            // You can implement thumbnail generation here
            return $this->url;
        }
        return null;
    }

    public function isImage()
    {
        return str_starts_with($this->mime_type, 'image/');
    }
}
