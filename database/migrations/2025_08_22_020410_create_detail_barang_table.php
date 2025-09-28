<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('detail_barang', function (Blueprint $table) {
            $table->id();
            $table->string('tiket_sparepart');
            $table->foreign('tiket_sparepart')->references('tiket_sparepart')->on('list_barang')->onDelete('cascade');

            $table->string('serial_number')->nullable();
            $table->unsignedBigInteger('jenis_id');
            $table->unsignedBigInteger('tipe_id');
            $table->string('kode_region')->nullable();

            $table->foreign('jenis_id')->references('id')->on('jenis_barang')->onDelete('cascade');
            $table->foreign('tipe_id')->references('id')->on('tipe_barang')->onDelete('cascade');
            $table->foreign('kode_region')->references('kode_region')->on('region')->onDelete('cascade');

            $table->string('spk')->nullable();
            $table->string('pic')->nullable();
            $table->string('kategori')->nullable();
            $table->enum('status', ['tersedia', 'dikirim', 'habis'])->default("tersedia");
            $table->string('department')->nullable();
            $table->date('tanggal');
            
            $table->string('vendor')->nullable();
            $table->integer('quantity')->default(0);
            $table->decimal('harga', 15, 2)->nullable();
            $table->text('keterangan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_barang');
    }
};
