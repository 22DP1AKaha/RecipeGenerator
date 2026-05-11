<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

// Tilts starp programmu un DeepSeek AI
class AIRecipeGeneratorService
{
    private string $apiKey;
    private string $apiUrl = 'https://api.deepseek.com/chat/completions';
    private string $model = 'deepseek-chat';
    private float $temperature = 0.7; // 0 - vienmēr tādu pašu atbildi, 1 - katrreiz savādāku. 0.7 ir labs vidusceļš

    public function __construct()
    {
        // API atslēga jāpaņem no .env (services.deepseek.api_key)
        $this->apiKey = config('services.deepseek.api_key', env('DEEPSEEK_API_KEY'));

        // Bez atslēgas neko nevarēsim izdarīt - labāk, lai uzreiz izkrīt, nekā vēlāk
        if (!$this->apiKey) {
            throw new \RuntimeException('DeepSeek API key is not configured');
        }
    }

    public function generateRecipe(string $ingredients, array $options = []): array
    {
        // Sagatavojam tekstu, ko sūtīsim AI
        $prompt = $this->buildPrompt($ingredients, $options);

        try {
            // Aiziet pie DeepSeek
            $response = $this->callAPI($prompt);

            // AI atbild JSON struktūrā, mums vajag tikai pašu tekstu
            return [
                'success' => true,
                'recipe'  => $response['choices'][0]['message']['content'] ?? '',
            ];
        } catch (\Exception $e) {
            // Logā ieraksts par kļūdu, lietotājam atgriežam neveiksmes paziņojumu
            Log::error('Recipe generation error', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error'   => $e->getMessage(),
            ];
        }
    }

    private function buildPrompt(string $ingredients, array $options): string
    {
        // Šis ir galvenais teksts, ko AI redzēs - jo precīzāks formāts, jo vieglāk pēc tam parsēt
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

        // Pievienojam diētas, ja tādas ir
        if (!empty($options['dietary_restrictions'])) {
            $basePrompt .= " Ievēro šādus ierobežojumus: " . implode(', ', $options['dietary_restrictions']) . ".";
        }

        // Un alerģijas
        if (!empty($options['allergies'])) {
            $basePrompt .= " Izvairīties no: " . implode(', ', $options['allergies']) . ".";
        }

        return $basePrompt;
    }

    private function callAPI(string $prompt): array
    {
        // HTTP pieprasījums - 60s timeout, 2x atkārtot
        $response = Http::timeout(60)
            ->retry(2, 1000)
            ->withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type'  => 'application/json',
            ])
            ->post($this->apiUrl, [
                'model'    => $this->model,
                'messages' => [
                    [
                        // Ar šo AI "noskaņojas" kā pavārs, kas atbild latviski
                        'role'    => 'system',
                        'content' => 'Tu esi pavārs. Atbildi tikai latviski.'
                    ],
                    [
                        // Pati prasība par recepti
                        'role'    => 'user',
                        'content' => $prompt
                    ],
                ],
                'temperature' => $this->temperature,
                'max_tokens'  => 1500, // 1500 tokenu vajadzētu būt pilnīgi pietiekoši
            ]);

        // Ja DeepSeek atgrieza kļūdu - metam exception, lai augstāk var to apstrādāt
        if ($response->failed()) {
            throw new \RuntimeException(
                'DeepSeek API request failed: ' . $response->status() . ' - ' . $response->body()
            );
        }

        // JSON automātiski iegūstam kā masīvu
        return $response->json();
    }

    // Setteris, ja kādreiz vajag mainīt modeli no ārpuses
    public function setModel(string $model): self
    {
        $this->model = $model;
        return $this;
    }

    // Tāpat ar temperatūru (radošumu)
    public function setTemperature(float $temperature): self
    {
        $this->temperature = $temperature;
        return $this;
    }
}
