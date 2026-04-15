<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailService
{
    public function __construct(private ConfigService $configService)
    {
        $this->applySmtpConfig();
    }

    private function applySmtpConfig(): void
    {
        $smtp = $this->configService->getSmtp();

        if (!$smtp) return;

        Config::set('mail.default', 'smtp');
        Config::set('mail.mailers.smtp.host', $smtp['host']);
        Config::set('mail.mailers.smtp.port', $smtp['port']);
        Config::set('mail.mailers.smtp.username', $smtp['username']);
        Config::set('mail.mailers.smtp.password', $smtp['password']);
        Config::set('mail.mailers.smtp.encryption', $smtp['encryption']);
        Config::set('mail.from.address', $smtp['from_email']);
        Config::set('mail.from.name', $smtp['from_name']);
    }

    public function send(string $email, string $subject, string $body): array
    {
        try {
            Mail::mailer('smtp')->html($body, function ($message) use ($email, $subject) {
                $message->to($email)->subject($subject);
            });

            return ['success' => true];
        } catch (\Exception $e) {
            Log::error('Email send failed', ['email' => $email, 'error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
