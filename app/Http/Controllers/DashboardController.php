<?php

namespace App\Http\Controllers;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $postCount = Post::count();
        $publishedCount = Post::published()->count();
        $draftCount = Post::draft()->count();
        $categoryCount = Category::count();
        $recentPosts = Post::latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'postCount', 'publishedCount', 
            'draftCount', 'categoryCount', 'recentPosts'
        ));
    }
}
