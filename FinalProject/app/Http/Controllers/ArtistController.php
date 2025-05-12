<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Artist;
use Inertia\Inertia;


class ArtistController extends Controller
{

    function artistDetail($id)
    {
        $artist = Artist::with(['artworks.style'])->findOrFail($id);

        return Inertia::render('ArtistDetail', [
            'title' => "{$artist->name} - Artist Page",
            'headerImage' => '/images/catalogTop.jpg',
            'artist' => $artist,
        ]);
    }


    function artistCatalog()
    {
        $artists = Artist::paginate(12); 


        return Inertia::render('ArtistCatalog', [
            'headerImage' => '/images/catalogTop.jpg',
            'title' => 'Artist Catalog',
            'artists' => $artists,
        ]);
    }


}
