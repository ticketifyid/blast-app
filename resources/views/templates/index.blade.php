<!-- resources/views/templates/index.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="page-header d-flex justify-content-between align-items-center">
        <h1>Templates</h1>
        <a href="{{ route('templates.create') }}" class="btn btn-dark btn-sm">Buat Template</a>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Nama</th>
                        <th>Channel</th>
                        <th>Dibuat</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($templates as $template)
                        <tr>
                            <td class="ps-4">{{ $template->name }}</td>
                            <td>
                                <span
                                    class="badge {{ $template->channel === 'wa' ? 'bg-success' : 'bg-primary' }} bg-opacity-10 {{ $template->channel === 'wa' ? 'text-success' : 'text-primary' }}">
                                    {{ strtoupper($template->channel) }}
                                </span>
                            </td>
                            <td class="text-muted">{{ $template->created_at->format('d M Y') }}</td>
                            <td class="text-end pe-4">
                                <a href="{{ route('templates.edit', $template) }}"
                                    class="btn btn-link btn-sm text-dark p-0 me-3">Edit</a>
                                <form action="{{ route('templates.destroy', $template) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Hapus template ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-link btn-sm text-danger p-0">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">Belum ada template</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($templates->hasPages())
            <div class="card-footer bg-white">
                {{ $templates->links() }}
            </div>
        @endif
    </div>
@endsection
