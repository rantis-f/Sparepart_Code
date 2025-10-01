@extends('layouts.kepalagudang')
@section('title', 'Dashboard Kepala Gudang')

@push('styles')
@endpush

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">Dashboard Kepala Gudang</h3>
        <span class="badge badge-date"><i class="bi bi-calendar me-1"></i>
            <span id="current-date">{{ date('d F Y') }}</span>
        </span>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="dashboard-card p-4">
                <div class="card-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-box-arrow-in-down"></i>
                </div>
                <h4 class="stats-number">{{ $totalMasuk ?? 0 }}</h4>
                <p class="stats-title">IN</p>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="dashboard-card p-4">
                <div class="card-icon bg-danger bg-opacity-10 text-danger">
                    <i class="bi bi-box-arrow-up"></i>
                </div>
               <h4 class="stats-number">{{ $totalKeluar ?? 0 }}</h4>
                <p class="stats-title">OUT</p>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="dashboard-card p-4">
                <div class="card-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <h4 class="stats-number">{{ $totalPending ?? 0 }}</h4>
                <p class="stats-title">Pending Request</p>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="dashboard-card p-4">
                <div class="card-icon bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-clock-history"></i>
                </div>
                <h4 class="stats-number">{{ $totalTransaksi }}</h4>
                <p class="stats-title">Transaksi Hari Ini</p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="table-container">
                <h5><i class="bi bi-box-arrow-in-down me-2"></i>Log Sparepart (IN)</h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Sparepart</th>
                                <th>Jumlah</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($detailMasuk as $index => $d)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $d->jenis_nama ?? (optional($d->jenis)->nama ?? '-') }}
                                        {{ $d->tipe_nama ?? (optional($d->tipe)->nama ?? '-') }}</td>
                                    <td>{{ $d->total_qty }}</td>
                                    <td>{{ \Carbon\Carbon::parse($d->tanggal)->format('d M Y') }}</td>
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
                <div class="d-flex justify-content-end mt-3">
                    <a href="{{ route('kepalagudang.sparepart.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua
                        <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="table-container">
                <h5><i class="bi bi-box-arrow-up me-2"></i>Log Sparepart (OUT)</h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Sparepart</th>
                                <th>Jumlah</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                             @forelse($detailKeluar as $index => $d)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $d->nama_barang }}</td>
                                    <td>{{ $d->jumlah }}</td>
                                    <td>{{ \Carbon\Carbon::parse($d->tanggal)->format('d M Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox display-4 d-block mb-2"></i>
                                         Tidak ada data Sparepart
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-end mt-3">
                    <a href="{{ route('kepalagudang.history.index') }}" class="btn btn-sm btn-outline-primary">
                        Lihat Semua <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function updateDate() {
            const now = new Date();
            const options = {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            };
            const formattedDate = now.toLocaleDateString('id-ID', options);
            const el = document.getElementById('current-date');
            if (el) el.textContent = formattedDate;
        }

        updateDate();

        const navToggler = document.querySelector('.navbar-toggler');
        if (navToggler) {
            navToggler.addEventListener('click', function() {
                const sb = document.querySelector('.sidebar');
                if (sb) sb.classList.toggle('show');
            });
        }
    </script>
@endpush
