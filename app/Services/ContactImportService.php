<?php

namespace App\Services;

use App\Models\Contact;
use App\Models\ContactGroup;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;

class ContactImportService
{
    public function getHeaders(string $filePath): array
    {
        $headings = (new HeadingRowImport)->toArray($filePath);
        return $headings[0][0] ?? [];
    }

    public function import(string $filePath, string $groupName, string $nameColumn, string $phoneColumn, string $emailColumn): ContactGroup
    {
        $rows    = Excel::toArray([], $filePath)[0];
        $headers = array_shift($rows);
        $headers = array_map(fn($h) => Str::slug((string) $h, '_'), $headers);

        $nameIndex  = array_search(Str::slug((string) $nameColumn, '_'), $headers);
        $phoneIndex = array_search(Str::slug((string) $phoneColumn, '_'), $headers);
        $emailIndex = array_search(Str::slug((string) $emailColumn, '_'), $headers);

        $group = ContactGroup::create(['name' => $groupName]);

        DB::transaction(function () use ($rows, $group, $nameIndex, $phoneIndex, $emailIndex) {
            foreach ($rows as $row) {
                $phone = $phoneIndex !== false ? ($row[$phoneIndex] ?? null) : null;
                $email = $emailIndex !== false ? ($row[$emailIndex] ?? null) : null;
                $name  = $nameIndex !== false ? ($row[$nameIndex] ?? 'Unknown') : 'Unknown';

                $contact = Contact::firstOrCreate(
                    ['phone' => $phone, 'email' => $email],
                    ['name' => $name]
                );

                $group->contacts()->syncWithoutDetaching($contact->id);
            }
        });

        return $group;
    }

    public function toArray(string $filePath): array
    {
        $rows    = Excel::toArray([], $filePath)[0];
        $headers = array_shift($rows);
        $headers = array_map(fn($h) => Str::slug((string) $h, '_'), $headers);

        return array_map(function ($row) use ($headers) {
            $count = count($headers);
            $row   = array_slice(array_pad($row, $count, null), 0, $count);
            return array_combine($headers, $row);
        }, $rows);
    }
}
