<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramBot
{
    private string $token;
    private string $baseUrl;

    public function __construct()
    {
        $this->token = config('services.telegram.token');
        $this->baseUrl = sprintf('https://api.telegram.org/bot%s', $this->token);
    }

    public function sendMessage(string $chatId, string $text, array $options = []): void
    {
        $payload = array_merge([
            'chat_id' => $chatId,
            'text' => $text,
        ], $options);

        $this->post('sendMessage', $payload);
    }

    public function sendDocument(string $chatId, string $absolutePath, string $fileName, string $caption = ''): void
    {
        if (!is_file($absolutePath)) {
            Log::error('telegram.document_missing', ['path' => $absolutePath]);
            $this->sendMessage($chatId, 'Le fichier commande est temporairement indisponible.');
            return;
        }

        $handle = fopen($absolutePath, 'r');

        try {
            Http::attach('document', $handle, $fileName)
                ->post($this->endpoint('sendDocument'), [
                    'chat_id' => $chatId,
                    'caption' => $caption,
                ])->throw();
        } catch (\Throwable $e) {
            Log::error('telegram.send_document_failed', [
                'chat_id' => $chatId,
                'error' => $e->getMessage(),
            ]);
        } finally {
            if (is_resource($handle)) {
                fclose($handle);
            }
        }
    }

    public function answerCallbackQuery(string $callbackQueryId, string $text = ''): void
    {
        $payload = ['callback_query_id' => $callbackQueryId];
        if ($text) {
            $payload['text'] = $text;
        }
        $this->post('answerCallbackQuery', $payload);
    }

    private function post(string $method, array $payload): void
    {
        try {
            Http::post($this->endpoint($method), $payload)->throw();
        } catch (\Throwable $e) {
            Log::error('telegram.api_error', [
                'method' => $method,
                'payload' => $payload,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function endpoint(string $method): string
    {
        return sprintf('%s/%s', $this->baseUrl, $method);
    }
}
