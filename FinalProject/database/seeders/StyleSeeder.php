<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StyleSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('styles')->insert([
            [
                'name' => 'Impressionism',
                'description' => 'A 19th-century art movement characterized by small, thin brush strokes and an emphasis on the accurate depiction of light.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Cubism',
                'description' => 'An early-20th-century avant-garde art movement that revolutionized European painting and sculpture.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Surrealism',
                'description' => 'An early 20th-century movement aimed at releasing the creative potential of the unconscious mind.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Abstract Expressionism',
                'description' => 'A post–World War II art movement characterized by large-scale, non-representational works and emotional intensity.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Renaissance',
                'description' => 'A period of European art revival from the 14th to the 17th century emphasizing realism, perspective, and classical themes.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Baroque',
                'description' => 'A dramatic, richly detailed style of art that emerged in Europe in the 17th century.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Pop Art',
                'description' => 'An art movement that emerged in the 1950s, drawing inspiration from popular and commercial culture.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Fauvism',
                'description' => 'A style of painting with vivid expressionistic and non-naturalistic use of color, developed by Matisse and others.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Expressionism',
                'description' => 'A style where artists seek to express emotional experience rather than physical reality.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Realism',
                'description' => 'A style that aims to depict subjects as they appear in everyday life, without embellishment or interpretation.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Minimalism',
                'description' => 'An art movement that emphasizes simplicity, basic forms, and minimal detail.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
