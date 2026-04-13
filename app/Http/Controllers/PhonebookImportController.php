<?php

namespace App\Http\Controllers;

use App\Services\ContactImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PhonebookImportController extends Controller
{
    public function __construct(private ContactImportService $contactImportService) {}

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        $path    = $request->file('file')->store('temp', 'local');
        $headers = $this->contactImportService->getHeaders(Storage::disk('local')->path($path));

        return response()->json([
            'path'    => $path,
            'headers' => $headers,
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'path'         => 'required|string',
            'group_name'   => 'required|string|max:255',
            'name_column'  => 'required|string',
            'phone_column' => 'nullable|string',
            'email_column' => 'nullable|string',
        ]);

        $group = $this->contactImportService->import(
            Storage::disk('local')->path($request->path),
            $request->group_name,
            $request->name_column,
            $request->phone_column ?? '',
            $request->email_column ?? '',
        );

        return redirect()->route('contact-groups.show', $group)
            ->with('success', 'Import berhasil.');
    }
}
