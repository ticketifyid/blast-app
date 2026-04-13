<!-- resources/views/config/index.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="page-header d-flex justify-content-between align-items-center">
        <h1>Config</h1>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">WhatsApp (MPWA)</div>
                <div class="card-body">
                    <form action="{{ route('config.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="type" value="whatsapp">
                        <div class="mb-3">
                            <label class="form-label">Base URL</label>
                            <input type="url" name="whatsapp[base_url]"
                                class="form-control @error('whatsapp.base_url') is-invalid @enderror"
                                value="{{ old('whatsapp.base_url', $whatsapp['base_url'] ?? '') }}">
                            @error('whatsapp.base_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">API Key</label>
                            <input type="text" name="whatsapp[api_key]"
                                class="form-control @error('whatsapp.api_key') is-invalid @enderror"
                                value="{{ old('whatsapp.api_key', $whatsapp['api_key'] ?? '') }}">
                            @error('whatsapp.api_key')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Sender (Nomor Device)</label>
                            <input type="text" name="whatsapp[sender]"
                                class="form-control @error('whatsapp.sender') is-invalid @enderror"
                                value="{{ old('whatsapp.sender', $whatsapp['sender'] ?? '') }}" placeholder="628xxxxxxxxxx">
                            @error('whatsapp.sender')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-dark btn-sm">Simpan</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">SMTP Email</div>
                <div class="card-body">
                    <form action="{{ route('config.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="type" value="smtp">
                        <div class="row g-3 mb-3">
                            <div class="col-8">
                                <label class="form-label">Host</label>
                                <input type="text" name="smtp[host]"
                                    class="form-control @error('smtp.host') is-invalid @enderror"
                                    value="{{ old('smtp.host', $smtp['host'] ?? '') }}">
                                @error('smtp.host')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-4">
                                <label class="form-label">Port</label>
                                <input type="number" name="smtp[port]"
                                    class="form-control @error('smtp.port') is-invalid @enderror"
                                    value="{{ old('smtp.port', $smtp['port'] ?? '') }}">
                                @error('smtp.port')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="smtp[username]"
                                class="form-control @error('smtp.username') is-invalid @enderror"
                                value="{{ old('smtp.username', $smtp['username'] ?? '') }}">
                            @error('smtp.username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="smtp[password]"
                                class="form-control @error('smtp.password') is-invalid @enderror"
                                value="{{ old('smtp.password', $smtp['password'] ?? '') }}">
                            @error('smtp.password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Encryption</label>
                            <select name="smtp[encryption]"
                                class="form-select @error('smtp.encryption') is-invalid @enderror">
                                <option value="tls" {{ ($smtp['encryption'] ?? '') === 'tls' ? 'selected' : '' }}>TLS
                                </option>
                                <option value="ssl" {{ ($smtp['encryption'] ?? '') === 'ssl' ? 'selected' : '' }}>SSL
                                </option>
                            </select>
                            @error('smtp.encryption')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="form-label">From Email</label>
                                <input type="email" name="smtp[from_email]"
                                    class="form-control @error('smtp.from_email') is-invalid @enderror"
                                    value="{{ old('smtp.from_email', $smtp['from_email'] ?? '') }}">
                                @error('smtp.from_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-6">
                                <label class="form-label">From Name</label>
                                <input type="text" name="smtp[from_name]"
                                    class="form-control @error('smtp.from_name') is-invalid @enderror"
                                    value="{{ old('smtp.from_name', $smtp['from_name'] ?? '') }}">
                                @error('smtp.from_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <button type="submit" class="btn btn-dark btn-sm">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
