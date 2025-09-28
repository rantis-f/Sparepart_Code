<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Permintaan extends Model
{
    protected $table = 'permintaan';

    protected $fillable = [
        'user_id',
        'tanggal_permintaan',
        'status',
        'status_ro',
        'status_gudang',
        'status_admin',
        'status_super_admin',
        'catatan_ro',
        'catatan_gudang',
        'catatan_admin',
        'catatan_super_admin',
        'approved_by_ro',
        'approved_by_gudang',
        'approved_by_admin',
        'approved_by_super_admin',
    ];
    protected $casts = [
        'status' => 'string',
        'status_ro' => 'string',
        'status_gudang' => 'string',
        'status_admin' => 'string',
        'status_super_admin' => 'string',
        'approved_by_ro' => 'integer',
        'approved_by_gudang' => 'integer',
        'approved_by_admin' => 'integer',
        'approved_by_super_admin' => 'integer',
    ];

    public $timestamps = false;

    /**
     * Boot method untuk auto-generate tiket
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($permintaan) {
            $user = Auth::check() ? Auth::user() : null;
            $region = $user ? strtoupper($user->region ?? 'XXX') : 'XXX';
            $bulan = now()->format('m'); // 01 - 12
            $tahun = now()->year;

            // Cari tiket terakhir dengan pola: REQ-{region}-{bulan}-{tahun}-xxx
            $lastPermintaan = self::where('tiket', 'LIKE', "REQ-{$region}-{$bulan}-{$tahun}-%")
                ->orderBy('id', 'desc')
                ->first();

            $number = 1;
            if ($lastPermintaan && preg_match('/(\d+)$/', $lastPermintaan->tiket, $matches)) {
                $number = (int)$matches[1] + 1;
            }

            $numberPadded = str_pad($number, 3, '0', STR_PAD_LEFT);

            $permintaan->tiket = "REQ-{$region}-{$bulan}-{$tahun}-{$numberPadded}";
        });
    }

    // Relasi
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function details()
    {
        return $this->hasMany(PermintaanDetail::class, 'tiket', 'tiket');
    }

    public function histori()
    {
        return $this->hasOne(HistoriPermintaan::class, 'tiket', 'tiket');
    }
    public function pengiriman()
    {
        return $this->hasOne(Pengiriman::class, 'tiket_permintaan', 'tiket');
    }
}