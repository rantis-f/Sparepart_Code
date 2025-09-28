<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengiriman extends Model
{
    // ✅ Perbaikan: tabel yang benar
    protected $table = 'pengiriman';

    // Kolom yang bisa diisi
    protected $fillable = [
        'tiket_pengiriman',
        'user_id',
        'tiket_permintaan',
        'tanggal_transaksi',
        'status',
        'ekspedisi',
        'img_gudang',
        'img_user',
        'tanggal_perubahan'
    ];
    public $timestamps = false;

    // Relasi ke user
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke permintaan
    public function permintaan()
    {
        return $this->belongsTo(Permintaan::class, 'tiket_permintaan', 'tiket');
    }

    // Relasi ke detail pengiriman
    public function details()
    {
        return $this->hasMany(PengirimanDetail::class, 'tiket_pengiriman', 'tiket_pengiriman');
    }

    public function attachments()
    {
        return $this->hasMany(\App\Models\Attachment::class, 'pengiriman_id');
    }
}
