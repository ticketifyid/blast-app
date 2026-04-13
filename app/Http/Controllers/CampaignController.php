<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\ContactGroup;
use App\Models\Template;
use App\Services\ContactImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CampaignController extends Controller
{
    public function __construct(private ContactImportService $contactImportService) {}

    public function index()
    {
        $campaigns = Campaign::latest()->paginate(20);

        return view('campaigns.index', compact('campaigns'));
    }

    public function create()
    {
        $templates     = Template::all();
        $contactGroups = ContactGroup::withCount('contacts')->get();

        return view('campaigns.create', compact('templates', 'contactGroups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'               => 'required|string|max:255',
            'type'               => 'required|in:transactional,marketing',
            'channel'            => 'required|in:wa,email',
            'template_id'        => 'nullable|exists:templates,id',
            'subject'            => 'required_if:channel,email|nullable|string|max:255',
            'body'               => 'required|string',
            'scheduled_at'       => 'required|date|after:now',
            'file'               => 'required_if:type,transactional|nullable|file|mimes:xlsx,xls,csv',
            'name_column'        => 'required_if:type,transactional|nullable|string',
            'phone_column'       => 'nullable|string',
            'email_column'       => 'nullable|string',
            'contact_groups'     => 'required_if:type,marketing|nullable|array',
            'contact_groups.*'   => 'exists:contact_groups,id',
            'use_qr'             => 'boolean',
            'qr_column'          => 'required_if:use_qr,true|nullable|string',
            'image'              => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        ]);

        $filePath  = null;
        $imagePath = null;
        $group     = null;

        if ($request->type === 'transactional' && $request->hasFile('file')) {
            $filePath = $request->file('file')->store('blasts', 'public');

            $group = $this->contactImportService->import(
                Storage::disk('public')->path($filePath),
                $request->name,
                $request->name_column,
                $request->phone_column ?? '',
                $request->email_column ?? '',
            );
        }

        if ($request->channel === 'wa' && $request->hasFile('image')) {
            $imagePath = $request->file('image')->store('campaign-images', 'public');
        }

        $campaign = Campaign::create([
            'name'         => $request->name,
            'type'         => $request->type,
            'channel'      => $request->channel,
            'template_id'  => $request->template_id,
            'subject'      => $request->subject,
            'body'         => $request->body,
            'file_path'    => $filePath,
            'image_path'   => $imagePath,
            'use_qr'        => $request->boolean('use_qr'),
            'qr_column'     => $request->qr_column,
            'name_column'   => $request->name_column,
            'phone_column'  => $request->phone_column ?: null,
            'email_column'  => $request->email_column ?: null,
            'status'        => 'scheduled',
            'scheduled_at' => $request->scheduled_at,
        ]);

        if ($request->type === 'marketing' && $request->contact_groups) {
            $campaign->contactGroups()->sync($request->contact_groups);
        }

        if ($group) {
            $campaign->contactGroups()->syncWithoutDetaching($group->id);
        }

        return redirect()->route('campaigns.show', $campaign)
            ->with('success', 'Campaign berhasil dibuat.');
    }

    public function show(Campaign $campaign)
    {
        $recipients = $campaign->recipients()->latest()->paginate(50);
        $stats      = [
            'sent'    => $campaign->recipients()->where('status', 'sent')->count(),
            'failed'  => $campaign->recipients()->where('status', 'failed')->count(),
            'skipped' => $campaign->recipients()->where('status', 'skipped')->count(),
        ];

        return view('campaigns.show', compact('campaign', 'recipients', 'stats'));
    }
}
