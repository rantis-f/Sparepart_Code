<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JenisBarang;

class HomeController extends Controller
{
    public function index()
    {
        return view('user.home');
    }

   public function jenisBarang(Request $request)
{
    $kategori = $request->get('kategori');
    $status = $request->get('status'); // filter Tersedia/Habis

    $query = JenisBarang::with('listBarang.details');

    if ($kategori) {
        $query->where('kategori', $kategori);
    }

    $jenisBarang = $query->get()->map(function($jenis) use ($status) {
        if ($status) {
            // Filter listBarang berdasarkan quantity di details
            $jenis->listBarang = $jenis->listBarang->filter(function($barang) use ($status) {
                $totalQty = $barang->details->sum('quantity');
                if ($status === 'tersedia') return $totalQty > 0;
                if ($status === 'habis') return $totalQty <= 0;
                return true;
            });
        }
        return $jenis;
    });

    return view('user.jenisbarang', compact('jenisBarang'));
}

}