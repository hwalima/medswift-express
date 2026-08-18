<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class GroqService
{
    private string $apiKey;
    private string $model;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey  = config('groq.api_key');
        $this->model   = config('groq.model');
        $this->baseUrl = config('groq.base_url');
    }

    /**
     * Run the full agentic loop: send messages, execute any tool calls,
     * and return the final assistant text response.
     */
    public function chat(array $messages, User $user): string
    {
        $toolsService = app(AiToolsService::class);
        $tools        = $toolsService->getToolDefinitions($user);

        for ($i = 0; $i < 6; $i++) {
            $payload = [
                'model'       => $this->model,
                'messages'    => $messages,
                'temperature' => 0.65,
                'max_tokens'  => 2048,
            ];

            if (! empty($tools)) {
                $payload['tools']       = $tools;
                $payload['tool_choice'] = 'auto';
            }

            $response = Http::withHeaders(['Authorization' => 'Bearer ' . $this->apiKey])
                ->timeout(30)
                ->post($this->baseUrl . '/chat/completions', $payload);

            if (! $response->successful()) {
                throw new RuntimeException('Groq API error (' . $response->status() . '): ' . $response->body());
            }

            $choice  = $response->json('choices.0.message');
            $content = $choice['content'] ?? null;

            // No tool calls — return the final answer
            if (empty($choice['tool_calls'])) {
                return $content ?? 'I could not generate a response. Please try again.';
            }

            // Append the assistant's tool-call message and execute each call
            $messages[] = $choice;

            foreach ($choice['tool_calls'] as $call) {
                $name   = $call['function']['name'];
                $args   = json_decode($call['function']['arguments'] ?? '{}', true) ?? [];
                $result = $toolsService->execute($name, $args, $user);

                $messages[] = [
                    'role'         => 'tool',
                    'tool_call_id' => $call['id'],
                    'content'      => json_encode($result),
                ];
            }
        }

        return 'I was unable to complete your request after several attempts. Please try rephrasing.';
    }

    /** Build the system prompt injected at the start of every conversation. */
    public static function systemPrompt(User $user): array
    {
        $role = ucfirst($user->role ?? 'client');
        $now  = now()->format('D, d M Y H:i');

        $base = <<<PROMPT
You are **Swiftie**, the intelligent logistics assistant for **MedSwift Express** — a medical courier service specialising in biological sample transit, laboratory specimens, and medical supply delivery across South Africa.

**Your capabilities:**
- Track shipments in real-time using tracking numbers
- List a user's active or recent shipments
- Generate detailed, itemised shipping quotes in ZAR
- Explain cold-chain compliance requirements (SANS, WHO guidelines)
- Help clients book pickups or understand billing
- For admin users: provide operations summaries, exception reports, and route insights

**Pricing model (ZAR):**
| Component | Rate |
|---|---|
| Base courier rate | R 280 |
| Refrigerated surcharge | R 180 |
| Frozen surcharge | R 350 |
| Urgent priority | R 450 |
| Biohazard handling | R 220 |
| Fuel levy | 8% of subtotal |
| VAT | 15% |

**Behaviour rules:**
- Always respond in professional, clear English
- Use markdown (tables, bold, bullet lists) to structure responses
- For frozen or biohazardous samples, always include a brief compliance note
- When generating a quote, use the `calculate_quote` tool, then present the result as a formatted invoice summary
- Quote numbers follow the format: `QT-{year}-{random 5 digits}`
- Estimated delivery: Urgent = same day, Routine = next business day
- Never expose internal IDs or passwords

Current user: **{$user->name}** | Role: **{$role}** | Organisation: {$user->organisation}
Current time: {$now}
PROMPT;

        return [['role' => 'system', 'content' => $base]];
    }
}
