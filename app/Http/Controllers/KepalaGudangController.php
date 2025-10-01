<?php

namespace App\Http\Controllers;

use App\Models\Permintaan;
use App\Models\Pengiriman;
use Illuminate\Http\Request;
use App\Models\PengirimanDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\DetailBarang;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\Attachment;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class KepalaGudangController extends Controller
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

            $totalMasuk = $groups->count();
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



        $totalKeluar = $detailKeluar->count();


        $totalTransaksi = Permintaan::whereDate('tanggal_perubahan', $date)
            ->where('status_super_admin', 'approved')
            ->count();

        $totalPending = Permintaan::where('status_gudang', 'on progres')
            ->count();


        return view('kepalagudang.dashboard', compact(
            'detailMasuk',
            'detailKeluar',
            'totalMasuk',
            'totalKeluar',
            'totalPending',
            'date',
            'totalTransaksi'
        ));
    }

    /**
     * Tampilkan daftar request yang sudah di-approve Kepala RO
     */
    public function requestIndex()
    {
        $requests = Permintaan::where('status_ro', 'approved')
            ->whereIn('status_gudang', ['pending', 'on progres'])
            ->with(['user', 'details']) // Load relasi jika diperlukan
            ->orderBy('id', 'desc')
            ->get();

        return view('kepalagudang.request', compact('requests'));
    }

    public function sparepartIndex()
    {
        return view('kepalagudang.sparepart');
    }

    public function kirim($id)
    {
        $permintaan = Pengiriman::findOrFail($id);
        $permintaan->status = 'on_delivery';
        $permintaan->save();

        return redirect()->back()->with('success', 'Barang berhasil dikirim.');
    }



    public function historyIndex(Request $request)
    {
        $query = Permintaan::with(['user', 'details'])
            ->where('status_gudang', '!=', 'pending');


        // 🔹 Filter berdasarkan status
        if ($request->filled('status')) {
            $status = $request->status;
            if ($status === 'disetujui') {
                $query->where('status_gudang', 'approved');
            } elseif ($status === 'ditolak') {
                $query->where('status_gudang', 'rejected');
            } elseif ($status === 'diproses') {
                $query->where('status_gudang', 'on progres');
            } elseif ($status === 'dikirim') {
                $query->whereHas('pengiriman'); // jika ada relasi pengiriman
            }
        }

        // 🔹 Filter berdasarkan tanggal
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('tanggal_permintaan', [
                $request->date_from,
                $request->date_to
            ]);
        }

        $requests = $query->orderBy('id', 'desc')->get();

        return view('kepalagudang.history', compact('requests'));
    }
    public function historyDetailApi($tiket)
    {
        $permintaan = Permintaan::with(['user', 'details'])
            ->where('tiket', $tiket)
            ->firstOrFail();

        $pengiriman = Pengiriman::with('details', 'attachments')
            ->where('tiket_permintaan', $tiket)
            ->first();

        return response()->json([
            'permintaan' => $permintaan,
            'pengiriman' => $pengiriman,
        ]);
    }


    public function rejectGudang(Request $request, $tiket)
    {
        try {
            $permintaan = Permintaan::where('tiket', $tiket)->first();

            // Ambil catatan dari request (opsional)
            $catatan = $request->input('catatan', 'Ditolak oleh Kepala Gudang');

            // Update semua status jadi rejected
            $permintaan->update([
                'status_gudang' => 'rejected',
                'status_ro' => 'rejected',
                'status_admin' => 'rejected',
                'status_super_admin' => 'rejected',
                'status_barang' => 'rejected',
                'status' => 'ditolak',
                'catatan_gudang' => $catatan,
            ]);



            // ✅ Kembalikan JSON sukses
            return response()->json([
                'success' => true,
                'message' => 'Permintaan berhasil ditolak.'
            ]);
        } catch (\Exception $e) {
            // ✅ Tangani error & kembalikan JSON error
            return response()->json([
                'success' => false,
                'message' => 'Gagal menolak permintaan: ' . $e->getMessage()
            ], 500);
        }
    }



    public function approveGudang($tiket, Request $request): JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            Log::warning('approveGudang: unauthenticated attempt');
            return response()->json(['success' => false, 'message' => 'Anda harus login.'], 401);
        }
        if ((int)$user->role !== 3) {
            Log::warning('approveGudang: access denied', ['user_id' => $user->id, 'role' => $user->role]);
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        // jika items dikirim sebagai JSON string via FormData
        if ($request->has('items') && is_string($request->input('items'))) {
            $decoded = json_decode($request->input('items'), true);
            if (json_last_error() === JSON_ERROR_NONE) {
                // overwrite request->items menjadi array untuk validasi
                $request->merge(['items' => $decoded]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Items JSON tidak valid.'
                ], 422);
            }
        }

        // Validation
        $rules = [
            'tiket' => 'required|string|exists:permintaan,tiket',
            'tanggal_pengiriman' => ['required', 'date'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.kategori' => ['required', 'string'],
            'items.*.nama_item' => ['required', 'string'],
            'items.*.jumlah' => ['required', 'integer', 'min:1'],
            'ekspedisi' => ['nullable', 'in:ya,tidak'],
            'file_upload' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,doc,docx', 'max:5120'],
            'files.*' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,doc,docx', 'max:5120'],
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            Log::info('approveGudang: validation failed', ['errors' => $validator->errors()->all()]);
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid.',
                'errors' => $validator->errors()
            ], 422);
        }

        // ✅ Cek duplikat Serial Number dalam satu pengiriman
        $serialNumbers = collect($request->items)->pluck('sn')->filter();
        if ($serialNumbers->count() !== $serialNumbers->unique()->count()) {
            return response()->json([
                'success' => false,
                'message' => 'Serial Number tidak boleh sama dalam satu pengiriman.'
            ], 422);
        }

        // Lanjutkan ke validasi stok seperti sebelumnya...
        if ($serialNumbers->isNotEmpty()) {
            // ... kode cek stok ...
        }

        $serialNumbers = collect($request->items)->pluck('sn')->filter();

        if ($serialNumbers->isNotEmpty()) {
            $barangList = DetailBarang::whereIn('serial_number', $serialNumbers)
                ->get(['serial_number', 'quantity']);

            $invalidSn = [];

            foreach ($request->items as $item) {
                $sn = $item['sn'] ?? null;
                if (!$sn) continue;

                $barang = $barangList->firstWhere('serial_number', $sn);
                if (!$barang) {
                    return response()->json([
                        'success' => false,
                        'message' => "SN '$sn' tidak ditemukan di database."
                    ], 422);
                }

                if ($barang->quantity <= 0) {
                    $invalidSn[] = $sn;
                }
            }

            if (!empty($invalidSn)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat mengirim: SN berikut stoknya habis: ' . implode(', ', $invalidSn)
                ], 422);
            }
        }


        DB::beginTransaction();
        try {
            // ambil permintaan & cek status
            $permintaan = Permintaan::where('tiket', $tiket)->firstOrFail();

            // pastikan status sesuai (sesuaikan string sesuai DB Anda)
            if ($permintaan->status_gudang !== 'on progres') {
                return response()->json([
                    'success' => false,
                    'message' => 'Permintaan ini sudah diproses sebelumnya. Tidak dapat diproses ulang.'
                ], 400);
            }

            // buat tiket pengiriman
            $tiketKirim = 'TKT-KRM-' . now()->format('YmdHis');

            // simpan pengiriman
            $pengiriman = Pengiriman::create([
                'tiket_pengiriman' => $tiketKirim,
                'user_id' => $user->id,
                'tiket_permintaan' => $tiket,
                'tanggal_transaksi' => $request->input('tanggal_pengiriman'),
                'ekspedisi' => $request->input('ekspedisi', 'tidak'),
                'tanggal_perubahan' => now(),
            ]);

            // simpan detail pengiriman
            foreach ($request->input('items', []) as $item) {
                PengirimanDetail::create([
                    'tiket_pengiriman' => $tiketKirim,
                    'nama' => $item['nama_item'] ?? null,
                    'kategori' => $item['kategori'] ?? null,
                    'merk' => $item['merk'] ?? null,
                    'sn' => $item['sn'] ?? null,
                    'tipe' => $item['tipe'] ?? null,
                    'deskripsi' => $item['deskripsi'] ?? null,
                    'jumlah' => $item['jumlah'] ?? 0,
                    'keterangan' => $item['keterangan'] ?? null,
                ]);
            }

            // file handling: terima files[] (multi) atau file_upload (single)
            $storedPaths = [];
            $uploadedFiles = [];
            if ($request->hasFile('files')) {
                $uploadedFiles = $request->file('files');
            } elseif ($request->hasFile('file_upload')) {
                $uploadedFiles = [$request->file('file_upload')];
            }

            foreach ($uploadedFiles as $file) {
                if (!$file->isValid()) continue;
                $original = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $ext = $file->getClientOriginalExtension();
                $filename = time() . '_' . Str::slug($original) . '_' . Str::random(6) . '.' . $ext;
                $folder = 'uploads/' . now()->format('Y/m');
                $path = $file->storeAs($folder, $filename, 'public');

                // simpan attachment
                Attachment::create([
                    'pengiriman_id' => $pengiriman->id,
                    'tiket_pengiriman' => $pengiriman->tiket_pengiriman,
                    'type' => 'img_gudang',
                    'filename' => $filename,
                    'path' => $path,
                    'mime' => $file->getClientMimeType(),
                    'size' => $file->getSize(),
                ]);

                $storedPaths[] = $path;
            }

            // backward compatibility: simpan first path ke img_gudang di table pengiriman
            if (!empty($storedPaths)) {
                $pengiriman->img_gudang = $storedPaths[0];
                $pengiriman->save();
            }

            // update permintaan

            $permintaan->update([
                'status_gudang' => 'approved',
                'status_admin' => 'on progres', // atau sesuai alur Anda
                'approved_by_admin' => $user->id,
                'catatan_admin' => $request->catatan ?? null,
                'status' => 'diterima',
            ]);

            DB::commit();

            Log::info('approveGudang: success', ['tiket' => $tiket, 'tiket_pengiriman' => $tiketKirim, 'user_id' => $user->id]);

            return response()->json([
                'success' => true,
                'message' => 'Permintaan berhasil dikirim ke Admin untuk proses selanjutnya.',
                'tiket_pengiriman' => $tiketKirim,
                'files' => array_map(fn($p) => asset('storage/' . $p), $storedPaths),
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('approveGudang: exception ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem. ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * Tolak permintaan
     */
    public function reject(Request $request)
    {
        try {
            $request->validate(['tiket' => 'required|string|exists:permintaan,tiket']);

            $user = Auth::user();
            if (!$user || $user->role !== 3) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
            }

            $permintaan = Permintaan::where('tiket', $request->tiket)->firstOrFail();

            if ($permintaan->status_gudang !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Permintaan sudah diproses.'
                ], 400);
            }

            $permintaan->update([
                'status_gudang' => 'rejected',
                'status_ro' => 'rejected',
                'status_admin' => 'rejected',
                'status_super_admin' => 'rejected',
                'status_barang' => 'rejected', // 🔥 Wajib!
                'catatan_gudang' => $request->catatan ?? 'Ditolak oleh Kepala Gudang',
                'status' => 'ditolak',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Permintaan berhasil ditolak.'
            ]);
        } catch (\Exception $e) {
            \Log::error("💥 ERROR DI REJECT(): " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menolak permintaan.'
            ], 500);
        }
    }

    public function snInfo(Request $request)
    {
        $sn = $request->query('sn');

        if (empty($sn)) {
            return response()->json([
                'success' => false,
                'message' => 'Parameter SN kosong.',
                'item' => null,
            ], 200);
        }

        // Cari detail_barang yang punya serial_number tersebut
        $detail = DetailBarang::with(['listBarang', 'vendor']) // load relasi terkait
            ->where('serial_number', $sn)
            ->first();

        if (!$detail || !$detail->listBarang) {
            return response()->json([
                'success' => false,
                'message' => 'SN tidak ditemukan.',
                'item' => null,
            ], 200);
        }

        $item = $detail->listBarang;

        return response()->json([
            'success' => true,
            'item' => [
                'id' => $item->id,
                'tipe_id' => $detail->tipe_id ?? null,
                'vendor_id' => $detail->vendor_id ?? null,
                'keterangan' => $detail->keterangan ?? null,
                'jenis_id' => $detail->jenis_id ?? null,
                'serial_number' => $detail->serial_number ?? null,
            ],
        ], 200);
    }


    public function closedFormIndex()
    {
        // Ambil permintaan yang sudah "diterima" / closed — sesuaikan kondisi where() jika Anda pakai status lain
        $permintaans = Permintaan::with(['user', 'pengiriman']) // pastikan relasi user ada
            ->whereHas('pengiriman', function ($q) {
                $q->where('status', 'diterima');
            })
            ->orderBy('id', 'desc')
            ->get();

        // Ambil pengiriman terkait (by tiket) — lakukan sekali memakai pengumpulan tiket untuk efisiensi
        $tiketList = $permintaans->pluck('tiket')->filter()->unique()->values()->all();

        $pengirimans = Pengiriman::whereIn('tiket_permintaan', $tiketList)
            ->with('attachments') // kalau Attachment relation ada di model Pengiriman
            ->get()
            ->groupBy('tiket_permintaan');

        // Mapping ke struktur yang sama seperti dummy
        $result = $permintaans->map(function ($p) use ($pengirimans) {
            // cari pengiriman terbaru untuk tiket ini (jika ada)
            $pengGroup = $pengirimans->get($p->tiket);
            $pengiriman = null;
            if ($pengGroup && $pengGroup instanceof \Illuminate\Support\Collection) {
                // ambil paling baru berdasarkan id atau tanggal_transaksi jika ada
                $pengiriman = $pengGroup->sortByDesc('id')->first();
            }

            // cari attachment (foto bukti) jika ada — coba dari pengiriman.attachments dulu,
            // fallback ke kolom pada permintaan (mis. foto_bukti_penerimaan)
            $fotoPath = null;
            if ($pengiriman && $pengiriman->attachments && $pengiriman->attachments->isNotEmpty()) {
                // pilih attachment tipe gambar (opsional: filter by type jika ada)
                $att = $pengiriman->attachments->first();
                $fotoPath = $att->path ?? null;
            } elseif (!empty($p->foto_bukti_penerimaan)) {
                $fotoPath = $p->foto_bukti_penerimaan;
            }

            // ubah path ke URL yang bisa diakses jika ada
            $fotoUrl = $fotoPath ? asset('storage/' . ltrim($fotoPath, '/')) : null;

            return (object) [
                'tiket' => $p->tiket,
                'user' => $p->user ?? null,
                'tanggal_penerimaan' => $p->tanggal_penerimaan ?? $p->updated_at ?? null,
                'foto_bukti_penerimaan' => $fotoUrl,
                'no_resi' => $pengiriman->no_resi ?? $p->no_resi ?? null,
            ];
        });

        return view('kepalagudang.closed-form', ['permintaans' => $result]);
    }

    public function verifyClosedForm(Request $request, $tiket)
    {
        $permintaan = Permintaan::where('tiket', $tiket)->first();
        if (! $permintaan) {
            Log::warning('verifyClosedForm: tiket permintaan tidak ditemukan', ['tiket' => $tiket]);
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Tiket tidak ditemukan'], 404);
            }
            return redirect()->back()->with('error', 'Tiket tidak ditemukan');
        }

        $pengiriman = Pengiriman::where('tiket_permintaan', $tiket)->first();
        if (! $pengiriman) {
            Log::warning('verifyClosedForm: pengiriman tidak ditemukan untuk tiket', ['tiket' => $tiket]);
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Data pengiriman tidak ditemukan'], 404);
            }
            return redirect()->back()->with('error', 'Data pengiriman tidak ditemukan');
        }

        // Jika sudah closed, kembalikan pesan informatif
        if ($pengiriman->status === 'closed') {
            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Pengiriman sudah berstatus closed', 'pengiriman' => $pengiriman]);
            }
            return redirect()->back()->with('info', 'Pengiriman sudah berstatus closed');
        }

        DB::beginTransaction();
        try {
            $pengiriman->status = 'close';
            $pengiriman->tanggal_perubahan = now(); // catat waktu perubahan, opsional
            $pengiriman->save();

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Berhasil diverifikasi!', 'pengiriman' => $pengiriman]);
            }
            return redirect()->back()->with('success', 'Berhasil diverifikasi!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('verifyClosedForm: gagal update status', ['tiket' => $tiket, 'message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Gagal menyimpan konfirmasi.'], 500);
            }
            return redirect()->back()->with('error', 'Gagal menyimpan konfirmasi.');
        }
    }
    public function getValidasiDetail($tiket)
    {
        // ambil permintaan dengan relasi user & details
        $permintaan = Permintaan::with(['user', 'details'])
            ->where('tiket', $tiket)
            ->first();

        if (!$permintaan) {
            return response()->json(['success' => false, 'message' => 'Permintaan tidak ditemukan.'], 404);
        }

        // ambil pengiriman terkait (jika ada) beserta details dan attachments
        $pengiriman = Pengiriman::with(['details', 'attachments'])
            ->where('tiket_permintaan', $tiket)
            ->first();

        // konversi data permintaan ke array
        $permintaanData = $permintaan->toArray();

        $pengirimanData = null;
        if ($pengiriman) {
            // ubah attachments ke bentuk yang aman (sertakan URL jika file ada)
            $attachments = collect($pengiriman->attachments ?? [])->map(function ($att) {
                // $att bisa berupa model atau array tergantung toArray() sebelumnya — akses dengan property/array safe
                $path = is_array($att) ? ($att['path'] ?? null) : ($att->path ?? null);
                $id = is_array($att) ? ($att['id'] ?? null) : ($att->id ?? null);
                $type = is_array($att) ? ($att['type'] ?? null) : ($att->type ?? null);
                $filename = is_array($att) ? ($att['filename'] ?? null) : ($att->filename ?? null);
                $mime = is_array($att) ? ($att['mime'] ?? null) : ($att->mime ?? null);
                $size = is_array($att) ? ($att['size'] ?? null) : ($att->size ?? null);

                $url = null;
                if ($path) {
                    // jika file fisik ada di disk public -> buat URL asset('storage/...')
                    if (Storage::disk('public')->exists($path)) {
                        $url = asset('storage/' . $path);
                    } else {
                        // fallback: jika Anda menyimpan full URL di path, kembalikan langsung
                        if (filter_var($path, FILTER_VALIDATE_URL)) {
                            $url = $path;
                        } else {
                            $url = null;
                        }
                    }
                }

                return [
                    'id' => $id,
                    'type' => $type,
                    'filename' => $filename,
                    'url' => $url,
                    'mime' => $mime,
                    'size' => $size,
                ];
            })->values()->all();

            // ambil data pengiriman dan ganti attachments dengan URL-ready array
            $pengirimanData = $pengiriman->toArray();
            $pengirimanData['attachments'] = $attachments;
        }

        return response()->json([
            'success' => true,
            'permintaan' => $permintaanData,
            'pengiriman' => $pengirimanData,
        ]);
    }
}
