<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rules\Unique;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pengiriman_detail', function (Blueprint $table) {
            $table->id();
            $table->string('tiket_pengiriman');
            $table->string('nama_item');
            $table->text('deskripsi')->nullable();
            $table->integer('jumlah');
            $table->text('keterangan')->nullable();

            $table->foreign('tiket_pengiriman')
                ->references('tiket_pengiriman')->on('pengiriman')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.  
     */
    public function down(): void
    {
        Schema::dropIfExists('pengiriman_detail');
    }
};
