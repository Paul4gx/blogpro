<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PostController;

Route::prefix('v1')->group(function () {
    // Public endpoints (no auth required)
    Route::get('/posts', [PostController::class, 'index']);
    Route::get('/posts/{slug}', [PostController::class, 'show']);
    Route::get('/categories', [PostController::class, 'categories']);
    Route::get('/categories/{slug}/posts', [PostController::class, 'postsByCategory']);
});