<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $fillable = ['nama'];

    protected $table = 'vendor';

    public $timestamps = false;

    public function details()
    {
        return $this->hasMany(DetailBarang::class, 'vendor_id');
    }
}