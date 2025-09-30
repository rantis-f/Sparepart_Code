@extends('layouts.superadmin')

@section('title', 'Dashboard Superadmin')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">Dashboard Overview</h3>
        <span class="badge bg-light text-dark">
            <i class="bi bi-calendar me-1"></i> {{ date('d F Y') }}
        </span>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="dashboard-card p-4">
                <div class="card-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-box-arrow-in-down"></i>
                </div>
                <h4 class="stats-number">{{ $totalMasuk ?? 0 }}</h4>
                <p class="stats-title">Barang Masuk</p>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="dashboard-card p-4">
                <div class="card-icon bg-danger bg-opacity-10 text-danger">
                    <i class="bi bi-box-arrow-up"></i>
                </div>
                <h4 class="stats-number">{{ $totalKeluar ?? 0 }}</h4>
                <p class="stats-title">Barang Keluar</p>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="dashboard-card p-4">
                <div class="card-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                @if (Auth::id() === 15)
                    <h4 class="stats-number">{{ $totalAdminPending ?? 0 }}</h4>
                @elseif(Auth::id() === 16)
                    <h4 class="stats-number">{{ $totalSuperadminPending ?? 0 }}</h4>
                @else
                    <h4 class="stats-number">0</h4>
                @endif
                <p class="stats-title">Pending</p>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="dashboard-card p-4">
                <div class="card-icon bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-clock-history"></i>
                </div>
                <h4 class="stats-number">{{ $totalKeluar ?? 0 }}</h4>
                <p class="stats-title">Transaksi Hari Ini</p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="table-container">
                <h5><i class="bi bi-box-arrow-in-down me-2"></i>Barang Masuk Terbaru</h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Barang</th>
                                <th>Jumlah</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($detail as $index => $d)
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
                    <a href="{{ route('kepalagudang.sparepart.index') }}" class="btn btn-sm btn-outline-primary">Lihat
                        Semua
                        <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="table-container">
                <h5><i class="bi bi-box-arrow-up me-2"></i>Barang Keluar Terbaru</h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Barang</th>
                                <th>Jumlah</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox display-4 d-block mb-2"></i>
                                    Tidak ada data keluar
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-end mt-3">
                    <a href="#" class="btn btn-sm btn-outline-primary">Lihat Semua <i
                            class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </div>
@endsection
