<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengirimanDetail extends Model
{
    protected $table = 'pengiriman_detail';

    protected $fillable = [
        'tiket_pengiriman',
        'nama',
        'kategori',
        'merk',
        'sn',
        'tipe',
        'deskripsi',
        'jumlah',
        'keterangan',
    ];

    public $timestamps = false;

    public function pengiriman()
    {
        return $this->belongsTo(Pengiriman::class, 'tiket_pengiriman', 'tiket_pengiriman');
    }
}
