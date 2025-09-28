<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoriPengiriman extends Model
{
    protected $fillable = [
        'pengiriman_id',
        'status',
        'tanggal_transaksi',
        'nama_item',
        'deskripsi',
        'jumlah',
        'keterangan'
    ];

    public function pengiriman()
    {
        return $this->belongsTo(Pengiriman::class);
    }
}
