<!-- resources/views/campaigns/index.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="page-header d-flex justify-content-between align-items-center">
        <h1>Campaigns</h1>
        <a href="{{ route('campaigns.create') }}" class="btn btn-dark btn-sm">Buat Campaign</a>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Nama</th>
                        <th>Type</th>
                        <th>Channel</th>
                        <th>Status</th>
                        <th>Jadwal</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($campaigns as $campaign)
                        <tr>
                            <td class="ps-4">
                                <a href="{{ route('campaigns.show', $campaign) }}"
                                    class="text-dark text-decoration-none fw-medium">
                                    {{ $campaign->name }}
                                </a>
                            </td>
                            <td>
                                <span class="text-muted small">{{ $campaign->type }}</span>
                            </td>
                            <td>
                                <span
                                    class="badge {{ $campaign->channel === 'wa' ? 'bg-success' : 'bg-primary' }} bg-opacity-10 {{ $campaign->channel === 'wa' ? 'text-success' : 'text-primary' }}">
                                    {{ strtoupper($campaign->channel) }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $statusColor =
                                        [
                                            'scheduled' => 'secondary',
                                            'processing' => 'warning',
                                            'completed' => 'success',
                                            'failed' => 'danger',
                                        ][$campaign->status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $statusColor }} bg-opacity-10 text-{{ $statusColor }}">
                                    {{ $campaign->status }}
                                </span>
                            </td>
                            <td class="text-muted small">{{ $campaign->scheduled_at?->format('d M Y H:i') ?? '-' }}</td>
                            <td class="text-end pe-4">
                                <a href="{{ route('campaigns.show', $campaign) }}"
                                    class="btn btn-link btn-sm text-dark p-0">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Belum ada campaign</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($campaigns->hasPages())
            <div class="card-footer bg-white">
                {{ $campaigns->links() }}
            </div>
        @endif
    </div>
@endsection
