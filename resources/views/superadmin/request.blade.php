@extends('layouts.superadmin')

@section('title', 'Request Barang - Superadmin')

@section('content')

    <!-- Filter Card -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('superadmin.request.index') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="start_date" class="form-label">Dari Tanggal</label>
                        <input type="date" class="form-control" id="start_date" name="start_date"
                            value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-4">
                        <label for="end_date" class="form-label">Sampai Tanggal</label>
                        <input type="date" class="form-control" id="end_date" name="end_date"
                            value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="bi bi-filter me-1"></i> Terapkan Filter
                        </button>
                        <a href="{{ route('superadmin.request.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-clockwise me-1"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>>

    @if ($requests->count() > 0)
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="dashboard-card p-3 bg-white border rounded">
                    <div class="d-flex align-items-center">
                        <div class="bg-info bg-opacity-10 p-3 rounded me-3">
                            <i class="bi bi-clock-history text-info fs-4"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">Total Request</h6>
                            <h4 class="mb-0 fw-bold text-info">{{ $totalRequests }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif


    <!-- Table -->
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID Request</th>
                            <th>Requester</th>
                            <th>Tanggal Request</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $req)
                            <tr>
                                <td><span class="fw-bold">{{ $req->tiket }}</span></td>
                                <td>{{ $req->user->name ?? 'User' }}</td>
                                <td>{{ \Illuminate\Support\Carbon::parse($req->tanggal_permintaan)->translatedFormat('d M Y') }}
                                </td>
                                <td>
                                    @if (Auth::id() === 15)
                                        <span class="badge bg-secondary">Menunggu Approval Admin</span>
                                    @elseif(Auth::id() === 16)
                                        <span class="badge bg-info">Menunggu Approval Superadmin</span>
                                    @endif
                                </td>
                                <td class="action-buttons">
                                    <button class="btn btn-info btn-sm btn-detail" data-tiket="{{ $req->tiket }}"
                                        data-requester="{{ $req->user->name ?? 'User' }}"
                                        data-tanggal="{{ \Illuminate\Support\Carbon::parse($req->tanggal_permintaan)->translatedFormat('d M Y') }}">
                                        <i class="bi bi-eye"></i> Detail
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">
                                    @if (Auth::id() === 15)
                                        Tidak ada permintaan yang menunggu approval Admin.
                                    @elseif(Auth::id() === 16)
                                        Tidak ada permintaan yang menunggu approval Superadmin.
                                    @else
                                        Anda tidak memiliki akses.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- ✅ Pagination Dinamis -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    Menampilkan {{ $requests->firstItem() ?? 0 }} hingga {{ $requests->lastItem() ?? 0 }} dari
                    {{ $requests->total() }} entri
                </div>
                <nav>
                    {{ $requests->links() }}
                </nav>
            </div>
        </div>
    </div>

    <!-- Modal Detail -->
    <div class="modal fade" id="modalDetail" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title"><i class="bi bi-eye"></i> Detail Request</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <!-- Data Request -->
                    <h6 class="fw-bold text-primary mb-3"><i class="bi bi-cart-check"></i> Data Request</h6>
                    <div class="mb-3">
                        <p><strong>No Tiket:</strong> <span id="modal-tiket-display">-</span></p>
                        <p><strong>Requester:</strong> <span id="modal-requester-display">-</span></p>
                        <p><strong>Tanggal Request:</strong> <span id="modal-tanggal-display">-</span></p>
                        <p><strong>Status:</strong>
                            <span id="modal-status-display" class="badge"></span>
                        </p>
                    </div>

                    <!-- Items Diminta -->
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
                            <tbody id="detail-request-body">
                                <tr>
                                    <td colspan="5" class="text-center">Memuat data...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <hr>

                    <!-- Data Pengiriman -->
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
                            <tbody id="detail-pengiriman-body">
                                <tr>
                                    <td colspan="7" class="text-center">Memuat data pengiriman...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="modal-footer">
                    @if (Auth::id() === 15)
                        <button class="btn btn-success btn-approve-modal" data-tiket="">
                            <i class="bi bi-check-circle"></i> Approve (Admin)
                        </button>
                        <button class="btn btn-danger btn-reject-modal" data-tiket="">
                            <i class="bi bi-x-circle"></i> Tolak (Admin)
                        </button>
                    @elseif(Auth::id() === 16)
                        <button class="btn btn-success btn-approve-modal" data-tiket="">
                            <i class="bi bi-check-circle"></i> Approve Final (Superadmin)
                        </button>
                        <button class="btn btn-danger btn-reject-modal" data-tiket="">
                            <i class="bi bi-x-circle"></i> Tolak (Superadmin)
                        </button>
                    @endif
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.allRequests = @json($requests->items());
        // Filter pencarian (client-side)
        document.getElementById('searchFilter')?.addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            document.querySelectorAll('tbody tr').forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });

        // Buka modal detail
        document.querySelectorAll('.btn-detail').forEach(button => {
            button.addEventListener('click', function() {
                const tiket = this.dataset.tiket;
                const requester = this.dataset.requester;
                const tanggal = this.dataset.tanggal;

                // Reset modal info dasar
                document.getElementById('modal-tiket-display').textContent = tiket;
                document.getElementById('modal-requester-display').textContent = requester;
                document.getElementById('modal-tanggal-display').textContent = tanggal;

                // Set status badge
                const statusBadge = document.getElementById('modal-status-display');
                if ({{ Auth::id() }} === 15) {
                    statusBadge.textContent = 'Menunggu Approval Admin';
                    statusBadge.className = 'badge bg-warning';
                } else if ({{ Auth::id() }} === 16) {
                    statusBadge.textContent = 'Menunggu Approval Superadmin';
                    statusBadge.className = 'badge bg-info';
                }

                // Cari data request di allRequests
                const req = allRequests.find(r => r.tiket === tiket);

                // Isi detail request (items diminta)
                const detailBody = document.getElementById('detail-request-body');
                detailBody.innerHTML = '';
                if (req && req.details && req.details.length > 0) {
                    req.details.forEach((item, index) => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                    <td>${index + 1}</td>
                    <td>${item.nama_item || '-'}</td>
                    <td>${item.deskripsi || '-'}</td>
                    <td>${item.jumlah || 0}</td>
                    <td>${item.keterangan || '-'}</td>
                `;
                        detailBody.appendChild(tr);
                    });
                } else {
                    detailBody.innerHTML =
                        '<tr><td colspan="5" class="text-center">Tidak ada item diminta.</td></tr>';
                }

                // Isi data pengiriman
                const pengirimanBody = document.getElementById('detail-pengiriman-body');
                pengirimanBody.innerHTML = '';
                if (req && req.pengiriman && req.pengiriman.details && req.pengiriman.details.length > 0) {
                    req.pengiriman.details.forEach((item, index) => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                    <td>${index + 1}</td>
                    <td>${item.nama_item || item.nama || '-'}</td>
                    <td>${item.merk || '-'}</td>
                    <td>${item.sn || '-'}</td>
                    <td>${item.tipe || '-'}</td>
                    <td>${item.jumlah || 0}</td>
                    <td>${item.keterangan || '-'}</td>
                `;
                        pengirimanBody.appendChild(tr);
                    });
                    // Jika ada tanggal pengiriman, tampilkan juga
                    if (req.pengiriman.tanggal_transaksi) {
                        const tanggal = new Date(req.pengiriman.tanggal_transaksi);
                        const options = {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric'
                        };
                        const formattedTanggal = tanggal.toLocaleDateString('id-ID', options);

                        document.getElementById('modal-tanggal-pengiriman-display').textContent =
                            formattedTanggal;

                    } else {
                        document.getElementById('modal-tanggal-pengiriman-display').textContent = '-';
                    }
                } else {
                    pengirimanBody.innerHTML =
                        '<tr><td colspan="7" class="text-center">Belum ada data pengiriman.</td></tr>';
                    document.getElementById('modal-tanggal-pengiriman-display').textContent = '-';
                }

                // Update data tiket untuk tombol approve/reject
                document.querySelectorAll('.btn-approve-modal, .btn-reject-modal').forEach(btn => {
                    btn.dataset.tiket = tiket;
                });

                // Tampilkan modal
                const modal = new bootstrap.Modal(document.getElementById('modalDetail'));
                modal.show();
            });
        });




        // Approve
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-approve-modal')) {
                const tiket = e.target.dataset.tiket;

                if (!tiket) {
                    alert('Tiket tidak ditemukan');
                    return;
                }

                if (confirm('Apakah Anda yakin ingin menyetujui request ini?')) {
                    fetch(`/superadmin/request/${tiket}/approve`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content'),
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                tiket: tiket
                            })
                        })

                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert(data.message);
                                location.reload();
                            } else {
                                alert('Gagal: ' + data.message);
                            }
                        })
                        .catch(err => {
                            console.error('Error:', err);
                            alert('Terjadi kesalahan teknis.');
                        });
                }
            }
        });

        // Reject
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-reject-modal')) {
                const tiket = e.target.dataset.tiket;
                const reason = prompt('Masukkan alasan penolakan:');

                if (!tiket) {
                    alert('Tiket tidak ditemukan');
                    return;
                }

                if (reason) {
                    fetch(`/superadmin/request/${tiket}/reject`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content'),
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                catatan: reason
                            })
                        })

                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert(data.message);
                                location.reload();
                            } else {
                                alert('Gagal: ' + data.message);
                            }
                        })
                        .catch(err => {
                            console.error('Error:', err);
                            alert('Terjadi kesalahan teknis.');
                        });
                }
            }
        });
    </script>
@endpush
