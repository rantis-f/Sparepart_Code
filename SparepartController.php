<?php

namespace App\Http\Controllers;

use App\Models\ListBarang;
use App\Models\Region;
use App\Models\JenisBarang;
use App\Models\TipeBarang;
use App\Models\DetailBarang;
use Illuminate\Http\Request;

class SparepartController extends Controller
{
    public function index(Request $request)
{
    $query = ListBarang::with(['details', 'jenisBarang', 'tipeBarang']);

    // Filter jenis barang (ini ada di DB, aman)
    if ($request->filled('jenis')) {
        $query->whereHas('jenisBarang', function ($q) use ($request) {
            $q->where('jenis', $request->jenis);
        });
    }

    // Filter search (tiket_sparepart atau tipe barang)
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('tiket_sparepart', 'like', "%$search%")
              ->orWhereHas('tipeBarang', function($q2) use ($search) {
                  $q2->where('tipe', 'like', "%$search%");
              });
        });
    }

    // Ambil data (tanpa filter status dulu)
    $listBarang = $query->orderBy('tanggal', 'desc')->get();

    // Kalau filter status dipilih, lakukan di Collection
    if ($request->filled('status')) {
        $listBarang = $listBarang->filter(function ($item) use ($request) {
            return $item->status === $request->status;
        });
    }

    // Pagination manual setelah filtering collection
    $perPage = 5;
    $page = request('page', 1);
    $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
        $listBarang->forPage($page, $perPage),
        $listBarang->count(),
        $perPage,
        $page,
        ['path' => request()->url(), 'query' => request()->query()]
    );

    $regions = Region::all();
    $jenis = JenisBarang::all();
    $tipe = TipeBarang::all();

    $totalQty = DetailBarang::sum('quantity');
    $totalTersedia = $listBarang->where('status', ListBarang::STATUS_TERSEDIA)->sum('quantity');
    $totalDipesan  = $listBarang->where('status', ListBarang::STATUS_DIPESAN)->sum('quantity');
    $totalHabis    = $listBarang->where('status', ListBarang::STATUS_HABIS)->sum('quantity');

    return view('superadmin.sparepart', [
        'listBarang'    => $paginated,
        'regions'       => $regions,
        'jenis'         => $jenis,
        'tipe'          => $tipe,
        'totalQty'      => $totalQty,
        'totalTersedia' => $totalTersedia,
        'totalDipesan'  => $totalDipesan,
        'totalHabis'    => $totalHabis,
    ]);
}



    public function store(Request $request)
    {
        $request->validate([
            // Validasi ListBarang
            'tiket_sparepart' => 'required|unique:list_barang,tiket_sparepart',
            'tanggal'         => 'required|date',
            'kode_region'     => 'required',

            // Validasi DetailBarang
            'nama_barang'   => 'required|string|max:255',
            'serial_number' => 'nullable|string',
            'spk'           => 'nullable|string',
            'harga'         => 'nullable|numeric',
            'quantity'      => 'required|integer|min:1',
            'jenis_id'      => 'required|exists:jenis_barang,id',
            'tipe_id'       => 'required|exists:tipe_barang,id',
        ]);

        // Simpan ListBarang
        $list = ListBarang::create([
            'tiket_sparepart' => $request->tiket_sparepart,
            'tanggal'         => $request->tanggal,
            'kode_region'     => $request->kode_region,
        ]);

        // Simpan DetailBarang
        DetailBarang::create([
            'tiket_sparepart' => $list->tiket_sparepart,
            'nama_barang'     => $request->nama_barang,
            'serial_number'   => $request->serial_number,
            'spk'             => $request->spk,
            'harga'           => $request->harga,
            'quantity'        => $request->quantity,
            'jenis_id'        => $request->jenis_id,
            'tipe_id'         => $request->tipe_id,
        ]);

        return redirect()->back()->with('success', 'List & Detail Barang berhasil ditambahkan!');
    }


    // Hapus list barang + semua detailnya
    public function destroyList($id)
    {
        $list = ListBarang::findOrFail($id);
        $list->detailBarang()->delete(); // hapus semua detail
        $list->delete();

        return redirect()->back()->with('success', 'List Barang beserta detail berhasil dihapus!');
    }

    // Hapus detail barang tertentu
    public function destroyDetail($id)
    {
        $detail = DetailBarang::findOrFail($id);
        $detail->delete();

        return redirect()->back()->with('success', 'Detail Barang berhasil dihapus!');
    }

    public function showDetail($tiket_sparepart)
{
    $list = ListBarang::with(['details', 'jenisBarang', 'tipeBarang'])
        ->where('tiket_sparepart', $tiket_sparepart)
        ->firstOrFail();

    return response()->json([
        'success' => true,
        'id'      => $list->tiket_sparepart,
        'tanggal' => \Carbon\Carbon::parse($list->tanggal)->format('d F Y'),
        'type'       => $list->tipeBarang->tipe ?? '-',
        'jenis'      => $list->jenisBarang->jenis ?? '-',
        'items'   => $list->details->map(function ($d) {
            return [
                'serial'     => $d->serial_number,
                'harga'      => $d->harga,
                'vendor'     => $d->vendor ?? '-',
                'spk'        => $d->spk,
                'keterangan' => $d->keterangan,
            ];
        }),
    ]);
}

}
