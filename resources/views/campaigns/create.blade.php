<!-- resources/views/campaigns/create.blade.php -->
@extends('layouts.app')

@section('content')
<div class="page-header">
    <a href="{{ route('campaigns.index') }}" class="text-muted text-decoration-none small">Campaigns</a>
    <span class="text-muted small mx-1">/</span>
    <h1 class="d-inline">Buat Campaign</h1>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <strong>Gagal menyimpan campaign:</strong>
        <ul class="mb-0 mt-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('campaigns.store') }}" method="POST" enctype="multipart/form-data" id="campaign-form">
@csrf

<div class="row g-4">
    <div class="col-md-8">

        <!-- Info Dasar -->
        <div class="card mb-4">
            <div class="card-header">Info Dasar</div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Nama Campaign</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}">
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Type</label>
                        <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" onchange="toggleType()">
                            <option value="transactional" {{ old('type') === 'transactional' ? 'selected' : '' }}>Transaksional</option>
                            <option value="marketing" {{ old('type') === 'marketing' ? 'selected' : '' }}>Pemasaran</option>
                        </select>
                        @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Channel</label>
                        <select name="channel" id="channel" class="form-select @error('channel') is-invalid @enderror" onchange="toggleChannel()">
                            <option value="wa" {{ old('channel') === 'wa' ? 'selected' : '' }}>WhatsApp</option>
                            <option value="email" {{ old('channel') === 'email' ? 'selected' : '' }}>Email</option>
                        </select>
                        @error('channel') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Kontak (Transaksional) -->
        <div class="card mb-4" id="section-transactional">
            <div class="card-header">Upload Excel</div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">File Excel</label>
                    <input type="file" id="excel-file" name="file" class="form-control @error('file') is-invalid @enderror" accept=".xlsx,.xls,.csv" onchange="loadHeaders()">
                    @error('file') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Mapping kolom (muncul setelah upload) -->
                <div id="mapping-section" style="display:none;">
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Kolom Nama</label>
                            <select name="name_column" id="map-name" class="form-select"></select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Kolom Phone <span class="text-muted">(opsional)</span></label>
                            <select name="phone_column" id="map-phone" class="form-select">
                                <option value="">— Tidak ada —</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Kolom Email <span class="text-muted">(opsional)</span></label>
                            <select name="email_column" id="map-email" class="form-select">
                                <option value="">— Tidak ada —</option>
                            </select>
                        </div>
                    </div>

                    <!-- QR Code (khusus WA) -->
                    <div id="qr-section">
                        <hr>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="use_qr" id="use_qr" value="1" {{ old('use_qr') ? 'checked' : '' }} onchange="toggleQr()">
                            <label class="form-check-label" for="use_qr">Generate QR Code</label>
                        </div>
                        <div id="qr-column-section" style="{{ old('use_qr') ? '' : 'display:none' }}">
                            <label class="form-label">Kolom Value QR</label>
                            <select name="qr_column" id="map-qr" class="form-select"></select>
                        </div>
                    </div>

                    <!-- Chip variabel -->
                    <div id="variables-section" class="mt-3">
                        <label class="form-label text-muted small">Variabel dari Excel</label>
                        <div id="variable-chips" class="d-flex flex-wrap gap-2"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kontak (Marketing) -->
        <div class="card mb-4" id="section-marketing" style="display:none;">
            <div class="card-header">Pilih Kontak</div>
            <div class="card-body">
                @if($contactGroups->isEmpty())
                    <p class="text-muted small mb-0">Belum ada group kontak. <a href="{{ route('contact-groups.index') }}">Buat group dulu</a>.</p>
                @else
                    @foreach($contactGroups as $group)
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="contact_groups[]" value="{{ $group->id }}" id="group-{{ $group->id }}" {{ in_array($group->id, old('contact_groups', [])) ? 'checked' : '' }}>
                        <label class="form-check-label" for="group-{{ $group->id }}">
                            {{ $group->name }} <span class="text-muted small">({{ $group->contacts_count }} kontak)</span>
                        </label>
                    </div>
                    @endforeach
                    @error('contact_groups') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                @endif
            </div>
        </div>

        <!-- Pesan -->
        <div class="card mb-4">
            <div class="card-header">Pesan</div>
            <div class="card-body">
                <div class="mb-3" id="subject-field" style="display:none;">
                    <label class="form-label">Subject</label>
                    <input type="text" id="subject" name="subject" class="form-control @error('subject') is-invalid @enderror" value="{{ old('subject') }}">
                    @error('subject') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Chip variabel marketing -->
                <div id="marketing-variables" style="display:none;" class="mb-3">
                    <label class="form-label text-muted small">Variabel tersedia</label>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="insertVar('name')">@{{name}}</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="insertVar('phone')">@{{phone}}</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="insertVar('email')">@{{email}}</button>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Body</label>
                    <textarea name="body" id="body" class="form-control @error('body') is-invalid @enderror" rows="8">{{ old('body') }}</textarea>
                    @error('body') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                @if($templates->isNotEmpty())
                <div class="mb-0">
                    <label class="form-label text-muted small">Atau pakai template</label>
                    <select name="template_id" id="template-select" class="form-select form-select-sm" onchange="loadTemplate()">
                        <option value="">— Pilih template —</option>
                        @foreach($templates as $template)
                            <option value="{{ $template->id }}" data-body="{{ $template->body }}" data-subject="{{ $template->subject }}" data-channel="{{ $template->channel }}">
                                {{ $template->name }} ({{ strtoupper($template->channel) }})
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif
            </div>
        </div>

        <!-- Gambar (khusus WA) -->
        <div class="card mb-4" id="image-section" >
            <div class="card-header">Gambar <span class="text-muted fw-normal small">(opsional, khusus WA)</span></div>
            <div class="card-body">
                <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept=".jpg,.jpeg,.png">
                @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                <div class="form-text">Kalau diisi, pesan akan dikirim sebagai gambar dengan caption. Maks 2MB.</div>
            </div>
        </div>

    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">Jadwal</div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Tanggal & Waktu</label>
                    <input type="datetime-local" name="scheduled_at" class="form-control @error('scheduled_at') is-invalid @enderror" value="{{ old('scheduled_at') }}">
                    @error('scheduled_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <button type="submit" class="btn btn-dark w-100 btn-sm">Buat Campaign</button>
                <a href="{{ route('campaigns.index') }}" class="btn btn-outline-secondary w-100 btn-sm mt-2">Batal</a>
            </div>
        </div>
    </div>
</div>
</form>
@endsection

@push('scripts')
<script>
const templates = @json($templates->keyBy('id'));

function toggleType() {
    const type = document.getElementById('type').value;
    document.getElementById('section-transactional').style.display = type === 'transactional' ? 'block' : 'none';
    document.getElementById('section-marketing').style.display     = type === 'marketing' ? 'block' : 'none';
    document.getElementById('marketing-variables').style.display   = type === 'marketing' ? 'block' : 'none';
}

function toggleChannel() {
    const channel = document.getElementById('channel').value;
    document.getElementById('subject-field').style.display  = channel === 'email' ? 'block' : 'none';
    document.getElementById('image-section').style.display  = channel === 'wa' ? 'block' : 'none';
    document.getElementById('qr-section').style.display     = 'block';
}

function toggleQr() {
    const checked = document.getElementById('use_qr').checked;
    document.getElementById('qr-column-section').style.display = checked ? 'block' : 'none';
}

async function loadHeaders() {
    const fileInput = document.getElementById('excel-file');
    const file      = fileInput.files[0];
    if (!file) return;

    const formData = new FormData();
    formData.append('file', file);
    formData.append('_token', '{{ csrf_token() }}');

    let headers;
    try {
        const res = await fetch('{{ route('excel.headers') }}', {
            method: 'POST',
            headers: { 'Accept': 'application/json' },
            body: formData,
        });

        const data = await res.json();

        if (!res.ok) {
            const msg = data.message || Object.values(data.errors || {}).flat().join(', ') || 'Gagal membaca file.';
            alert('Error: ' + msg);
            return;
        }

        headers = data.headers;
    } catch (e) {
        alert('Gagal membaca file Excel. Pastikan formatnya xlsx, xls, atau csv.');
        return;
    }

    if (!headers || headers.length === 0) {
        alert('Header tidak ditemukan. Pastikan baris pertama Excel berisi nama kolom.');
        return;
    }

    // Isi semua dropdown mapping
    const selects = {
        'map-name':  false,
        'map-phone': true,
        'map-email': true,
        'map-qr':    true,
    };

    Object.entries(selects).forEach(([id, hasEmpty]) => {
        const sel = document.getElementById(id);
        sel.innerHTML = hasEmpty ? '<option value="">— Tidak ada —</option>' : '';
        headers.forEach(h => sel.innerHTML += `<option value="${h}">${h}</option>`);
    });

    // Chip variabel
    const chips = document.getElementById('variable-chips');
    chips.innerHTML = '';
    headers.forEach(h => {
        const btn = document.createElement('button');
        btn.type      = 'button';
        btn.className = 'btn btn-outline-secondary btn-sm';
        btn.textContent = '{' + '{' + h + '}' + '}';
        btn.onclick   = () => insertVar(h);
        chips.appendChild(btn);
    });

    document.getElementById('mapping-section').style.display = 'block';
}

function insertVar(varName) {
    const textarea = document.getElementById('body');
    const start    = textarea.selectionStart;
    const end      = textarea.selectionEnd;
    textarea.value = textarea.value.substring(0, start) + '{' + '{' + varName + '}' + '}' + textarea.value.substring(end);
    textarea.focus();
    textarea.selectionStart = textarea.selectionEnd = start + varName.length + 4;
}

function loadTemplate() {
    const select   = document.getElementById('template-select');
    const selected = select.options[select.selectedIndex];
    if (!selected.value) return;

    document.getElementById('body').value = selected.dataset.body;

    const subject = document.getElementById('subject');
    if (subject) subject.value = selected.dataset.subject ?? '';
}

// Init
toggleType();
toggleChannel();

// Restore mapping section jika ada old input (setelah validation error)
@if (old('name_column'))
(function () {
    const oldValues = {
        'map-name':  '{{ old('name_column') }}',
        'map-phone': '{{ old('phone_column') }}',
        'map-email': '{{ old('email_column') }}',
        'map-qr':    '{{ old('qr_column') }}',
    };
    Object.entries(oldValues).forEach(([id, val]) => {
        if (!val) return;
        const sel = document.getElementById(id);
        const opt = new Option(val, val, true, true);
        sel.add(opt);
    });
    document.getElementById('mapping-section').style.display = 'block';
})();
@endif
</script>
@endpush