@extends('layouts.kepalagudang')

@section('title', 'Daftar Sparepart - Kepalagudang')

@push('styles')
@endpush

@section('content')
    <input type="hidden" name="_token" id="csrf_token" value="{{ csrf_token() }}">

    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="fw-bold mb-0"><i class="bi bi-tools me-2"></i>Daftar Sparepart</h4>
                <p class="text-muted mb-0">Kelola data sparepart di gudang</p>
            </div>
            <div>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahSparepartModal">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Sparepart
                </button>
                <a href="{{ route('kepalagudang.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-4 col-md-6">
            <div class="dashboard-card p-4">
                <div class="card-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-tools"></i>
                </div>
                <h4 class="stats-number">{{ $totalQty }}</h4>
                <p class="stats-title">Total Sparepart</p>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="dashboard-card p-4">
                <div class="card-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-check-circle"></i>
                </div>
                <h4 class="stats-number">{{ $totalBaru }}</h4>
                <p class="stats-title">Sparepart Baru</p>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="dashboard-card p-4">
                <div class="card-icon bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-arrow-repeat"></i>
                </div>
                <h4 class="stats-number">{{ $totalLama }}</h4>
                <p class="stats-title">Sparepart Lama</p>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="filter-card">
        <h5 class="mb-3"><i class="bi bi-funnel me-2"></i>Filter Data</h5>
        <form method="GET" action="{{ route('kepalagudang.sparepart.index') }}">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="jenisFilter" class="form-label">Jenis Sparepart</label>
                    <select class="form-select" name="nama" id="jenisFilter">
                        <option value="">Semua Jenis</option>
                        @foreach ($jenis as $j)
                            <option value="{{ $j->id }}"
                                {{ (string) request('nama') === (string) $j->id ? 'selected' : '' }}>
                                {{ $j->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="statusFilter" class="form-label">Status Sparepart</label>
                    <select class="form-select" name="status" id="statusFilter">
                        <option value="">Semua Status</option>
                        <option value="sparepart baru" {{ request('status') == 'sparepart baru' ? 'selected' : '' }}>
                            Sparepart Baru</option>
                        <option value="sparepart lama" {{ request('status') == 'sparepart lama' ? 'selected' : '' }}>
                            Sparepart Lama</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="searchFilter" class="form-label">Cari Sparepart</label>
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Cari ID atau nama sparepart..."
                            name="search" value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="tanggalMulai" class="form-label">Tanggal Mulai</label>
                    <input type="date" class="form-control" id="tanggalMulai" name="tanggal_mulai"
                        value="{{ request('tanggal_mulai') }}">
                </div>
                <div class="col-md-4">
                    <label for="tanggalBerakhir" class="form-label">Tanggal Berakhir</label>
                    <input type="date" class="form-control" id="tanggalBerakhir" name="tanggal_berakhir"
                        value="{{ request('tanggal_berakhir') }}">
                </div>
                <div class="col-md-4">
                    <label for="kategoriFilter" class="form-label">Kategori Sparepart</label>
                    <select class="form-select" name="kategori" id="kategoriFilter">
                        <option value="">Semua Kategori</option>
                        <option value="aset" {{ request('kategori') == 'aset' ? 'selected' : '' }}>Aset</option>
                        <option value="non-aset" {{ request('kategori') == 'non-aset' ? 'selected' : '' }}>Non Aset
                        </option>
                    </select>
                </div>
                <div class="col-12 text-end">
                    <a href="{{ route('kepalagudang.sparepart.index') }}" class="btn btn-light me-2">
                        <i class="bi bi-arrow-clockwise me-1"></i> Reset
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-filter me-1"></i> Terapkan Filter
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="table-container">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID Sparepart</th>
                        <th>Jenis & Type</th>
                        <th>Quantity</th>
                        @if ($filterStatus === 'sparepart lama')
                            <th>Sparepart Lama</th>
                        @else
                            <th>Sparepart Baru</th>
                        @endif
                        <th>Kategori</th>
                        <th>Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($listBarang as $barang)
                        <tr>
                            <td><span class="fw-bold">{{ $barang->tiket_sparepart }}</span></td>
                            <td>
                                {{ $barang->jenisBarang?->nama ?? '-' }}
                                {{ $barang->tipeBarang?->nama ?? '-' }}
                            </td>
                            <td>{{ $barang->quantity }}</td>
                            @if ($filterStatus === 'sparepart lama')
                                <td>{{ $totalsPerTiket[$barang->tiket_sparepart]['sparepart lama'] ?? 0 }}</td>
                            @else
                                <td>{{ $totalsPerTiket[$barang->tiket_sparepart]['sparepart baru'] ?? 0 }}</td>
                            @endif
                            <td>{{ ucwords(str_replace('-', ' ', $barang->kategori)) }}</td>
                            <td>
                                <button class="btn btn-info btn-sm btn-detail"
                                    onclick="showDetail('{{ $barang->tiket_sparepart }}')" title="Detail">
                                    <i class="bi bi-eye"></i> Detail
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="bi bi-inbox display-4 d-block mb-2"></i>
                                Tidak ada data sparepart
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-3">
        <div class="text-muted">
            Menampilkan {{ $listBarang->firstItem() }} hingga {{ $listBarang->lastItem() }} dari
            {{ $listBarang->total() }} entri
        </div>
        <nav aria-label="Page navigation">
            {{ $listBarang->appends(request()->query())->links('pagination::bootstrap-5') }}
        </nav>
    </div>

    <!-- Modal Tambah Sparepart -->
    <div class="modal fade" id="tambahSparepartModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('kepalagudang.sparepart.store') }}" id="sparepartForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="tambahSparepartModalLabel"><i
                                class="bi bi-plus-circle me-2"></i>Tambah Sparepart Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @csrf
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="kategori" class="form-label">Kategori</label>
                                <select class="form-select @error('kategori') is-invalid @enderror" id="kategori"
                                    name="kategori" required>
                                    <option value="" selected>Pilih kategori</option>
                                    <option value="aset" {{ old('kategori') == 'aset' ? 'selected' : '' }}>Aset</option>
                                    <option value="non-aset" {{ old('kategori') == 'non-aset' ? 'selected' : '' }}>Non
                                        Aset</option>
                                </select>

                                @error('kategori')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="jenisSparepart" class="form-label">Jenis Sparepart</label>
                                <select class="form-select @error('jenisSparepart') is-invalid @enderror"
                                    id="jenisSparepart" name="jenisSparepart" required>
                                    <option value="" selected>Pilih jenis sparepart</option>
                                    @foreach ($jenis as $j)
                                        <option value="{{ $j->id }}"
                                            {{ old('jenisSparepart') == $j->id ? 'selected' : '' }}>{{ $j->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('jenisSparepart')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="typeSparepart" class="form-label">Type Sparepart</label>
                                <select class="form-select @error('typeSparepart') is-invalid @enderror"
                                    id="typeSparepart" name="typeSparepart" required>
                                    <option value="" selected>Pilih tipe sparepart</option>
                                    @foreach ($tipe as $t)
                                        <option value="{{ $t->id }}"
                                            {{ old('typeSparepart') == $t->id ? 'selected' : '' }}>{{ $t->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('typeSparepart')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="serialNumber" class="form-label">Serial Number</label>
                                <input type="text" class="form-control @error('serial_number') is-invalid @enderror"
                                    id="serialNumber" name="serial_number" value="{{ old('serial_number') }}"
                                    placeholder="Masukkan serial number">
                                @error('serial_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="quantity" class="form-label">Quantity</label>
                                <input type="number" class="form-control @error('quantity') is-invalid @enderror"
                                    id="quantity" name="quantity" min="1" required
                                    value="{{ old('quantity', 1) }}">
                                @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="tanggal" class="form-label">Tanggal</label>
                                <input type="date" class="form-control @error('tanggal') is-invalid @enderror"
                                    id="tanggal" name="tanggal" value="{{ old('tanggal') }}" required>
                                @error('tanggal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="spk" class="form-label">SPK</label>
                                <input type="text" class="form-control @error('spk') is-invalid @enderror"
                                    id="spk" name="spk" value="{{ old('spk') }}">
                                @error('spk')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="harga" class="form-label">Harga</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control @error('harga') is-invalid @enderror"
                                        id="harga" name="harga" required value="{{ old('harga') }}">
                                </div>
                                @error('harga')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="vendor" class="form-label">Vendor</label>
                                <select class="form-select @error('vendor') is-invalid @enderror" id="vendor"
                                    name="vendor" required>
                                    <option value="" selected>Pilih vendor</option>
                                    @foreach ($vendor as $v)
                                        <option value="{{ $v->id }}"
                                            {{ old('vendor') == $v->id ? 'selected' : '' }}>{{ $v->nama }}</option>
                                    @endforeach
                                </select>
                                @error('vendor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="pic" class="form-label">PIC</label>
                                <input type="text" class="form-control @error('pic') is-invalid @enderror"
                                    id="pic" name="pic" required value="{{ old('pic') }}">
                                @error('pic')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="department" class="form-label">Department</label>
                                <input type="text" class="form-control @error('department') is-invalid @enderror"
                                    id="department" name="department" value="{{ old('department') }}">
                                @error('department')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status"
                                    name="status" required>
                                    <option value="" selected>Pilih Status</option>
                                    <option value="sparepart baru"
                                        {{ old('status') == 'sparepart baru' ? 'selected' : '' }}>Sparepart Baru
                                    </option>
                                    <option value="sparepart lama"
                                        {{ old('status') == 'sparepart lama' ? 'selected' : '' }}>Sparepart Lama
                                    </option>
                                </select>

                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="keterangan" class="form-label">Keterangan</label>
                                <textarea class="form-control" id="keterangan" name="keterangan" rows="3">{{ old('keterangan') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Sparepart</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Detail Modal -->
    <div class="modal fade" id="sparepartDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-receipt me-2"></i>Detail Sparepart</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex justify-content-center">
                        <div class="spinner-border text-primary" id="sparepart-spinner" role="status"><span
                                class="visually-hidden">Loading...</span></div>
                    </div>

                    <div id="sparepart-content" style="display:none;">
                        <div class="row mb-3">
                            <div class="col-md-6"><strong>ID Sparepart:</strong> <span id="trx-id"></span></div>
                        </div>

                        <h6 class="mt-3 mb-2">Daftar Sparepart:</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Serial Number</th>
                                        <th>Type</th>
                                        <th>Jenis</th>
                                        <th>Status</th>
                                        <th>Harga</th>
                                        <th>Vendor</th>
                                        <th>SPK</th>
                                        <th>Qty</th>
                                        <th>PIC</th>
                                        <th>Keterangan</th>
                                        <th>Tanggal</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="trx-items-list"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">Tutup</button></div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Sparepart (mirip form tambah) -->
    <div class="modal fade" id="editSparepartModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editSparepartForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit Sparepart</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="original_id" id="edit-original-id" />
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="edit-kategori" class="form-label">Kategori</label>
                                <select class="form-select" id="edit-kategori" name="kategori" disabled>
                                    <option value="aset"
                                        {{ old('ketegori', optional($listBarang->first())->kategori) == 'aset' ? 'selected' : '' }}>
                                        Aset</option>
                                    <option value="non-aset"
                                        {{ old('kategori', optional($listBarang->first())->kategori) == 'non-aset' ? 'selected' : '' }}>
                                        Non Aset</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="edit-jenisSparepart" class="form-label">Jenis Sparepart</label>
                                <select class="form-select" id="edit-jenisSparepart" name="jenisSparepart" disabled>
                                    @foreach ($jenis as $j)
                                        <option value="{{ $j->id }}">{{ $j->nama }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="edit-typeSparepart" class="form-label">Type Sparepart</label>
                                <select class="form-select" id="edit-typeSparepart" name="typeSparepart" disabled>
                                    <option value="">Pilih tipe sparepart</option>
                                    @foreach ($tipe as $t)
                                        <option value="{{ $t->id }}">{{ $t->nama }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="edit-serialNumber" class="form-label">Serial Number</label>
                                <input type="text" class="form-control" id="edit-serialNumber" name="serial_number">
                            </div>

                            <div class="col-md-6">
                                <label for="edit-quantity" class="form-label">Quantity</label>
                                <input type="number" class="form-control" id="edit-quantity" name="quantity"
                                    min="1" required>
                            </div>

                            <div class="col-md-6">
                                <label for="edit-tanggal" class="form-label">Tanggal</label>
                                <input type="date" class="form-control" id="edit-tanggal" name="tanggal" required>
                            </div>

                            <div class="col-md-6">
                                <label for="edit-spk" class="form-label">SPK</label>
                                <input type="text" class="form-control" id="edit-spk" name="spk">
                            </div>

                            <div class="col-md-6">
                                <label for="edit-harga" class="form-label">Harga</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control" id="edit-harga" name="harga" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="edit-vendor" class="form-label">Vendor</label>
                                <select class="form-select" id="edit-vendor" name="vendor">
                                    <option value="">Pilih vendor</option>
                                    @foreach ($vendor as $v)
                                        <option value="{{ $v->id }}">{{ $v->nama }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="edit-pic" class="form-label">PIC</label>
                                <input type="text" class="form-control" id="edit-pic" name="pic" required>
                            </div>

                            <div class="col-md-6">
                                <label for="edit-department" class="form-label">Department</label>
                                <input type="text" class="form-control" id="edit-department" name="department">
                            </div>
                            <div class="col-md-6">
                                <label for="edit-status" class="form-label">Status</label>
                                <select class="form-select" id="edit-status" name="status" required>
                                    <option value="sparepart baru"
                                        {{ old('status', $detail->first()->status ?? '') == 'sparepart baru' ? 'selected' : '' }}>
                                        Sparepart Baru
                                    </option>

                                    <option value="sparepart lama"
                                        {{ old('status', $detail->first()->status ?? '') == 'sparepart lama' ? 'selected' : '' }}>
                                        Sparepart Lama
                                    </option>
                                </select>


                            </div>

                            <div class="col-12">
                                <label for="edit-keterangan" class="form-label">Keterangan</label>
                                <textarea class="form-control" id="edit-keterangan" name="keterangan" rows="3"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="editSaveBtn">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Konfirmasi Hapus -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus</h5><button type="button" class="btn-close"
                        data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p id="confirmDeleteText" class="mb-0">Yakin ingin menghapus item?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="confirmCancelBtn"
                        data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn"><span
                            id="confirmDeleteBtnText">Hapus</span><span id="confirmDeleteSpinner"
                            class="spinner-border spinner-border-sm ms-2" role="status"
                            style="display:none;"></span></button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast container -->
    <div class="toast-container position-fixed top-0 end-0 p-3" id="toastContainer" style="z-index:10800;"></div>

@endsection

@push('scripts')
    <script>
        @if (session('success') || session('error'))
            window.flash = {
                message: {!! json_encode(session('success') ?? session('error')) !!},
                type: {!! json_encode(session('success') ? 'success' : 'danger') !!}
            };
        @endif
        if (window.flash && window.flash.message) {
            document.addEventListener('DOMContentLoaded', () => {
                showToast(window.flash.message, window.flash.type || 'info');
                delete window.flash;
            });
        }
        /* ====== Helpers ====== */
        function showToast(message, type = 'info', options = {
            delay: 5000,
            autohide: true
        }) {
            const container = document.getElementById('toastContainer');
            if (!container) return console.warn('Toast container not found');

            const id = 'toast-' + Date.now() + Math.floor(Math.random() * 1000);
            const bgClass = {
                success: 'bg-success text-white',
                danger: 'bg-danger text-white',
                warning: 'bg-warning text-dark',
                info: 'bg-info text-white',
                secondary: 'bg-secondary text-white'
            } [type] || 'bg-secondary text-white';

            const closeBtnClass = bgClass.includes('text-white') ? 'btn-close btn-close-white' : 'btn-close';

            const icon = {
                success: '<i class="bi bi-check-circle-fill me-2"></i>',
                danger: '<i class="bi bi-x-circle-fill me-2"></i>',
                warning: '<i class="bi bi-exclamation-triangle-fill me-2"></i>',
                info: '<i class="bi bi-info-circle-fill me-2"></i>',
                secondary: '<i class="bi bi-bell-fill me-2"></i>'
            } [type] || '';

            const html = `
<div id="${id}" class="toast ${bgClass} shadow" role="alert" aria-live="assertive" aria-atomic="true">
  <div class="d-flex">
    <div class="toast-body">${icon}<span>${message}</span></div>
    <button type="button" class="${closeBtnClass} me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
  </div>
</div>
`;
            container.insertAdjacentHTML('beforeend', html);
            const toastEl = document.getElementById(id);
            const toast = new bootstrap.Toast(toastEl, {
                delay: options.delay,
                autohide: options.autohide
            });
            toast.show();
            toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
            return toast;
        }

        function showConfirm(message, okLabel = 'Hapus', cancelLabel = 'Batal') {
            return new Promise((resolve) => {
                const modalEl = document.getElementById('confirmDeleteModal');
                const bodyText = document.getElementById('confirmDeleteText');
                const okBtn = document.getElementById('confirmDeleteBtn');
                const cancelBtn = document.getElementById('confirmCancelBtn');
                const spinner = document.getElementById('confirmDeleteSpinner');
                const okText = document.getElementById('confirmDeleteBtnText');

                bodyText.textContent = message;
                okText.textContent = okLabel;
                cancelBtn.textContent = cancelLabel;

                const modal = new bootstrap.Modal(modalEl, {
                    backdrop: 'static',
                    keyboard: false
                });
                let resolved = false;

                const cleanup = () => {
                    okBtn.removeEventListener('click', onOk);
                    cancelBtn.removeEventListener('click', onCancel);
                    modalEl.removeEventListener('hidden.bs.modal', onHidden);
                    spinner.style.display = 'none';
                    okBtn.disabled = false;
                };

                const onOk = () => {
                    resolved = true;
                    cleanup();
                    modal.hide();
                    resolve(true);
                };
                const onCancel = () => {
                    resolved = true;
                    cleanup();
                    modal.hide();
                    resolve(false);
                };
                const onHidden = () => {
                    if (!resolved) {
                        cleanup();
                        resolve(false);
                    }
                };

                okBtn.addEventListener('click', onOk);
                cancelBtn.addEventListener('click', onCancel);
                modalEl.addEventListener('hidden.bs.modal', onHidden);
                modal.show();
            });
        }

        /* ====== Page logic ====== */
        let sparepartDetailModal;
        document.addEventListener("DOMContentLoaded", function() {
            sparepartDetailModal = new bootstrap.Modal(document.getElementById('sparepartDetailModal'));

            @if ($errors->any())
                const modal = new bootstrap.Modal(document.getElementById('tambahSparepartModal'));
                modal.show();
            @endif
        });

        function formatRupiah(val) {
            const num = Number(String(val).replace(/\D/g, '')) || 0;
            const formattedNum = (num / 100).toFixed(2);
            return 'Rp ' + new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(formattedNum);
        }


        function escapeHtml(str) {
            if (str === null || str === undefined) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        function showTransaksiDetail(data) {
            document.getElementById('sparepart-spinner').style.display = 'block';
            document.getElementById('sparepart-content').style.display = 'none';
            document.getElementById('trx-id').textContent = data.id || '-';

            const tbody = document.getElementById('trx-items-list');
            tbody.innerHTML = "";


            data.items.forEach((item, i) => {
                let statusClass = 'bg-secondary';
                if (item.status === 'sparepart baru') statusClass = 'bg-success';
                else if (item.status === 'sparepart lama') statusClass = 'bg-warning';

                const idForBtn = item.id || '';
                const dataAttrs = [
                    `data-item-id="${escapeHtml(idForBtn)}"`,
                    `data-item-serial="${escapeHtml(item.serial)}"`,
                    `data-item-quantity="${escapeHtml(item.quantity)}"`,
                    `data-item-tanggal="${escapeHtml(item.tanggal)}"`,
                    `data-item-spk="${escapeHtml(item.spk)}"`,
                    `data-item-harga="${escapeHtml(item.harga)}"`,
                    `data-item-vendor-id="${escapeHtml(item.vendor_id)}"`,
                    `data-item-status="${escapeHtml(item.status)}"`,
                    `data-item-pic="${escapeHtml(item.pic)}"`,
                    `data-item-department="${escapeHtml(item.department)}"`,
                    `data-item-keterangan="${escapeHtml(item.keterangan)}"`,
                    `data-item-jenis="${escapeHtml(data.jenis)}"`,
                    `data-item-type="${escapeHtml(data.type)}"`
                ].join(' ');

                const row = `
<tr>
    <td>${i + 1}</td>
    <td>${escapeHtml(item.serial) || '-'}</td>
    <td>${escapeHtml(data.type) || '-'}</td>
    <td>${escapeHtml(data.jenis) || '-'}</td>
    <td><span class="badge ${statusClass}">${item.status ? (item.status.charAt(0).toUpperCase() + item.status.slice(1)) : '-'}</span></td>
    <td>${item.harga ? formatRupiah(item.harga) : '-'}</td>
    <td>${escapeHtml(item.vendor) || '-'}</td>
    <td>${escapeHtml(item.spk) || '-'}</td>
    <td>${escapeHtml(item.quantity) || '-'}</td>
    <td>${escapeHtml(item.pic) || '-'}</td>
    <td>${escapeHtml(item.keterangan) || '-'}</td>
    <td>${escapeHtml(item.tanggal) || '-'}</td>
    <td>
        <button class="btn btn-primary btn-action btn-edit" ${dataAttrs} data-bs-toggle="tooltip" title="Edit">
            <i class="bi bi-pencil"></i>
        </button>
        <button class="btn btn-danger btn-action btn-delete" data-id="${escapeHtml(idForBtn)}" data-bs-toggle="tooltip" title="Hapus">
            <i class="bi bi-trash"></i>
        </button>
    </td>
</tr>
`;
                tbody.insertAdjacentHTML("beforeend", row);
            });

            // re-init tooltip
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(el) {
                return new bootstrap.Tooltip(el);
            });

            document.getElementById('sparepart-spinner').style.display = 'none';
            document.getElementById('sparepart-content').style.display = 'block';
            sparepartDetailModal.show();
        }

        function showDetail(tiket_sparepart) {
            fetch(`/kepalagudang/sparepart/${tiket_sparepart}/detail`)
                .then(res => res.json())
                .then(data => {
                    const status = document.getElementById('statusFilter') ? document.getElementById('statusFilter')
                        .value : '';
                    if (status) {
                        data.items = data.items.filter(item => (item.status || '').toString() === status.toString());
                    }
                    showTransaksiDetail(data);
                })
                .catch(err => {
                    console.error(err);
                    alert('Gagal mengambil detail!');
                });
        }
        document.addEventListener('click', (e) => {
            const editBtn = e.target.closest('.btn-edit');
            console.log(editBtn)
            if (editBtn) {
                const serial = editBtn.getAttribute('data-item-serial') || '';
                const jenis = editBtn.getAttribute('data-item-jenis') || '';
                const tipe = editBtn.getAttribute('data-item-type') || '';
                const quantity = editBtn.getAttribute('data-item-quantity') || '';
                const tanggal = editBtn.getAttribute('data-item-tanggal') || '';
                const spk = editBtn.getAttribute('data-item-spk') || '';
                const harga = editBtn.getAttribute('data-item-harga') || '';
                const vendor_id = editBtn.getAttribute('data-item-vendor-id') || '';
                const status = editBtn.getAttribute('data-item-status') || '';
                const pic = editBtn.getAttribute('data-item-pic') || '';
                const department = editBtn.getAttribute('data-item-department') || '';
                const keterangan = editBtn.getAttribute('data-item-keterangan') || '';
                const itemId = editBtn.getAttribute('data-item-id') || '';

                document.getElementById('edit-original-id').value = itemId;
                document.getElementById('edit-serialNumber').value = serial;
                document.getElementById('edit-quantity').value = quantity;
                document.getElementById('edit-tanggal').value = tanggal;
                document.getElementById('edit-spk').value = spk;
                document.getElementById('edit-harga').value = harga;

                document.getElementById('edit-vendor').value = vendor_id;
                document.getElementById('edit-status').value = status;
                document.getElementById('edit-pic').value = pic;
                document.getElementById('edit-department').value = department;
                document.getElementById('edit-keterangan').value = keterangan;

                console.log(vendor_id)
                // const selectedName = select.options[select.selectedIndex].text;
                const jenisSelect = document.getElementById('edit-jenisSparepart');
                const tipeSelect = document.getElementById('edit-typeSparepart');

                if (jenisSelect) {
                    for (let opt of jenisSelect.options) {
                        if ((opt.value && opt.value === jenis) || (opt.text && opt.text.trim() === jenis.trim())) {
                            opt.selected = true;
                            break;
                        }
                    }
                }
                if (tipeSelect) {
                    for (let opt of tipeSelect.options) {
                        if ((opt.value && opt.value === tipe) || (opt.text && opt.text.trim() === tipe.trim())) {
                            opt.selected = true;
                            break;
                        }
                    }
                }

                const editModalEl = document.getElementById('editSparepartModal');
                const editModal = new bootstrap.Modal(editModalEl);
                editModal.show();
                return;
            }

            const delBtn = e.target.closest('.btn-delete');
            if (!delBtn) return;

            (async () => {
                const btn = delBtn;
                const id = btn.dataset.id;
                if (!id) {
                    showToast('Id tidak ditemukan.', 'warning');
                    return;
                } else {}

                const confirmed = await showConfirm('Yakin ingin menghapus item dengan id: ' + id + ' ?',
                    'Hapus', 'Batal');
                if (!confirmed) return;

                btn.disabled = true;
                const originalHtml = btn.innerHTML;
                btn.innerHTML =
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';

                try {
                    const tokenInput = document.querySelector('input[name="_token"]') || document
                        .getElementById('csrf_token');
                    const token = tokenInput ? tokenInput.value : '';

                    const url = `/kepalagudang/sparepart/${encodeURIComponent(id)}`;

                    const res = await fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        }
                    });

                    const data = await res.json().catch(() => ({}));
                    if (!res.ok) throw new Error(data.message || `Gagal menghapus (status ${res.status})`);

                    const tr = btn.closest('tr');
                    if (tr) tr.remove();

                    if (data.listDeleted && typeof sparepartDetailModal !== 'undefined')
                        sparepartDetailModal.hide();
                    showToast(data.message || 'Berhasil dihapus.', 'success');

                    setTimeout(() => location.reload(), 1100);
                } catch (err) {
                    console.error(err);
                    showToast('Terjadi kesalahan: ' + (err.message || err), 'danger');
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }
            })();
        });

        (function attachEditSubmit() {
            const form = document.getElementById('editSparepartForm');
            if (!form) return;
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const saveBtn = document.getElementById('editSaveBtn');
                const originalHtml = saveBtn.innerHTML;
                saveBtn.disabled = true;
                saveBtn.innerHTML =
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...';

                try {
                    const originalId = document.getElementById('edit-original-id').value;
                    const tokenInput = document.querySelector('input[name="_token"]') || document
                        .getElementById('csrf_token');
                    const token = tokenInput ? tokenInput.value : '';

                    const params = new URLSearchParams();
                    params.append('_token', token);
                    params.append('_method', 'PUT');

                    params.append('jenisSparepart', document.getElementById('edit-jenisSparepart') ?
                        document.getElementById('edit-jenisSparepart').value : '');
                    params.append('typeSparepart', document.getElementById('edit-typeSparepart') ? document
                        .getElementById('edit-typeSparepart').value : '');
                    params.append('serial_number', document.getElementById('edit-serialNumber').value);
                    params.append('quantity', document.getElementById('edit-quantity').value);
                    params.append('tanggal', document.getElementById('edit-tanggal').value);
                    params.append('spk', document.getElementById('edit-spk').value);
                    params.append('harga', document.getElementById('edit-harga').value);
                    params.append('vendor', document.getElementById('edit-vendor').value);
                    params.append('vendor_id', document.getElementById('edit-vendor').value);
                    params.append('pic', document.getElementById('edit-pic').value);
                    params.append('status', document.getElementById('edit-status').value);
                    params.append('department', document.getElementById('edit-department').value);
                    params.append('keterangan', document.getElementById('edit-keterangan').value);

                    const url = `/kepalagudang/sparepart/${encodeURIComponent(originalId)}`;

                    const res = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
                            'Accept': 'application/json'
                        },
                        body: params.toString()
                    });

                    const data = await res.json().catch(() => ({}));
                    if (!res.ok) throw new Error(data.message || `Gagal menyimpan (status ${res.status})`);

                    const editModalEl = document.getElementById('editSparepartModal');
                    const editModalInstance = bootstrap.Modal.getInstance(editModalEl);
                    if (editModalInstance) editModalInstance.hide();

                    showToast(data.message || 'Perubahan berhasil disimpan.', 'success');
                    setTimeout(() => location.reload(), 1200);
                } catch (err) {
                    console.error(err);
                    showToast('Terjadi kesalahan: ' + (err.message || err), 'danger');
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = originalHtml;
                }
            });
        })();

        document.addEventListener('DOMContentLoaded', function() {
            const kategoriSelect = document.getElementById('kategori');
            const serialNumberInput = document.getElementById('serialNumber');
            const jenisSelect = document.getElementById('jenisSparepart');
            const tipeSelect = document.getElementById('typeSparepart');
            const vendorSelect = document.getElementById('vendor');

            // Semua field kecuali kategori & serialNumber
            const otherFields = document.querySelectorAll(
                '#sparepartForm select:not(#kategori), #sparepartForm input:not(#serialNumber), #sparepartForm textarea'
            );

            // Fungsi untuk update status Serial Number
            function updateSerialNumberField(kategori) {
                if (!kategori || kategori === 'non-aset') {
                    serialNumberInput.disabled = true;
                    serialNumberInput.value = '';
                } else if (kategori === 'aset') {
                    serialNumberInput.disabled = false;
                }
            }

            // Inisialisasi: semua field disabled + serial number disabled
            otherFields.forEach(field => field.disabled = true);
            updateSerialNumberField(''); // disabled karena belum pilih kategori

            if (kategoriSelect) {
                kategoriSelect.addEventListener('change', function() {
                    const kategori = this.value;

                    if (kategori) {
                        otherFields.forEach(field => field.disabled = false);
                        updateSerialNumberField(kategori); // aktifkan/nonaktifkan SN

                        // Filter jenis & tipe
                        let filteredJenis = jenisData.filter(j => j.kategori === kategori);
                        jenisSelect.innerHTML = '<option value="" selected>Pilih jenis sparepart</option>';
                        filteredJenis.forEach(j => {
                            const option = document.createElement('option');
                            option.value = j.id;
                            option.textContent = j.nama;
                            jenisSelect.appendChild(option);
                        });

                        let filteredTipe = tipeData.filter(t => t.kategori === kategori);
                        tipeSelect.innerHTML = '<option value="" selected>Pilih tipe sparepart</option>';
                        filteredTipe.forEach(t => {
                            const option = document.createElement('option');
                            option.value = t.id;
                            option.textContent = t.nama;
                            tipeSelect.appendChild(option);
                        });
                    } else {
                        // Jika kategori dikosongkan
                        otherFields.forEach(field => field.disabled = true);
                        updateSerialNumberField(''); // disabled
                        jenisSelect.innerHTML = '<option value="" selected>Pilih jenis sparepart</option>';
                        tipeSelect.innerHTML = '<option value="" selected>Pilih tipe sparepart</option>';
                        if (vendorSelect) vendorSelect.innerHTML =
                            '<option value="" selected>Pilih vendor</option>';
                    }
                });
            }
        });

        const jenisData = @json($jenis);
        const tipeData = @json($tipe);
        const vendorData = @json($vendor);
    </script>
@endpush