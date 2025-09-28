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
        Schema::create('pengiriman', function (Blueprint $table) {
            $table->id();
            $table->string('tiket_pengiriman')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('tiket_permintaan');
            $table->date('tanggal_transaksi')->useCurrent();
            $table->enum('status', ['dikirim', 'diterima', 'diproses'])->default('diproses');
            $table->timestamp('tanggal_perubahan')->useCurrent();

            $table->foreign('tiket_permintaan')->references('tiket')->on('permintaan')->onDelete('cascade');
            $table->index('tiket_pengiriman');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengiriman');
    }
};
