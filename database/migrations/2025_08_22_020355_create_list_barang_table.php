<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateListBarangTable extends Migration
{
    public function up()
    {
        Schema::create('list_barang', function (Blueprint $table) {
            $table->id();
            $table->string('tiket_sparepart')->unique();
            $table->unsignedBigInteger('jenis_id')->nullable();
            $table->unsignedBigInteger('tipe_id')->nullable();
            $table->string('kode_region')->nullable();
            $table->enum('kategori', ['aset', 'non-aset']);

            $table->foreign('jenis_id')
                ->references('id')
                ->on('jenis_barang')
                ->onDelete('set null');

            $table->foreign('tipe_id')
                ->references('id')
                ->on('tipe_barang')
                ->onDelete('set null');

            $table->foreign('kode_region')
                ->references('kode_region')
                ->on('region')
                ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('list_barang');
    }
}
