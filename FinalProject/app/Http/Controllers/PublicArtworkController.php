<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PublicArtwork;
use App\Models\Style;
use App\Models\Artist;
use App\Models\PublicArtworkComment;
use App\Models\User;
use App\Models\Artwork;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class PublicArtworkController extends Controller
{
    public function community()
    {
        return Inertia::render('Community', [
            'title' => 'Community Uploads',
            'uploads' => PublicArtwork::with(['user', 'style', 'likes', 'comments.user'])
                ->latest()->get(),
        ]);        
    }

    public function toggleLike($id)
    {
        $artwork = PublicArtwork::findOrFail($id);
        $user = Auth::user();

        $liked = $artwork->likes()->where('user_id', $user->id)->exists();

        if ($liked) {
            $artwork->likes()->detach($user->id);
        } else {
            $artwork->likes()->attach($user->id);
        }

        return response()->json([
            'liked' => !$liked,
            'likes_count' => $artwork->likes()->count(),
        ]);
    }
    public function addComment(Request $request, $id)
    {
        $request->validate(['content' => 'required|string|max:1000']);

        $comment = PublicArtworkComment::create([
            'user_id' => Auth::check() ? Auth::id() : null,
            'public_artwork_id' => $id,
            'content' => $request->content,
        ]);

        return response()->json([
            'message' => 'Comment added successfully',
            'comment' => $comment->load('user'),
        ]);
    }

    public function deleteComment($id)
    {
        $comment = PublicArtworkComment::findOrFail($id);
        $user = Auth::user();

        // Allow user to delete their own comment or admin to delete any
        if ($comment->user_id === $user->id || $user->is_admin) {
            $comment->delete();
            return response()->json(['message' => 'Comment deleted']);
        }

        return response()->json(['message' => 'Unauthorized'], 403);
    }

    public function show($id)
    {
        $artwork = PublicArtwork::with(['style'])->findOrFail($id);

        // Add this block to reflect saved/liked status
        if (Auth::check()) {
            $user = Auth::user();
            $artwork->is_saved = $user->savedArtworks()->where('artwork_id', $id)->exists();
            $artwork->is_liked = $artwork->likes()->where('user_id', $user->id)->exists();
        } else {
            $artwork->is_saved = false;
            $artwork->is_liked = false;
        }

        return Inertia::render('PublicArtworkDetails', [
            'title' => $artwork->title,
            'headerImage' => '/images/catalogTop.jpg',
            'artwork' => $artwork,
            'relatedArtworks' => Artwork::with(['artist','style'])
                ->where('style_id', $artwork->style_id)
                ->where('id', '!=', $artwork->id)
                ->limit(6)
                ->get(),
        ]);
    }
    public function upload()
    {
        return Inertia::render('UploadArt', [
            'title' => 'Upload Artwork',
            'styles' => Style::all(),
            'artists' => Artist::all(),
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
            'artist_name' => 'nullable|string|max:255',
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
            $artwork = PublicArtwork::findOrFail($validated['id']);

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('artworks', 'public');
                $validated['image_path'] = 'storage/' . $path;
            }

            $artwork->update([
                'title' => $validated['title'],
                'artist_id' => $validated['artist_id'] ?? $artwork->artist_id,
                'style_id' => $validated['style_id'],
                'artist_name' => $validated['artist_name'],
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

            PublicArtwork::create([
                'title' => $validated['title'],
                'artist_id' => $validated['artist_id'] ?? Auth::id(),
                'artist_name' => $validated['artist_name'],
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
        $artwork = PublicArtwork::findOrFail($id);

        if (!Auth::user()->is_admin && Auth::id() !== $artwork->user_id) {
            abort(403);
        }

        return Inertia::render('EditArt', [
            'title' => 'Edit Artwork',
            'artwork' => $artwork,
            'styles' => Style::all(),
        ]);
    }

    public function destroy($id)
    {
        $artwork = PublicArtwork::findOrFail($id);

        $user = Auth::user();
        if (! $user->is_admin && $artwork->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        if ($artwork->image_path) {
            Storage::disk('public')
                ->delete(str_replace('storage/', '', $artwork->image_path));
        }

        $artwork->delete();

        return response()->noContent();
    }

    public function approve($id)
    {
        $artwork = PublicArtwork::findOrFail($id);
        $artwork->is_approved = true;
        $artwork->save();

        return back()->with('success', 'Artwork approved.');
    }

    public function adminDashboard()
    {
        $pending = PublicArtwork::with(['user', 'style'])
            ->where('is_approved', false)
            ->latest()
            ->get();

        return Inertia::render('Admin/ManagePublicArtworks', [
            'title' => 'Moderate Artworks',
            'submissions' => $pending,
        ]);
    }
}
