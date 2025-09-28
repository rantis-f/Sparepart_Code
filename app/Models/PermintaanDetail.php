<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermintaanDetail extends Model
{
    protected $table = 'permintaan_detail';

    protected $fillable = [
        'tiket',
        'nama_item',
        'deskripsi',
        'jumlah',
        'keterangan',
    ];

    public $timestamps = false;

    public function permintaan()
    {
        return $this->belongsTo(Permintaan::class, 'tiket', 'tiket');
    }
       public function list_barang()
    {
        return $this->belongsTo(ListBarang::class, 'list_barang_id');
    }
}
