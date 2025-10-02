@extends('layouts.kepalagudang')

@section('title', 'Request Sparepart - Kepalagudang')

@push('styles')
    <style>
        .table-success th {
            text-align: center;
            font-weight: 600;
        }

        .table tbody td {
            vertical-align: middle;
        }

        /* Kolom No & Aksi: kecil */
        .no-col,
        .aksi-col {
            width: 50px;
            min-width: 50px;
        }

        /* Kolom Nama, Tipe, Keterangan: lebih besar */
        .nama-col,
        .tipe-col,
        .merk-col,
        .kategori-col,
        .jumlah-col,
        .keterangan-col {
            width: 130px;
            min-width: 130px;
        }
    </style>
@endpush

@section('content')

    <h4 class="page-title"><i class="bi bi-cart-check me-2"></i> Daftar Request Sparepart</h4>
    <p class="page-subtitle">Kelola Request dari Field Technisian</p>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="statusFilter" class="form-label">Status</label>
                    <select class="form-select" id="statusFilter">
                        <option value="">Semua Status</option>
                        <option value="disetujui">Disetujui</option>
                        <option value="diproses">Diproses</option>
                        <option value="dikirim">Dikirim</option>
                        <option value="diterima">Diterima</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="dateFilter" class="form-label">Tanggal</label>
                    <input type="date" class="form-control" id="dateFilter">
                </div>
                <div class="col-md-4">
                    <label for="searchFilter" class="form-label">Pencarian</label>
                    <input type="text" class="form-control" id="searchFilter"
                        placeholder="Cari ID Request, Requester, atau Barang...">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-primary w-100">Terapkan Filter</button>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID Request</th>
                            <th>Requester</th>
                            <th>Tanggal Request</th>
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
                                <td class="action-buttons">
                                    <button class="btn btn-success btn-sm btn-terima" data-tiket="{{ $req->tiket }}"
                                        data-requester="{{ $req->user->name ?? 'User' }}"
                                        data-tanggal="{{ \Illuminate\Support\Carbon::parse($req->tanggal_permintaan)->translatedFormat('d M Y') }}">
                                        <i class="bi bi-check-circle"></i> Terima
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada permintaan yang menunggu proses pengiriman.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <nav aria-label="Page navigation" class="mt-4">
                <ul class="pagination justify-content-center">
                    <li class="page-item disabled">
                        <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
                    </li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item">
                        <a class="page-link" href="#">Next</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>


    <!-- Modal Terima & Kirim Barang -->
    <div class="modal fade" id="modalTerima" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="bi bi-box-seam"></i> Terima & Kirim Barang</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <!-- Data Request (readonly) -->
                    <h6 class="fw-bold text-primary mb-3"><i class="bi bi-cart-check"></i> Data Request (readonly)</h6>
                    <div class="mb-3">
                        <p><strong>No Tiket:</strong> <span id="modal-tiket-display">-</span></p>
                        <p><strong>Requester:</strong> <span id="modal-requester">-</span></p>
                        <p><strong>Tanggal Request:</strong> <span id="modal-tanggal">-</span></p>
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
                            <tbody id="detail-request-body">
                                <!-- Akan diisi otomatis oleh JS -->
                            </tbody>
                        </table>
                    </div>

                    <hr>

                    <!-- Form Pengiriman -->
                    <h6 class="fw-bold text-success mb-3"><i class="bi bi-truck"></i> Form Pengiriman</h6>
                    <!-- enctype ditambahkan karena bisa submit file (walau kita pakai FormData di JS) -->
                    <form id="formPengiriman" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="tiket" value="" id="tiketInput">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Pengiriman</label>
                                <input type="date" class="form-control" name="tanggal_pengiriman"
                                    id="tanggal_pengiriman" required>
                            </div>
                        </div>
                        <div class="mt-3 table-responsive">
                            <table class="table table-bordered" id="tabelBarang">
                                <thead class="table-success">
                                    <tr>
                                        <th class="no-col">No</th>
                                        <th class="kategori-col">Kategori</th>
                                        <th class="sn-col">Nomor Serial</th>
                                        <th class="nama-col">Nama</th>
                                        <th class="tipe-col">Tipe</th>
                                        <th class="merk-col">Merk</th>
                                        <th class="jumlah-col">Jumlah</th>
                                        <th class="keterangan-col">Keterangan</th>
                                        <th class="aksi-col">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="no-col">1</td>
                                        <td class="kategori-col">
                                            <select class="form-control kategori-select" name="kategori">
                                                <option value="">Kategori</option>
                                                <option value="aset">Aset</option>
                                                <option value="non-aset">Non-Aset</option>
                                            </select>
                                        </td>
                                        <td class="sn-col"><input type="text" class="form-control sn-input"
                                                placeholder="Nomor Serial" disabled></td>
                                        <td class="nama-col">
                                            <select class="form-control nama-item-select" name="nama_item">
                                                <option value="">Pilih Nama</option>
                                            </select>
                                        </td>
                                        <td class="tipe-col">
                                            <select class="form-control tipe-select" name="tipe">
                                                <option value="">Pilih Tipe</option>
                                            </select>
                                        </td>
                                        <td class="merk-col">
                                            <select class="form-control merk-select" name="merk">
                                                <option value="">Pilih Merk</option>
                                            </select>
                                        </td>

                                        <td class="jumlah-col"><input type="number" class="form-control" value="1"
                                                min="1" required></td>
                                        <td class="keterangan-col">
                                            <input type="text" class="form-control" name="keterangan"
                                                placeholder="Keterangan">
                                        </td>
                                        <td class="aksi-col">
                                            <button type="button" class="btn btn-danger btn-sm"
                                                onclick="hapusBaris(this)">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <button type="button" class="btn btn-outline-success mt-2" onclick="tambahBaris()">
                            <i class="bi bi-plus"></i> Tambah Baris
                        </button>



                        <!-- Layout Kanan-Kiri untuk Opsi Ekspedisi dan Upload File -->
                        <div class="row mt-3">
                            <!-- Kolom Kiri: Opsi Ekspedisi -->
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-header bg-light">
                                        <h6 class="card-title mb-0"><i class="bi bi-truck"></i> Opsi Pengiriman</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Menggunakan ekspedisi?</label>
                                            <div class="d-flex gap-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="ekspedisi"
                                                        id="ekspedisiYa" value="ya">
                                                    <label class="form-check-label" for="ekspedisiYa">
                                                        <i class="bi bi-check-circle"></i> Ya
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="ekspedisi"
                                                        id="ekspedisiTidak" value="tidak" checked>
                                                    <label class="form-check-label" for="ekspedisiTidak">
                                                        <i class="bi bi-x-circle"></i> Tidak
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Kolom Kanan: Upload File -->
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-header bg-light">
                                        <h6 class="card-title mb-0"><i class="bi bi-paperclip"></i> Lampiran File</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Upload File Pendukung</label>
                                            <input type="file" class="form-control" name="files[]" id="fileUpload"
                                                multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                            <div class="form-text mt-2">
                                                <small>Format: PDF, JPG, PNG, DOC, DOCX<br>Maksimal per file: 5MB</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <!-- Tombol Batal -->
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>

                    <!-- Tombol Reject -->
                    <button type="button" class="btn btn-danger" onclick="rejectRequest()">
                        <i class="bi bi-x-circle"></i> Reject
                    </button>

                    <!-- Tombol Approve -->
                    <button type="button" class="btn btn-primary" onclick="approveRequest()">
                        <i class="bi bi-check-circle"></i> Approve
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function() {
            // -----------------------
            // Utility / Setup
            // -----------------------
            function getCsrfToken() {
                return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || null;
            }

            function getSelectedId(selectEl) {
                if (!selectEl) return null;
                const opt = selectEl.options[selectEl.selectedIndex];
                return (opt && opt.dataset && opt.dataset.id) ? opt.dataset.id : null;
            }

            // ensureOption helper
            function ensureOption(selectEl, id, label) {
                if (!selectEl) return null;
                const idStr = (id === undefined || id === null) ? null : String(id);
                let opt = Array.from(selectEl.options).find(o =>
                    (idStr !== null && o.dataset && String(o.dataset.id) === idStr) ||
                    (idStr === null && o.value === label)
                );
                if (!opt) {
                    opt = document.createElement('option');
                    opt.value = label ?? (idStr ?? '');
                    if (idStr !== null) opt.dataset.id = idStr;
                    opt.textContent = label ?? (idStr ?? opt.value);
                    selectEl.appendChild(opt);
                }
                return opt;
            }

            // -----------------------
            // API loaders
            // -----------------------
            async function loadItemsByKategori(selectKategori, targetSelect) {
                const kategori = selectKategori?.value;
                if (!kategori || !targetSelect) return;
                const url = `/requestbarang/api/jenis-barang?kategori=${encodeURIComponent(kategori)}`;
                targetSelect.innerHTML = '<option value="">Memuat nama...</option>';
                try {
                    const res = await fetch(url);
                    if (!res.ok) throw res;
                    const items = await res.json();
                    targetSelect.innerHTML = '<option value="">Pilih </option>';
                    (items || []).forEach(i => {
                        const opt = document.createElement('option');
                        opt.value = i.nama ?? i.name ?? '';
                        if (i.id !== undefined && i.id !== null) opt.dataset.id = i.id;
                        opt.textContent = i.nama ?? i.name ?? opt.value;
                        targetSelect.appendChild(opt);
                    });
                } catch (e) {
                    targetSelect.innerHTML = '<option value="">Gagal muat</option>';
                }
            }

            async function loadTipeByKategoriAndJenis(selectKategori, selectJenis, targetSelect) {
                const kategori = selectKategori?.value;
                const jenisId = getSelectedId(selectJenis);
                if (!kategori || !jenisId || !targetSelect) {
                    if (targetSelect) targetSelect.innerHTML = '<option value="">Pilih </option>';
                    return;
                }
                const url =
                    `/requestbarang/api/tipe-barang?kategori=${encodeURIComponent(kategori)}&jenis_id=${encodeURIComponent(jenisId)}`;
                targetSelect.innerHTML = '<option value="">Memuat tipe...</option>';
                try {
                    const res = await fetch(url);
                    if (!res.ok) throw res;
                    const tipes = await res.json();
                    targetSelect.innerHTML = '<option value="">Pilih </option>';
                    (tipes || []).forEach(t => {
                        const opt = document.createElement('option');
                        opt.value = t.nama ?? t.name ?? '';
                        if (t.id !== undefined && t.id !== null) opt.dataset.id = t.id;
                        opt.textContent = t.nama ?? t.name ?? opt.value;
                        targetSelect.appendChild(opt);
                    });
                } catch (e) {
                    targetSelect.innerHTML = '<option value="">Gagal muat</option>';
                }
            }

            async function loadVendors(selectJenis, selectTipe, targetSelect) {
                const jenisId = getSelectedId(selectJenis);
                const tipeId = getSelectedId(selectTipe);
                if (!jenisId || !tipeId || !targetSelect) {
                    if (targetSelect) targetSelect.innerHTML = '<option value="">Pilih </option>';
                    return;
                }
                const url =
                    `/requestbarang/api/vendor?jenis_id=${encodeURIComponent(jenisId)}&tipe_id=${encodeURIComponent(tipeId)}`;
                targetSelect.innerHTML = '<option value="">Memuat merk...</option>';
                try {
                    const res = await fetch(url);
                    if (!res.ok) throw res;
                    const vendors = await res.json();
                    targetSelect.innerHTML = '<option value="">Pilih </option>';
                    (vendors || []).forEach(v => {
                        const opt = document.createElement('option');
                        opt.value = v.nama ?? v.name ?? '';
                        if (v.id !== undefined && v.id !== null) opt.dataset.id = v.id;
                        opt.textContent = v.nama ?? v.name ?? opt.value;
                        targetSelect.appendChild(opt);
                    });
                } catch (e) {
                    targetSelect.innerHTML = '<option value="">Gagal muat</option>';
                }
            }

            // -----------------------
            // SN lookup
            // -----------------------
            async function fetchItemBySN(sn) {
                if (!sn) return null;
                try {
                    const res = await fetch(`/kepalagudang/sn-info?sn=${encodeURIComponent(sn)}`);
                    if (!res.ok) return null;
                    const data = await res.json();
                    return data.item ?? data;
                } catch (e) {
                    return null;
                }
            }

            // -----------------------
            // Row build & populate
            // -----------------------
            function buildRow(idx, item = {}) {
                const tr = document.createElement('tr');
                tr.innerHTML = `
      <td class="no-col">${idx + 1}</td>
      <td class="kategori-col">
        <select class="form-select kategori-select" name="items[${idx}][kategori]">
          <option value="">Select</option>
          <option value="aset">Aset</option>
          <option value="non-aset">Non-Aset</option>
        </select>
      </td>
      <td class="sn-col"><input type="text" class="form-control sn-input" name="items[${idx}][sn]" placeholder="Nomor Serial" disabled></td>
      <td class="nama-col">
        <select class="form-select nama-item-select" name="items[${idx}][nama_item]">
          <option value="">Select</option>
        </select>
        <input type="hidden" class="jenis-id" name="items[${idx}][jenis_id]" value="">
      </td>
      <td class="tipe-col">
        <select class="form-select tipe-select" name="items[${idx}][tipe]">
          <option value="">Select</option>
        </select>
        <input type="hidden" class="tipe-id" name="items[${idx}][tipe_id]" value="">
      </td>
      <td class="merk-col">
        <select class="form-select merk-select" name="items[${idx}][merk]">
          <option value="">Select</option>
        </select>
        <input type="hidden" class="vendor-id" name="items[${idx}][vendor_id]" value="">
      </td>
      <td class="jumlah-col"><input type="number" class="form-control" name="items[${idx}][jumlah]" value="${item.jumlah || 1}" min="1" required></td>
      <td class="keterangan-col">
        <input type="text" class="form-control" name="items[${idx}][keterangan]" value="${item.keterangan || ''}" placeholder="Keterangan">
      </td>
      <td class="aksi-col">
        <button type="button" class="btn btn-danger btn-sm" onclick="hapusBaris(this)">
          <i class="bi bi-trash"></i>
        </button>
      </td>
    `;
                return tr;
            }

            async function populateRowWithItem(tr, item = {}, snInfo = null) {
                try {
                    const kategoriSelect = tr.querySelector('.kategori-select');
                    const namaSelect = tr.querySelector('.nama-item-select');
                    const tipeSelect = tr.querySelector('.tipe-select');
                    const merkSelect = tr.querySelector('.merk-select');
                    const snInput = tr.querySelector('.sn-input');
                    const jumlahInput = tr.querySelector('input[name*="[jumlah]"]');
                    const keteranganInput = tr.querySelector('input[name*="[keterangan]"]');

                    // 1) set kategori
                    if (item.kategori) {
                        kategoriSelect.value = item.kategori;
                        kategoriSelect.dispatchEvent(new Event('change'));
                    } else {
                        if (namaSelect) namaSelect.innerHTML = '<option value="">Pilih </option>';
                        if (tipeSelect) tipeSelect.innerHTML = '<option value="">Pilih </option>';
                        if (merkSelect) merkSelect.innerHTML = '<option value="">Pilih </option>';
                    }

                    // 2) load nama (jenis) jika kategori tersedia
                    if (kategoriSelect.value) {
                        await loadItemsByKategori(kategoriSelect, namaSelect);

                        const jenisId = item.jenis_id ?? item.nama_item_id ?? null;
                        const jenisLabel = item.nama_item ?? item.nama_item_label ?? null;

                        if (jenisId) {
                            let opt = Array.from(namaSelect.options).find(o => o.dataset && String(o.dataset.id) ===
                                String(jenisId));
                            if (!opt) {
                                opt = document.createElement('option');
                                opt.value = jenisLabel ?? String(jenisId);
                                opt.dataset.id = String(jenisId);
                                opt.textContent = jenisLabel ?? String(jenisId);
                                namaSelect.appendChild(opt);
                            }
                            namaSelect.value = opt.value;
                            namaSelect.dispatchEvent(new Event('change'));
                        } else if (jenisLabel) {
                            let opt = Array.from(namaSelect.options).find(o => o.value === jenisLabel);
                            if (!opt) {
                                opt = document.createElement('option');
                                opt.value = jenisLabel;
                                opt.textContent = jenisLabel;
                                namaSelect.appendChild(opt);
                            }
                            namaSelect.value = opt.value;
                            namaSelect.dispatchEvent(new Event('change'));
                        }
                    }

                    // 3) load tipe jika nama terpilih
                    if (namaSelect.value) {
                        await loadTipeByKategoriAndJenis(kategoriSelect, namaSelect, tipeSelect);

                        const tipeId = item.tipe_id ?? null;
                        const tipeLabel = item.tipe ?? item.tipe_label ?? null;
                        if (tipeId) {
                            let tipeOpt = Array.from(tipeSelect.options).find(o => o.dataset && String(o.dataset
                                .id) === String(tipeId));
                            if (!tipeOpt) {
                                tipeOpt = document.createElement('option');
                                tipeOpt.value = tipeLabel ?? String(tipeId);
                                tipeOpt.dataset.id = String(tipeId);
                                tipeOpt.textContent = tipeLabel ?? String(tipeId);
                                tipeSelect.appendChild(tipeOpt);
                            }
                            tipeSelect.value = tipeOpt.value;
                            tipeSelect.dispatchEvent(new Event('change'));
                        } else if (tipeLabel) {
                            let tipeOpt = Array.from(tipeSelect.options).find(o => o.value === tipeLabel);
                            if (!tipeOpt) {
                                tipeOpt = document.createElement('option');
                                tipeOpt.value = tipeLabel;
                                tipeOpt.textContent = tipeLabel;
                                tipeSelect.appendChild(tipeOpt);
                            }
                            tipeSelect.value = tipeOpt.value;
                            tipeSelect.dispatchEvent(new Event('change'));
                        }
                    } else {
                        if (tipeSelect) tipeSelect.innerHTML = '<option value="">Pilih </option>';
                    }

                    // 4) load merk/vendor jika nama & tipe terpilih
                    if (namaSelect.value && tipeSelect.value) {
                        await loadVendors(namaSelect, tipeSelect, merkSelect);

                        const vendorId = item.vendor_id ?? item.merk_id ?? null;
                        const vendorLabel = item.merk ?? item.merk_label ?? null;
                        if (vendorId) {
                            let findOpt = Array.from(merkSelect.options).find(o => o.dataset && String(o.dataset
                                .id) === String(vendorId));
                            if (!findOpt) {
                                const newOpt = document.createElement('option');
                                newOpt.value = vendorLabel ?? String(vendorId);
                                newOpt.dataset.id = String(vendorId);
                                newOpt.textContent = vendorLabel ?? String(vendorId);
                                merkSelect.appendChild(newOpt);
                                findOpt = newOpt;
                            }
                            merkSelect.value = findOpt.value;
                        } else if (vendorLabel) {
                            let findOpt = Array.from(merkSelect.options).find(o => o.value === vendorLabel);
                            if (!findOpt) {
                                const newOpt = document.createElement('option');
                                newOpt.value = vendorLabel;
                                newOpt.textContent = vendorLabel;
                                merkSelect.appendChild(newOpt);
                                findOpt = newOpt;
                            }
                            merkSelect.value = findOpt.value;
                        }
                    } else {
                        if (merkSelect) merkSelect.innerHTML = '<option value="">Pilih </option>';
                    }

                    // 5) jumlah
                    if (jumlahInput) jumlahInput.value = item.jumlah || 1;

                    // 6) SN handling
                    const snFromRequest = item.sn || item.serial_number || null;
                    if (!snInfo && snFromRequest) snInfo = await fetchItemBySN(snFromRequest);

                    if (snInput) {
                        if (kategoriSelect && kategoriSelect.value === 'aset') {
                            snInput.disabled = false;
                            snInput.required = true;
                        } else {
                            snInput.disabled = true;
                            snInput.required = false;
                            snInput.value = '';
                        }
                        if (snFromRequest) snInput.value = snFromRequest;
                    }

                    // 7) keterangan
                    if (keteranganInput) {
                        keteranganInput.value = (snInfo && (snInfo.keterangan ?? snInfo.note ?? null)) ? (snInfo
                            .keterangan ?? snInfo.note) : '';
                    }

                    // 8) kalau SN punya data, apply (ensureOption)
                    if (snInfo && kategoriSelect.value) {
                        if (snInfo.nama_id || snInfo.id) {
                            const nid = snInfo.nama_id ?? snInfo.id;
                            const label = snInfo.nama ?? snInfo.name ?? String(nid);
                            const opt = ensureOption(namaSelect, nid, label);
                            if (opt) {
                                namaSelect.value = opt.value;
                                namaSelect.dispatchEvent(new Event('change'));
                                await loadTipeByKategoriAndJenis(kategoriSelect, namaSelect, tipeSelect);
                                await loadVendors(namaSelect, tipeSelect, merkSelect);
                            }
                        }
                        if (snInfo.tipe_id) {
                            const tid = snInfo.tipe_id;
                            const label = snInfo.tipe_nama ?? snInfo.tipe ?? String(tid);
                            const optT = ensureOption(tipeSelect, tid, label);
                            if (optT) {
                                tipeSelect.value = optT.value;
                                tipeSelect.dispatchEvent(new Event('change'));
                            }
                        }
                        if (snInfo.vendor_id) {
                            const vid = snInfo.vendor_id;
                            const label = snInfo.vendor_nama ?? snInfo.vendor ?? String(vid);
                            const optV = ensureOption(merkSelect, vid, label);
                            if (optV) merkSelect.value = optV.value;
                        }
                    }

                    // 9) update hidden id inputs
                    const jenisIdInput = tr.querySelector('.jenis-id');
                    const tipeIdInput = tr.querySelector('.tipe-id');
                    const vendorIdInput = tr.querySelector('.vendor-id');

                    if (jenisIdInput && namaSelect) jenisIdInput.value = getSelectedId(namaSelect) || '';
                    if (tipeIdInput && tipeSelect) tipeIdInput.value = getSelectedId(tipeSelect) || '';
                    if (vendorIdInput && merkSelect) vendorIdInput.value = getSelectedId(merkSelect) || '';

                } catch (err) {
                    console.error('populateRowWithItem error:', err);
                }
            }

            // -----------------------
            // Buttons / Modal handling
            // -----------------------
            window.hapusBaris = function(button) {
                const tr = button.closest('tr');
                const tbody = tr.parentElement;
                if (tbody.children.length > 1) {
                    tr.remove();
                    Array.from(tbody.children).forEach((row, i) => {
                        const noCell = row.querySelector('.no-col');
                        if (noCell) noCell.textContent = i + 1;
                        row.querySelectorAll('[name]').forEach(el => {
                            const name = el.getAttribute('name');
                            if (!name) return;
                            const newName = name.replace(/items$$\d+$$/g, `items[${i}]`);
                            el.setAttribute('name', newName);
                        });
                    });
                } else {
                    alert('Minimal satu baris harus ada.');
                }
            };

            window.tambahBaris = function() {
                const tbody = document.querySelector('#tabelBarang tbody');
                const nomorBaru = tbody.children.length + 1;
                const tr = buildRow(nomorBaru - 1, {});
                tbody.appendChild(tr);

                // initial SN state for the new row
                const kategori = tr.querySelector('.kategori-select')?.value;
                const snInput = tr.querySelector('.sn-input');
                if (snInput) {
                    if (!kategori) {
                        snInput.disabled = true;
                        snInput.placeholder = 'Pilih kategori terlebih dahulu';
                        snInput.removeAttribute('required');
                    } else if (kategori === 'aset') {
                        snInput.disabled = false;
                        snInput.required = true;
                        snInput.placeholder = 'Nomor Serial (wajib untuk aset)';
                    } else {
                        snInput.disabled = true;
                        snInput.placeholder = 'Tidak diperlukan untuk Non-Aset';
                        snInput.removeAttribute('required');
                    }
                }
            };

            // allRequests must be provided by Blade: const allRequests = @json($requests);
            const allRequests = @json($requests);

            document.querySelectorAll('.btn-terima').forEach(button => {
                button.addEventListener('click', function() {
                    const tiket = this.dataset.tiket;
                    const requester = this.dataset.requester;
                    const tanggal = this.dataset.tanggal;

                    document.getElementById('tiketInput').value = tiket;
                    document.getElementById('modal-tiket-display').textContent = tiket;
                    document.getElementById('modal-requester').textContent = requester;
                    document.getElementById('modal-tanggal').textContent = tanggal;

                    const req = allRequests.find(r => r.tiket === tiket) || {
                        details: []
                    };
                    const detailBody = document.getElementById('detail-request-body');
                    detailBody.innerHTML = '';

                    if (req.details && req.details.length) {
                        req.details.forEach((item, index) => {
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
            <td>${index + 1}</td>
            <td>${item.nama_item ?? item.nama ?? '-'}</td>
            <td>${item.deskripsi ?? '-'}</td>
            <td>${item.jumlah ?? '-'}</td>
            <td>${item.keterangan ?? '-'}</td>
          `;
                            detailBody.appendChild(tr);
                        });
                    } else {
                        const tr = document.createElement('tr');
                        tr.innerHTML = '<td colspan="6" class="text-center">Tidak ada item.</td>';
                        detailBody.appendChild(tr);
                    }

                    // build form rows
                    const tbody = document.querySelector('#tabelBarang tbody');
                    tbody.innerHTML = '';
                    if (!req.details || req.details.length === 0) {
                        tbody.appendChild(buildRow(0, {}));
                    } else {
                        req.details.forEach((item, idx) => tbody.appendChild(buildRow(idx, item)));
                    }

                    // show modal and populate rows (fetch SNs in parallel)
                    const modalEl = document.getElementById('modalTerima');
                    const modal = new bootstrap.Modal(modalEl);

                    async function onShown() {
                        const rows = Array.from(document.querySelectorAll('#tabelBarang tbody tr'));
                        const snList = (req.details || []).map(d => d.sn || d.serial_number ||
                            null);
                        const fetchPromises = snList.map(sn => sn ? fetchItemBySN(sn) : Promise
                            .resolve(null));
                        const snInfos = await Promise.all(fetchPromises);
                        for (let i = 0; i < rows.length; i++) {
                            const tr = rows[i];
                            const item = (req.details && req.details[i]) ? req.details[i] : {};
                            const snInfoForThis = snInfos[i] || null;
                            await populateRowWithItem(tr, item, snInfoForThis);
                        }
                        modalEl.removeEventListener('shown.bs.modal', onShown);
                    }

                    modalEl.addEventListener('shown.bs.modal', onShown);
                    modal.show();
                });
            });

            // -----------------------
            // Approve / Reject
            // -----------------------
            async function approveRequest() {
                const tiket = document.getElementById('tiketInput').value;
                if (!tiket) {
                    alert('Tiket tidak ditemukan.');
                    return;
                }

                const csrfToken = getCsrfToken();
                if (!csrfToken) {
                    alert('CSRF token tidak ditemukan.');
                    return;
                }

                // tanggal
                const tanggalInput = document.getElementById('tanggal_pengiriman');
                if (!tanggalInput || !tanggalInput.value) {
                    alert('Tanggal Pengiriman wajib diisi.');
                    return;
                }
                const tanggalPengiriman = tanggalInput.value;
                const catatan = document.querySelector('[name="catatan"]')?.value || '';

                // ambil data items
                const rows = document.querySelectorAll('#tabelBarang tbody tr');
                const items = [];
                let valid = true;
                for (const row of rows) {
                    const kategori = row.querySelector('.kategori-select')?.value || '';
                    const namaLabel = row.querySelector('.nama-item-select')?.value?.trim() || '';
                    const sn = row.querySelector('.sn-col input')?.value.trim() || null;
                    const jumlahVal = row.querySelector('.jumlah-col input')?.value.trim();
                    const jumlah = jumlahVal ? parseInt(jumlahVal, 10) : 0;
                    const keterangan = row.querySelector('.keterangan-col input')?.value.trim() || null;

                    if (!kategori || !namaLabel || !jumlah || jumlah <= 0) {
                        valid = false;
                        continue;
                    }
                    if (kategori === 'aset' && (!sn || sn === '')) {
                        alert(`Serial Number wajib diisi untuk barang Aset di baris ${row.rowIndex}.`);
                        return;
                    }

                    items.push({
                        kategori,
                        nama_item: namaLabel,
                        tipe: row.querySelector('.tipe-select')?.value || null,
                        merk: row.querySelector('.merk-select')?.value || null,
                        jenis_id: parseInt(row.querySelector('.jenis-id')?.value) || null,
                        tipe_id: parseInt(row.querySelector('.tipe-id')?.value) || null,
                        vendor_id: parseInt(row.querySelector('.vendor-id')?.value) || null,
                        sn: sn || null,
                        jumlah,
                        keterangan: keterangan || null
                    });
                }

                if (!valid || items.length === 0) {
                    alert('Isi minimal satu barang dengan lengkap.');
                    return;
                }

                // ekspedisi (hanya ya/tidak)
                const ekspedisi = document.querySelector('input[name="ekspedisi"]:checked')?.value || 'tidak';

                // Build FormData - IMPORTANT: append _token as well for safety
                const fd = new FormData();
                fd.append('_token', csrfToken); // helpful for serverside CSRF
                fd.append('tiket', tiket);
                fd.append('tanggal_pengiriman', tanggalPengiriman);
                fd.append('catatan', catatan);
                fd.append('ekspedisi', ekspedisi);
                fd.append('items', JSON.stringify(items));

                // files[] append (multiple) + backward compatibility file_upload
                const fileEl = document.getElementById('fileUpload');
                if (fileEl && fileEl.files && fileEl.files.length) {
                    for (let i = 0; i < fileEl.files.length; i++) fd.append('files[]', fileEl.files[i]);
                    fd.append('file_upload', fileEl.files[0]);
                }

                // DEBUG: inspect FormData (for dev only)
                // This will print keys and values (files shown as File objects)
                for (const pair of fd.entries()) {
                    console.log('FD', pair[0], pair[1]);
                }

                try {
                    const response = await fetch(`/kepalagudang/request/${encodeURIComponent(tiket)}/approve`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken // do not set Content-Type
                        },
                        body: fd
                    });

                    if (!response.ok) {
                        const txt = await response.text();
                        console.error('Server response not OK:', response.status, txt);
                        // show server validation JSON if present
                        try {
                            const json = JSON.parse(txt);
                            console.error('Server JSON error:', json);
                            alert('Gagal: ' + (json.message || txt));
                        } catch (e) {
                            alert('Server error ' + response.status);
                        }
                        return;
                    }

                    const data = await response.json();
                    if (data.success) {
                        alert(data.message || 'Berhasil.');
                        location.reload();
                    } else {
                        alert('Gagal: ' + (data.message || 'Terjadi kesalahan.'));
                    }

                } catch (err) {
                    console.error('Approve request error:', err);
                    alert('Terjadi kesalahan teknis. Silakan coba lagi atau refresh halaman.');
                }
            }
            window.approveRequest = approveRequest;

            function rejectRequest() {
                const tiket = document.getElementById('tiketInput').value;
                if (!tiket) return;

                const catatan = prompt('Masukkan alasan penolakan (opsional):', '');
                const csrfToken = getCsrfToken();
                if (!csrfToken) {
                    alert('CSRF token tidak ditemukan. Silakan refresh halaman.');
                    return;
                }

                fetch(`/kepalagudang/request/${tiket}/reject`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            tiket,
                            catatan
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            const modal = bootstrap.Modal.getInstance(document.getElementById('modalTerima'));
                            modal.hide();
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
            window.rejectRequest = rejectRequest;

            // -----------------------
            // Event delegation: selects, SN inputs
            // -----------------------
            document.addEventListener('change', function(e) {
                const el = e.target;

                if (el.matches('.kategori-select')) {
                    const tr = el.closest('tr');
                    const namaSelect = tr.querySelector('.nama-item-select');
                    const snInput = tr.querySelector('.sn-input');

                    if (snInput) {
                        if (el.value === 'aset') {
                            snInput.disabled = false;
                            snInput.required = true;
                            snInput.placeholder = 'Nomor Serial (wajib untuk aset)';
                        } else if (el.value === 'non-aset') {
                            snInput.disabled = true;
                            snInput.required = false;
                            snInput.value = '';
                            snInput.placeholder = 'Tidak diperlukan untuk Non-Aset';
                        } else {
                            snInput.disabled = true;
                            snInput.required = false;
                            snInput.value = '';
                            snInput.placeholder = 'Pilih kategori terlebih dahulu';
                        }
                    }

                    if (typeof loadItemsByKategori === 'function') loadItemsByKategori(el, namaSelect);
                }

                if (el.matches('.nama-item-select')) {
                    const tr = el.closest('tr');
                    const hid = tr.querySelector('.jenis-id');
                    const selId = getSelectedId(el);
                    if (hid) hid.value = selId || '';
                    const kategori = tr.querySelector('.kategori-select');
                    const tipe = tr.querySelector('.tipe-select');
                    if (kategori && tipe) loadTipeByKategoriAndJenis(kategori, el, tipe);
                }

                if (el.matches('.tipe-select')) {
                    const tr = el.closest('tr');
                    const hid = tr.querySelector('.tipe-id');
                    const selId = getSelectedId(el);
                    if (hid) hid.value = selId || '';
                    const nama = tr.querySelector('.nama-item-select');
                    const merk = tr.querySelector('.merk-select');
                    if (nama && merk) loadVendors(nama, el, merk);
                }

                if (el.matches('.merk-select')) {
                    const tr = el.closest('tr');
                    const hid = tr.querySelector('.vendor-id');
                    const selId = getSelectedId(el);
                    if (hid) hid.value = selId || '';
                }
            });

            // SN input handlers (focusout & Enter)
            document.addEventListener('focusout', function(e) {
                const el = e.target;
                if (el.matches('#tabelBarang .sn-col input')) {
                    if (el.disabled) return;
                    handleSnInputEvent(el);
                }
            }, true);

            document.addEventListener('keydown', function(e) {
                const el = e.target;
                if (e.key === 'Enter' && el.matches('#tabelBarang .sn-col input')) {
                    e.preventDefault();
                    if (el.disabled) return;
                    handleSnInputEvent(el);
                }
            });

            async function handleSnInputEvent(inputEl) {
                if (!inputEl || inputEl.disabled) return;
                const sn = inputEl.value.trim();
                const tr = inputEl.closest('tr');
                if (!tr) return;
                const kategoriSelect = tr.querySelector('.kategori-select');
                if (kategoriSelect && kategoriSelect.value !== 'aset') return;

                const keteranganInput = tr.querySelector('.keterangan-col input, .keterangan-col textarea');
                if (keteranganInput) keteranganInput.value = '';

                if (!sn) return;

                const snInfo = await fetchItemBySN(sn);
                if (!snInfo) {
                    alert(`SN "${sn}" tidak ditemukan di database.`);
                    if (keteranganInput) keteranganInput.value = '';
                    return;
                }

                const namaSelect = tr.querySelector('.nama-item-select');
                const tipeSelect = tr.querySelector('.tipe-select');
                const merkSelect = tr.querySelector('.merk-select');

                // set nama/jenis
                const namaId = snInfo.nama_id ?? snInfo.jenis_id ?? null;
                if (namaId) {
                    const optNama = ensureOption(namaSelect, namaId, snInfo.nama ?? snInfo.name ?? String(namaId));
                    if (optNama) {
                        namaSelect.value = optNama.value;
                        namaSelect.dispatchEvent(new Event('change'));
                    }
                } else if (snInfo.nama || snInfo.name) {
                    const optNama = ensureOption(namaSelect, null, snInfo.nama ?? snInfo.name);
                    if (optNama) {
                        namaSelect.value = optNama.value;
                        namaSelect.dispatchEvent(new Event('change'));
                    }
                }

                // load tipe and set
                await loadTipeByKategoriAndJenis(kategoriSelect, namaSelect, tipeSelect);
                const tipeId = snInfo.tipe_id ?? null;
                if (tipeId) {
                    const optT = ensureOption(tipeSelect, tipeId, snInfo.tipe_nama ?? snInfo.tipe ?? String(
                        tipeId));
                    if (optT) {
                        tipeSelect.value = optT.value;
                        tipeSelect.dispatchEvent(new Event('change'));
                    }
                } else if (snInfo.tipe_nama || snInfo.tipe) {
                    const optT = ensureOption(tipeSelect, null, snInfo.tipe_nama ?? snInfo.tipe);
                    if (optT) {
                        tipeSelect.value = optT.value;
                        tipeSelect.dispatchEvent(new Event('change'));
                    }
                }

                // load vendors and set merk
                await loadVendors(namaSelect, tipeSelect, merkSelect);
                const vendorId = snInfo.vendor_id ?? null;
                if (vendorId) {
                    const optV = ensureOption(merkSelect, vendorId, snInfo.vendor_nama ?? snInfo.vendor ?? String(
                        vendorId));
                    if (optV) merkSelect.value = optV.value;
                } else if (snInfo.vendor_nama || snInfo.vendor) {
                    const optV = ensureOption(merkSelect, null, snInfo.vendor_nama ?? snInfo.vendor);
                    if (optV) merkSelect.value = optV.value;
                }

                // keterangan
                if (keteranganInput) keteranganInput.value = snInfo.keterangan ?? snInfo.note ?? '';

                // update hidden id inputs
                const jenisIdInput = tr.querySelector('.jenis-id');
                const tipeIdInput = tr.querySelector('.tipe-id');
                const vendorIdInput = tr.querySelector('.vendor-id');
                if (jenisIdInput && namaSelect) jenisIdInput.value = getSelectedId(namaSelect) || '';
                if (tipeIdInput && tipeSelect) tipeIdInput.value = getSelectedId(tipeSelect) || '';
                if (vendorIdInput && merkSelect) vendorIdInput.value = getSelectedId(merkSelect) || '';
            }

            // Initial state for static rows (on page load)
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('#tabelBarang tbody tr').forEach((tr, idx) => {
                    const kategori = tr.querySelector('.kategori-select')?.value;
                    const snInput = tr.querySelector('.sn-col input');
                    if (!snInput) return;
                    if (!kategori) {
                        snInput.disabled = true;
                        snInput.placeholder = 'Pilih kategori terlebih dahulu';
                        snInput.removeAttribute('required');
                    } else if (kategori === 'aset') {
                        snInput.disabled = false;
                        snInput.required = true;
                        snInput.placeholder = 'Nomor Serial (wajib untuk aset)';
                    } else {
                        snInput.disabled = true;
                        snInput.value = '';
                        snInput.placeholder = 'Tidak diperlukan untuk Non-Aset';
                        snInput.removeAttribute('required');
                    }

                    // reindex names for compatibility
                    tr.querySelectorAll('[name]').forEach(el => {
                        const name = el.getAttribute('name');
                        if (!name) return;
                        const newName = name.replace(/items$$\d+$$/g, `items[${idx}]`);
                        el.setAttribute('name', newName);
                    });
                });
            });

        })();
    </script>
@endpush
