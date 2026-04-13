<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeService
{
    public function generateOrderQr(string $noBill): string
    {
        return QrCode::format('png')
            ->size(300)
            ->margin(2)
            ->errorCorrection('M')
            ->generate($noBill);
    }

    public function saveAndGetUrl(string $noBill): string
    {
        $png  = $this->generateOrderQr($noBill);
        $path = "qr/{$noBill}.png";

        Storage::disk('public')->put($path, $png);

        return url("storage/{$path}");
    }

    public function deleteQr(string $noBill): void
    {
        $path = "qr/{$noBill}.png";
        Storage::disk('public')->delete($path);
    }
}
