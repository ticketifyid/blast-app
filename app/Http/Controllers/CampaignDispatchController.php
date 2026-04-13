<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessBlastJob;
use App\Models\Campaign;

class CampaignDispatchController extends Controller
{
    public function __invoke(Campaign $campaign)
    {
        if (!in_array($campaign->status, ['scheduled', 'failed'])) {
            return redirect()->back()->with('error', 'Campaign tidak bisa di-dispatch.');
        }

        ProcessBlastJob::dispatch($campaign);

        return redirect()->back()->with('success', 'Campaign sedang diproses.');
    }
}
