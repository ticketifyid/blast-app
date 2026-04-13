<!-- resources/views/templates/edit.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="page-header">
        <a href="{{ route('templates.index') }}" class="text-muted text-decoration-none small">Templates</a>
        <span class="text-muted small mx-1">/</span>
        <h1 class="d-inline">Edit Template</h1>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('templates.update', $template) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama Template</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $template->name) }}">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Channel</label>
                        <select name="channel" id="channel" class="form-select @error('channel') is-invalid @enderror"
                            onchange="toggleSubject()">
                            <option value="wa" {{ old('channel', $template->channel) === 'wa' ? 'selected' : '' }}>
                                WhatsApp</option>
                            <option value="email" {{ old('channel', $template->channel) === 'email' ? 'selected' : '' }}>
                                Email</option>
                        </select>
                        @error('channel')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3" id="subject-field"
                    style="{{ old('channel', $template->channel) === 'wa' ? 'display:none' : '' }}">
                    <label class="form-label">Subject</label>
                    <input type="text" name="subject" class="form-control @error('subject') is-invalid @enderror"
                        value="{{ old('subject', $template->subject) }}">
                    @error('subject')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                @if ($variables)
                    <div class="mb-3">
                        <label class="form-label">Variabel</label>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach ($variables as $var)
                                <button type="button" class="btn btn-outline-secondary btn-sm"
                                    onclick="insertVariable('{{ $var }}')">
                                    {{ '{{' . $var . '}}' }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="mb-3">
                    <label class="form-label">Body</label>
                    <textarea name="body" id="body" class="form-control @error('body') is-invalid @enderror" rows="8">{{ old('body', $template->body) }}</textarea>
                    @error('body')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-dark btn-sm">Update</button>
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

        function insertVariable(varName) {
            const textarea = document.getElementById('body');
            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            const text = textarea.value;
            textarea.value = text.substring(0, start) + '{{' + varName + '}}' + text.substring(end);
            textarea.focus();
            textarea.selectionStart = textarea.selectionEnd = start + varName.length + 4;
        }
    </script>
@endpush
