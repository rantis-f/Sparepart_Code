<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    protected $table = 'users';

    protected $fillable = [
        'name', 
        'email', 
        'password', 
        'role', 
        'region', 
        'mobile_number', 
        'perusahaan', 
        'nokt', 
        'alamat', 
        'bagian', 
        'atasan',
        'email_verified_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    public function permintaan()
    {
        return $this->hasMany(Permintaan::class);
    }

    public function pengiriman()
    {
        return $this->hasMany(Pengiriman::class);
    }

    public function verifikasi()
    {
        return $this->hasMany(VerifikasiPermintaan::class, 'user_id');
    }

    public function signedVerifikasi()
    {
        return $this->hasMany(VerifikasiPermintaan::class, 'signed_by');
    }

    public function getRoleNameAttribute()
{
    return match($this->role) {
        '1' => 'Superadmin',
        '2' => 'Regional Office Head',
        '3' => 'Warehouse Head',
        '4' => 'Field Technician',
        default => '-',
    };
}

public function getRoleBadgeAttribute()
{
    return match($this->role) {
        '1' => 'bg-primary',
        '2' => 'bg-success',
        '3' => 'bg-warning text-dark',
        '4' => 'bg-info',
        default => 'bg-secondary',
    };
}

}