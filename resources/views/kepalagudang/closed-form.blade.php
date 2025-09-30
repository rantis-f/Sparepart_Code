@extends('layouts.kepalagudang')

@section('title', 'Closed Form - Validasi User')

@section('content')
    <div class="container-fluid">
        <div class="page-header">
            <h1 class="page-title">
                <i class="bi bi-file-earmark-check me-2 text-primary"></i>
                Closed Form (Validasi User)
            </h1>
            <p class="page-subtitle">Klik "Closed" untuk memverifikasi detail permintaan</p>
        </div>

        <div class="table-container">
            <h5><i class="bi bi-list-ul me-2"></i> Daftar Permintaan Tervalidasi</h5>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Nomor Tiket</th>
                            <th>Requester</th>
                            <th>Tanggal Validasi</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($permintaans as $index => $p)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><span class="text-primary fw-bold">{{ $p->tiket }}</span></td>
                                <td>{{$p ->user->name ?? '-'}}</td>
                                <td>{{ \Carbon\Carbon::parse($p->tanggal_penerimaan)->translatedFormat('j F Y') }}</td>
                                <td>
                                    <!-- 🔘 Tombol "Closed" di kolom Aksi -->
                                    <button class="btn btn-sm btn-success" onclick="showClosedDetail('{{ $p->tiket }}')">
                                        <i class="bi bi-check-circle me-1"></i> Closed
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Detail Closed Form -->
    <div class="modal fade" id="modalClosedDetail" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="bi bi-clipboard-check"></i> Verifikasi Closed Form</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Data Request -->
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

                    <!-- Data Pengiriman -->
                    <h6 class="fw-bold text-success mb-3"><i class="bi bi-truck"></i> Data Pengiriman</h6>
                    <div class="mb-3">
                        <p><strong>Tanggal Pengiriman:</strong> <span id="modal-tanggal-pengiriman-display">-</span></p>
                        <p><strong>Ekspedisi:</strong> <span id="modal-ekspedisi-display">-</span></p>
                        <p><strong>Nomor Resi:</strong> <span id="modal-resi-display">-</span></p>
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
                    <h6 class="fw-bold text-info mb-3"><i class="bi bi-image"></i>Lampiran</h6>
                    <div class="row">
                        <!-- Card Bukti Pengiriman (Kiri) -->
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-header bg-warning text-dark">
                                    <i class="bi bi-truck me-1"></i> Bukti Pengiriman
                                </div>
                                <div class="card-body text-center">
                                    <div id="bukti-pengiriman-preview" class="d-flex justify-content-center align-items-center"
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
                                    <div id="bukti-penerimaan-preview" class="d-flex justify-content-center align-items-center"
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <!-- 🔘 Tombol "Closed" di dalam modal -->
                    <button type="button" class="btn btn-success" id="btnClosedFinal">
                        <i class="bi bi-check-circle me-1"></i> Closed
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
<script>
function showClosedDetail(tiket) {
    const modalEl = document.getElementById('modalClosedDetail');
    const modal = new bootstrap.Modal(modalEl);
    modal.show();

    // Reset UI
    document.getElementById('modal-tiket-display').textContent = '-';
    document.getElementById('modal-requester-display').textContent = '-';
    document.getElementById('modal-tanggal-request-display').textContent = '-';
    document.getElementById('modal-tanggal-pengiriman-display').textContent = '-';
    document.getElementById('modal-ekspedisi-display').textContent = '-';
    document.getElementById('modal-resi-display').textContent = '-';
    document.getElementById('request-table-body').innerHTML = '<tr><td colspan="5" class="text-center">Memuat...</td></tr>';
    document.getElementById('pengiriman-table-body').innerHTML = '<tr><td colspan="7" class="text-center">Memuat...</td></tr>';
    document.getElementById('bukti-pengiriman-preview').innerHTML = '<div class="text-muted"><i class="bi bi-image display-6"></i><p class="mt-2">Memuat bukti pengiriman...</p></div>';
    document.getElementById('bukti-penerimaan-preview').innerHTML = '<div class="text-muted"><i class="bi bi-image display-6"></i><p class="mt-2">Memuat bukti penerimaan...</p></div>';

    // fetch dari server
    fetch(`/kepalagudang/closed-form/${encodeURIComponent(tiket)}/detail`)
        .then(res => {
            if (!res.ok) throw new Error('Network response was not ok: ' + res.status);
            return res.json();
        })
        .then(data => {
            const permintaan = data.permintaan;
            const pengiriman = data.pengiriman;
             const attachments = data?.pengiriman?.attachments || [];

            // permintaan
            document.getElementById('modal-tiket-display').textContent = permintaan.tiket ?? tiket;
            document.getElementById('modal-requester-display').textContent = permintaan.user?.name ?? (permintaan.user?.region ? `User ${permintaan.user.region}` : 'User');
            document.getElementById('modal-tanggal-request-display').textContent = permintaan.tanggal_permintaan ? new Date(permintaan.tanggal_permintaan).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '-';

            // permintaan items
            const reqBody = document.getElementById('request-table-body');
            reqBody.innerHTML = '';
            if (permintaan.details && permintaan.details.length) {
                permintaan.details.forEach((it, idx) => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `<td>${idx+1}</td>
                                    <td>${it.nama_item ?? it.nama ?? '-'}</td>
                                    <td>${it.deskripsi ?? '-'}</td>
                                    <td>${it.jumlah ?? '-'}</td>
                                    <td>${it.keterangan ?? '-'}</td>`;
                    reqBody.appendChild(tr);
                });
            } else {
                reqBody.innerHTML = '<tr><td colspan="5" class="text-center">Tidak ada item.</td></tr>';
            }

            // pengiriman
            if (pengiriman) {
                document.getElementById('modal-tanggal-pengiriman-display').textContent = pengiriman.tanggal_transaksi ? new Date(pengiriman.tanggal_transaksi).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '-';
                document.getElementById('modal-ekspedisi-display').textContent = pengiriman.ekspedisi ?? '-';
                document.getElementById('modal-resi-display').textContent = pengiriman.no_resi ?? '-';

                // pengiriman items
                const pengBody = document.getElementById('pengiriman-table-body');
                pengBody.innerHTML = '';
                if (pengiriman.details && pengiriman.details.length) {
                    pengiriman.details.forEach((it, idx) => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `<td>${idx+1}</td>
                                        <td>${it.nama ?? '-'}</td>
                                        <td>${it.merk ?? '-'}</td>
                                        <td>${it.sn ?? '-'}</td>
                                        <td>${it.tipe ?? '-'}</td>
                                        <td>${it.jumlah ?? '-'}</td>
                                        <td>${it.keterangan ?? '-'}</td>`;
                        pengBody.appendChild(tr);
                    });
                } else {
                    pengBody.innerHTML = '<tr><td colspan="7" class="text-center">Tidak ada item pengiriman.</td></tr>';
                }

                // attachments: tampilkan gambar bukti pengiriman & bukti penerimaan (cari berdasarkan type)
                const buktiPengirimanEl = document.getElementById('bukti-pengiriman-preview');
                const buktiPenerimaanEl = document.getElementById('bukti-penerimaan-preview');
                buktiPengirimanEl.innerHTML = '';
                buktiPenerimaanEl.innerHTML = '';

                // coba cari type 'img_gudang' dan 'img_user' (atau sesuaikan type Anda)
                const imgGudang = attachments.find(a => a.type === 'img_gudang') || attachments[0] || null;
                const imgUser = attachments.find(a => a.type === 'img_user') || null;

                if (imgGudang && imgGudang.url) {
                    buktiPengirimanEl.innerHTML = `<div class="text-center">
                        <a href="${imgGudang.url}" target="_blank">
                            <img src="${imgGudang.url}" class="img-fluid rounded border" style="max-height:300px" alt="${imgGudang.filename}">
                        </a>
                        <p class="mt-2 small text-muted">${imgGudang.filename}</p>
                    </div>`;
                } else {
                    buktiPengirimanEl.innerHTML = `<div class="text-muted"><i class="bi bi-image display-6"></i><p class="mt-2">Belum ada bukti pengiriman</p></div>`;
                }

                if (imgUser && imgUser.url) {
                    buktiPenerimaanEl.innerHTML = `<div class="text-center">
                        <a href="${imgUser.url}" target="_blank">
                            <img src="${imgUser.url}" class="img-fluid rounded border" style="max-height:300px" alt="${imgUser.filename}">
                        </a>
                        <p class="mt-2 small text-muted">${imgUser.filename}</p>
                    </div>`;
                } else {
                    buktiPenerimaanEl.innerHTML = `<div class="text-muted"><i class="bi bi-image display-6"></i><p class="mt-2">Belum ada bukti penerimaan</p></div>`;
                }

            } else {
                // jika belum ada pengiriman
                document.getElementById('modal-tanggal-pengiriman-display').textContent = '-';
                document.getElementById('modal-ekspedisi-display').textContent = '-';
                document.getElementById('modal-resi-display').textContent = '-';
                document.getElementById('pengiriman-table-body').innerHTML = '<tr><td colspan="7" class="text-center">Belum ada data pengiriman.</td></tr>';
                document.getElementById('bukti-pengiriman-preview').innerHTML = `<div class="text-muted"><i class="bi bi-image display-6"></i><p class="mt-2">Belum ada bukti pengiriman</p></div>`;
                document.getElementById('bukti-penerimaan-preview').innerHTML = `<div class="text-muted"><i class="bi bi-image display-6"></i><p class="mt-2">Belum ada bukti penerimaan</p></div>`;
            }

            // simpan tiket di tombol close (data attribute) untuk pemanggilan close endpoint
            const btnClosed = document.getElementById('btnClosedFinal');
            btnClosed.dataset.tiket = tiket;
        })
        .catch(err => {
            console.error('Fetch error:', err);
            document.getElementById('request-table-body').innerHTML = '<tr><td colspan="5" class="text-center text-danger">Gagal memuat data.</td></tr>';
            document.getElementById('pengiriman-table-body').innerHTML = '<tr><td colspan="7" class="text-center text-danger">Gagal memuat data.</td></tr>';
            document.getElementById('bukti-pengiriman-preview').innerHTML = `<div class="text-muted"><i class="bi bi-exclamation-triangle display-6"></i><p class="mt-2">Gagal memuat.</p></div>`;
            document.getElementById('bukti-penerimaan-preview').innerHTML = `<div class="text-muted"><i class="bi bi-exclamation-triangle display-6"></i><p class="mt-2">Gagal memuat.</p></div>`;
        });
}

// tombol Closed: panggil endpoint close
document.getElementById('btnClosedFinal').addEventListener('click', function () {
    const tiket = this.dataset.tiket;
    if (!tiket) return alert('Tiket tidak diketahui.');

    if (!confirm('Apakah Anda yakin ingin menutup form ini?')) return;

    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    fetch(`/kepalagudang/closed-form/${encodeURIComponent(tiket)}/verify`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert(data.message || 'Form berhasil ditutup.');
            location.reload();
        } else {
            alert('Gagal menutup form: ' + (data.message || ''));
        }
    })
    .catch(err => {
        console.error(err);
        alert('Terjadi kesalahan teknis.');
    });
});
</script>
    @endpush
@endsection