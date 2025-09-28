<?php

namespace App\Http\Controllers;

use App\Models\ListBarang;
use App\Models\Region;
use App\Models\JenisBarang;
use App\Models\TipeBarang;
use App\Models\DetailBarang;
use App\Models\Vendor;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class SparepartController extends Controller
{

    public function index(Request $request)
    {
        $jenisSparepart = JenisBarang::all();

        $query = ListBarang::with(['details', 'jenisBarang', 'tipeBarang']);

        if ($request->filled('nama')) {
            $query->whereHas('jenisBarang', function ($q) use ($request) {
                $q->where('id', $request->nama);
            });
        }

        if ($request->filled('status')) {
            $query->whereHas('details', function ($q) use ($request) {
                $q->where('status', $request->status);
            });
        }
        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_berakhir')) {
            $query->whereHas('details', function ($q) use ($request) {
                $q->whereBetween('tanggal', [$request->tanggal_mulai, $request->tanggal_berakhir]);
            });
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $keywords = explode(' ', $search);

                $q->where('tiket_sparepart', 'like', "%$search%")
                    ->orWhere(function ($q2) use ($keywords) {
                        if (count($keywords) > 1) {
                            $q2->whereHas('jenisBarang', function ($q3) use ($keywords) {
                                $q3->where('nama', 'like', "%{$keywords[0]}%");
                            })
                                ->whereHas('tipeBarang', function ($q3) use ($keywords) {
                                    $q3->where('nama', 'like', "%{$keywords[1]}%");
                                });
                        }
                    });
            });
        }

        $listBarang = $query->orderBy('tiket_sparepart', 'desc')->paginate(5);

        $totalPerStatus = DB::table('detail_barang as d')
            ->select('d.status', DB::raw('SUM(d.quantity) as total_quantity'))
            ->groupBy('d.status')
            ->pluck('total_quantity', 'status');

        $totalBaru = $totalPerStatus->get('sparepart baru', 0);
        $totalLama  = $totalPerStatus->get('sparepart lama', 0);

        $totalsPerTiket = [];

        foreach ($listBarang as $barang) {
            $tiket = $barang->tiket_sparepart;

            $totalPerStatus = collect($barang->details)
                ->groupBy('status')
                ->map(fn($items) => $items->sum('quantity'));

            $totalsPerTiket[$tiket] = [
                'sparepart baru' => $totalPerStatus->get('sparepart baru', 0),
                'sparepart lama'  => $totalPerStatus->get('sparepart lama', 0),
            ];
        }

        $regions = Region::all();
        $jenis = JenisBarang::all();
        $tipe = TipeBarang::all();
        $vendor = Vendor::all();
        $detail = DetailBarang::all();
        $totalQty = DetailBarang::sum('quantity');

        return view('kepalagudang.sparepart', [
            'listBarang'    => $listBarang,
            'regions'       => $regions,
            'jenis'         => $jenis,
            'tipe'          => $tipe,
            'vendor'         => $vendor,
            'detail'         => $detail,
            'jenisSparepart' => $jenisSparepart,
            'totalQty'      => $totalQty,
            'totalBaru' => $totalBaru,
            'totalLama'  => $totalLama,
            'totalsPerTiket' => $totalsPerTiket,
            'filterJenis'   => $request->jenis,
            'filterStatus'  => $request->status,
            'search'        => $request->search,
            'filterTanggalMulai' => $request->tanggal_mulai,
            'filterTanggalBerakhir' => $request->tanggal_berakhir,
            'filterKategori' => $request->kategori,
        ]);
    }
    public function indexAdmin(Request $request)
    {
        $jenisSparepart = JenisBarang::all();

        $query = ListBarang::with(['details', 'jenisBarang', 'tipeBarang']);

        if ($request->filled('nama')) {
            $query->whereHas('jenisBarang', function ($q) use ($request) {
                $q->where('id', $request->nama);
            });
        }

        if ($request->filled('status')) {
            $query->whereHas('details', function ($q) use ($request) {
                $q->where('status', $request->status);
            });
        }
        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_berakhir')) {
            $query->whereHas('details', function ($q) use ($request) {
                $q->whereBetween('tanggal', [$request->tanggal_mulai, $request->tanggal_berakhir]);
            });
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $keywords = explode(' ', $search);

                $q->where('tiket_sparepart', 'like', "%$search%")
                    ->orWhere(function ($q2) use ($keywords) {
                        if (count($keywords) > 1) {
                            $q2->whereHas('jenisBarang', function ($q3) use ($keywords) {
                                $q3->where('nama', 'like', "%{$keywords[0]}%");
                            })
                                ->whereHas('tipeBarang', function ($q3) use ($keywords) {
                                    $q3->where('nama', 'like', "%{$keywords[1]}%");
                                });
                        }
                    });
            });
        }

        $listBarang = $query->orderBy('tiket_sparepart', 'desc')->paginate(5);

        $totalPerStatus = DB::table('detail_barang as d')
            ->select('d.status', DB::raw('SUM(d.quantity) as total_quantity'))
            ->groupBy('d.status')
            ->pluck('total_quantity', 'status');

        $totalTersedia = $totalPerStatus->get('tersedia', 0);
        $totalDikirim  = $totalPerStatus->get('dikirim', 0);
        $totalHabis    = $totalPerStatus->get('habis', 0);

        $totalsPerTiket = [];

        foreach ($listBarang as $barang) {
            $tiket = $barang->tiket_sparepart;

            $totalPerStatus = collect($barang->details)
                ->groupBy('status')
                ->map(fn($items) => $items->sum('quantity'));

            $totalsPerTiket[$tiket] = [
                'sparepart baru' => $totalPerStatus->get('sparepart baru', 0),
                'sparepart lama'  => $totalPerStatus->get('sparepart lama', 0),
            ];
        }

        $regions = Region::all();
        $jenis = JenisBarang::all();
        $tipe = TipeBarang::all();
        $vendor = Vendor::all();
        $detail = DetailBarang::all();
        $totalQty = DetailBarang::sum('quantity');

        return view('superadmin.sparepart', [
            'listBarang'    => $listBarang,
            'regions'       => $regions,
            'jenis'         => $jenis,
            'tipe'          => $tipe,
            'vendor'         => $vendor,
            'detail'         => $detail,
            'jenisSparepart' => $jenisSparepart,
            'totalQty'      => $totalQty,
            'totalTersedia' => $totalTersedia,
            'totalDikirim'  => $totalDikirim,
            'totalHabis'    => $totalHabis,
            'totalsPerTiket' => $totalsPerTiket,
            'filterJenis'   => $request->jenis,
            'filterStatus'  => $request->status,
            'search'        => $request->search,
            'filterTanggalMulai' => $request->tanggal_mulai,
            'filterTanggalBerakhir' => $request->tanggal_berakhir,
            'filterKategori' => $request->kategori,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal'        => 'required|date',
            'kategori'       => 'required|string',
            'serial_number'  => 'nullable|string',
            'spk'            => 'nullable|string',
            'harga'          => 'nullable|numeric',
            'quantity'       => 'required|integer|min:1',
            'jenisSparepart' => 'required|exists:jenis_barang,id',
            'typeSparepart'  => 'required|exists:tipe_barang,id',
            'keterangan'     => 'nullable|string',
            'pic'            => 'required|string',
            'status'         => 'required|string',
            'vendor'         => 'required|exists:vendor,id',
            'department'     => 'nullable|string'
        ]);

        DB::transaction(function () use ($request) {
            $list = ListBarang::where('jenis_id', $request->jenisSparepart)
                ->where('tipe_id', $request->typeSparepart)
                ->where('kategori', $request->kategori)
                ->first();
            if (!$list) {
                $list = ListBarang::create([
                    'jenis_id'        => $request->jenisSparepart,
                    'tipe_id'         => $request->typeSparepart,
                    'kategori'        => $request->kategori,
                ]);
            }

            DetailBarang::create([
                'tiket_sparepart' => $list->tiket_sparepart,
                'serial_number'   => $request->serial_number,
                'spk'             => $request->spk,
                'tanggal'         => $request->tanggal,
                'harga'           => $request->harga,
                'quantity'        => $request->quantity,
                'jenis_id'        => $request->jenisSparepart,
                'tipe_id'         => $request->typeSparepart,
                'keterangan'      => $request->keterangan,
                'vendor_id'       => $request->vendor,
                'kategori'        => $request->kategori,
                'status'          => $request->status,
                'pic'             => $request->pic,
                'department'      => $request->department,
            ]);
        });


        return redirect()->back()->with('success', 'List & Detail Barang berhasil ditambahkan!');
    }


    public function update(Request $request, $id)
    {

        $request->validate([
            'serial_number' => 'nullable|string',
            'harga'         => 'required|numeric',
            'quantity'      => 'required|numeric',
            'spk'           => 'nullable|string',
            'vendor'        => 'required|exists:vendor,id',
            'pic'           => 'required|string',
            'department'    => 'nullable|string',
            'keterangan'    => 'nullable|string',
            'tanggal'       => 'required|date',
        ]);

        $detail = DetailBarang::where('id', $id)->firstOrFail();

        $detail->update([
            'serial_number' => $request->serial_number,
            'harga'         => $request->harga,
            'quantity'      => $request->quantity,
            'vendor_id'     => $request->vendor,
            'spk'           => $request->spk,
            'pic'           => $request->pic,
            'department'    => $request->department,
            'keterangan'    => $request->keterangan,
            'status'        => $request->status,
            'tanggal'       => $request->tanggal,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Detail sparepart berhasil diperbarui.',
                'data' => $detail
            ]);
        }

        return redirect()->route('kepalagudang.sparepart.index')->with('success', 'Detail sparepart berhasil diperbarui.');
    }


