<?php

namespace App\Http\Controllers\TheArt;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Artwork;
use Inertia\Inertia;
use App\Models\PublicArtwork;

class ArtDisplayController extends Controller
{
    public function index()
    {
        $featured = Artwork::inRandomOrder()->limit(4)->with('artist')->get();

        $popular = Artwork::with('artist')
            ->orderBy('views', 'desc')
            ->limit(5)
            ->get();

        $mostSaved = Artwork::with('artist')
            ->withCount('saves')
            ->orderBy('saves_count', 'desc')
            ->limit(5)
            ->get();

        $publicArtworks = PublicArtwork::with('user')
            ->whereNotNull('user_id')
            ->latest()
            ->get();

        return Inertia::render('Home', [
            'title' => 'Home',
            'featuredArtworks' => $featured,
            'popularArtworks' => $popular,
            'mostSavedArtworks' => $mostSaved,
            'publicArtworks' => $publicArtworks,
        ]);
    }


    public function catalog()
    {
        $artworks = Artwork::with('artist')->paginate(12);

        if (Auth::check()) {
            $savedIds = Auth::user()->savedArtworks()->pluck('artwork_id')->toArray();

            // Add is_saved to each artwork
            $artworks->getCollection()->transform(function ($artwork) use ($savedIds) {
                $artwork->is_saved = in_array($artwork->id, $savedIds);
                return $artwork;
            });
        } else {
            // No user, default all to not saved
            $artworks->getCollection()->transform(function ($artwork) {
                $artwork->is_saved = false;
                return $artwork;
            });
        }

        return Inertia::render('ArtCatalog', [
            'title' => 'theArt Catalog',
            'headerImage' => '/images/catalogTop.jpg',
            'artworks' => $artworks,
        ]);
    }


    public function show($id)
    {
        $artwork = Artwork::with(['artist', 'style'])->findOrFail($id);
        $artwork->increment('views');

        // Add this block to reflect saved/liked status
        if (Auth::check()) {
            $user = Auth::user();
            $artwork->is_saved = $user->savedArtworks()->where('artwork_id', $id)->exists();
            $artwork->is_liked = $artwork->likes()->where('user_id', $user->id)->exists();
        } else {
            $artwork->is_saved = false;
            $artwork->is_liked = false;
        }

        return Inertia::render('ArtworkDetails', [
            'title' => $artwork->title,
            'headerImage' => '/images/catalogTop.jpg',
            'artwork' => $artwork,
            'relatedArtworks' => Artwork::with(['artist', 'style'])
                ->where('style_id', $artwork->style_id)
                ->where('id', '!=', $artwork->id)
                ->limit(6)
                ->get(),
        ]);
    }


    public function saved()
    {
        return Inertia::render('SavedCollection', [
            'title' => 'Saved theArt',
            'headerImage' => '/images/homeTop.jpg',
            'savedArtworks' => Auth::user()->savedArtworks()->with('artist')->get(),
            'showSearch' => true,
        ]);
    }

    public function about()
    {
        return Inertia::render('About', [
            'title' => 'About theArt',
            'headerImage' => '/images/aboutTop.jpg',
            'showSearch' => false,
        ]);
    }

    public function testYourself()
    {
        return Inertia::render('TestYourself', [
            'title' => 'Test Yourself with theArt',
            'headerImage' => '/images/homeTop.jpg',
            'showSearch' => false,
        ]);
    }
}