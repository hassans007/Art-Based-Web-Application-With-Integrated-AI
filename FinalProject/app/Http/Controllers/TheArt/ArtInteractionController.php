<?php
namespace App\Http\Controllers\TheArt;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Artwork;
use Illuminate\Http\Request;
use App\Models\Artist;
use App\Models\Style;

class ArtInteractionController extends Controller
{
    public function toggleSave(Request $request, $id)
    {
        $user = Auth::user();
        $alreadySaved = $user->savedArtworks()->where('artwork_id', $id)->exists();

        $alreadySaved
            ? $user->savedArtworks()->detach($id)
            : $user->savedArtworks()->attach($id);

        return response()->json(['saved' => !$alreadySaved]);
    }

    public function toggleLike(Request $request, $id)
    {
        $user = Auth::user();
        $artwork = Artwork::findOrFail($id);

        $liked = $artwork->likes()->where('user_id', $user->id)->exists();

        $liked
            ? $artwork->likes()->detach($user->id)
            : $artwork->likes()->attach($user->id);

        return response()->json([
            'liked' => !$liked,
            'likes_count' => $artwork->likes()->count(),
        ]);
    }

    public function getStyles()
    {
        return response()->json(
            Style::select('id', 'name')->orderBy('name')->get()
        );
    }

    public function getNationalities()
    {
        return response()->json(
            Artist::select('nationality')
                ->whereNotNull('nationality')
                ->distinct()
                ->pluck('nationality')
        );
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $styleId = $request->input('style_id');
        $timePeriod = $request->input('time_period');
        $artistId = $request->input('artist_id');
        $nationality = $request->input('nationality');
        $sortBy = $request->input('sort_by', 'newest');
        $type = $request->input('type', 'artwork'); 

        if ($type === 'artwork') {
            $artworkQuery = Artwork::query()->with('artist');

            if ($query) {
                $artworkQuery->where(function ($q) use ($query) {
                    $q->where('title', 'like', "%$query%")
                    ->orWhereHas('artist', function ($q2) use ($query) {
                        $q2->where('name', 'like', "%$query%");
                    });
                });
            }

            if ($styleId) {
                $artworkQuery->where('style_id', $styleId);
            }

            if ($timePeriod) {
                [$startYear, $endYear] = explode('-', $timePeriod);
                $artworkQuery->whereBetween('year_created', [$startYear, $endYear]);
            }

            if ($artistId) {
                $artworkQuery->where('artist_id', $artistId);
            }

            // Sorting
            if ($sortBy === 'newest') {
                $artworkQuery->orderByDesc('year_created');
            } elseif ($sortBy === 'oldest') {
                $artworkQuery->orderBy('year_created');
            } elseif ($sortBy === 'most_viewed') {
                $artworkQuery->orderByDesc('views');
            }

            $artworks = $artworkQuery->limit(10)->get();

            $results = $artworks->map(function ($artwork) {
                return [
                    'type' => 'artwork',
                    'id' => $artwork->id,
                    'title' => $artwork->title,
                    'image_path' => asset($artwork->image_path),
                    'artist' => [
                        'name' => $artwork->artist->name ?? 'Unknown',
                    ],
                ];
            });

        } elseif ($type === 'artist') {
            $artistQuery = Artist::query();

            if ($query) {
                $artistQuery->where('name', 'like', "%$query%");
            }

            if ($nationality) {
                $artistQuery->where('nationality', 'like', "%$nationality%");
            }

            if ($timePeriod) {
                [$startYear, $endYear] = explode('-', $timePeriod);
                $artistQuery->whereBetween('birth_year', [$startYear, $endYear]);
            }

            $artists = $artistQuery->limit(10)->get();

            $results = $artists->map(function ($artist) {
                return [
                    'type' => 'artist',
                    'id' => $artist->id,
                    'name' => $artist->name,
                    'profile_image' => asset($artist->profile_image),
                    'birth_year' => $artist->birth_year,
                ];
            });
        } else {
            $results = collect(); 
        }

        return response()->json($results);
    }





}
