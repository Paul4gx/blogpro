<?php

namespace App\Http\Controllers;

use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function index()
    {
        $media = Media::where('user_id', auth()->id())
            ->latest()
            ->paginate(24);

        return view('admin.media.index', compact('media'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
        ]);

        $file = $request->file('file');
        $path = $file->store('media', 'public');

        $media = Media::create([
            'user_id' => auth()->id(),
            'name' => $file->getClientOriginalName(),
            'file_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'path' => $path,
            'disk' => 'public',
            'size' => $file->getSize(),
        ]);

        return response()->json([
            'success' => true,
            'media' => $media,
            'url' => $media->url,
        ]);
    }

    public function destroy(Media $media)
    {
        if ($media->user_id !== auth()->id()) {
            abort(403);
        }

        Storage::disk($media->disk)->delete($media->path);
        $media->delete();

        return back()->with('success', 'Media deleted successfully!');
    }
}
