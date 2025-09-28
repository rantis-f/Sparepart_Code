<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoriPermintaan extends Model
{
    protected $table = 'histori_permintaan';
    protected $fillable = [
        'permintaan_id',
        'status',
        'tanggal_transaksi',
        'nama_item',
        'deskripsi',
        'jumlah',
        'keterangan'
    ];

    public function permintaan()
    {
        return $this->belongsTo(Permintaan::class, 'tiket', 'tiket');
    }
}
