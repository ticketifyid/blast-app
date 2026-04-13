<?php

namespace App\Http\Controllers;

use App\Services\ContactImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExcelHeaderController extends Controller
{
    public function __construct(private ContactImportService $contactImportService) {}

    public function __invoke(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        $path    = $request->file('file')->store('temp', 'local');
        $headers = $this->contactImportService->getHeaders(Storage::disk('local')->path($path));

        return response()->json(['headers' => $headers]);
    }
}
