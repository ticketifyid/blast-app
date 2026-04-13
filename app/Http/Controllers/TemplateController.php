<?php

namespace App\Http\Controllers;

use App\Models\Template;
use App\Services\TemplateService;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    public function __construct(private TemplateService $templateService) {}

    public function index()
    {
        $templates = Template::latest()->paginate(20);

        return view('templates.index', compact('templates'));
    }

    public function create()
    {
        return view('templates.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'channel' => 'required|in:wa,email',
            'subject' => 'required_if:channel,email|nullable|string|max:255',
            'body'    => 'required|string',
        ]);

        Template::create($request->only('name', 'channel', 'subject', 'body'));

        return redirect()->route('templates.index')->with('success', 'Template berhasil disimpan.');
    }

    public function edit(Template $template)
    {
        $variables = $this->templateService->extractVariables($template->body);

        return view('templates.edit', compact('template', 'variables'));
    }

    public function update(Request $request, Template $template)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'channel' => 'required|in:wa,email',
            'subject' => 'required_if:channel,email|nullable|string|max:255',
            'body'    => 'required|string',
        ]);

        $template->update($request->only('name', 'channel', 'subject', 'body'));

        return redirect()->route('templates.index')->with('success', 'Template berhasil diupdate.');
    }

    public function destroy(Template $template)
    {
        $template->delete();

        return redirect()->route('templates.index')->with('success', 'Template berhasil dihapus.');
    }
}
