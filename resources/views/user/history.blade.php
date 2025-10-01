@extends('layouts.user')

@section('title', 'History Barang')

@section('content')
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="page-title mb-1"><i class="bi bi-clock-history me-2"></i> History Barang</h4>
            <p class="text-muted mb-0">Riwayat permintaan dan penerimaan barang Anda.</p>
        </div>
        <div class="badge bg-primary fs-6 p-2">
            <i class="bi bi-list-check me-1"></i> Total: {{ $requests->count() }} Request
        </div>
    </div>

    <!-- Filter Section -->
    <form method="GET" action="{{ route('history.index') }}" class="filter-card mb-4">
        <div class="row g-3">
            <!-- Filter Status -->
            <div class="col-md-3">
                <label class="form-label fw-semibold">Status</label>
                <select class="form-select" name="statusFilter">
                    <option value="">Semua Status</option>
                    <option value="close" {{ request('statusFilter') == 'close' ? 'selected' : '' }}>Close</option>
                    <option value="diterima" {{ request('statusFilter') == 'diterima' ? 'selected' : '' }}>Diterima</option>
                    <option value="rejected" {{ request('statusFilter') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>

            <!-- Tanggal Awal -->
            <div class="col-md-3">
                <label class="form-label fw-semibold">Tanggal Awal</label>
                <input type="date" class="form-control" name="start_date" value="{{ request('start_date') }}">
            </div>

            <!-- Tanggal Akhir -->
            <div class="col-md-3">
                <label class="form-label fw-semibold">Tanggal Akhir</label>
                <input type="date" class="form-control" name="end_date" value="{{ request('end_date') }}">
            </div>

            <!-- Tombol Terapkan & Reset -->
            <div class="col-md-3 d-flex align-items-end">
                <div class="d-grid gap-2 d-flex">
                    <button type="submit" class="btn btn-primary px-3">
                        <i class="bi bi-funnel me-1"></i> Terapkan
                    </button>
                    <a href="{{ route('history.index') }}" class="btn btn-light px-3">
                        <i class="bi bi-arrow-clockwise"></i> Reset
                    </a>
                </div>
            </div>
        </div>
    </form>

    <!-- Request List -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="bg-blue-700 text-white">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase">No</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase">Nama Tiket</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $index => $req)
                            <tr class="ticket-row hover:bg-gray-50 cursor-pointer transition-colors">
                                <td class="px-4 py-3 text-sm text-center">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 text-sm font-medium text-blue-600">{{ $req->tiket }}</td>
                                <td class="px-4 py-3 text-sm">
                                    {{ \Carbon\Carbon::parse($req->tanggal_permintaan)->translatedFormat('l, d F Y') }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <div class="flex items-center space-x-2">
                                        @php
                                            $status = '';
                                            $bg = '';

                                            if ($req->pengiriman?->status === 'close') {
                                                $status = 'Close';
                                                $bg = 'bg-green-100 text-green-800';
                                            } elseif ($req->status_super_admin === 'rejected') {
                                                $status = 'Ditolak';
                                                $bg = 'bg-red-100 text-red-800';
                                            } else {
                                                $status = 'Diterima';
                                                $bg = 'bg-gray-100 text-gray-800';
                                            }
                                        @endphp

                                        <span
                                            class="inline-flex items-center px-2 py-1 text-xs font-medium {{ $bg }} rounded-full"
                                            data-status-badge data-status="{{ $req->status_barang }}">
                                            {{ $status }}
                                        </span>

                                        <button type="button"
                                            onclick="showStatusDetailModal('{{ $req->tiket }}', 'user')"
                                            class="text-blue-600 hover:text-blue-800 focus:outline-none"
                                            title="Lihat detail progres approval">
                                        </button>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <button class="btn btn-info btn-sm btn-detail" data-tiket="{{ $req->tiket }}">
                                        <i class="bi bi-eye me-1"></i> Detail
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">
                                    <i class="fas fa-inbox fa-3x text-gray-400 block mb-3"></i>
                                    <p>Belum ada riwayat permintaan.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <nav aria-label="Page navigation" class="mt-4">
        <ul class="pagination justify-content-center">
            <li class="page-item disabled">
                <a class="page-link" href="#" tabindex="-1">Previous</a>
            </li>
            <li class="page-item active"><a class="page-link" href="#">1</a></li>
            <li class="page-item"><a class="page-link" href="#">2</a></li>
            <li class="page-item"><a class="page-link" href="#">3</a></li>
            <li class="page-item">
                <a class="page-link" href="#">Next</a>
            </li>
        </ul>
    </nav>

    <!-- Modal Detail -->
    <div class="modal fade" id="modalDetail" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title"><i class="bi bi-clock-history"></i> Detail History Barang</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <!-- Data Request (readonly) -->
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
                                    <td colspan="5" class="text-center">Memuat data...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <hr>

                    <!-- Data Pengiriman (readonly) -->
                    <h6 class="fw-bold text-success mb-3"><i class="bi bi-truck"></i> Data Pengiriman</h6>
                    <div class="mb-3">
                        <p><strong>Tanggal Pengiriman:</strong> <span id="modal-tanggal-pengiriman-display">-</span></p>
                        <p><strong>No Resi:</strong> <span id="modal-resi-display">-</span></p>
                        <p><strong>Lampiran:</strong>
                            <span id="modal-attachment-display">-</span>
                        </p>
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
                                    <td colspan="7" class="text-center">Memuat data...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <hr>

                    <!-- 🔸 Bagian Bukti Penerimaan - Layout Kiri Kanan -->
                    <h6 class="fw-bold text-info mb-3"><i class="bi bi-image"></i> Bukti Penerimaan</h6>
                    <div class="row">
                        <!-- Card Bukti Pengiriman (Kiri) -->
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-header bg-warning text-dark">
                                    <i class="bi bi-truck me-1"></i> Bukti Pengiriman
                                </div>
                                <div class="card-body text-center">
                                    <div id="bukti-pengiriman-preview"
                                        class="d-flex justify-content-center align-items-center"
                                        style="min-height: 200px;">
                                        <div class="text-muted">
                                            <i class="bi bi-image display-6"></i>
                                            <p class="mt-2">Belum ada bukti pengiriman</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Bukti Penerimaan (Kanan) -->
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-header bg-info text-white">
                                    <i class="bi bi-check-circle me-1"></i> Bukti Penerimaan
                                </div>
                                <div class="card-body text-center">
                                    <div id="bukti-penerimaan-preview"
                                        class="d-flex justify-content-center align-items-center"
                                        style="min-height: 200px;">
                                        <div class="text-muted">
                                            <i class="bi bi-image display-6"></i>
                                            <p class="mt-2">Belum ada bukti penerimaan</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tetap pertahankan cache dan fungsi modal
            window.requestCache = window.requestCache || {};
            window.requestPromises = window.requestPromises || {};

            function normalizeStatus(val) {
                if (val === null || val === undefined) return 'pending';
                const v = String(val).toLowerCase();
                if (v.includes('approve') || v.includes('disetujui') || v === 'approved') return 'approved';
                if (v.includes('reject') || v.includes('ditolak') || v === 'rejected') return 'rejected';
                return 'pending';
            }

            function pickCatatan(perm) {
                return perm.catatan_final ?? perm.catatan_super_admin ?? perm.catatan_admin ?? perm
                    .catatan_gudang ?? perm.catatan_ro ?? null;
            }

            function getTicketData(tiket) {
                if (window.requestCache[tiket]) return Promise.resolve(window.requestCache[tiket]);
                if (window.requestPromises[tiket]) return window.requestPromises[tiket];

                const url = `/user/validasi/${encodeURIComponent(tiket)}/api?_=${Date.now()}`;
                const p = fetch(url)
                    .then(res => {
                        if (!res.ok) throw new Error('Network response not ok');
                        return res.json();
                    })
                    .then(json => {
                        if (!json || json.success !== true) throw new Error('Invalid API response');
                        const payload = {
                            permintaan: json.permintaan || null,
                            pengiriman: json.pengiriman || null
                        };
                        window.requestCache[tiket] = payload;
                        delete window.requestPromises[tiket];
                        return payload;
                    })
                    .catch(err => {
                        delete window.requestPromises[tiket];
                        throw err;
                    });

                window.requestPromises[tiket] = p;
                return p;
            }

            function showDetailModal() {
                const el = document.getElementById('modalDetail');
                if (!el) return;
                new bootstrap.Modal(el).show();
            }

            function populateRequestModal(payload) {
                const perm = payload.permintaan || {};
                const peng = payload.pengiriman || null;

                document.getElementById('modal-tiket-display').textContent = perm.tiket || '-';
                document.getElementById('modal-requester-display').textContent = (perm.user && perm.user.name) ?
                    perm.user.name : (perm.requester_name || 'User');
                document.getElementById('modal-tanggal-request-display').textContent = perm.tanggal_permintaan ?
                    new Date(perm.tanggal_permintaan).toLocaleDateString('id-ID', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    }) :
                    '-';

                const reqBody = document.getElementById('request-table-body');
                reqBody.innerHTML = '';
                (perm.details || []).forEach((it, i) => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                <td>${i + 1}</td>
                <td>${it.nama_item ?? it.nama ?? '-'}</td>
                <td>${it.deskripsi ?? '-'}</td>
                <td>${it.jumlah ?? 0}</td>
                <td>${it.keterangan ?? '-'}</td>
            `;
                    reqBody.appendChild(tr);
                });

                if (peng) {
                    document.getElementById('modal-tanggal-pengiriman-display').textContent = peng
                        .tanggal_transaksi ?
                        new Date(peng.tanggal_transaksi).toLocaleDateString('id-ID', {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                        }) :
                        '-';
                    document.getElementById('modal-resi-display').textContent = peng.no_resi || peng.nomor_resi ||
                        '-';

                    const pengBody = document.getElementById('pengiriman-table-body');
                    pengBody.innerHTML = '';
                    (peng.details || []).forEach((it, i) => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                    <td>${i + 1}</td>
                    <td>${it.nama ?? it.nama_item ?? '-'}</td>
                    <td>${it.merk ?? '-'}</td>
                    <td>${it.sn ?? '-'}</td>
                    <td>${it.tipe ?? '-'}</td>
                    <td>${it.jumlah ?? 0}</td>
                    <td>${it.keterangan ?? '-'}</td>
                `;
                        pengBody.appendChild(tr);
                    });

                    const attachments = peng.attachments || [];
                    document.getElementById('bukti-pengiriman-preview').innerHTML =
                        `<div class="text-muted"><i class="bi bi-image display-6"></i><p class="mt-2">Belum ada bukti pengiriman</p></div>`;
                    document.getElementById('bukti-penerimaan-preview').innerHTML =
                        `<div class="text-muted"><i class="bi bi-image display-6"></i><p class="mt-2">Belum ada bukti penerimaan</p></div>`;

                    if (attachments.length) {
                        const imgGudang = attachments.find(a => a.type === 'img_gudang') || attachments[0];
                        const imgUser = attachments.find(a => a.type === 'img_user');

                        if (imgGudang && imgGudang.url) {
                            document.getElementById('bukti-pengiriman-preview').innerHTML = `
                        <div class="text-center">
                            <a href="${imgGudang.url}" target="_blank" rel="noopener">
                                <img src="${imgGudang.url}" class="img-fluid rounded border" style="max-height:300px" alt="${imgGudang.filename ?? ''}">
                            </a>
                            <p class="mt-2 small text-muted">${imgGudang.filename ?? ''}</p>
                        </div>`;
                        }
                        if (imgUser && imgUser.url) {
                            document.getElementById('bukti-penerimaan-preview').innerHTML = `
                        <div class="text-center">
                            <a href="${imgUser.url}" target="_blank" rel="noopener">
                                <img src="${imgUser.url}" class="img-fluid rounded border" style="max-height:300px" alt="${imgUser.filename ?? ''}">
                            </a>
                            <p class="mt-2 small text-muted">${imgUser.filename ?? ''}</p>
                        </div>`;
                        }
                    }
                } else {
                    document.getElementById('pengiriman-table-body').innerHTML =
                        '<tr><td colspan="7" class="text-center">Belum ada data pengiriman.</td></tr>';
                }
            }

            // Tombol Detail
            document.querySelectorAll('.btn-detail').forEach(btn => {
                btn.addEventListener('click', function() {
                    const tiket = this.dataset.tiket;
                    document.getElementById('modal-tiket-display').textContent = '-';
                    document.getElementById('modal-requester-display').textContent = '-';
                    document.getElementById('modal-tanggal-request-display').textContent = '-';
                    document.getElementById('modal-tanggal-pengiriman-display').textContent = '-';
                    document.getElementById('modal-resi-display').textContent = '-';
                    document.getElementById('request-table-body').innerHTML =
                        '<tr><td colspan="5" class="text-center">Memuat data...</td></tr>';
                    document.getElementById('pengiriman-table-body').innerHTML =
                        '<tr><td colspan="7" class="text-center">Memuat data...</td></tr>';
                    document.getElementById('bukti-pengiriman-preview').innerHTML =
                        '<div class="text-muted"><i class="bi bi-image display-6"></i><p class="mt-2">Memuat bukti pengiriman...</p></div>';
                    document.getElementById('bukti-penerimaan-preview').innerHTML =
                        '<div class="text-muted"><i class="bi bi-image display-6"></i><p class="mt-2">Memuat bukti penerimaan...</p></div>';

                    getTicketData(tiket)
                        .then(payload => {
                            populateRequestModal(payload);
                            showDetailModal();
                        })
                        .catch(err => {
                            console.error('Gagal memuat detail:', err);
                            alert('Gagal memuat detail request.');
                        });
                });
            });

            // Modal status
            window.showStatusDetailModal = function(tiket, userRole) {
                getTicketData(tiket)
                    .then(payload => {
                        const perm = payload.permintaan || {};
                        const statusObj = {
                            ro: normalizeStatus(perm.status_ro),
                            gudang: normalizeStatus(perm.status_gudang),
                            admin: normalizeStatus(perm.status_admin),
                            super_admin: normalizeStatus(perm.status_super_admin),
                            catatan: pickCatatan(perm)
                        };

                        const modal = document.getElementById('status-detail-modal');
                        if (modal && modal.__x) {
                            modal.__x.$data.status = statusObj;
                            modal.__x.$data.role = userRole || 'user';
                            modal.__x.$data.showStatusDetail = true;
                        } else {
                            console.warn('Alpine instance for status modal not found');
                        }
                    })
                    .catch(err => {
                        console.error('Gagal memuat status:', err);
                        alert('Gagal muat detail status approval.');
                    });
            };

            // 🔁 FUNGSI UTAMA: Apply Semua Filter
            function applyFilters() {
                const statusFilter = document.getElementById('statusFilter')?.value;
                const dateFrom = document.getElementById('dateFromFilter')?.value;
                const dateTo = document.getElementById('dateToFilter')?.value;
                const search = document.getElementById('searchFilter')?.value.toLowerCase();

                document.querySelectorAll('tbody tr.ticket-row').forEach(row => {
                    const badge = row.querySelector('[data-status-badge]');
                    const status = badge ? badge.dataset.status : '';
                    const tanggalCellText = row.querySelector('td:nth-child(3)')?.textContent.trim() ||
                    ''; // e.g., "Senin, 05 April 2024"
                    const searchContent = row.textContent.toLowerCase();

                    // 🔹 Parse tanggal Indonesia ke format YYYY-MM-DD
                    const parsedDate = parseIndonesianDate(tanggalCellText);
                    if (!parsedDate) return; // skip jika gagal parse

                    let show = true;

                    // Filter Status
                    if (statusFilter === 'close') show = status === 'close';
                    else if (statusFilter === 'rejected') show = status === 'rejected';

                    // Filter Rentang Tanggal
                    if (dateFrom) show = show && parsedDate >= dateFrom;
                    if (dateTo) show = show && parsedDate <= dateTo;

                    // Filter Pencarian
                    if (search) show = show && searchContent.includes(search);

                    row.style.display = show ? '' : 'none';
                });
            }

            // 🔹 Fungsi bantu: konversi "Senin, 05 April 2024" → "2024-04-05"
            function parseIndonesianDate(indoText) {
                const dayNames = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
                const monthNames = [
                    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                ];

                try {
                    // Hapus nama hari jika ada
                    let datePart = indoText.replace(/^[^,]+,\s*/, '').trim(); // hasil: "05 April 2024"

                    const parts = datePart.split(' ');
                    if (parts.length !== 3) return null;

                    const day = parseInt(parts[0], 10);
                    const monthIndex = monthNames.indexOf(parts[1]);
                    const year = parseInt(parts[2], 10);

                    if (isNaN(day) || monthIndex === -1 || isNaN(year)) return null;

                    // Buat objek Date dan format ke ISO string (YYYY-MM-DD)
                    const date = new Date(year, monthIndex, day);
                    if (date.getDate() !== day) return null; // validasi tanggal

                    return date.toISOString().split('T')[0]; // output: YYYY-MM-DD
                } catch (e) {
                    console.error('Error parsing date:', e);
                    return null;
                }
            }

            // 🎯 Event: Terapkan Filter saat tombol diklik
            document.getElementById('applyFilter')?.addEventListener('click', function() {
                const from = document.getElementById('dateFromFilter').value;
                const to = document.getElementById('dateToFilter').value;

                // Validasi rentang tanggal
                if (from && to && to < from) {
                    alert('Tanggal akhir tidak boleh lebih awal dari tanggal awal.');
                    document.getElementById('dateToFilter').value = '';
                    return;
                }

                applyFilters();
            });

            // 🔁 Reset Filter
            document.getElementById('resetFilter')?.addEventListener('click', function() {
                document.getElementById('statusFilter').value = '';
                document.getElementById('dateFromFilter').value = '';
                document.getElementById('dateToFilter').value = '';
                document.getElementById('searchFilter').value = '';

                applyFilters();
            });
        });
    </script>
@endsection