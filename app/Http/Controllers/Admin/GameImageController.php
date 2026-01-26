<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GameImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            $path = $request->file('image')->store('photo', 'public');
            
            // Delete old image if it exists
            if ($game->image_path && Storage::disk('public')->exists($game->image_path)) {
                 Storage::disk('public')->delete($game->image_path);
            }
            
            $game->image_path = $path;
            $game->save();
        }

        return redirect()->route('admin.game-images.index')->with('success', 'Game image updated successfully.');
    }
}
