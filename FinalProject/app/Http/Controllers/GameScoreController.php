<?php

namespace App\Http\Controllers;

use App\Models\GameScore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GameScoreController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'game_name' => 'required|string',
            'score' => 'required|integer',
            'total_questions' => 'required|integer',
        ]);

        GameScore::create([
            'user_id' => Auth::id(),
            'game_name' => $request->game_name,
            'score' => $request->score,
            'total_questions' => $request->total_questions,
        ]);

        return response()->json(['message' => 'Score saved successfully']);
    }

}
