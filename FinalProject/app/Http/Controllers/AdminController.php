<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Artwork;
use App\Models\Style;
use App\Models\User;
use App\Models\PublicArtwork;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function dashboard()
    {
        return Inertia::render('Admin/AdminDashboard', [
            'title' => 'Admin Dashboard',
            'stats' => [
                'totalArtworks' => Artwork::count(),
                'pendingPublic' => PublicArtwork::where('is_approved', false)->count(),
                'totalUsers' => User::count(),
            ],
            'mainArtworks' => Artwork::with('artist')->latest()->get(),
            'publicArtworks' => PublicArtwork::with('user')->latest()->get(),
        ]);
    }

    public function show($id)
    {
        $artwork = PublicArtwork::with('user', 'style', 'likes', 'comments.user')->findOrFail($id);
        $user = Auth::user();

        return Inertia::render('ArtworkDetails', [
            'title' => 'Artwork Details',
            'artwork' => $artwork,
            'is_liked' => $user ? $artwork->likes()->where('user_id', $user->id)->exists() : false,
            'is_saved' => $user ? $user->savedArtworks()->where('artwork_id', $id)->exists() : false,
        ]);
    }

    public function upload()
    {
        return Inertia::render('UploadArt', [
            'title' => 'Upload Main Artwork',
            'styles' => Style::all(),
            'role' => Auth::user()?->is_admin ? 'admin' : 'user',
        ]);
    }

    public function save(Request $request)
    {
        $validated = $request->validate([
            'id' => 'nullable|exists:artworks,id',
            'title' => 'required|string|max:255',
            'style_id' => 'required|string',
            'custom_style' => 'nullable|string|max:255',
            'description' => 'required|string|max:1000',
            'image' => 'nullable|image|max:2048',
            'custom_artist' => 'nullable|string|max:255',
            'artist_id' => 'nullable|string',
            'image_path' => 'nullable|string',
        ]);
    
        if ($validated['style_id'] === 'custom') {
            $validated['style_id'] = Style::firstOrCreate(
                ['name' => $validated['custom_style']],
                ['description' => 'Custom Style']
            )->id;
        }
    
        if (!empty($validated['artist_id']) && $validated['artist_id'] === 'custom') {
            $validated['artist_id'] = User::firstOrCreate(
                ['name' => $validated['custom_artist']],
                ['email' => uniqid() . '@placeholder.com', 'password' => bcrypt('password')]
            )->id;
        }
    
        if (!empty($validated['id'])) {
            $artwork = Artwork::findOrFail($validated['id']);
    
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('artworks', 'public');
                $validated['image_path'] = 'storage/' . $path;
            }
    
            $artwork->update([
                'title' => $validated['title'],
                'artist_id' => $validated['artist_id'] ?? $artwork->artist_id,
                'style_id' => $validated['style_id'],
                'description' => $validated['description'],
                'image_path' => $validated['image_path'] ?? $artwork->image_path,
            ]);
    
            return redirect($request->input('redirect_to', route('home')))
                ->with('success', 'Artwork updated successfully.');

        } else {
            if (!$request->hasFile('image')) {
                return redirect()->back()->withErrors(['image' => 'Image is required for new artworks.']);
            }
    
            $path = $request->file('image')->store('artworks', 'public');
    
            Artwork::create([
                'title' => $validated['title'],
                'artist_id' => $validated['artist_id'] ?? Auth::id(),
                'user_id' => Auth::id(),
                'year_created' => now()->year,
                'style_id' => $validated['style_id'],
                'description' => $validated['description'],
                'image_path' => 'storage/' . $path,
            ]);
    
            return redirect($request->input('redirect_to', route('home')))
            ->with('success', 'Artwork uploaded successfully.');        
        }
    }

    public function edit($id)
    {
        $artwork = Artwork::findOrFail($id);

        if (!Auth::user()->is_admin && Auth::id() !== $artwork->user_id) {
            abort(403);
        }

        return Inertia::render('EditArt', [
            'title' => 'Edit Artwork',
            'artwork' => $artwork,
            'styles' => Style::all(),
            'artists' =>Artist::all(),
        ]);
    }


    public function destroyMainArtwork($id)
    {
        $artwork = Artwork::findOrFail($id);
        $artwork->delete();

        return response()->noContent();
    }

}
