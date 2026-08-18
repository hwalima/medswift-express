<?php

namespace App\Http\Controllers;

use App\Services\GroqService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class AiAssistantController extends Controller
{
    public function __construct(private GroqService $groq) {}

    public function chat(): View
    {
        return view('ai.chat');
    }

    public function message(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $user    = auth()->user();
        $history = session('ai_conversation', GroqService::systemPrompt($user));

        $history[] = ['role' => 'user', 'content' => $request->input('message')];

        try {
            $reply = $this->groq->chat($history, $user);
        } catch (Throwable $e) {
            return response()->json(['error' => 'The AI assistant is temporarily unavailable. Please try again shortly.'], 503);
        }

        $history[] = ['role' => 'assistant', 'content' => $reply];

        // Keep only the system prompt + last 30 turns to avoid token overflow
        $systemMsg = array_shift($history);
        session(['ai_conversation' => array_merge(
            [$systemMsg],
            array_slice($history, -30)
        )]);

        return response()->json(['reply' => $reply]);
    }

    public function clearHistory(): JsonResponse
    {
        session()->forget('ai_conversation');

        return response()->json(['ok' => true]);
    }
}
