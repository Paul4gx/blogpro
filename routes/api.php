<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PostController;

Route::prefix('v1')->group(function () {
    // Public endpoints (no auth required)
    Route::get('/posts', [PostController::class, 'index']);
    Route::get('/posts/search', [PostController::class, 'search']);
    Route::get('/posts/popular', [PostController::class, 'popular']);
    Route::get('/posts/{slug}', [PostController::class, 'show']);
    Route::get('/categories', [PostController::class, 'categories']);
    Route::get('/categories/{slug}/posts', [PostController::class, 'postsByCategory']);
    Route::get('/tags', [PostController::class, 'tags']);
    Route::get('/tags/{slug}/posts', [PostController::class, 'postsByTag']);

    // Protected endpoints (require authentication)
    Route::middleware('auth:sanctum')->group(function () {
        // Add authenticated routes here if needed
    });
});
