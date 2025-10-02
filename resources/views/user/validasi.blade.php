@extends('layouts.user')

@section('title', 'Validasi Penerimaan sparepart')

@section('content')
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="page-title mb-1"><i class="bi bi-cart-check me-2"></i> Validasi Penerimaan sparepart</h4>
            <p class="text-muted mb-0">Konfirmasi penerimaan sparepart yang sudah dikirim ke Anda.</p>
        </div>
        <div class="badge bg-primary fs-6 p-2">
            <i class="bi bi-list-check me-1"></i> Total: {{ $requests->count() }} Request
        </div>
    </div>


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
                                        @if ($req->status_penerimaan == 'diterima')
                                            <span
                                                class="inline-flex items-center px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Diterima</span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">Dikirim</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    @if ($req->status_penerimaan != 'diterima')
                                        <button class="btn btn-success btn-sm btn-terima"
                                            data-tiket="{{ $req->tiket }}" data-bs-toggle="modal"
                                            data-bs-target="#modalTerima">
                                            <i class="bi bi-check-circle me-1"></i> Terima
                                        </button>
                                    @else
                                        <button class="btn btn-outline-success btn-sm" disabled>
                                            <i class="bi bi-check-circle me-1"></i> Sudah Diterima
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">
                                    <i class="fas fa-inbox fa-3x text-gray-400 block mb-3"></i>
                                    <p>Belum ada sparepart yang menunggu konfirmasi.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <!-- Modal Terima Barang -->
    <div class="modal fade" id="modalTerima" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="bi bi-check-circle"></i> Konfirmasi Penerimaan Barang</h5>
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
                        <p><strong>Tanggal Pengiriman:</strong> <span id="modal-tanggal-pengiriman-display">-</span>
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
                    
                    <!-- Bukti Pengiriman -->
                    <div class="row mt-3">
                        <div class="col-12">
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
                    </div>

                    <!-- Layout Kanan-Kiri untuk Opsi Ekspedisi dan Upload File -->
                    <div class="row mt-3">
                        <!-- Kolom Kiri: Opsi Ekspedisi -->
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header bg-light">
                                    <h6 class="card-title mb-0"><i class="bi bi-ticket-perforated"></i> Resi Pengiriman</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Nomor Resi</label>
                                        <input type="text" class="form-control" name="no_resi"
                                            placeholder="Nomor tracking pengiriman">
                                    </div>
                                    <!-- Form tambahan jika memilih Ya -->
                                    <div id="formEkspedisi" class="mt-3" style="display: none;">
                                        <div class="mb-3">
                                            <label class="form-label">Nama Ekspedisi</label>
                                            <input type="text" class="form-control" name="nama_ekspedisi"
                                                placeholder="JNE, TIKI, POS Indonesia">
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
                                        <input type="file" class="form-control" name="file_upload"
                                            id="fileUpload">
                                        <div class="form-text mt-2">
                                            <small>Format: PDF, JPG, PNG, DOC, DOCX<br>Maksimal: 5MB</small>
                                        </div>
                                    </div>
                                    <div id="previewFoto" class="mt-2" style="display: none;">
                                        <img src="" alt="Preview" class="img-thumbnail" style="max-width: 200px;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Input hidden untuk tiket -->
                    <input type="hidden" id="inputTiket" name="tiket">
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-success" id="btnKonfirmasi">
                        <i class="bi bi-check-circle me-1"></i> Konfirmasi Penerimaan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
    (function () {
      document.addEventListener('DOMContentLoaded', function () {
        // -----------------------
        // Helper functions
        // -----------------------
        const safeQuery = (sel, root = document) => root.querySelector(sel);
        const safeQueryAll = (sel, root = document) => Array.from(root.querySelectorAll(sel));
        const getCsrfToken = () =>
          document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        const formatDateId = (iso) => {
          try {
            return new Date(iso).toLocaleDateString('id-ID', {
              year: 'numeric',
              month: 'long',
              day: 'numeric'
            });
          } catch (e) {
            return iso ?? '-';
          }
        };

        // -----------------------
        // Preview image (create if missing)
        // -----------------------
        const fileInput =
          safeQuery('#fileUpload') || safeQuery('input[type="file"][name="file_upload"]') || null;
        let previewImg = safeQuery('#previewFoto img');

        if (!previewImg && fileInput && fileInput.parentElement) {
          const previewContainer = safeQuery('#previewFoto');
          if (previewContainer) {
            previewImg = document.createElement('img');
            previewImg.alt = 'Preview';
            previewImg.className = 'img-thumbnail';
            previewImg.style.maxWidth = '200px';
            previewContainer.appendChild(previewImg);
          }
        }

        if (fileInput) {
          fileInput.addEventListener('change', function (e) {
            const file = e.target.files && e.target.files[0];
            if (!file) {
              if (previewImg) { 
                const previewContainer = safeQuery('#previewFoto');
                if (previewContainer) previewContainer.style.display = 'none';
                previewImg.src = ''; 
              }
              return;
            }
            const reader = new FileReader();
            reader.onload = function (ev) {
              if (previewImg) { 
                previewImg.src = ev.target.result; 
                const previewContainer = safeQuery('#previewFoto');
                if (previewContainer) previewContainer.style.display = 'block';
              }
            };
            reader.readAsDataURL(file);
          });
        }

        // -----------------------
        // Ensure hidden tiket input exists
        // -----------------------
        let inputTiket = safeQuery('#inputTiket');
        const modalTerima = safeQuery('#modalTerima');

        if (!inputTiket) {
          inputTiket = document.createElement('input');
          inputTiket.type = 'hidden';
          inputTiket.id = 'inputTiket';
          inputTiket.name = 'tiket';
          const body = modalTerima ? (safeQuery('.modal-body', modalTerima) || modalTerima) : document.body;
          body.appendChild(inputTiket);
        }

        // -----------------------
        // Bootstrap modal helper (Bootstrap 5)
        // -----------------------
        const getBootstrapModal = (element) => {
          if (!element) return null;
          if (typeof bootstrap === 'undefined' || !bootstrap.Modal) return null;
          return bootstrap.Modal.getOrCreateInstance(element);
        };
        const modalInstanceTerima = getBootstrapModal(modalTerima);

        // Table placeholders
        const requestTableBody = safeQuery('#request-table-body');
        const pengirimanTableBody = safeQuery('#pengiriman-table-body');

        // -----------------------
        // loadRequestDetailToModal — robust version
        // -----------------------
        async function loadRequestDetailToModal(tiket) {
          if (!tiket) return;

          // Reset UI placeholders
          safeQuery('#modal-tiket-display') && (safeQuery('#modal-tiket-display').textContent = '-');
          safeQuery('#modal-requester-display') && (safeQuery('#modal-requester-display').textContent = '-');
          safeQuery('#modal-tanggal-request-display') && (safeQuery('#modal-tanggal-request-display').textContent = '-');
          safeQuery('#modal-tanggal-pengiriman-display') && (safeQuery('#modal-tanggal-pengiriman-display').textContent = '-');

          if (inputTiket) inputTiket.value = tiket;
          if (previewImg) { 
            const previewContainer = safeQuery('#previewFoto');
            if (previewContainer) previewContainer.style.display = 'none';
            previewImg.src = ''; 
          }

          if (requestTableBody) requestTableBody.innerHTML = '<tr><td colspan="5" class="text-center">Memuat data...</td></tr>';
          if (pengirimanTableBody) pengirimanTableBody.innerHTML = '<tr><td colspan="7" class="text-center">Memuat data...</td></tr>';

          try {
            const url = `/user/validasi/${encodeURIComponent(tiket)}/api?_=${Date.now()}`;
            const resp = await fetch(url, {
              method: 'GET',
              headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' },
              credentials: 'same-origin',
              cache: 'no-store'
            });

            const rawText = await resp.clone().text();
            if (!resp.ok) {
              console.error('Fetch error:', resp.status, rawText);
              throw new Error(`Fetch gagal: ${resp.status}`);
            }

            let data;
            try {
              data = rawText ? JSON.parse(rawText) : {};
            } catch (e) {
              console.error('JSON parse error. Response text:', rawText);
              throw e;
            }

            // Safe extraction
            const permintaan = data.permintaan || null;
            const pengiriman = data.pengiriman || null;
            const attachments = Array.isArray(pengiriman?.attachments) ? pengiriman.attachments : [];

            // Populate permintaan basic info
            if (permintaan) {
              safeQuery('#modal-tiket-display') && (safeQuery('#modal-tiket-display').textContent = permintaan.tiket ?? tiket);
              safeQuery('#modal-requester-display') && (safeQuery('#modal-requester-display').textContent = permintaan.user?.name ?? '-');
              if (permintaan.tanggal_permintaan) {
                safeQuery('#modal-tanggal-request-display') && (safeQuery('#modal-tanggal-request-display').textContent = formatDateId(permintaan.tanggal_permintaan));
              }

              // Preview foto_bukti_penerimaan (opsional)
              if (previewImg && permintaan.foto_bukti_penerimaan) {
                let src = permintaan.foto_bukti_penerimaan;
                if (!/^https?:\/\//i.test(src) && !src.startsWith('/')) src = '/storage/' + src;
                previewImg.src = src;
                const previewContainer = safeQuery('#previewFoto');
                if (previewContainer) previewContainer.style.display = 'block';
              }
            } else {
              safeQuery('#modal-tiket-display') && (safeQuery('#modal-tiket-display').textContent = tiket);
            }

            // Populate permintaan details table
            if (requestTableBody) {
              requestTableBody.innerHTML = '';
              const details = Array.isArray(permintaan?.details) ? permintaan.details : [];
              if (details.length === 0) {
                requestTableBody.innerHTML = '<tr><td colspan="5" class="text-center">Tidak ada item.</td></tr>';
              } else {
                details.forEach((item, idx) => {
                  const tr = document.createElement('tr');
                  tr.innerHTML = `
                    <td>${idx + 1}</td>
                    <td>${item.nama_item ?? item.nama ?? '-'}</td>
                    <td>${item.deskripsi ?? '-'}</td>
                    <td>${item.jumlah ?? '-'}</td>
                    <td>${item.keterangan ?? '-'}</td>
                  `;
                  requestTableBody.appendChild(tr);
                });
              }
            }

            // Populate pengiriman table
            if (pengiriman) {
              if (pengiriman.tanggal_transaksi) {
                safeQuery('#modal-tanggal-pengiriman-display') && (safeQuery('#modal-tanggal-pengiriman-display').textContent = formatDateId(pengiriman.tanggal_transaksi));
              }
              if (pengirimanTableBody) {
                pengirimanTableBody.innerHTML = '';
                const pDetails = Array.isArray(pengiriman.details) ? pengiriman.details : [];
                if (pDetails.length === 0) {
                  pengirimanTableBody.innerHTML = '<tr><td colspan="7" class="text-center">Tidak ada item pengiriman.</td></tr>';
                } else {
                  pDetails.forEach((item, idx) => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                      <td>${idx + 1}</td>
                      <td>${item.nama ?? item.nama_item ?? '-'}</td>
                      <td>${item.merk ?? '-'}</td>
                      <td>${item.sn ?? '-'}</td>
                      <td>${item.tipe ?? '-'}</td>
                      <td>${item.jumlah ?? '-'}</td>
                      <td>${item.keterangan ?? '-'}</td>
                    `;
                    pengirimanTableBody.appendChild(tr);
                  });
                }
              }
            } else {
              if (pengirimanTableBody) pengirimanTableBody.innerHTML = '<tr><td colspan="7" class="text-center">Belum ada data pengiriman.</td></tr>';
            }

            // Attachments: bukti pengiriman & bukti penerimaan (safe DOM updates)
            const buktiPengirimanEl = document.getElementById('bukti-pengiriman-preview');
            const buktiPenerimaanEl = document.getElementById('bukti-penerimaan-preview');

            if (buktiPengirimanEl) buktiPengirimanEl.innerHTML = '';
            if (buktiPenerimaanEl) buktiPenerimaanEl.innerHTML = '';

            const imgGudang = attachments.find(a => a.type === 'img_gudang') || attachments[0] || null;
            const imgUser = attachments.find(a => a.type === 'img_user') || null;

            if (imgGudang && imgGudang.url) {
              if (buktiPengirimanEl) {
                buktiPengirimanEl.innerHTML = `
                  <div class="text-center">
                    <a href="${imgGudang.url}" target="_blank" rel="noopener">
                      <img src="${imgGudang.url}" class="img-fluid rounded border" style="max-height:300px" alt="${imgGudang.filename}">
                    </a>
                    <p class="mt-2 small text-muted">${imgGudang.filename}</p>
                  </div>`;
              }
            } else {
              if (buktiPengirimanEl) buktiPengirimanEl.innerHTML = `<div class="text-muted"><i class="bi bi-image display-6"></i><p class="mt-2">Belum ada bukti pengiriman</p></div>`;
            }

            if (imgUser && imgUser.url) {
              if (buktiPenerimaanEl) {
                buktiPenerimaanEl.innerHTML = `
                  <div class="text-center">
                    <a href="${imgUser.url}" target="_blank" rel="noopener">
                      <img src="${imgUser.url}" class="img-fluid rounded border" style="max-height:300px" alt="${imgUser.filename}">
                    </a>
                    <p class="mt-2 small text-muted">${imgUser.filename}</p>
                  </div>`;
              }
            } else {
              if (buktiPenerimaanEl) buktiPenerimaanEl.innerHTML = `<div class="text-muted"><i class="bi bi-image display-6"></i><p class="mt-2">Belum ada bukti penerimaan</p></div>`;
            }

            if (!buktiPengirimanEl) console.debug('Elemen #bukti-pengiriman-preview tidak ditemukan di DOM.');
            if (!buktiPenerimaanEl) console.debug('Elemen #bukti-penerimaan-preview tidak ditemukan di DOM.');

          } catch (err) {
            console.error('Error loadRequestDetailToModal:', err);
            if (requestTableBody) requestTableBody.innerHTML = '<tr><td colspan="5" class="text-center">Gagal memuat data.</td></tr>';
            if (pengirimanTableBody) pengirimanTableBody.innerHTML = '<tr><td colspan="7" class="text-center">Gagal memuat data.</td></tr>';
            alert('Gagal memuat detail request. Lihat console untuk detail.');
          }
        }

        // -----------------------
        // Bind .btn-terima clicks
        // -----------------------
        safeQueryAll('.btn-terima').forEach((button) => {
          button.addEventListener('click', function () {
            const tiket = this.dataset.tiket || this.getAttribute('data-tiket');
            if (!tiket) return;
            loadRequestDetailToModal(tiket).finally(() => { if (modalInstanceTerima) modalInstanceTerima.show(); });
          });
        });

        // -----------------------
        // Submit konfirmasi (FormData)
        // -----------------------
        const btnKonfirmasi = safeQuery('#btnKonfirmasi');
        if (btnKonfirmasi) {
          btnKonfirmasi.addEventListener('click', async function () {
            const tiketVal = (inputTiket && inputTiket.value) ||
              (safeQuery('#modal-tiket-display') && safeQuery('#modal-tiket-display').textContent) || '';
            if (!tiketVal) { alert('Tiket tidak ditemukan. Coba tutup dan buka kembali modal.'); return; }

            const fd = new FormData();
            fd.append('tiket', tiketVal);

            const namaEkspedisiEl = safeQuery('input[name="nama_ekspedisi"]');
            const noResiEls = safeQueryAll('input[name="no_resi"]');
            const noResiEl = noResiEls.find(i => i.offsetParent !== null && i.value.trim() !== '') || noResiEls[0] || null;

            if (namaEkspedisiEl && namaEkspedisiEl.value.trim() !== '') fd.append('nama_ekspedisi', namaEkspedisiEl.value.trim());
            if (noResiEl && noResiEl.value.trim() !== '') fd.append('no_resi', noResiEl.value.trim());

            // Attach first chosen file (if any)
            const fileEls = safeQueryAll('input[type="file"]');
            for (const fEl of fileEls) {
              if (fEl.files && fEl.files.length > 0) { fd.append(fEl.name || 'file_upload', fEl.files[0]); break; }
            }

            // Append other named inputs inside modal (skip duplicates)
            const otherInputs = safeQueryAll('#modalTerima [name]');
            otherInputs.forEach(el => {
              const name = el.name;
              if (!name) return;
              if (el.type === 'file') return;
              if (name === 'tiket' || name === 'nama_ekspedisi' || name === 'no_resi') return;
              const val = el.value !== undefined ? el.value : '';
              fd.append(name, val);
            });

            // Post to endpoint
            const endpoint = `/user/validasi/${encodeURIComponent(tiketVal)}/terima`;
            try {
              const resp = await fetch(endpoint, { method: 'POST', headers: { 'X-CSRF-TOKEN': getCsrfToken() }, body: fd });
              console.log(resp)
const text = await resp.text();

if (!resp.ok) {
  console.error('Server returned non-OK:', resp.status, text);
  alert('Server error: ' + resp.status + '. Lihat console untuk detail.');
  return;
}

let data;
try { data = text ? JSON.parse(text) : {}; } catch (e) {
  console.error('Invalid JSON response:', text);
  alert('Response JSON invalid. Lihat console untuk detail.');
  return;
}

              if (resp.ok && data && data.success) {
                alert(data.message || 'Penerimaan berhasil dikonfirmasi.');
                if (modalInstanceTerima) modalInstanceTerima.hide();
                window.location.reload();
              } else {
                const msg = data?.message || `Gagal: status ${resp.status}`;
                alert(msg);
              }
            } catch (err) {
              console.error('Error saat submit konfirmasi:', err);
              alert('Terjadi kesalahan saat mengirim konfirmasi. Cek console untuk detail.');
            }
          });
        }

        // -----------------------
        // Search filter
        // -----------------------
        const searchInput = safeQuery('#searchFilter');
        if (searchInput) {
          searchInput.addEventListener('input', function () {
            const filter = this.value.trim().toLowerCase();
            const rows = document.querySelectorAll('.table.table-hover tbody tr');
            rows.forEach(row => {
              const text = row.textContent.toLowerCase();
              row.style.display = filter === '' || text.includes(filter) ? '' : 'none';
            });
          });
        }

        // -----------------------
        // Toggle formEkspedisi
        // -----------------------
        (function handleFormEkspedisiToggle() {
          const formEkspedisi = safeQuery('#formEkspedisi');
          if (!formEkspedisi) return;

          const toggle = safeQuery('[name="show_ekspedisi"]') || safeQuery('#toggleEkspedisi');

          if (!toggle) {
            const anyValue = safeQueryAll('#formEkspedisi input, #formEkspedisi select, #formEkspedisi textarea')
              .some(i => i.value && i.value.trim() !== '');
            formEkspedisi.style.display = anyValue ? 'block' : 'none';
            return;
          }

          const apply = () => {
            if (toggle.type === 'checkbox') formEkspedisi.style.display = toggle.checked ? 'block' : 'none';
            else {
              const val = toggle.value;
              formEkspedisi.style.display = val === '1' || val.toLowerCase() === 'ya' || val.toLowerCase() === 'yes' ? 'block' : 'none';
            }
          };

          toggle.addEventListener('change', apply);
          apply();
        })();

        // End DOMContentLoaded
      });
    })();
    </script>
@endsection