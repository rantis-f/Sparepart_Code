<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermintaanDetailTable extends Migration
{
    public function up()
    {
        Schema::create('permintaan_detail', function (Blueprint $table) {
            $table->id();
            $table->string('tiket');
            $table->string('nama_item', 50);
            $table->string('deskripsi', 255);
            $table->integer('jumlah');
            $table->string('keterangan')->nullable();

            $table->foreign('tiket')->references('tiket')->on('permintaan')->onDelete('cascade');
            $table->index('tiket');
        });
    }

    public function down()
    {
        Schema::dropIfExists('permintaan_detail');
    }
}
