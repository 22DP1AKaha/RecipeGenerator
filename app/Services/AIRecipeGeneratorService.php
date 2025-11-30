<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIRecipeGeneratorService
{
    private string $apiKey;
    private string $apiUrl = 'https://api.deepseek.com/chat/completions';
    private string $model = 'deepseek-chat';

    public function __construct()
    {
        $this->apiKey = config('services.deepseek.api_key', env('DEEPSEEK_API_KEY'));

        if (!$this->apiKey) {
            throw new \Exception('DeepSeek API key is not configured');
        }
    }

    public function generateRecipe(string $ingredients, array $options = []): array
    {
        $prompt = $this->buildPrompt($ingredients, $options);

        try {
            $response = $this->callAPI($prompt);

            return [
                'success' => true,
                'recipe' => $response['choices'][0]['message']['content'] ?? '',
            ];
        } catch (\Exception $e) {
            Log::error('Recipe generation error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function buildPrompt(string $ingredients, array $options): string
    {
        $basePrompt = "Izveido vienu īsu recepti no šiem produktiem (nav obligāti jāizmanto visas sastāvdaļas): {$ingredients} (Nav pieejama neviena cita sastāvdaļa).

Formāts (OBLIGĀTI JĀIEVĒRO):

Nosaukums:
[receptes nosaukums]

Sastāvdaļas:
- [sastāvdaļa ar daudzumu]
- [sastāvdaļa ar daudzumu]

Pagatavošana:
1. [pirmais solis]
2. [otrais solis]
3. [trešais solis]

SVARĪGI: Katrs pagatavošanas solis JĀBŪT atsevišķā rindā ar numuru. Neraksti visus soļus vienā rindā. Neizmanto markdown formātus.";

        if (!empty($options['dietary_restrictions'])) {
            $basePrompt .= " Ievēro šādus ierobežojumus: " . implode(', ', $options['dietary_restrictions']) . ".";
        }

        if (!empty($options['allergies'])) {
            $basePrompt .= " Izvairīties no: " . implode(', ', $options['allergies']) . ".";
        }

        return $basePrompt;
    }

    private function callAPI(string $prompt): array
    {
        $response = Http::timeout(60)
            ->retry(2, 1000)
            ->withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type'  => 'application/json',
            ])
            ->post($this->apiUrl, [
                'model' => $this->model,
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
            throw new \Exception(
                'DeepSeek API request failed: ' . $response->status() . ' - ' . $response->body()
            );
        }

        return $response->json();
    }

    public function setModel(string $model): self
    {
        $this->model = $model;
        return $this;
    }

    public function setTemperature(float $temperature): self
    {
        $this->temperature = $temperature;
        return $this;
    }
}
