<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\URL;
use App\Models\Artwork;
use App\Models\User;

class ArtworkSeeder extends Seeder
{
    public function run(): void
    {
        $artworks = [
            [
                'title' => 'Starry Night',
                'artist_id' => 2,
                'year_created' => 1889,
                'style_id' => 1,
                'image_path' => URL::asset('images/artworks/starry.jpg'),
                'description' => 'A swirling night sky over a quiet town by Vincent van Gogh.',
            ],
            [
                'title' => 'Guernica',
                'artist_id' => 1,
                'year_created' => 1937,
                'style_id' => 2,
                'image_path' => URL::asset('images/artworks/PicassoGuernica.jpg'),
                'description' => 'A powerful anti-war painting by Pablo Picasso.',
            ],
            [
                'title' => 'Mona Lisa',
                'artist_id' => 3,
                'year_created' => 1503,
                'style_id' => 5,
                'image_path' => URL::asset('images/artworks/mona-lisa.jpg'),
                'description' => 'The most famous portrait in the world by Leonardo da Vinci.',
            ],
            [
                'title' => 'Water Lilies',
                'artist_id' => 4,
                'year_created' => 1906,
                'style_id' => 1,
                'image_path' => URL::asset('images/artworks/water-lilies.jpg'),
                'description' => 'One of Monet’s iconic reflections on light and water.',
            ],
            [
                'title' => 'The Persistence of Memory',
                'artist_id' => 5,
                'year_created' => 1931,
                'style_id' => 3,
                'image_path' => URL::asset('images/artworks/persistance-of-memory.jpg'),
                'description' => 'Melting clocks in a dreamlike landscape by Salvador Dalí.',
            ],
            [
                'title' => 'The Two Fridas',
                'artist_id' => 6,
                'year_created' => 1939,
                'style_id' => 3,
                'image_path' => URL::asset('images/artworks/the-two-fridas-1939.jpg'),
                'description' => 'A dual self-portrait expressing identity and emotion.',
            ],
            [
                'title' => "Campbell's Soup Cans",
                'artist_id' => 7,
                'year_created' => 1962,
                'style_id' => 7,
                'image_path' => URL::asset('images/artworks/campbell-soup.jpeg'),
                'description' => 'Iconic pop art using commercial imagery.',
            ],
            [
                'title' => 'The Creation of Adam',
                'artist_id' => 8,
                'year_created' => 1512,
                'style_id' => 5,
                'image_path' => URL::asset('images/artworks/Michelangelo_-_Creation_of_Adam.jpg'),
                'description' => 'Part of the Sistine Chapel ceiling frescoes.',
            ],
            [
                'title' => 'Red Canna',
                'artist_id' => 9,
                'year_created' => 1924,
                'style_id' => 10,
                'image_path' => URL::asset('images/artworks/red-canna.jpg'),
                'description' => 'A floral painting symbolizing beauty and abstraction.',
            ],
            [
                'title' => 'The Dance',
                'artist_id' => 10,
                'year_created' => 1910,
                'style_id' => 8,
                'image_path' => URL::asset('images/artworks/the-dance.jpg'),
                'description' => 'Celebrates movement and vibrant colors in pure form.',
            ],
            [
                'title' => 'Number 1A',
                'artist_id' => 11,
                'year_created' => 1948,
                'style_id' => 4,
                'image_path' => URL::asset('images/artworks/number-1a.jpg'),
                'description' => 'A bold drip painting capturing raw motion and emotion.',
            ],
            [
                'title' => 'The Night Watch',
                'artist_id' => 12,
                'year_created' => 1642,
                'style_id' => 10,
                'image_path' => URL::asset('images/artworks/night-watch.jpg'),
                'description' => 'A dramatic militia portrait showing depth and movement.',
            ],
            [
                'title' => 'Sunflowers',
                'artist_id' => 2,
                'year_created' => 1888,
                'style_id' => 1,
                'image_path' => URL::asset('images/artworks/sunflowers.jpg'),
                'description' => 'One of the most famous still-life paintings by Vincent van Gogh.',
            ],
            [
                'title' => 'Cafe Terrace at Night',
                'artist_id' => 2,
                'year_created' => 1888,
                'style_id' => 1,
                'image_path' => URL::asset('images/artworks/cafe-terrace.jpeg'),
                'description' => 'Depicts a brightly lit café terrace under a starry night sky in Arles.',
            ],
        ];

        $users = User::pluck('id');

        foreach ($artworks as $data) {
            $data['views'] = rand(100, 5000);
            $artwork = Artwork::create($data);

            if ($users->isNotEmpty()) {
                $likers = $users->random(rand(1, min(5, $users->count())));
                $artwork->likes()->attach($likers);
            }
        }

    }
}
