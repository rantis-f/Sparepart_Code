<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Region extends Model
{
    use HasFactory;
    protected $table = 'region';
    public $timestamps = false;
    protected $primaryKey = 'id_region';
    protected $keyType = 'int';

    protected $fillable = [
        'nama_region',
        'kode_region',
        'email',
        'alamat',
        'koordinat'
    ];

    public function listBarang()
    {
        return $this->hasMany(ListBarang::class, 'kode_region', 'kode_region');
    }

     public function users()
    {
        return $this->belongsTo(User::class, 'region');
    }
}
