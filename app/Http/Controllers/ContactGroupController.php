<?php

namespace App\Http\Controllers;

use App\Models\ContactGroup;
use Illuminate\Http\Request;

class ContactGroupController extends Controller
{
    public function index()
    {
        $groups = ContactGroup::withCount('contacts')->latest()->get();

        return view('contact-groups.index', compact('groups'));
    }

    public function show(ContactGroup $contactGroup)
    {
        $contacts = $contactGroup->contacts()->paginate(50);

        return view('contact-groups.show', compact('contactGroup', 'contacts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        ContactGroup::create($request->only('name'));

        return redirect()->back()->with('success', 'Group berhasil dibuat.');
    }

    public function destroy(ContactGroup $contactGroup)
    {
        $contactGroup->delete();

        return redirect()->back()->with('success', 'Group berhasil dihapus.');
    }
}
