<!-- resources/views/contact-groups/index.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="page-header d-flex justify-content-between align-items-center">
        <h1>Phonebook</h1>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#importModal">
                Import Excel
            </button>
            <button class="btn btn-dark btn-sm" data-bs-toggle="modal" data-bs-target="#createModal">
                Buat Group
            </button>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Nama Group</th>
                        <th>Jumlah Kontak</th>
                        <th>Dibuat</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($groups as $group)
                        <tr>
                            <td class="ps-4">
                                <a href="{{ route('contact-groups.show', $group) }}"
                                    class="text-dark text-decoration-none fw-medium">
                                    {{ $group->name }}
                                </a>
                            </td>
                            <td class="text-muted">{{ $group->contacts_count }} kontak</td>
                            <td class="text-muted">{{ $group->created_at->format('d M Y') }}</td>
                            <td class="text-end pe-4">
                                <form action="{{ route('contact-groups.destroy', $group) }}" method="POST"
                                    onsubmit="return confirm('Hapus group ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-link btn-sm text-danger p-0">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">Belum ada group</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Buat Group -->
    <div class="modal fade" id="createModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('contact-groups.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Buat Group</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Group</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name') }}" autofocus>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary btn-sm"
                            data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-dark btn-sm">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Import Excel -->
    <div class="modal fade" id="importModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Import Kontak dari Excel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Step 1: Upload -->
                    <div id="step-upload">
                        <div class="mb-3">
                            <label class="form-label">File Excel</label>
                            <input type="file" id="import-file" class="form-control" accept=".xlsx,.xls,.csv">
                        </div>
                        <button type="button" class="btn btn-dark btn-sm" onclick="uploadImportFile()">Lanjut</button>
                    </div>

                    <!-- Step 2: Mapping -->
                    <div id="step-mapping" style="display:none;">
                        <form action="{{ route('phonebook.import') }}" method="POST" id="import-form">
                            @csrf
                            <input type="hidden" name="path" id="import-path">
                            <div class="mb-3">
                                <label class="form-label">Nama Group</label>
                                <input type="text" name="group_name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Kolom Nama</label>
                                <select name="name_column" id="map-name" class="form-select" required></select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Kolom Phone <span class="text-muted">(opsional)</span></label>
                                <select name="phone_column" id="map-phone" class="form-select">
                                    <option value="">— Tidak ada —</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Kolom Email <span class="text-muted">(opsional)</span></label>
                                <select name="email_column" id="map-email" class="form-select">
                                    <option value="">— Tidak ada —</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-dark btn-sm">Import</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        async function uploadImportFile() {
            const file = document.getElementById('import-file').files[0];
            if (!file) return alert('Pilih file terlebih dahulu.');

            const formData = new FormData();
            formData.append('file', file);
            formData.append('_token', '{{ csrf_token() }}');

            const res = await fetch('{{ route('phonebook.upload') }}', {
                method: 'POST',
                body: formData
            });
            const data = await res.json();

            document.getElementById('import-path').value = data.path;

            const selects = ['map-name', 'map-phone', 'map-email'];
            selects.forEach(id => {
                const sel = document.getElementById(id);
                const hasEmpty = id !== 'map-name';
                sel.innerHTML = hasEmpty ? '<option value="">— Tidak ada —</option>' : '';
                data.headers.forEach(h => {
                    sel.innerHTML += `<option value="${h}">${h}</option>`;
                });
            });

            document.getElementById('step-upload').style.display = 'none';
            document.getElementById('step-mapping').style.display = 'block';
        }
    </script>
@endpush
