<!-- resources/views/templates/create.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="page-header">
        <a href="{{ route('templates.index') }}" class="text-muted text-decoration-none small">Templates</a>
        <span class="text-muted small mx-1">/</span>
        <h1 class="d-inline">Buat Template</h1>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('templates.store') }}" method="POST">
                @csrf
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama Template</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name') }}">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Channel</label>
                        <select name="channel" id="channel" class="form-select @error('channel') is-invalid @enderror"
                            onchange="toggleSubject()">
                            <option value="wa" {{ old('channel') === 'wa' ? 'selected' : '' }}>WhatsApp</option>
                            <option value="email" {{ old('channel') === 'email' ? 'selected' : '' }}>Email</option>
                        </select>
                        @error('channel')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3" id="subject-field" style="{{ old('channel', 'wa') === 'wa' ? 'display:none' : '' }}">
                    <label class="form-label">Subject</label>
                    <input type="text" name="subject" class="form-control @error('subject') is-invalid @enderror"
                        value="{{ old('subject') }}">
                    @error('subject')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Body</label>
                    <textarea name="body" id="body" class="form-control @error('body') is-invalid @enderror" rows="8">{{ old('body') }}</textarea>
                    @error('body')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Gunakan <code>@{{ nama_variabel }}</code> untuk variabel dinamis.</div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-dark btn-sm">Simpan</button>
                    <a href="{{ route('templates.index') }}" class="btn btn-outline-secondary btn-sm">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function toggleSubject() {
            const channel = document.getElementById('channel').value;
            document.getElementById('subject-field').style.display = channel === 'email' ? 'block' : 'none';
        }
    </script>
@endpush
