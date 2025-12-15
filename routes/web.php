<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\MediaController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    
    // Posts
    Route::get('/posts', [PostController::class, 'index'])->name('admin.posts.index');
    Route::get('/posts/create', [PostController::class, 'create'])->name('admin.posts.create');
    Route::post('/posts', [PostController::class, 'store'])->name('admin.posts.store');
    Route::post('/posts/bulk-action', [PostController::class, 'bulkAction'])->name('admin.posts.bulk-action');
    Route::get('/posts/api/{post}', [PostController::class, 'showApi'])->name('admin.posts.showapi');
    Route::get('/posts/{post}', [PostController::class, 'show'])->name('admin.posts.show');
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('admin.posts.edit');
    Route::put('/posts/{post}', [PostController::class, 'update'])->name('admin.posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('admin.posts.destroy');
    
    // Categories
    Route::get('/categories', [CategoryController::class, 'index'])->name('admin.categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('admin.categories.store');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('admin.categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('admin.categories.destroy');
    
    // Tags
    Route::get('/tags', [TagController::class, 'index'])->name('admin.tags.index');
    Route::post('/tags', [TagController::class, 'store'])->name('admin.tags.store');
    Route::put('/tags/{tag}', [TagController::class, 'update'])->name('admin.tags.update');
    Route::delete('/tags/{tag}', [TagController::class, 'destroy'])->name('admin.tags.destroy');
    Route::get('/tags/search', [TagController::class, 'search'])->name('admin.tags.search');
    
    // Media
    Route::get('/media', [MediaController::class, 'index'])->name('admin.media.index');
    Route::post('/media', [MediaController::class, 'store'])->name('admin.media.store');
    Route::delete('/media/{media}', [MediaController::class, 'destroy'])->name('admin.media.destroy');
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('admin.profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('admin.profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('admin.profile.destroy');
});

// Public preview route
Route::get('/posts/preview/{token}', function ($token) {
    $post = \App\Models\Post::where('preview_token', $token)->firstOrFail();
    return view('posts.preview', compact('post'));
})->name('posts.preview');

require __DIR__.'/auth.php';
