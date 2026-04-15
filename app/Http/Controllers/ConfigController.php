<?php

namespace App\Http\Controllers;

use App\Services\ConfigService;
use Illuminate\Http\Request;

class ConfigController extends Controller
{
    public function __construct(private ConfigService $configService) {}

    public function index()
    {
        $whatsapp = $this->configService->getWhatsapp();
        $smtp     = $this->configService->getSmtp();

        return view('config.index', compact('whatsapp', 'smtp'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type'              => 'required|in:whatsapp,smtp',

            'whatsapp.base_url'   => 'required_if:type,whatsapp|url',
            'whatsapp.api_key'    => 'required_if:type,whatsapp|string',
            'whatsapp.sender'     => 'required_if:type,whatsapp|string',
            'whatsapp.delay_min'  => 'required_if:type,whatsapp|integer|min:1',
            'whatsapp.delay_max'  => 'required_if:type,whatsapp|integer|min:1',

            'smtp.host'         => 'required_if:type,smtp|string',
            'smtp.port'         => 'required_if:type,smtp|numeric',
            'smtp.username'     => 'required_if:type,smtp|string',
            'smtp.password'     => 'required_if:type,smtp|string',
            'smtp.encryption'   => 'required_if:type,smtp|in:tls,ssl',
            'smtp.from_email'   => 'required_if:type,smtp|email',
            'smtp.from_name'    => 'required_if:type,smtp|string',
        ]);

        $type = $request->input('type');
        $this->configService->set($type, $request->input($type));

        return redirect()->back()->with('success', 'Config berhasil disimpan.');
    }
}
