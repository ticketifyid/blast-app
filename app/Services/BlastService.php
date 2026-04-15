<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\CampaignRecipient;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BlastService
{
    public function __construct(
        private TemplateService      $templateService,
        private WhatsappService      $whatsappService,
        private EmailService         $emailService,
        private ContactImportService $contactImportService,
        private QrCodeService        $qrCodeService,
        private ConfigService        $configService,
    ) {}

    public function execute(Campaign $campaign): void
    {
        $campaign->update(['status' => 'processing', 'started_at' => now()]);

        try {
            if ($campaign->isTransactional()) {
                $this->blastTransactional($campaign);
            } else {
                $this->blastMarketing($campaign);
            }

            $campaign->update(['status' => 'completed', 'finished_at' => now()]);
        } catch (\Exception $e) {
            Log::error('Blast failed', ['campaign_id' => $campaign->id, 'error' => $e->getMessage()]);
            $campaign->update(['status' => 'failed', 'finished_at' => now()]);
        }
    }

    private function blastTransactional(Campaign $campaign): void
    {
        $rows = $this->contactImportService->toArray(
            Storage::disk('public')->path($campaign->file_path)
        );

        $nameCol  = $campaign->name_column;
        $phoneCol = $campaign->phone_column;
        $emailCol = $campaign->email_column;

        $seen = [];

        foreach ($rows as $row) {
            $phone  = $phoneCol ? ($row[$phoneCol] ?? null) : null;
            $email  = $emailCol ? ($row[$emailCol] ?? null) : null;
            $name   = $nameCol  ? ($row[$nameCol]  ?? null) : null;
            $target = $campaign->isWa() ? $phone : $email;

            if (!$target) continue;

            if (in_array($target, $seen)) {
                $this->logRecipient($campaign, $name, $phone, $email, 'skipped', 'Duplicate');
                continue;
            }

            $seen[] = $target;

            $body = $this->templateService->render($campaign->body, $row);

            $qrUrl = null;
            if ($campaign->isWa() && $campaign->use_qr && $campaign->qr_column && isset($row[$campaign->qr_column])) {
                $qrUrl = $this->qrCodeService->saveAndGetUrl($row[$campaign->qr_column]);
            }

            $result = $this->sendMessage($campaign, $target, $body, $campaign->subject, $qrUrl);

            if ($qrUrl) {
                $this->qrCodeService->deleteQr($row[$campaign->qr_column]);
            }

            $this->logRecipient(
                $campaign,
                $name,
                $phone,
                $email,
                $result['success'] ? 'sent' : 'failed',
                $result['error'] ?? null,
            );

            $campaign->isWa() ? $this->waDelay() : sleep(1);
        }
    }

    private function blastMarketing(Campaign $campaign): void
    {
        $seen = [];

        $campaign->contactGroups()
            ->with('contacts')
            ->each(function ($group) use ($campaign, &$seen) {
                foreach ($group->contacts as $contact) {
                    $target = $campaign->isWa() ? $contact->phone : $contact->email;

                    if (!$target) continue;

                    if (in_array($target, $seen)) {
                        $this->logRecipient($campaign, $contact->name, $contact->phone, $contact->email, 'skipped', 'Duplicate');
                        continue;
                    }

                    $seen[] = $target;

                    $variables = [
                        'name'  => $contact->name,
                        'phone' => $contact->phone,
                        'email' => $contact->email,
                    ];

                    $body = $this->templateService->render($campaign->body, $variables);

                    $result = $this->sendMessage($campaign, $target, $body, $campaign->subject);

                    $this->logRecipient(
                        $campaign,
                        $contact->name,
                        $contact->phone,
                        $contact->email,
                        $result['success'] ? 'sent' : 'failed',
                        $result['error'] ?? null,
                    );

                    $campaign->isWa() ? $this->waDelay() : sleep(1);
                }
            });
    }

    private function waDelay(): void
    {
        $wa  = $this->configService->getWhatsapp();
        $min = max(1, (int) ($wa['delay_min'] ?? 5));
        $max = max($min, (int) ($wa['delay_max'] ?? 15));
        sleep(rand($min, $max));
    }

    private function sendMessage(Campaign $campaign, string $target, string $body, ?string $subject = null, ?string $qrUrl = null): array
    {
        if ($campaign->isWa()) {
            $imageUrl = $qrUrl ?? $campaign->getImageUrl();

            return $imageUrl
                ? $this->whatsappService->sendWithImage($target, $body, $imageUrl)
                : $this->whatsappService->send($target, $body);
        }

        return $this->emailService->send($target, $subject ?? '', $body);
    }

    private function logRecipient(Campaign $campaign, ?string $name, ?string $phone, ?string $email, string $status, ?string $errorMessage = null): void
    {
        CampaignRecipient::create([
            'campaign_id'   => $campaign->id,
            'name'          => $name,
            'phone'         => $phone,
            'email'         => $email,
            'status'        => $status,
            'error_message' => $errorMessage,
            'sent_at'       => $status === 'sent' ? now() : null,
        ]);
    }
}
