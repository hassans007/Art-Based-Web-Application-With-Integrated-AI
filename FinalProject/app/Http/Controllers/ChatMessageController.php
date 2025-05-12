<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\Auth;

class ChatMessageController extends Controller
{
    public function index()
    {
        return ChatMessage::with('user')->latest()->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        return ChatMessage::create([
            'user_id' => Auth::id(),
            'message' => $request->message,
        ]);
    }

    public function destroy($id)
    {
        $message = ChatMessage::findOrFail($id);

        if (Auth::user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $message->delete();
        return response()->json(['success' => true]);
    }

    public function clearAll()
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        ChatMessage::truncate();
        return response()->json(['success' => true]);
    }
}

