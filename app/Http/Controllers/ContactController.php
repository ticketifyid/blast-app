<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $contacts = Contact::query()
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%")
                ->orWhere('phone', 'like', "%{$request->search}%")
                ->orWhere('email', 'like', "%{$request->search}%"))
            ->with('groups')
            ->latest()
            ->paginate(50);

        return view('contacts.index', compact('contacts'));
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();

        return redirect()->back()->with('success', 'Kontak berhasil dihapus.');
    }
}
