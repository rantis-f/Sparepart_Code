@extends('layouts.superadmin')

@section('title', 'Daftar Sparepart - Kepalagudang')

@push('styles')
@endpush

@section('content')
    <input type="hidden" name="_token" id="csrf_token" value="{{ csrf_token() }}">



    <!-- Filter Card -->
    <div class="filter-card">
        <h5 class="mb-3"><i class="bi bi-funnel me-2"></i>Filter Data</h5>
        <form method="GET" action="{{ route('superadmin.sparepart.index') }}">
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
                    <a href="{{ route('superadmin.sparepart.index') }}" class="btn btn-light me-2">
                        <i class="bi bi-arrow-clockwise me-1"></i> Reset
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-filter me-1"></i> Terapkan Filter
                    </button>
                </div>
            </div>
        </form>
    </div>

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

    <!-- Table -->
    <div class="table-container">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID Sparepart</th>
                        <th>Jenis & Type</th>
                        <th>Quantity</th>
                        @if ($filterStatus === 'habis')
                            <th>Habis</th>
                        @elseif ($filterStatus === 'dikirim')
                            <th>Dikirim</th>
                        @else
                            <th>Tersedia</th>
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
                            @if ($filterStatus === 'habis')
                                <td>{{ $totalsPerTiket[$barang->tiket_sparepart]['habis'] ?? 0 }}</td>
                            @elseif ($filterStatus === 'dikirim')
                                <td>{{ $totalsPerTiket[$barang->tiket_sparepart]['dikirim'] ?? 0 }}</td>
                            @else
                                <td>{{ $totalsPerTiket[$barang->tiket_sparepart]['tersedia'] ?? 0 }}</td>
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

    <div class="toast-container position-fixed top-0 end-0 p-3" id="toastContainer" style="z-index:10800;"></div>

@endsection

@push('scripts')
    <script>
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
            }[type] || 'bg-secondary text-white';

            const closeBtnClass = bgClass.includes('text-white') ? 'btn-close btn-close-white' : 'btn-close';

            const icon = {
                success: '<i class="bi bi-check-circle-fill me-2"></i>',
                danger: '<i class="bi bi-x-circle-fill me-2"></i>',
                warning: '<i class="bi bi-exclamation-triangle-fill me-2"></i>',
                info: '<i class="bi bi-info-circle-fill me-2"></i>',
                secondary: '<i class="bi bi-bell-fill me-2"></i>'
            }[type] || '';

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

        /* ====== Page logic ====== */
        let sparepartDetailModal;
        document.addEventListener("DOMContentLoaded", function () {
            sparepartDetailModal = new bootstrap.Modal(document.getElementById('sparepartDetailModal'));
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
                if (item.status === 'tersedia') statusClass = 'bg-success';
                else if (item.status === 'habis') statusClass = 'bg-danger';
                else if (item.status === 'dipesan' || item.status === 'dikirim') statusClass = 'bg-warning';

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
    </tr>
    `;
                tbody.insertAdjacentHTML("beforeend", row);
            });

            // re-init tooltip
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (el) {
                return new bootstrap.Tooltip(el);
            });

            document.getElementById('sparepart-spinner').style.display = 'none';
            document.getElementById('sparepart-content').style.display = 'block';
            sparepartDetailModal.show();
        }

function showDetail(tiket_sparepart) {
    fetch(`/superadmin/sparepart/${tiket_sparepart}/detail`)
        .then(res => res.json())
        .then(data => {
            const status = document.getElementById('statusFilter')?.value || '';
            const tanggalMulai = document.getElementById('tanggalMulai')?.value || '';
            const tanggalBerakhir = document.getElementById('tanggalBerakhir')?.value || '';

            // Filter status (jika ada)
            if (status) {
                data.items = data.items.filter(item => (item.status || '').toString() === status.toString());
            }

            // Filter tanggal (jika ada)
            if (tanggalMulai && tanggalBerakhir) {
                data.items = data.items.filter(item => {
                    const tgl = new Date(item.tanggal);
                    return tgl >= new Date(tanggalMulai) && tgl <= new Date(tanggalBerakhir);
                });
            }

            showTransaksiDetail(data);
        })
        .catch(err => {
            console.error(err);
            alert('Gagal mengambil detail!');
        });
}
    </script>
@endpush