<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log; // Add logging

class DeepSeekRecipeController extends Controller
{
    public function generateRecipe(Request $request)
    {
        try {
            $request->validate([
                'ingredients' => 'required|string',
            ]);
            
            $apiKey = env('DEEPSEEK_API_KEY');
            if (!$apiKey) {
                throw new \Exception('DeepSeek API key is not configured');
            }
            
            $prompt = "Īsa recepte latviski ar: {$request->ingredients}. Iekļaut: 1) Nosaukums, 2) Sastāvdaļas, 3) Īsas instrukcijas.";
            
            $response = Http::timeout(60)
                ->retry(2, 1000)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type'  => 'application/json',
                ])->post('https://api.deepseek.com/chat/completions', [
                    'model' => 'deepseek-chat',
                    'messages' => [
                        [
                            'role' => 'system', 
                            'content' => 'Tu esi pavārs. Atbildi tikai latviski.'
                        ],
                        [
                            'role' => 'user', 
                            'content' => $prompt
                        ],
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 1500,
                ]);
            
            if ($response->failed()) {
                return response()->json([
                    'error' => 'DeepSeek API request failed',
                    'status' => $response->status(),
                    'response' => $response->json()
                ], 500);
            }
            
            return response()->json([
                'recipe' => $response->json('choices.0.message.content', '')
            ]);
            
        } catch (\Throwable $e) {
            \Log::error('Recipe generation error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Internal server error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}