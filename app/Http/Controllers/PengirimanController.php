<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use App\Models\Permintaan;
use App\Models\Pengiriman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PengirimanController extends Controller
{
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'tiket' => 'required|string|exists:permintaan,tiket',
    //         'tanggal_pengiriman' => 'required|date',
    //         'items' => 'required|array|min:1',
    //         'items.*.nama_item' => 'required|string|max:255',
    //         'items.*.jumlah' => 'required|integer|min:1',
    //     ]);

    //     // Cek role Kepala Gudang
    //     if (Auth::user()->role !== 3) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Akses ditolak.'
    //         ], 403);
    //     }

    //     $tiket = $request->tiket;
    //     $permintaan = Permintaan::where('tiket', $tiket)->firstOrFail();

    //     // Cek status form
    //     if ($permintaan->status !== 'diterima') {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Form belum disetujui oleh Super Admin.'
    //         ], 400);
    //     }

    //     // Cek apakah sudah dikirim
    //     if ($permintaan->status_barang !== 'pending') {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Barang sudah pernah dikirim.'
    //         ], 400);
    //     }

    //     DB::beginTransaction();
    //     try {
    //         // Simpan pengiriman
    //         $pengiriman = Pengiriman::create([
    //             'tiket_pengiriman' => $tiket,
    //             'tiket_permintaan' => $tiket,
    //             'user_id' => Auth::id(),
    //             'tanggal_transaksi' => $request->tanggal_pengiriman,
    //             'status' => 'dikirim',
    //             'tanggal_perubahan' => now(),
    //         ]);

    //         // Simpan detail
    //         foreach ($request->items as $item) {
    //             PengirimanDetail::create([
    //                 'tiket_pengiriman' => $tiket,
    //                 'nama' => $item['nama_item'], // ✅ pakai kolom yang bener
    //                 'kategori' => $item['kategori'] ?? null,
    //                 'merk' => $item['merk'] ?? null,
    //                 'sn' => $item['sn'] ?? null,
    //                 'tipe' => $item['tipe'] ?? null,
    //                 'deskripsi' => $item['deskripsi'] ?? null,
    //                 'jumlah' => $item['jumlah'],
    //                 'keterangan' => $item['keterangan'] ?? null,
    //             ]);
    //         }

    //         // Update status barang
    //         $permintaan->update([
    //             'status_barang' => 'on_delivery'
    //         ]);

    //         DB::commit();

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Data pengiriman berhasil disimpan.',
    //             'redirect' => route('kepalagudang.request.index')
    //         ]);

    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Gagal menyimpan data pengiriman.'
    //         ], 500);
    //     }
    // }


public function terimaBarang(Request $request, $tiket)
{
    $request->validate([
        'no_resi' => 'nullable|string|max:255',
        'bukti_penerimaan' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
        'files.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
        'file_upload' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
    ]);

    // Temukan permintaan
    $permintaan = Permintaan::where('tiket', $tiket)->first();
    if (! $permintaan) {
        Log::warning('terimaBarang: tiket tidak ditemukan', ['tiket' => $tiket]);
        return response()->json(['success' => false, 'message' => 'Tiket tidak ditemukan'], 404);
    }

    // Cari pengiriman berdasarkan tiket_permintaan
    $pengiriman = Pengiriman::where('tiket_permintaan', $tiket)->first();

    // Jika tidak ada, buat baru (sertakan tiket_permintaan)
    if (! $pengiriman) {
        $pengiriman = Pengiriman::create([
            'tiket_permintaan' => $tiket,
            'tanggal_transaksi' => now(),
            'details' => json_encode([]),
            'status' => 'on_delivery',
        ]);
    }

    DB::beginTransaction();
    try {
        // Update nomor resi / nama ekspedisi jika disediakan
        if ($request->filled('no_resi')) {
            $pengiriman->no_resi = $request->input('no_resi');
        }

        // Pastikan pengiriman punya tiket_pengiriman agar Attachment terhubung.
        if (empty($pengiriman->tiket_pengiriman)) {
            $pengiriman->tiket_pengiriman = 'TKT-KRM-' . now()->format('YmdHis') . '-' . Str::random(4);
            // jangan save dulu; akan disave setelah penanganan file / update status
        }

        $storedPaths = [];

        // Kumpulkan uploaded files: prefer files[] -> file_upload -> bukti_penerimaan
        $uploadedFiles = [];
        if ($request->hasFile('files')) {
            $uploadedFiles = $request->file('files');
        } elseif ($request->hasFile('file_upload')) {
            $uploadedFiles = [$request->file('file_upload')];
        } elseif ($request->hasFile('bukti_penerimaan')) {
            $uploadedFiles = [$request->file('bukti_penerimaan')];
        }

        foreach ($uploadedFiles as $file) {
            if (! $file || ! $file->isValid()) {
                Log::info('terimaBarang: file invalid atau null, skip', ['tiket' => $tiket]);
                continue;
            }

            $original = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $ext = $file->getClientOriginalExtension();
            $filename = time() . '_' . Str::slug($original) . '_' . Str::random(6) . '.' . $ext;
            $folder = 'bukti_penerimaan/' . now()->format('Y/m');
            $path = $file->storeAs($folder, $filename, 'public');

            if (! $path) {
                throw new \RuntimeException('Gagal menyimpan file ke disk');
            }

            $mime = $file->getClientMimeType();
            $size = $file->getSize();

            // Simpan ke tabel Attachment (asumsi model App\Models\Attachment ada)
            \App\Models\Attachment::create([
                'pengiriman_id' => $pengiriman->id,
                'tiket_pengiriman' => $pengiriman->tiket_pengiriman,
                'type' => 'img_user',
                'filename' => $filename,
                'path' => $path,
                'mime' => $mime,
                'size' => $size,
            ]);

            $storedPaths[] = $path;
        }

        // Jika ada file, simpan first path ke kolom img_user (backward compat)
        if (! empty($storedPaths)) {
            $pengiriman->img_user = $storedPaths[0];
        }

        // Update status pengiriman
        $pengiriman->status = 'diterima';
        $pengiriman->save();

        DB::commit();

        // Ambil ulang data pengiriman + attachments (jika ada relasi)
        $freshPengiriman = $pengiriman->fresh();
        $attachments = [];
        if (method_exists($freshPengiriman, 'attachments')) {
            $attachments = $freshPengiriman->attachments()->get()->map(function ($a) {
                return [
                    'id' => $a->id,
                    'filename' => $a->filename,
                    'url' => Storage::url($a->path),
                    'mime' => $a->mime,
                    'size' => $a->size,
                    'created_at' => $a->created_at,
                ];
            })->toArray();
        }

        Log::info('terimaBarang: success', ['tiket' => $tiket, 'pengiriman_id' => $pengiriman->id]);

        return response()->json([
            'success' => true,
            'message' => 'Konfirmasi penerimaan berhasil.',
            'updated_status' => $pengiriman->status,
            'pengiriman' => $freshPengiriman,
            'attachments' => $attachments,
            'bukti_penerimaan_urls' => array_map(fn($p) => asset('storage/' . $p), $storedPaths),
        ]);
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('terimaBarang exception: ' . $e->getMessage(), ['trace' => $e->getTraceAsString(), 'tiket' => $tiket]);
        if (config('app.debug')) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan konfirmasi: ' . $e->getMessage(),
                'exception' => $e->getTraceAsString(),
            ], 500);
        }
        return response()->json(['success' => false, 'message' => 'Gagal menyimpan konfirmasi.'], 500);
    }
}
    public function validasiIndex()
    {
        $user = auth()->user();

        $requests = Permintaan::with(['details', 'pengiriman.details'])
            ->where('user_id', $user->id)
            ->where('status_gudang', 'approved')
            ->where('status_penerimaan', '!=', 'diterima')
            ->orderBy('tanggal_permintaan', 'desc')
            ->get();

        $data = $requests; // tambahkan ini
        return view('user.validasi', compact('requests', 'data'));
    }
}