public function showDetail(Request $request, $tiket_sparepart)
{
    $list = ListBarang::with(['jenisBarang', 'tipeBarang'])
        ->where('tiket_sparepart', $tiket_sparepart)
        ->firstOrFail();

    // Ambil detail berdasarkan filter
    $detailsQuery = $list->details()->newQuery();

    if ($request->filled('status')) {
        $detailsQuery->where('status', $request->status);
    }

    if ($request->filled('tanggal_mulai') && $request->filled('tanggal_berakhir')) {
        $detailsQuery->whereBetween('tanggal', [$request->tanggal_mulai, $request->tanggal_berakhir]);
    }

    // Tambahan filter lain jika diperlukan (misalnya vendor_id, department, dsb.)
    if ($request->filled('vendor_id')) {
        $detailsQuery->where('vendor_id', $request->vendor_id);
    }

    $filteredDetails = $detailsQuery->with('vendor')->get();

    return response()->json([
        'success' => true,
        'id'      => $list->tiket_sparepart,
        'tanggal' => \Carbon\Carbon::parse($list->tanggal)->format('d F Y'),
        'type'    => $list->tipeBarang->nama ?? '-',
        'jenis'   => $list->jenisBarang->nama ?? '-',
        'items'   => $filteredDetails->map(function ($d) {
            return [
                'id'         => $d->id,
                'serial'     => $d->serial_number,
                'status'     => $d->status,
                'harga'      => $d->harga,
                'vendor'     => $d->vendor->nama ?? '-',
                'vendor_id'  => $d->vendor_id ?? '-',
                'quantity'   => $d->quantity,
                'spk'        => $d->spk,
                'pic'        => $d->pic,
                'department' => $d->department,
                'keterangan' => $d->keterangan,
                'tanggal'    => \Carbon\Carbon::parse($d->tanggal)->format('Y-m-d')
            ];
        }),
    ]);
}



    public function destroy(Request $request, $id)
    {
        $detail = DetailBarang::findOrFail($id);


        $tiket = $detail->tiket_sparepart;
        $detail->delete();
        $listDeleted = false;
        $list = ListBarang::where('tiket_sparepart', $tiket)->first();
        if ($list && $list->details()->count() === 0) {
            $list->delete();
            $listDeleted = true;
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Detail sparepart berhasil dihapus.',
                'redirect' => route('kepalagudang.sparepart.index'),
                'listDeleted' => $listDeleted
            ]);
        }

        return redirect()->route('kepalagudang.sparepart.index')->with('success', 'Detail sparepart berhasil dihapus.');
    }
}