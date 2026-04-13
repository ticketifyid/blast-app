<!-- resources/views/contact-groups/show.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <a href="{{ route('contact-groups.index') }}" class="text-muted text-decoration-none small">Phonebook</a>
            <span class="text-muted small mx-1">/</span>
            <h1 class="d-inline">{{ $contactGroup->name }}</h1>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>{{ $contacts->total() }} kontak</span>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Nama</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Dibuat</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contacts as $contact)
                        <tr>
                            <td class="ps-4">{{ $contact->name }}</td>
                            <td>{{ $contact->phone ?? '-' }}</td>
                            <td>{{ $contact->email ?? '-' }}</td>
                            <td class="text-muted">{{ $contact->created_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">Belum ada kontak di group ini</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($contacts->hasPages())
            <div class="card-footer bg-white">
                {{ $contacts->links() }}
            </div>
        @endif
    </div>
@endsection
