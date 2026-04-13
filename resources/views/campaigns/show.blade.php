<!-- resources/views/campaigns/show.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <a href="{{ route('campaigns.index') }}" class="text-muted text-decoration-none small">Campaigns</a>
            <span class="text-muted small mx-1">/</span>
            <h1 class="d-inline">{{ $campaign->name }}</h1>
        </div>
        @if (in_array($campaign->status, ['scheduled', 'failed']))
            <form action="{{ route('campaigns.dispatch', $campaign) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-dark btn-sm" onclick="return confirm('Dispatch campaign sekarang?')">
                    Dispatch Sekarang
                </button>
            </form>
        @endif
    </div>

    <!-- Info Campaign -->
    <div class="row g-4 mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="text-muted small">Type</div>
                            <div class="fw-medium">{{ $campaign->type }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted small">Channel</div>
                            <div class="fw-medium">{{ strtoupper($campaign->channel) }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted small">Status</div>
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
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted small">Jadwal</div>
                            <div class="fw-medium">{{ $campaign->scheduled_at?->format('d M Y H:i') ?? '-' }}</div>
                        </div>
                        @if ($campaign->started_at)
                            <div class="col-md-3">
                                <div class="text-muted small">Mulai</div>
                                <div class="fw-medium">{{ $campaign->started_at->format('d M Y H:i') }}</div>
                            </div>
                        @endif
                        @if ($campaign->finished_at)
                            <div class="col-md-3">
                                <div class="text-muted small">Selesai</div>
                                <div class="fw-medium">{{ $campaign->finished_at->format('d M Y H:i') }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="row g-3 text-center">
                        <div class="col-4">
                            <div class="text-success fw-semibold fs-5">{{ $stats['sent'] }}</div>
                            <div class="text-muted small">Terkirim</div>
                        </div>
                        <div class="col-4">
                            <div class="text-danger fw-semibold fs-5">{{ $stats['failed'] }}</div>
                            <div class="text-muted small">Gagal</div>
                        </div>
                        <div class="col-4">
                            <div class="text-secondary fw-semibold fs-5">{{ $stats['skipped'] }}</div>
                            <div class="text-muted small">Dilewati</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Log Penerima -->
    <div class="card">
        <div class="card-header">Log Pengiriman</div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Nama</th>
                        <th>Phone / Email</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                        <th>Waktu Kirim</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recipients as $recipient)
                        <tr>
                            <td class="ps-4">{{ $recipient->name ?? '-' }}</td>
                            <td class="text-muted small">
                                {{ $campaign->isWa() ? $recipient->phone : $recipient->email ?? '-' }}</td>
                            <td>
                                @php
                                    $color =
                                        ['sent' => 'success', 'failed' => 'danger', 'skipped' => 'secondary'][
                                            $recipient->status
                                        ] ?? 'secondary';
                                @endphp
                                <span
                                    class="badge bg-{{ $color }} bg-opacity-10 text-{{ $color }}">{{ $recipient->status }}</span>
                            </td>
                            <td class="text-muted small">{{ $recipient->error_message ?? '-' }}</td>
                            <td class="text-muted small">{{ $recipient->sent_at?->format('d M Y H:i:s') ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Belum ada log</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($recipients->hasPages())
            <div class="card-footer bg-white">
                {{ $recipients->links() }}
            </div>
        @endif
    </div>
@endsection
