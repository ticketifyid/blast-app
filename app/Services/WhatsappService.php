<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappService
{
    private ?array $config;

    public function __construct(private ConfigService $configService)
    {
        $this->config = $this->configService->getWhatsapp();
    }

    public function send(string $phone, string $message): array
    {
        if (!$this->config) {
            return ['success' => false, 'error' => 'WhatsApp configuration not set'];
        }

        try {
            $response = Http::post($this->config['base_url'] . '/send-message', [
                'api_key' => $this->config['api_key'],
                'sender'  => $this->config['sender'],
                'number'  => $this->normalizePhone($phone),
                'message' => $message,
            ]);

            if ($response->successful()) {
                return ['success' => true];
            }

            return [
                'success' => false,
                'error'   => $response->json('message') ?? 'Unknown error',
            ];
        } catch (\Exception $e) {
            Log::error('WhatsApp send failed', ['phone' => $phone, 'error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function sendWithImage(string $phone, string $message, string $imageUrl): array
    {
        if (!$this->config) {
            return ['success' => false, 'error' => 'WhatsApp configuration not set'];
        }

        try {
            $response = Http::timeout(15)->post($this->config['base_url'] . '/send-media', [
                'api_key'    => $this->config['api_key'],
                'sender'     => $this->config['sender'],
                'number'     => $this->normalizePhone($phone),
                'media_type' => 'image',
                'caption'    => $message,
                'url'        => $imageUrl,
            ]);

            if ($response->successful()) {
                return ['success' => true];
            }

            return [
                'success' => false,
                'error'   => $response->json('message') ?? 'Unknown error',
            ];
        } catch (\Exception $e) {
            Log::error('WhatsApp send image failed', ['phone' => $phone, 'error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    private function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/\D/', '', $phone);

        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        return $phone;
    }
}
