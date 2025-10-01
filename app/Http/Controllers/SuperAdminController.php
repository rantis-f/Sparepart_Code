<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Permintaan;
use App\Models\Pengiriman;
use App\Models\PengirimanDetail;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\DetailBarang;

class SuperAdminController extends Controller
{
    public function dashboard(Request $request)
    {
        $date = $request->input('date', Carbon::today()->toDateString());

        // --- Barang Masuk ---
        $groups = DB::table('detail_barang')
            ->select('jenis_id', 'tipe_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('MAX(id) as latest_id'))
            ->whereDate('tanggal', $date)
            ->groupBy('jenis_id', 'tipe_id')
            ->get();

        if ($groups->isEmpty()) {
            $detailMasuk = collect();
            $totalMasuk = 0;
        } else {
            $latestIds = $groups->pluck('latest_id')->filter()->all();
            $latestRecords = DetailBarang::with(['jenis', 'tipe'])
                ->whereIn('id', $latestIds)
                ->get()
                ->keyBy('id');

            $detailMasuk = $groups->map(function ($g) use ($latestRecords, $date) {
                $r = $latestRecords->get($g->latest_id);
                return (object) [
                    'id' => $g->latest_id,
                    'tiket_sparepart' => $r ? $r->tiket_sparepart : null,
                    'jenis_nama' => $r && $r->jenis ? $r->jenis->nama : null,
                    'tipe_nama' => $r && $r->tipe ? $r->tipe->nama : null,
                    'total_qty' => (int) $g->total_qty,
                    'tanggal' => $r ? $r->tanggal : $date,
                ];
            })->sortByDesc('tiket_sparepart')->values();

            $totalMasuk = $groups->sum('total_qty');
        }

        // --- Barang Keluar ---
        $detailKeluar = Pengiriman::with('details', 'permintaan')
            ->whereHas('permintaan', function ($query) use ($date) {
                $query->whereDate('tanggal_perubahan', $date);
            })
            ->whereHas('permintaan', function ($query) use ($date) {
                $query->where('status_super_admin', 'approved');
            })
            ->orderBy('id', 'desc')->take(5)
            ->get()
            ->map(function ($item) {
                return $item->details->map(function ($detail) use ($item) {
                    return (object) [
                        'id' => $detail->id,
                        'nama_barang' => $detail->nama ?? '-',
                        'jumlah' => $detail->jumlah ?? 0,
                        'tanggal' => $item->tanggal_transaksi ?? '-',
                        'tiket' => $item->permintaan->tiket ?? '-',
                    ];
                });
            })->flatten(1);



        $totalKeluar = Pengiriman::with(['details', 'permintaan'])
    ->whereHas('permintaan', function ($query) use ($date) {
        $query->whereDate('tanggal_perubahan', $date)
              ->where('status_super_admin', 'approved');
    })
    ->get()
    ->flatMap(function ($pengiriman) {
        return $pengiriman->details;
    })
    ->sum('jumlah');

            $totalTransaksi= Permintaan::whereDate('tanggal_perubahan', $date)
                          ->where('status_super_admin', 'approved')
                          ->count();
 $totalAdminPending = Permintaan::where('status_admin', 'on progres')
                          ->count();
        $totalSuperadminPending = Permintaan::where('status_super_admin', 'on progres')
                          ->count();

        return view('superadmin.dashboard', compact('detailMasuk', 'date', 'totalMasuk', 'totalKeluar','detailKeluar', 'totalAdminPending','totalSuperadminPending','totalTransaksi'));
    }

    public function requestIndex()
    {
        $user = Auth::user();
        $tiket = request()->input('tiket');



        if ($user->id === 15) {
            // Admin (Mbak Inong): tampilkan jika belum diproses
            $requests = Permintaan::with(['user', 'details', 'pengiriman.details'])
                ->where('status_ro', 'approved')
                ->where('status_gudang', 'approved')
                ->where('status_admin', '!=', 'approved')   // ✅ Bukan approved
                ->where('status_admin', '!=', 'rejected')   // ✅ Bukan rejected
                ->orderBy('id', 'desc')
                ->paginate(10);
            $pengiriman = Pengiriman::with('details')
                ->where('tiket_permintaan', $tiket)
                ->first();
        } elseif ($user->id === 16) {
            // Super Admin (Mas Septian): tampilkan jika Admin sudah approve
            $requests = Permintaan::with(['user', 'details', 'pengiriman.details'])
                ->where('status_admin', 'approved')
                ->where('status_super_admin', '!=', 'approved')   // ✅ Belum disetujui
                ->where('status_super_admin', '!=', 'rejected')   // ✅ Belum ditolak
                ->orderBy('id', 'desc')
                ->paginate(10);
            $pengiriman = Pengiriman::with('details')
                ->where('tiket_permintaan', $tiket)
                ->first();
        } else {
            $requests = new LengthAwarePaginator([], 0, 10);
        }

        return view('superadmin.request', compact('requests', 'pengiriman'));
    }

    public function historyIndex(Request $request)
    {
        $user = Auth::user();

        $query = Permintaan::with(['user', 'details', 'pengiriman.details'])
            ->where(function ($q) use ($user) {
                // Untuk Admin (ID 15): tampilkan yang dia approve/reject
                if ($user->id === 15) {
                    $q->where('status_admin', 'approved')
                        ->orWhere('status_admin', 'rejected');
                }
                // Untuk Super Admin (ID 16): tampilkan yang dia approve/reject
                elseif ($user->id === 16) {
                    $q->where('status_super_admin', 'approved')
                        ->orWhere('status_super_admin', 'rejected');
                }
            })
            ->orWhereHas('pengiriman') // atau sudah dikirim
            ->orderBy('id', 'desc');

        // Filter tanggal
        if ($request->filled('dateFrom')) {
            $query->whereDate('tanggal_permintaan', '>=', $request->input('dateFrom'));
        }
        if ($request->filled('dateTo')) {
            $query->whereDate('tanggal_permintaan', '<=', $request->input('dateTo'));
        }

        $requests = $query->distinct()->paginate(10)->withQueryString();

        return view('superadmin.history', compact('requests'));
    }
    /**
     * Approve oleh Admin (Mbak Inong) atau Super Admin (Mas Septian)
     */
    public function approveRequest(Request $request)
    {
        $tiket = $request->tiket;
        $user = Auth::user();

        // 🔹 ADMIN (Mbak Inong) - ID 15
        if ($user->id === 15) {
            $permintaan = Permintaan::where('tiket', $tiket)->firstOrFail();

            if ($permintaan->status_ro !== 'approved' || $permintaan->status_gudang !== 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Permintaan belum disetujui oleh RO/Gudang.'
                ], 400);
            }

            // ✅ Set status admin + next step: Super Admin → on progres
            $permintaan->update([
                'status_admin' => 'approved',
                'status_super_admin' => 'on progres',
                'approved_by_admin' => $user->id,
                'catatan_admin' => $request->catatan ?? null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Permintaan disetujui oleh Admin (Mbak Inong). Telah dikirim ke Super Admin.'
            ]);
        }

