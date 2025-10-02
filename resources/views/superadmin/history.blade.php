@extends('layouts.superadmin')

@section('title', 'Histori Barang - Superadmin')

@section('content')
    <!-- Page Header -->


<div class="filter-card">
    <h5 class="mb-4"><i class="bi bi-funnel me-2"></i>Filter Data</h5>
    <form method="GET" action="{{ route('superadmin.history.index') }}">
        <div class="row">
            <div class="col-md-4 mb-4">
                <label for="dateFrom" class="form-label">Dari Tanggal</label>
                <input type="date" class="form-control" id="dateFrom" name="date_from" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-4 mb-4">
                <label for="dateTo" class="form-label">Sampai Tanggal</label>
                <input type="date" class="form-control" id="dateTo" name="date_to" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-4 mb-4">
                <label for="statusFilter" class="form-label">Status</label>
                <select class="form-select" id="statusFilter" name="status">
                    <option value="">Semua Status</option>
                    <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                    <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    <option value="on progres" {{ request('status') == 'on progres' ? 'selected' : '' }}>On Progress</option>
                </select>
            </div>
        </div>
        <div class="d-flex justify-content-end">
            <a href="{{ route('superadmin.history.index') }}" class="btn btn-light me-2">
                <i class="bi bi-arrow-clockwise me-1"></i> Reset
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-search me-1"></i> Terapkan Filter
            </button>
        </div>
    </form>
