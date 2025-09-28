@extends('layouts.kepalagudang')

@push('styles')
@endpush

@section('content')
    @php
        // default agar tidak terjadi "Undefined variable"
        $jenis  = $jenis  ?? collect();
        $tipe   = $tipe   ?? collect();
        $vendor = $vendor ?? collect();
        $region = $region ?? collect();
    @endphp

    <h4 class="page-title"><i class="bi bi-plus-circle me-2"></i> Tambah Data Sparepart</h4>
    <p class="page-subtitle">Kelola data jenis sparepart, tipe sparepart, dan vendor</p>

    <div class="card shadow-sm">
        <div class="card-body">
            <ul class="nav nav-tabs mb-4" id="sparepartTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="jenis-tab" data-bs-toggle="tab" data-bs-target="#jenis"
                        type="button" role="tab" aria-controls="jenis" aria-selected="true">
                        <i class="bi bi-grid me-1"></i> Jenis Sparepart
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tipe-tab" data-bs-toggle="tab" data-bs-target="#tipe" type="button"
                        role="tab" aria-controls="tipe" aria-selected="false">
                        <i class="bi bi-tag me-1"></i> Tipe Sparepart
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="vendor-tab" data-bs-toggle="tab" data-bs-target="#vendor" type="button"
                        role="tab" aria-controls="vendor" aria-selected="false">
                        <i class="bi bi-building me-1"></i> Vendor
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="region-tab" data-bs-toggle="tab" data-bs-target="#region" type="button"
                        role="tab" aria-controls="region" aria-selected="false">
                        <i class="bi bi-globe-europe-africa me-1"></i> Region
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button"
                        role="tab" aria-controls="users" aria-selected="false">
                        <i class="bi bi-person-gear me-1"></i> User
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="sparepartTabsContent">

                <!-- Tab Jenis Sparepart (default active) -->
                <div class="tab-pane fade show active" id="jenis" role="tabpanel" aria-labelledby="jenis-tab">
                    <form id="formJenis" class="simple-form" action="{{ route('kepalagudang.jenis.store') }}"
                        method="POST">
                        @csrf
                        <input type="hidden" name="id" id="jenisId">

                        <div class="mb-4">
                            <label for="namaJenis" class="form-label required-field">Nama Jenis Sparepart</label>
                            <input type="text" class="form-control form-control-lg" id="namaJenis" name="nama"
                                placeholder="Masukkan nama jenis sparepart" required value="{{ old('nama') }}">
                        </div>

                        <div class="mb-4">
                            <label for="kategoriJenis" class="form-label required-field">Kategori</label>
                            <select class="form-select form-select-lg" id="kategoriJenis" name="kategori" required>
                                <option value="">Pilih Kategori</option>
                                <option value="aset" {{ old('kategori') == 'aset' ? 'selected' : '' }}>Aset</option>
                                <option value="non-aset" {{ old('kategori') == 'non-aset' ? 'selected' : '' }}>Non Aset</option>
                            </select>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-save me-1"></i> Simpan Jenis Sparepart
                            </button>
                        </div>
                    </form>

                    <!-- Tabel Jenis Sparepart -->
                    <div class="table-responsive mt-4">
                        <h5 class="mb-3"><i class="bi bi-list-ul me-2"></i> Daftar Jenis Sparepart</h5>
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Jenis</th>
                                    <th>Kategori</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($jenis as $index => $j)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $j->nama ?? '-' }}</td>
                                        <td>
                                            <span class="badge {{ ($j->kategori ?? '') === 'aset' ? 'badge-aset' : 'badge-non-aset' }}">
                                                {{ $j->kategori ?? '-' }}
                                            </span>
                                        </td>
                                        <td>
                                            <!-- Tombol Edit -->
                                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                                data-bs-target="#editJenisModal{{ $j->id }}">
                                                <i class="bi bi-pencil"></i>
                                            </button>

                                            <!-- Modal Edit -->
                                            <div class="modal fade" id="editJenisModal{{ $j->id }}" tabindex="-1"
                                                aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="{{ route('kepalagudang.jenis.update', $j->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('PUT')

                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Edit Jenis Sparepart</h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal"></button>
                                                            </div>

                                                            <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <label>Nama Jenis</label>
                                                                    <input type="text" name="nama"
                                                                        class="form-control" value="{{ $j->nama ?? '' }}" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label>Kategori</label>
                                                                    <select name="kategori" class="form-control" required>
                                                                        <option value="aset" {{ ($j->kategori ?? '') == 'aset' ? 'selected' : '' }}>Aset</option>
                                                                        <option value="non-aset" {{ ($j->kategori ?? '') == 'non-aset' ? 'selected' : '' }}>Non-Aset</option>
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">Batal</button>
                                                                <button type="submit" class="btn btn-primary">Simpan</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Tombol Hapus -->
                                            <form action="{{ route('kepalagudang.jenis.destroy', $j->id) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('Yakin ingin menghapus data ini?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center">Tidak ada data</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tab Tipe Sparepart -->
                <div class="tab-pane fade" id="tipe" role="tabpanel" aria-labelledby="tipe-tab">
                    <form id="formTipe" class="simple-form" action="{{ route('kepalagudang.tipe.store') }}"
                        method="POST">
                        @csrf
                        <input type="hidden" name="id" id="tipeId">

                        <div class="mb-4">
                            <label for="namaTipe" class="form-label required-field">Nama Tipe Sparepart</label>
                            <input type="text" class="form-control form-control-lg" id="namaTipe" name="nama"
                                placeholder="Masukkan nama tipe sparepart" required value="{{ old('nama') }}">
                        </div>

                        <div class="mb-4">
                            <label for="kategoriTipe" class="form-label required-field">Kategori</label>
                            <select class="form-select form-select-lg" id="kategoriTipe" name="kategori" required>
                                <option value="">Pilih Kategori</option>
                                <option value="aset" {{ old('kategori') == 'aset' ? 'selected' : '' }}>Aset</option>
                                <option value="non-aset" {{ old('kategori') == 'non-aset' ? 'selected' : '' }}>Non Aset</option>
                            </select>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-save me-1"></i> Simpan Tipe Sparepart
                            </button>
                        </div>
                    </form>

                    <!-- Tabel Tipe Sparepart -->
                    <div class="table-responsive mt-4">
                        <h5 class="mb-3"><i class="bi bi-list-ul me-2"></i> Daftar Tipe Sparepart</h5>
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Tipe</th>
                                    <th>Kategori</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($tipe as $index => $t)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $t->nama ?? '-' }}</td>
                                        <td>
                                            <span class="badge {{ ($t->kategori ?? '') === 'aset' ? 'badge-aset' : 'badge-non-aset' }}">
                                                {{ $t->kategori ?? '-' }}
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                                data-bs-target="#editTipeModal{{ $t->id }}">
                                                <i class="bi bi-pencil"></i>
                                            </button>

                                            <div class="modal fade" id="editTipeModal{{ $t->id }}" tabindex="-1"
                                                aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="{{ route('kepalagudang.tipe.update', $t->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('PUT')

                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Edit Tipe Sparepart</h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal"></button>
                                                            </div>

                                                            <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <label>Nama Tipe</label>
                                                                    <input type="text" name="nama"
                                                                        class="form-control" value="{{ $t->nama ?? '' }}" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label>Kategori</label>
                                                                    <select name="kategori" class="form-control" required>
                                                                        <option value="aset" {{ ($t->kategori ?? '') == 'aset' ? 'selected' : '' }}>Aset</option>
                                                                        <option value="non-aset" {{ ($t->kategori ?? '') == 'non-aset' ? 'selected' : '' }}>Non-Aset</option>
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">Batal</button>
                                                                <button type="submit" class="btn btn-primary">Simpan</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            <form action="{{ route('kepalagudang.tipe.destroy', $t->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('Yakin ingin menghapus data ini?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center">Tidak ada data</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tab Vendor -->
                <div class="tab-pane fade" id="vendor" role="tabpanel" aria-labelledby="vendor-tab">
                    <form id="formVendor" class="simple-form" action="{{ route('kepalagudang.vendor.store') }}"
                        method="POST">
                        @csrf
                        <input type="hidden" name="id" id="vendorId">

                        <div class="mb-4">
                            <label for="namaVendor" class="form-label required-field">Nama Vendor</label>
                            <input type="text" class="form-control form-control-lg" id="namaVendor" name="nama"
                                placeholder="Masukkan nama vendor" required value="{{ old('nama') }}">
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-save me-1"></i> Simpan Vendor
                            </button>
                        </div>
                    </form>

                    <!-- Tabel Vendor -->
                    <div class="table-responsive mt-4">
                        <h5 class="mb-3"><i class="bi bi-list-ul me-2"></i> Daftar Vendor</h5>
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Vendor</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($vendor as $index => $v)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $v->nama ?? '-' }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                                data-bs-target="#editVendorModal{{ $v->id }}">
                                                <i class="bi bi-pencil"></i>
                                            </button>

                                            <div class="modal fade" id="editVendorModal{{ $v->id }}" tabindex="-1"
                                                aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="{{ route('kepalagudang.vendor.update', $v->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('PUT')

                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Edit Vendor</h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal"></button>
                                                            </div>

                                                            <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <label>Nama Vendor</label>
                                                                    <input type="text" name="nama" class="form-control"
                                                                        value="{{ $v->nama ?? '' }}" required>
                                                                </div>
                                                            </div>

                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">Batal</button>
                                                                <button type="submit" class="btn btn-primary">Simpan</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            <form action="{{ route('kepalagudang.vendor.destroy', $v->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('Yakin ingin menghapus vendor ini?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center">Tidak ada data</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tab Region -->
                <div class="tab-pane fade" id="region" role="tabpanel" aria-labelledby="region-tab">
                    <form id="formRegion" class="simple-form" action="{{ route('kepalagudang.region.store') }}"
                        method="POST">
                        @csrf
                        <input type="hidden" name="id" id="regionId">

                        <div class="mb-4">
                            <label for="namaRegion" class="form-label required-field">Nama Region</label>
                            <input type="text" class="form-control form-control-lg" id="namaRegion"
                                name="nama_region" placeholder="Masukkan nama region" required value="{{ old('nama_region') }}">
                        </div>

                        <div class="mb-4">
                            <label for="kodeRegion" class="form-label required-field">Kode Region</label>
                            <input type="text" class="form-control form-control-lg" id="kodeRegion"
                                name="kode_region" placeholder="Masukkan kode region" required value="{{ old('kode_region') }}">
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-save me-1"></i> Simpan Region
                            </button>
                        </div>
                    </form>

                    <!-- Tabel Region -->
                    <div class="table-responsive mt-4">
                        <h5 class="mb-3"><i class="bi bi-list-ul me-2"></i> Daftar Region</h5>
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Region</th>
                                    <th>Kode Region</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($region as $index => $r)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $r->nama_region ?? '-' }}</td>
                                        <td>{{ $r->kode_region ?? '-' }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                                data-bs-target="#editRegionModal{{ $r->id }}">
                                                <i class="bi bi-pencil"></i>
                                            </button>

                                            <div class="modal fade" id="editRegionModal{{ $r->id }}" tabindex="-1"
                                                aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="{{ route('kepalagudang.region.update', $r->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('PUT')

                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Edit Region</h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal"></button>
                                                            </div>

                                                            <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <label>Nama Region</label>
                                                                    <input type="text" name="nama_region"
                                                                        class="form-control"
                                                                        value="{{ $r->nama_region ?? '' }}" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label>Kode Region</label>
                                                                    <input type="text" name="kode_region"
                                                                        class="form-control"
                                                                        value="{{ $r->kode_region ?? '' }}" required>
                                                                </div>
                                                            </div>

                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">Batal</button>
                                                                <button type="submit" class="btn btn-primary">Simpan</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            <form action="{{ route('kepalagudang.region.destroy', $r->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('Yakin ingin menghapus data ini?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center">Tidak ada data</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tab Users -->
                <div class="tab-pane fade" id="users" role="tabpanel" aria-labelledby="users-tab">
                    <div class="d-flex justify-content-center align-items-start py-4">
                        <a href="{{ route('kepalagudang.user.index') }}" class="btn btn-primary btn-lg">
                            Kelola User
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmDeleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus data ini?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Hapus</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const sessionTab = {!! json_encode(session('activeTab', '')) !!};
            if (sessionTab) {
                const el = document.querySelector(`[data-bs-target="${sessionTab}"]`);
                if (el) {
                    new bootstrap.Tab(el).show();
                    return;
                }
            }

            let activeTab = localStorage.getItem("activeTab");
            if (activeTab) {
                let tabElement = document.querySelector(`[data-bs-target="${activeTab}"]`);
                if (tabElement) {
                    let tab = new bootstrap.Tab(tabElement);
                    tab.show();
                }
            }

            const tabButtons = document.querySelectorAll('button[data-bs-toggle="tab"]');
            tabButtons.forEach(button => {
                button.addEventListener("shown.bs.tab", function(e) {
                    let target = e.target.getAttribute("data-bs-target");
                    localStorage.setItem("activeTab", target);
                });
            });
        });
    </script>
@endpush