        // 🔹 SUPER ADMIN (Mas Septian) - ID 16
        if ($user->id === 16) {
            $permintaan = Permintaan::where('tiket', $tiket)->firstOrFail();

            if ($permintaan->status_admin !== 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Permintaan belum disetujui oleh Admin.'
                ], 400);
            }

            // ✅ Final approve → close semua status
            $permintaan->update([
                'status_super_admin' => 'approved',
                'approved_by_super_admin' => $user->id,
                'catatan_super_admin' => $request->catatan ?? null,
                'status_barang' => 'on_delivery',
            ]);

            $pengiriman = $permintaan->pengiriman;
            $pengiriman->update([
                'status' => 'on_delivery',
            ]);

            if ($pengiriman) {
                $snList = $pengiriman->details->pluck('sn')->filter()->map(function ($sn) {
                    return trim($sn);
                })->toArray();

                if (!empty($snList)) {
                    $barangList = \App\Models\DetailBarang::whereIn('serial_number', $snList)->get();

                    foreach ($pengiriman->details as $detail) {
                        $sn = trim($detail->sn);
                        $barang = $barangList->firstWhere('serial_number', $sn);

                        if (!$barang) {
                            return response()->json([
                                'success' => false,
                                'message' => "SN '$sn' tidak ditemukan di database."
                            ], 400);
                        }

                        if ($barang->quantity <= 0) {
                            return response()->json([
                                'success' => false,
                                'message' => "Stok habis untuk SN: $sn"
                            ], 400);
                        }
                    }

                    DetailBarang::whereIn('serial_number', $snList)
                        ->decrement('quantity', 1);
                }

                // 🔽 TAMBAHAN: Kurangi stok untuk barang non-aset (tanpa SN)
                foreach ($pengiriman->details as $detail) {
                    $kategori = $detail->kategori;
                    $jenis = $detail->nama;
                    $tipe   = $detail->tipe;
                    $jumlah   = $detail->jumlah;

                    if ($kategori === 'non-aset') {
                        // Cari stok barang di DetailBarang yang sesuai
                        $barang = \App\Models\DetailBarang::whereHas('jenis', function ($q) use ($jenis) {
                            $q->where('nama', $jenis);
                        })
                            ->whereHas('tipe', function ($q) use ($tipe) {
                                $q->where('nama', $tipe);
                            })
                            ->whereHas('listBarang', function ($q) {
                                $q->where('kategori', 'non-aset');
                            })
                            ->orderBy('tanggal', 'asc')
                            ->first();

                        if (!$barang) {
                            return response()->json([
                                'success' => false,
                                'message' => "Tidak ditemukan barang non-aset dengan jenis ID: $jenisId dan tipe ID: $tipeId"
                            ], 400);
                        }

                        // Ambil nama jenis barang dari relasi DetailBarang -> JenisBarang
                        $namaJenis = optional($barang->jenisBarang)->nama;

                        if ($barang->quantity < $jumlah) {
                            return response()->json([
                                'success' => false,
                                'message' => "Stok tidak cukup untuk barang non-aset jenis: $namaJenis. Dibutuhkan: $jumlah, Tersedia: {$barang->quantity}"
                            ], 400);
                        }

                        // Kurangi stok
                        $barang->decrement('quantity', $jumlah);
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Permintaan disetujui final oleh Super Admin (Mas Septian). Barang siap dikirim.'
            ]);
        }
    }

    /**
     * Close semua status menjadi 'close'
     */
    private function closeAllStatus($permintaan)
    {
        $permintaan->update([
            'status_ro' => 'close',
            'status_gudang' => 'close',
            'status_admin' => 'close',
            'status_super_admin' => 'close',
        ]);
    }

    /**
     * Tolak permintaan → broadcast rejected ke semua level
     */
    public function reject(Request $request, $tiket) // ✅ Ambil $tiket dari URL
    {
        try {
            $user = Auth::user();
            $permintaan = Permintaan::where('tiket', $tiket)->firstOrFail();
            $pengiriman = Pengiriman::where('tiket_permintaan', $tiket)->first();

            // 🔹 ADMIN (Mbak Inong) - ID 15
            if ($user->id === 15) {
                if ($permintaan->status_ro !== 'approved' || $permintaan->status_gudang !== 'approved') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Permintaan belum disetujui oleh RO/Gudang.'
                    ], 400);
                }

                $permintaan->update([
                    'status_admin' => 'rejected',
                    'status_super_admin' => 'rejected',
                    'status_gudang' => 'rejected',
                    'status_ro' => 'rejected',
                    'status_barang' => 'rejected', // ✅ 'closed', bukan 'rejected'
                    'approved_by_admin' => $user->id,
                    'catatan_admin' => $request->catatan ?? 'Ditolak oleh Admin (Mbak Inong)',
                    'status' => 'ditolak',
                ]);

                $pengiriman->update([
                    'status' => 'rejected',
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Permintaan ditolak oleh Admin.'
                ]);
            }

            // 🔹 SUPER ADMIN (Mas Septian) - ID 16
            if ($user->id === 16) {
                if ($permintaan->status_admin !== 'approved') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Permintaan belum disetujui oleh Admin.'
                    ], 400);
                }

                $permintaan->update([
                    'status_super_admin' => 'rejected',
                    'status_admin' => 'rejected',
                    'status_gudang' => 'rejected',
                    'status_ro' => 'rejected',
                    'approved_by_super_admin' => $user->id,
                    'catatan_super_admin' => $request->catatan ?? 'Ditolak oleh Super Admin (Mas Septian)',
                ]);

                $pengiriman->update([
                    'status' => 'rejected',
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Permintaan ditolak oleh Super Admin.'
                ]);
            }

            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        } catch (\Exception $e) {
            \Log::error('Reject Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menolak permintaan. Silakan coba lagi.'
            ], 500);
        }
    }
    public function historyDetailApi($tiket)
    {
        try {
            $permintaan = Permintaan::with(['user', 'details', 'pengiriman.details'])
                ->where('tiket', $tiket)
                ->firstOrFail();

            return response()->json([
                'permintaan' => [
                    'tiket' => $permintaan->tiket,
                    'user' => $permintaan->user,
                    'tanggal_permintaan' => $permintaan->tanggal_permintaan,
                    'details' => $permintaan->details,
                ],
                'pengiriman' => $permintaan->pengiriman ? [
                    'tanggal_transaksi' => $permintaan->pengiriman->tanggal_transaksi,
                    'details' => $permintaan->pengiriman->details
                ] : null
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }
    }
}
