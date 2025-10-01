@extends('layouts.kepalagudang')

@section('title', 'Manajemen Data User')
@section('page_title', 'Manajemen Data User')

@push('styles')
    <style>
        .password-container {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 38px;
            cursor: pointer;
            color: #6c757d;
            z-index: 5;
            background: white;
            padding: 0 5px;
        }

        /* Tuning ukuran tabel & action buttons */
        .table.table-hover th,
        .table.table-hover td {
            padding: 0.45rem 0.6rem;
            /* lebih compact */
            vertical-align: middle;
        }

        /* Buat kolom aksi lebih sempit dan mencegah wrapping */
        .table.table-hover th.action-col,
        .table.table-hover td.action-col {
            width: 125px;
            white-space: nowrap;
        }

        /* Konsistenkan ukuran tombol aksi kecil */
        .btn-action {
            padding: 0.25rem 0.45rem;
            font-size: 0.85rem;
            line-height: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            height: 32px;
            border-radius: 6px;
        }

        /* Ikon dalam tombol sedikit lebih kecil agar proporsional */
        .btn-action i {
            font-size: 1rem;
        }

        /* Untuk varian kecil (opsional, ketika menggunakan btn-sm) */
        .btn-action.btn-sm {
            padding: 0.2rem 0.35rem;
            min-width: 34px;
            height: 30px;
            font-size: 0.82rem;
        }
    </style>