</div>

    <!-- Export Button -->
    {{-- <div class="d-flex justify-content-end mb-3">
        <button class="btn btn-export">
            <i class="bi bi-download me-1"></i> Export Data
        </button>
    </div> --}}

    <!-- Table -->
    <div class="table-container mb-4">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID Transaksi</th>
                        <th>Requester</th>
                        <th>Status</th>
                        <th>Tanggal Transaksi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($requests as $req)
                        <tr>
                            <td><span class="fw-bold">{{ $req->tiket }}</span></td>
                            <td>{{ $req->user->name ?? '-' }}</td>
                           <td class="d-flex align-items-center gap-2">
    <!-- Status Badge -->
    @php
        $status = '';
        if ($req->status_super_admin === 'approved' && Auth::id() === 16) {
            $status = 'Disetujui';
        } elseif ($req->status_admin === 'approved' && Auth::id() === 15) {
            $status = 'Disetujui';
        } elseif ($req->status_super_admin === 'rejected') {
            $status = 'Ditolak';
        } else {
            $status = 'On Progress';
        } 

        // Warna badge
        $bg = '';
        if ($req->status_super_admin == 'approved' && Auth::id() === 16) {
            $bg = 'bg-success';
        } elseif ($req->status_admin == 'approved' && Auth::id() === 15) {
            $bg = 'bg-success';
        } elseif ($req->status_super_admin == 'rejected') {
            $bg = 'bg-danger';
        } else {
            $bg = 'bg-warning text-dark';
        } 
    @endphp

    <span class="badge {{ $bg }}">
        {{ $status }}
    </span>

                                <!-- 🔹 Ikon Mata - Tracking Approval -->
                                <button 
                                    type="button"
                                    onclick="showStatusDetailModal('{{ $req->tiket }}', 'super_admin')"
                                    class="inline-flex items-center justify-center w-6 h-6 text-white bg-blue-600 hover:bg-blue-700 rounded-full transition focus:outline-none"
                                    title="Lihat progres approval">
                                    <i class="fas fa-eye text-xs"></i>
                                </button>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($req->tanggal_permintaan)->format('Y-m-d') }}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary btn-history" data-bs-toggle="modal"
                                    data-bs-target="#modalHistory" data-tiket="{{ $req->tiket }}">
                                    <i class="bi bi-eye"></i> Detail
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

 <!-- Pagination -->
    <div class="pagination-container d-flex justify-content-between align-items-center">
        <div class="text-muted">
            Menampilkan {{ $requests->firstItem() ?? 0 }} hingga {{ $requests->lastItem() ?? 0 }} dari
            {{ $requests->total() }} entri
        </div>
        <nav aria-label="Page navigation">
            {{ $requests->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
        </nav>
    </div>

    <!-- ✅ Modal Detail History -->
    <div class="modal fade" id="modalHistory" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title"><i class="bi bi-clock-history"></i> Detail History Barang</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <h6 class="fw-bold text-primary mb-3"><i class="bi bi-cart-check"></i> Data Request</h6>
                    <div class="mb-3">
                        <p><strong>No Tiket:</strong> <span id="modal-tiket-display">-</span></p>
                        <p><strong>Requester:</strong> <span id="modal-requester-display">-</span></p>
                        <p><strong>Tanggal Request:</strong> <span id="modal-tanggal-request-display">-</span></p>
                    </div>

                    <div class="table-responsive mb-4">
                        <table class="table table-bordered">
                            <thead class="table-primary">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Item</th>
                                    <th>Deskripsi</th>
                                    <th>Jumlah Diminta</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody id="request-table-body">
                                <tr>
                                    <td colspan="5" class="text-center">Pilih tiket untuk melihat detail.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <hr>

                    <h6 class="fw-bold text-success mb-3"><i class="bi bi-truck"></i> Data Pengiriman</h6>
                    <div class="mb-3">
                        <p><strong>Tanggal Pengiriman:</strong> <span id="modal-tanggal-pengiriman-display">-</span></p>
                    </div>

                    <div class="table-responsive mb-4">
                        <table class="table table-bordered">
                            <thead class="table-success">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Item</th>
                                    <th>Merk</th>
                                    <th>SN</th>
                                    <th>Tipe</th>
                                    <th>Jumlah Dikirim</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody id="pengiriman-table-body">
                                <tr>
                                    <td colspan="7" class="text-center">Pilih tiket untuk melihat detail.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ✅ Include Komponen Modal Tracking -->
    @include('components.tracking-modal')

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // Load detail history
            document.querySelectorAll('.btn-history').forEach(button => {
                button.addEventListener('click', function () {
                    const tiket = this.dataset.tiket;

                    // Reset modal
                    document.getElementById('modal-tiket-display').textContent = '-';
                    document.getElementById('modal-requester-display').textContent = '-';
                    document.getElementById('modal-tanggal-request-display').textContent = '-';
                    document.getElementById('modal-tanggal-pengiriman-display').textContent = '-';

                    document.getElementById('request-table-body').innerHTML = '<tr><td colspan="5" class="text-center">Memuat data...</td></tr>';
                    document.getElementById('pengiriman-table-body').innerHTML = '<tr><td colspan="7" class="text-center">Memuat data...</td></tr>';

                    // Fetch data
                    fetch(`/superadmin/history/${tiket}/api`)
                        .then(response => {
                            if (!response.ok) throw new Error('Gagal ambil data');
                            return response.json();
                        })
                        .then(data => {
                            // Isi data request
                            document.getElementById('modal-tiket-display').textContent = data.permintaan.tiket;
                            document.getElementById('modal-requester-display').textContent = data.permintaan.user?.name || '-';
                            document.getElementById('modal-tanggal-request-display').textContent = new Date(data.permintaan.tanggal_permintaan)
                                .toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' });

                            // Isi tabel request
                            const requestTable = document.getElementById('request-table-body');
                            requestTable.innerHTML = '';
                            data.permintaan.details.forEach((item, index) => {
                                const tr = document.createElement('tr');
                                tr.innerHTML = `
                                    <td>${index + 1}</td>
                                    <td>${item.nama_item}</td>
                                    <td>${item.deskripsi || '-'}</td>
                                    <td>${item.jumlah}</td>
                                    <td>${item.keterangan || '-'}</td>
                                `;
                                requestTable.appendChild(tr);
                            });

                            // Isi data pengiriman
                            if (data.pengiriman) {
                                document.getElementById('modal-tanggal-pengiriman-display').textContent = new Date(data.pengiriman.tanggal_transaksi)
                                    .toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' });

                                const pengirimanTable = document.getElementById('pengiriman-table-body');
                                pengirimanTable.innerHTML = '';
                                data.pengiriman.details.forEach((item, index) => {
                                    const tr = document.createElement('tr');
                                    tr.innerHTML = `
                                        <td>${index + 1}</td>
                                        <td>${item.nama}</td>
                                        <td>${item.merk || '-'}</td>
                                        <td>${item.sn || '-'}</td>
                                        <td>${item.tipe || '-'}</td>
                                        <td>${item.jumlah}</td>
                                        <td>${item.keterangan || '-'}</td>
                                    `;
                                    pengirimanTable.appendChild(tr);
                                });
                            } else {
                                document.getElementById('modal-tanggal-pengiriman-display').textContent = '-';
                                document.getElementById('pengiriman-table-body').innerHTML = '<tr><td colspan="7" class="text-center">Belum ada pengiriman.</td></tr>';
                            }

                            // Buka modal
                            const modal = new bootstrap.Modal(document.getElementById('modalHistory'));
                            modal.show();
                        })
                        .catch(err => {
                            console.error('Error:', err);
                            alert('Gagal memuat detail: ' + err.message);
                        });
                });
            });

            // Fix backdrop setelah modal ditutup
            const modalElement = document.getElementById('modalHistory');
            if (modalElement) {
                modalElement.addEventListener('hidden.bs.modal', function () {
                    document.querySelector('.modal-backdrop')?.remove();
                    document.body.style.overflow = '';
                    document.body.classList.remove('modal-open');
                });
            }
        });
    </script>
@endpush