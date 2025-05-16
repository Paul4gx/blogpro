<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;


// routes/web.php
// Route::prefix('admin')->middleware(['auth', 'verified'])->group(function () {
Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    // Posts
    Route::get('/posts', [PostController::class, 'index'])->name('admin.posts.index');
    Route::get('/posts/create', [PostController::class, 'create'])->name('admin.posts.create');
    Route::post('/posts', [PostController::class, 'store'])->name('admin.posts.store');
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
    //profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('admin.profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('admin.profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('admin.profile.destroy');
});
require __DIR__.'/auth.php';
