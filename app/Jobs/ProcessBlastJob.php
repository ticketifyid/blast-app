<?php

namespace App\Jobs;

use App\Models\Campaign;
use App\Services\BlastService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessBlastJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 3600;
    public int $tries   = 1;

    public function __construct(private Campaign $campaign) {}

    public function handle(BlastService $blastService): void
    {
        $blastService->execute($this->campaign);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('ProcessBlastJob failed', [
            'campaign_id' => $this->campaign->id,
            'error'       => $exception->getMessage(),
        ]);

        $this->campaign->update([
            'status'      => 'failed',
            'finished_at' => now(),
        ]);
    }
}
