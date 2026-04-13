<!-- resources/views/contacts/index.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="page-header d-flex justify-content-between align-items-center">
        <h1>Contacts</h1>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <form action="{{ route('contacts.index') }}" method="GET" class="d-flex gap-2">
                <input type="text" name="search" class="form-control form-control-sm"
                    placeholder="Cari nama, phone, email..." value="{{ request('search') }}" style="width: 280px;">
                <button type="submit" class="btn btn-outline-secondary btn-sm">Cari</button>
                @if (request('search'))
                    <a href="{{ route('contacts.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                @endif
            </form>
            <span class="text-muted small">{{ $contacts->total() }} kontak</span>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Nama</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Group</th>
                        <th>Dibuat</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contacts as $contact)
                        <tr>
                            <td class="ps-4">{{ $contact->name }}</td>
                            <td>{{ $contact->phone ?? '-' }}</td>
                            <td>{{ $contact->email ?? '-' }}</td>
                            <td>
                                @foreach ($contact->groups as $group)
                                    <span class="badge bg-light text-dark border">{{ $group->name }}</span>
                                @endforeach
                            </td>
                            <td class="text-muted">{{ $contact->created_at->format('d M Y') }}</td>
                            <td class="text-end pe-4">
                                <form action="{{ route('contacts.destroy', $contact) }}" method="POST"
                                    onsubmit="return confirm('Hapus kontak ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-link btn-sm text-danger p-0">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Belum ada kontak</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($contacts->hasPages())
            <div class="card-footer bg-white">
                {{ $contacts->withQueryString()->links() }}
            </div>
        @endif
    </div>
@endsection
