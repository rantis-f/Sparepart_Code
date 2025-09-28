<?php

namespace App\Http\Controllers;

use App\Models\JenisBarang;
use App\Models\TipeBarang;
use App\Models\Vendor;
use App\Models\Region;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataController extends Controller
{
    public function index()
    {
        return view('kepalagudang.data', [
            'jenis' => JenisBarang::all(),
            'tipe' => TipeBarang::all(),
            'vendor' => Vendor::all(),
            'region' => Region::all(),
        ]);
    }

    // === JENIS ===
    public function storeJenis(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:50|unique:jenis_barang,nama',
            'kategori' => 'required|in:aset,non-aset',
        ]);

        DB::transaction(function () use ($request) {
            $jenis = JenisBarang::create([
                'nama' => $request->nama,
                'kategori' => $request->kategori,
            ]);

        });

        return redirect()->back()->with('success', 'Jenis barang berhasil ditambahkan.');
    }

    public function updateJenis(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:50|unique:jenis_barang,nama,' . $id,
            'kategori' => 'required|in:aset,non-aset',
        ]);

        $jenis = JenisBarang::findOrFail($id);
        $jenis->update($request->only('nama', 'kategori'));

        return redirect()->back()->with('success', 'Jenis barang berhasil diupdate.');
    }

    public function destroyJenis($id)
    {
        JenisBarang::destroy($id);
        return redirect()->back()->with('success', 'Jenis barang berhasil dihapus.');
    }

    // === TIPE ===
    public function storeTipe(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:tipe_barang,nama',
            'kategori' => 'required|in:aset,non-aset',
        ]);

        TipeBarang::create($request->only('nama', 'kategori'));

        return redirect()->back()->with('success', 'Tipe barang berhasil ditambahkan.');
    }

    public function updateTipe(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:tipe_barang,nama,' . $id,
            'kategori' => 'required|in:aset,non-aset',
        ]);

        $tipe = TipeBarang::findOrFail($id);
        $tipe->update($request->only('nama', 'kategori'));

        return redirect()->back()->with('success', 'Tipe barang berhasil diupdate.');
    }

    public function destroyTipe($id)
    {
        TipeBarang::destroy($id);
        return redirect()->back()->with('success', 'Tipe barang berhasil dihapus.');
    }
    // === Region ===
    public function storeRegion(Request $request)
    {
        $request->validate([
            'nama_region' => 'required|string|max:255',
            'kode_region' => 'required|string|max:255|unique:region,kode_region',
        ]);
        
        Region::create($request->only('nama_region', 'kode_region'));

        return redirect()->back()->with('success', 'T\Region berhasil ditambahkan.');
    }

    public function updateRegion(Request $request, $id)
    {
         $request->validate([
        'nama_region' => 'required|string|max:255',
        'kode_region' => [
            'required',
            'string',
            'max:255',
            Rule::unique('region', 'kode_region')->ignore($id),
        ],
    ]);

        $region = Region::findOrFail($id);
        $region->update($request->only('nama_region', 'kode_region'));

        return redirect()->back()->with('success', 'Region berhasil diupdate.');
    }

        public function destroyRegion($id)
    {
        Region::destroy($id);
        return redirect()->back()->with('success', 'Region berhasil dihapus.');
    }


    // === VENDOR ===
    public function storeVendor(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        Vendor::create($request->only('nama'));

        return redirect()->back()->with('success', 'Vendor berhasil ditambahkan.');
    }

    public function updateVendor(Request $request, $id)
    {
        $vendor = Vendor::findOrFail($id);
        $vendor->update($request->only('nama'));

        return redirect()->back()->with('success', 'Vendor berhasil diupdate.');
    }

    public function destroyVendor($id)
    {
        Vendor::destroy($id);
        return redirect()->back()->with('success', 'Vendor berhasil dihapus.');
    }
}