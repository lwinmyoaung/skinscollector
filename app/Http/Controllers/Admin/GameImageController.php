<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GameImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class GameImageController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        $games = GameImage::all();
        return view('admin.game-images.index', compact('games'));
    }

    public function edit($id)
    {
        $game = GameImage::findOrFail($id);
        return view('admin.game-images.edit', compact('game'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $game = GameImage::findOrFail($id);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            
            // Option 1: Fast Storage Flow
            // Upload to adminimages/photo
            $path = $file->store('photo', 'adminimages');
            
            // Delete old image if it exists and is in the new storage
            if ($game->image_path && Storage::disk('adminimages')->exists($game->image_path)) {
                 Storage::disk('adminimages')->delete($game->image_path);
            }
            
            $game->image_path = $path;
            $game->save();
            
            // Clear cache to reflect changes immediately
            Cache::forget('global.game_images');
        }

        return redirect()->route('admin.game-images.index')->with('success', 'Game image updated successfully.');
    }
}
