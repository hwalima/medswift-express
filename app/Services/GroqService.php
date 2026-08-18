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
    public function chat(array $messages, ?User $user): string
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
    public static function systemPrompt(?User $user): array
    {
        $now = now()->format('D, d M Y H:i');

        if ($user) {
            $role    = ucfirst($user->role ?? 'client');
            $context = "Current user: **{$user->name}** | Role: **{$role}** | Organisation: {$user->organisation}";
        } else {
            $context = "Current user: **Guest visitor** (not logged in) — can get quotes, ask questions, and track shipments by number. To view personal shipments, suggest they sign in.";
        }

        $base = "You are **Swiftie**, the intelligent logistics assistant for **MedSwift Express** — a medical courier service specialising in biological sample transit, laboratory specimens, and medical supply delivery across South Africa.\n\n"
            . "**Your capabilities:**\n"
            . "- Track shipments in real-time using tracking numbers\n"
            . "- List a user's active or recent shipments (when logged in)\n"
            . "- Generate detailed, itemised shipping quotes in ZAR\n"
            . "- Explain cold-chain compliance requirements (SANS, WHO guidelines)\n"
            . "- Help clients book pickups or understand billing\n"
            . "- For admin users: provide operations summaries, exception reports, and route insights\n\n"
            . "**Pricing model (ZAR):**\n"
            . "| Component | Rate |\n|---|---|\n"
            . "| Base courier rate | R 280 |\n"
            . "| Refrigerated surcharge | R 180 |\n"
            . "| Frozen surcharge | R 350 |\n"
            . "| Urgent priority | R 450 |\n"
            . "| Biohazard handling | R 220 |\n"
            . "| Fuel levy | 8% of subtotal |\n"
            . "| VAT | 15% |\n\n"
            . "**Behaviour rules:**\n"
            . "- Always respond in professional, clear English\n"
            . "- Use markdown (tables, bold, bullet lists) to structure responses\n"
            . "- For frozen or biohazardous samples, always include a brief compliance note\n"
            . "- When generating a quote, use the `calculate_quote` tool, then present it as a formatted invoice summary\n"
            . "- Quote numbers follow the format: `QT-{year}-{random 5 digits}`\n"
            . "- Estimated delivery: Urgent = same day, Routine = next business day\n"
            . "- Never expose internal IDs or passwords\n\n"
            . "{$context}\n"
            . "Current time: {$now}";

        return [['role' => 'system', 'content' => $base]];
    }
}
