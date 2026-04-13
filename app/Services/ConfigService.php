<?php

namespace App\Services;

use App\Models\Config;
use Illuminate\Support\Facades\Cache;

class ConfigService
{
    public function get(string $key): ?array
    {
        return Cache::remember("config.{$key}", 60, function () use ($key) {
            $config = Config::where('key', $key)->first();
            return $config ? $config->value : null;
        });
    }

    public function set(string $key, array $value): void
    {
        Config::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        Cache::forget("config.{$key}");
    }

    public function getWhatsapp(): ?array
    {
        return $this->get('whatsapp');
    }

    public function getSmtp(): ?array
    {
        return $this->get('smtp');
    }
}