@endpush
@section('content')
    <input type="hidden" name="_token" id="csrf_token" value="{{ csrf_token() }}">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="fw-bold mb-0"><i class="bi bi-person-fill-gear"></i> Manajemen Data User</h4>
                <p class="text-muted mb-0">Kelola semua data user</p>
            </div>
            <div>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="bi bi-plus-circle me-1"></i> Tambah User
                </button>
                <a href="{{ route('kepalagudang.data') }}" class="btn btn-outline-secondary">

                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>
    </div>


    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('kepalagudang.user.index') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="roleFilter" class="form-label">Role</label>
                        <select class="form-select" id="roleFilter" name="role">
                            <option value="">Semua Role</option> <!-- Ini memastikan role tidak terpilih otomatis -->
                            @foreach ($roles as $value => $label)
                                <option value="{{ $value }}"
                                    {{ (string) request('role') === (string) $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>


                    <div class="col-md-4">
                        <label for="regionFilter" class="form-label">Region</label>
                        <select class="form-select" id="regionFilter" name="region">
                            <option value="">Semua Region</option>
                            @foreach ($regions as $reg)
                                <option value="{{ $reg }}"
                                    {{ (string) request('region') === (string) $reg ? 'selected' : '' }}>
                                    {{ $reg }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="search" class="form-label">Search</label>
                        <div class="input-group">
                            <input type="text" id="search" name="search" class="form-control"
                                placeholder="Nama atau email" value="{{ request('search') }}">
                        </div>
                    </div>

                    <div class="col-12 text-end">
                        <a href="{{ route('kepalagudang.user.index') }}" class="btn btn-light me-2">
                            <i class="bi bi-arrow-clockwise me-1"></i> Reset
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-filter me-1"></i> Terapkan Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- User Table Card -->
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Jabatan</th>
                            <th>RO</th>
                            <th>Atasan</th>
                            <th>Joined</th>
                            <th class="action-col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $index => $user)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $user->name ?? '-' }}</td>
                                <td>{{ $user->email ?? '-' }}</td>
                                <td data-role-id="{{ $user->role }}">
                                    <span class="badge {{ $user->role_badge }}">
                                        {{ $user->role_name }}
                                    </span>
                                </td>
                                <td>{{ $user->bagian ?? '-' }}</td>
                                <td>{{ $user->region ?? '-' }}</td>
                                <td>{{ \Illuminate\Support\Str::title($user->atasan ?? '-') }}</td>
                                <td>{{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}</td>
                                <td>
                                    <div class="d-flex justify-content-between">
                                        <!-- Tombol Edit hanya tampil saat data ada -->
                                        <button class="btn btn-primary btn-action btn-edit" data-id="{{ $user->id }}"
                                            data-name="{{ $user->name }}" data-email="{{ $user->email }}"
                                            data-role="{{ $user->role }}" data-region="{{ $user->region }}"
                                            data-atasan="{{ $user->atasan }}" data-bs-toggle="modal"
                                            data-bs-target="#editUserModal">
                                            <i class="bi bi-pencil"></i>
                                        </button>

                                        <button class="btn btn-danger btn-action btn-delete" data-bs-toggle="modal"
                                            data-bs-target="#deleteUserModal" data-user-name="{{ $user->name }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach

                        <!-- Jika tidak ada data, tampilkan pesan -->
                        @forelse ($users as $user)
                            <!-- Data loop -->
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">Tidak ada data pengguna yang ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>


                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    Menampilkan {{ $users->firstItem() }} hingga {{ $users->lastItem() }} dari
                    {{ $users->total() }} entri
                </div>
                <nav aria-label="Page navigation">
                    {{ $users->appends(request()->query())->links('pagination::bootstrap-5') }}
                </nav>
            </div>
        </div>
    </div>

    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('kepalagudang.user.store') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="addUserModalLabel">Tambah User Baru</h5>
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
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama</label>
                            <input type="text" name="name" class="form-control" id="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" id="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" id="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="form-control"
                                id="password_confirmation" required>
                        </div>

                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-select" name="role" id="role" required>
                                <option value="">Pilih Role</option>
                                <option value="1">Superadmin</option>
                                <option value="2">Regional Office Head</option>
                                <option value="3">Warehouse Head</option>
                                <option value="4">Field Technician</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="region" class="form-label">Regional Office (RO)</label>
                            <select class="form-select" name="region" id="region">
                                <option value="">Pilih RO</option>
                                @foreach ($regions as $r)
                                    <option value="{{ $r->kode_region }}">{{ $r->nama_region}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="atasan" class="form-label">Atasan</label>
                            <input type="text" name="atasan" class="form-control" id="atasan" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    @if (isset($user))
        <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="editUserForm" action="{{ route('kepalagudang.user.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT') <!-- Method spoofing -->
                        <div class="modal-header">
                            <h5 class="modal-title" id="editUserModalLabel">Edit Data User</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="editUserId" name="userId" value="{{ $user->id }}">

                            <div class="mb-3">
                                <label for="editName" class="form-label">Nama</label>
                                <input type="text" name="name" class="form-control" id="editName"
                                    value="{{ old('name', $user->name) }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="editEmail" class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" id="editEmail"
                                    value="{{ old('email', $user->email) }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="editPassword" class="form-label">Password Baru (opsional)</label>
                                <div class="password-container">
                                    <input type="password" name="password" class="form-control" id="editPassword">
                                    <i class="bi bi-eye-slash password-toggle" id="toggleEditPassword"></i>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="editPasswordConfirmation" class="form-label">Konfirmasi Password Baru</label>
                                <div class="password-container">
                                    <input type="password" name="password_confirmation" class="form-control"
                                        id="editPasswordConfirmation">
                                    <i class="bi bi-eye-slash password-toggle" id="toggleEditPasswordConfirmation"></i>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="editRole" class="form-label">Role</label>
                                <select class="form-select" name="role" id="editRole" required>
                                    <option value="1" {{ old('role', $user->role) == 1 ? 'selected' : '' }}>
                                        Superadmin</option>
                                    <option value="2" {{ old('role', $user->role) == 2 ? 'selected' : '' }}>Regional
                                        Office Head</option>
                                    <option value="3" {{ old('role', $user->role) == 3 ? 'selected' : '' }}>Warehouse
                                        Head</option>
                                    <option value="4" {{ old('role', $user->role) == 4 ? 'selected' : '' }}>Field
                                        Technician</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="editRegion" class="form-label">Regional Office (RO)</label>
                                <select class="form-select" name="region" id="editRegion">
                                    <option value="JKT" {{ old('region', $user->region) == 'JKT' ? 'selected' : '' }}>
                                        RO Jakarta</option>
                                    <option value="bandung"
                                        {{ old('region', $user->region) == 'bandung' ? 'selected' : '' }}>RO Bandung
                                    </option>
                                    <option value="Pusat"
                                        {{ old('region', $user->region) == 'Pusat' ? 'selected' : '' }}>Pusat</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="editAtasan" class="form-label">Atasan</label>
                                <input type="text" name="atasan" class="form-control" id="editAtasan"
                                    value="{{ old('atasan', $user->atasan) }}" required>
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
    @endif




    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Hapus User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus user <strong id="deleteUserName">—</strong>?</p>
                    <p class="text-danger">Tindakan ini tidak dapat dibatalkan.</p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <!-- tombol konfirmasi: simpan id/data untuk JS -->
                    <button type="button" id="deleteConfirmBtn" class="btn btn-danger" data-user-id="">Hapus</button>
                </div>
            </div>
        </div>
    </div>
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

        const filterForm = document.querySelector('form');
        const submitButton = document.querySelector('button[type="submit"]');

        // Hanya submit ketika tombol Terapkan Filter diklik
        submitButton.addEventListener('click', function(event) {
            // Tidak melakukan submit secara otomatis
            event.preventDefault(); // Mencegah form di-submit saat pilihan berubah

            // Lakukan submit form secara manual
            filterForm.submit();
        });


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
            document.addEventListener('DOMContentLoaded', function() {
                const deleteModalEl = document.getElementById('deleteUserModal');
                const deleteUserNameEl = document.getElementById('deleteUserName');
                const deleteConfirmBtn = document.getElementById('deleteConfirmBtn');

                // Ketika modal dibuka via tombol dengan data- attributes, isi modal
                // Bootstrap akan memicu event 'show.bs.modal' pada elemen modal
                if (deleteModalEl) {
                    deleteModalEl.addEventListener('show.bs.modal', function(event) {
                        // button yang memicu modal
                        const triggerBtn = event.relatedTarget;
                        const userId = triggerBtn?.getAttribute('data-user-id') || '';
                        const userName = triggerBtn?.getAttribute('data-user-name') || '';

                        // set teks nama di modal
                        if (deleteUserNameEl) deleteUserNameEl.textContent = userName || '';

                        // simpan userId pada tombol konfirmasi
                        if (deleteConfirmBtn) deleteConfirmBtn.setAttribute('data-user-id', userId);
                    });
                }

                // Handler untuk tombol konfirmasi hapus
                if (deleteConfirmBtn) {
                    deleteConfirmBtn.addEventListener('click', async function() {
                        const userId = this.getAttribute('data-user-id');
                        if (!userId) {
                            showToast('User ID tidak ditemukan.', 'warning');
                            return;
                        }

                        // optional: konfirmasi ulang (atau gunakan showConfirm modal)
                        // this.disabled = true;

                        // ambil CSRF token (pastikan ada input hidden atau meta tag)
                        const csrfToken = document.getElementById('csrf_token') ? document
                            .getElementById('csrf_token').value : document.querySelector(
                                'meta[name="csrf-token"]')?.getAttribute('content');

                        try {
                            const params = new URLSearchParams();
                            params.append('_token', csrfToken || '');
                            params.append('_method', 'DELETE');

                            const res = await fetch(
                                `/kepalagudang/datauser/${encodeURIComponent(userId)}`, {
                                    method: 'POST', // Laravel: POST + _method=DELETE
                                    headers: {
                                        'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
                                        'Accept': 'application/json'
                                    },
                                    body: params.toString()
                                });

                            const data = await res.json().catch(() => ({}));
                            if (!res.ok) throw new Error(data.message || 'Gagal menghapus user.');

                            // Hapus baris dari DOM (jika ada)
                            const btnInRow = document.querySelector(
                                `.btn-delete[data-user-id="${userId}"]`);
                            const row = btnInRow ? btnInRow.closest('tr') : null;
                            if (row) row.remove();
                            else location.reload(); // fallback

                            // tutup modal
                            const modalInstance = bootstrap.Modal.getInstance(deleteModalEl);
                            if (modalInstance) modalInstance.hide();

                            showToast(data.message || 'User berhasil dihapus.', 'success');
                        } catch (err) {
                            console.error(err);
                            showToast(err.message || 'Terjadi kesalahan saat menghapus user.',
                                'danger');
                        } finally {
                            // this.disabled = false;
                        }
                    });
                }
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



        function escapeHtml(str) {
            if (str === null || str === undefined) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Ambil CSRF token dari hidden input Blade
            const csrfToken = document.getElementById('csrf_token') ? document.getElementById('csrf_token').value :
                '';

            // -------------------------
            // Password confirmation logic
            // -------------------------
            const passwordField = document.getElementById('editPassword');
            const passwordConfirmationField = document.getElementById('editPasswordConfirmation');
            if (passwordField && passwordConfirmationField) {
                passwordField.addEventListener('input', function() {
                    if (passwordField.value === '') {
                        passwordConfirmationField.disabled = true;
                        passwordConfirmationField.value = '';
                    } else {
                        passwordConfirmationField.disabled = false;
                    }
                });
            }

            // -------------------------
            // Edit modal: populate fields when clicking Edit
            // -------------------------
            document.querySelectorAll('.btn-edit').forEach(button => {
                button.addEventListener('click', function() {
                    const userId = this.getAttribute('data-id') || '';
                    const name = this.getAttribute('data-name') || '';
                    const email = this.getAttribute('data-email') || '';
                    const role = this.getAttribute('data-role') || '';
                    const region = this.getAttribute('data-region') || '';
                    const atasan = this.getAttribute('data-atasan') || '';

                    // set values in modal
                    const editUserIdInput = document.getElementById('editUserId');
                    if (editUserIdInput) editUserIdInput.value = userId;

                    const editName = document.getElementById('editName');
                    if (editName) editName.value = name;

                    const editEmail = document.getElementById('editEmail');
                    if (editEmail) editEmail.value = email;

                    const editRole = document.getElementById('editRole');
                    if (editRole) editRole.value = role;

                    const editRegion = document.getElementById('editRegion');
                    if (editRegion) editRegion.value = region;

                    const editAtasan = document.getElementById('editAtasan');
                    if (editAtasan) editAtasan.value = atasan;

                    // reset password fields
                    if (passwordField) passwordField.value = '';
                    if (passwordConfirmationField) {
                        passwordConfirmationField.value = '';
                        passwordConfirmationField.disabled = true;
                    }
                });
            });

            // -------------------------
            // Submit edit form via fetch
            // -------------------------
            const editForm = document.getElementById('editUserForm');
            if (editForm) {
                editForm.addEventListener('submit', async function(e) {
                    e.preventDefault();

                    const userId = document.getElementById('editUserId') ? document.getElementById(
                        'editUserId').value : null;
                    if (!userId) {
                        showToast('User ID tidak ditemukan.', 'warning');
                        return;
                    }

                    // Ambil data dari form
                    const formData = new FormData(editForm);

                    // Jika password kosong, hapus kedua field supaya backend tidak memvalidasi
                    if (!formData.get('password')) {
                        formData.delete('password');
                        formData.delete('password_confirmation');
                    }

                    // Override method dan sertakan token agar Laravel menerima sebagai PUT
                    formData.append('_method', 'PUT');
                    formData.append('_token', csrfToken);

                    try {
                        const res = await fetch(
                            `/kepalagudang/datauser/${encodeURIComponent(userId)}`, {
                                method: 'POST', // POST + _method=PUT
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken,
                                    'Accept': 'application/json'
                                },
                                body: formData
                            });

                        const data = await res.json().catch(() => ({}));

                        if (!res.ok) {
                            const msg = data.message || 'Gagal memperbarui data.';
                            throw new Error(msg);
                        }

                        // Success: jika backend mengembalikan updated user, update row DOM
                        if (data.user) {
                            const updated = data.user;
                            // Cari tombol edit yang berhubungan dengan userId
                            const editBtn = document.querySelector(`.btn-edit[data-id="${userId}"]`);
                            if (editBtn) {
                                // update data attributes on edit button for future edits
                                editBtn.setAttribute('data-name', updated.name || '');
                                editBtn.setAttribute('data-email', updated.email || '');
                                editBtn.setAttribute('data-role', updated.role || '');
                                editBtn.setAttribute('data-region', updated.region || '');
                                editBtn.setAttribute('data-atasan', updated.atasan || '');

                                // update table row cells
                                const tr = editBtn.closest('tr');
                                if (tr) {
                                    const cells = tr.querySelectorAll('td');

                                    if (cells.length >= 7) {
                                        if (cells[1]) cells[1].textContent = updated.name || '-';
                                        if (cells[2]) cells[2].textContent = updated.email || '-';
                                        if (cells[3]) {
                                            // update role badge (server should provide role_badge & role_name)
                                            const roleBadge = updated.role_badge ? updated.role_badge :
                                                'badge-secondary';
                                            const roleName = updated.role_name ? updated.role_name : (
                                                updated.role || '-');
                                            cells[3].innerHTML =
                                                `<span class="badge ${roleBadge}">${roleName}</span>`;
                                        }
                                        if (cells[4]) cells[4].textContent = updated.bagian || '-';
                                        if (cells[5]) cells[5].textContent = updated.region || '-';
                                        if (cells[6]) cells[6].textContent = updated.atasan ? updated
                                            .atasan.charAt(0).toUpperCase() + updated.atasan.slice(1) :
                                            '-';
                                    }
                                }
                            }
                        } else {
                            // fallback apabila server tidak mengembalikan user: reload halaman
                            location.reload();
                        }

                        // tutup modal edit
                        const editModalEl = document.getElementById('editUserModal');
                        if (editModalEl) {
                            const modalInstance = bootstrap.Modal.getInstance(editModalEl);
                            if (modalInstance) modalInstance.hide();
                        }

                        showToast(data.message || 'Data user berhasil diperbarui!', 'success');
                        setTimeout(() => location.reload(), 1200);
                    } catch (err) {
                        console.error(err);
                        showToast(data.message || 'Terjadi kesalahan saat memperbarui data.', 'danger');
                        saveBtn.disabled = false;
                        saveBtn.innerHTML = originalHtml;
                    }
                });
            }

            // -------------------------
            // Filter table (dynamic table selector)
            // -------------------------
            const searchInput = document.getElementById('searchFilter');
            const roleFilter = document.getElementById('roleFilter');

            // cari table rows di halaman ini (paling relevan)
            const table = document.querySelector('.card .table') || document.querySelector('table.table');
            const tableRows = table ? table.querySelectorAll('tbody tr') : [];




            // -------------------------
            // Delete modal: populate name and store userId for confirmation
            // -------------------------
            const deleteModal = document.getElementById('deleteUserModal');
            const deleteConfirmBtn = deleteModal ? deleteModal.querySelector('.modal-footer .btn-danger') : null;

            document.querySelectorAll('.btn-delete').forEach(button => {
                button.addEventListener('click', function() {
                    const tr = this.closest('tr');
                    // coba ambil userId dari tombol .btn-edit di baris yang sama
                    const editBtnInRow = tr ? tr.querySelector('.btn-edit') : null;
                    const userId = editBtnInRow ? editBtnInRow.getAttribute('data-id') : null;
                    const userName = this.getAttribute('data-user-name') || (tr ? tr.querySelector(
                        'td:nth-child(2)')?.textContent : '');

                    // set modal text
                    const strongEl = deleteModal ? deleteModal.querySelector('.modal-body strong') :
                        null;
                    if (strongEl) strongEl.textContent = userName || '';

                    // simpan userId pada tombol konfirmasi untuk digunakan nanti
                    if (deleteConfirmBtn) deleteConfirmBtn.setAttribute('data-user-id', userId ||
                        '');
                });
            });

            // -------------------------
            // Confirm delete handler
            // -------------------------
            if (deleteConfirmBtn) {
                deleteConfirmBtn.addEventListener('click', async function() {
                    const userId = this.getAttribute('data-user-id');
                    if (!userId) {
                        showToast('User ID tidak ditemukan.', 'warning');
                        return;
                    }

                    // disable button saat request
                    this.disabled = true;
                    const originalText = this.textContent;
                    this.textContent = 'Menghapus...';

                    try {
                        // gunakan form-encoded POST + _method=DELETE
                        const params = new URLSearchParams();
                        params.append('_token', csrfToken);
                        params.append('_method', 'DELETE');

                        const res = await fetch(
                            `/kepalagudang/datauser/${encodeURIComponent(userId)}`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
                                    'Accept': 'application/json'
                                },
                                body: params.toString()
                            });

                        const data = await res.json().catch(() => ({}));
                        if (!res.ok) {
                            throw new Error(data.message || 'Gagal menghapus user.');
                        }

                        // hapus row dari DOM
                        const editBtn = document.querySelector(`.btn-edit[data-id="${userId}"]`);
                        if (editBtn) {
                            const row = editBtn.closest('tr');
                            if (row) row.remove();
                        } else {
                            // fallback reload
                            location.reload();
                        }

                        // tutup modal
                        if (deleteModal) {
                            const modalInstance = bootstrap.Modal.getInstance(deleteModal);
                            if (modalInstance) modalInstance.hide();
                        }
                        showToast(data.message || 'User berhasil dihapus.', 'success');
                        setTimeout(() => location.reload(), 1100);
                    } catch (err) {
                        console.error(err);
                        showToast('Terjadi kesalahan: ' + (err.message || err), 'danger');
                        btn.disabled = false;
                        btn.innerHTML = originalHtml;
                    } finally {
                        this.disabled = false;
                        this.textContent = originalText;
                    }
                });
            }
        });
        document.addEventListener('DOMContentLoaded', function() {
            // Fungsi untuk toggle password
            function setupPasswordToggle(toggleId, inputId) {
                const toggle = document.getElementById(toggleId);
                const input = document.getElementById(inputId);

                toggle.addEventListener('click', function() {
                    // Toggle type input
                    const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                    input.setAttribute('type', type);

                    // Toggle icon
                    this.classList.toggle('bi-eye');
                    this.classList.toggle('bi-eye-slash');
                });
            }


            setupPasswordToggle('toggleEditPassword', 'editPassword');
            setupPasswordToggle('toggleEditPasswordConfirmation', 'editPasswordConfirmation');
        });
    </script>
@endpush
