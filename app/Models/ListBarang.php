<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ListBarang extends Model
{
    protected $table = 'list_barang';

    public $timestamps = false;

    protected $fillable = [
        'tiket_sparepart',
        'jenis_id',
        'tipe_id',
        'kategori'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($listBarang) {
            $lastTiketNumber = DB::table('list_barang')
                ->select(DB::raw('MAX(CAST(SUBSTRING(tiket_sparepart, 4) AS UNSIGNED)) as max_number'))
                ->value('max_number');

            $nextNumber = $lastTiketNumber ? $lastTiketNumber + 1 : 1;
            $listBarang->tiket_sparepart = 'SP-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        });
    }

    public function jenisBarang()
    {
        return $this->belongsTo(JenisBarang::class, 'jenis_id');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function tipeBarang()
    {
        return $this->belongsTo(TipeBarang::class, 'tipe_id');
    }

    public function details()
    {
        return $this->hasMany(DetailBarang::class, 'tiket_sparepart', 'tiket_sparepart');
    }

    public function getQuantityAttribute()
    {
        return $this->details()->sum('quantity');
    }
}