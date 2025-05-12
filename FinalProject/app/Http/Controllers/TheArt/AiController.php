<?php

namespace App\Http\Controllers\TheArt;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;
use Illuminate\Http\Request;

class AiController extends Controller
{
    public function aimodel()
    {
        return Inertia::render('AiModel', [
            'title' => 'AI Model',
            'headerImage' => '/images/homeTop.jpg',
        ]);
    }

    public function analyze(Request $request)
    {
        $request->validate(['art' => 'required|image|max:4096']);

        try {
            $image = $request->file('art');
            $response = Http::attach(
                'file', file_get_contents($image->getRealPath()), $image->getClientOriginalName()
            )->post('http://127.0.0.1:9000/analyze-art');

            if ($response->successful()) {
                $json = $response->json();
                return Inertia::render('AiModel', [
                    'title' => 'theArt Critique',
                    'style' => $json['style'] ?? 'Unknown',
                    'description' => $json['description'] ?? '',
                    'feedback' => $json['feedback'] ?? null,
                ]);
            }

            return back()->withErrors(['error' => 'AI model failed to respond.']);

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error communicating with the AI model: ' . $e->getMessage()]);
        }
    }


}
