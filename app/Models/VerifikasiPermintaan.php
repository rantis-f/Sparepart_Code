<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VerifikasiPermintaan extends Model
{
    protected $table = 'verifikasi_permintaan';

    protected $fillable = [
        'id',
        'user_id',
        'file_path',
        'status',
        'signature',
        'signed_by',
        'catatan'
    ];

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function signedBy()
    {
        return $this->belongsTo(User::class, 'signed_by');
    }
}
