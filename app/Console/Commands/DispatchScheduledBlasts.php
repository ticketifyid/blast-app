<?php

namespace App\Console\Commands;

use App\Jobs\ProcessBlastJob;
use App\Models\Campaign;
use Illuminate\Console\Command;

class DispatchScheduledBlasts extends Command
{
    protected $signature   = 'blast:dispatch';
    protected $description = 'Dispatch all scheduled campaigns that are due';

    public function handle(): void
    {
        $campaigns = Campaign::where('status', 'scheduled')
            ->where('scheduled_at', '<=', now())
            ->get();

        foreach ($campaigns as $campaign) {
            $campaign->update(['status' => 'processing']);
            ProcessBlastJob::dispatch($campaign);
            $this->info("Dispatched campaign: {$campaign->id} - {$campaign->name}");
        }
    }
}
