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
                    {{-- WA: plain textarea --}}
                    <textarea name="body" id="body-wa" class="form-control @error('body') is-invalid @enderror" rows="8"
                        style="{{ old('channel', $template->channel) === 'email' ? 'display:none' : '' }}">{{ old('body', $template->channel === 'wa' ? $template->body : '') }}</textarea>
                    {{-- Email: Quill editor --}}
                    <div id="quill-wrapper" style="{{ old('channel', $template->channel) === 'wa' ? 'display:none' : '' }}">
                        <div id="quill-editor" style="min-height:200px"></div>
                        <textarea name="body" id="body-email" class="d-none"></textarea>
                    </div>
                    @error('body')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
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
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
    <script>
        const quill = new Quill('#quill-editor', {
            theme: 'snow',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline'],
                    [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                    [{ 'color': [] }, { 'background': [] }],
                    [{ 'align': [] }],
                    ['link', 'image'],
                    ['clean']
                ]
            }
        });

        // Load existing email body into Quill
        @if(old('channel', $template->channel) === 'email')
            quill.root.innerHTML = @json(old('body', $template->body));
        @endif

        function toggleSubject() {
            const channel = document.getElementById('channel').value;
            document.getElementById('subject-field').style.display = channel === 'email' ? 'block' : 'none';
            document.getElementById('body-wa').style.display = channel === 'wa' ? 'block' : 'none';
            document.getElementById('quill-wrapper').style.display = channel === 'email' ? 'block' : 'none';
            document.getElementById('body-wa').name = channel === 'wa' ? 'body' : '';
            document.getElementById('body-email').name = channel === 'email' ? 'body' : '';
        }

        function insertVariable(varName) {
            const channel = document.getElementById('channel').value;
            if (channel === 'email') {
                const range = quill.getSelection(true);
                quill.insertText(range.index, '{{' + varName + '}}');
                quill.setSelection(range.index + varName.length + 4);
            } else {
                const textarea = document.getElementById('body-wa');
                const start = textarea.selectionStart;
                const end = textarea.selectionEnd;
                const text = textarea.value;
                textarea.value = text.substring(0, start) + '{{' + varName + '}}' + text.substring(end);
                textarea.focus();
                textarea.selectionStart = textarea.selectionEnd = start + varName.length + 4;
            }
        }

        // Sync Quill content to hidden textarea before submit
        document.querySelector('form').addEventListener('submit', function () {
            const channel = document.getElementById('channel').value;
            if (channel === 'email') {
                document.getElementById('body-email').value = quill.root.innerHTML;
            }
        });

        // Init toggle on page load
        toggleSubject();
    </script>
@endpush
