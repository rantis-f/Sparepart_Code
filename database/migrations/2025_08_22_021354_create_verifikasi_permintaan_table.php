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
        Schema::create('verifikasi_permintaan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('region_id')->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('file_path');
            $table->enum('status', ['pending', 'diterima', 'ditolak'])->default('pending');
            $table->longText('signature')->nullable();
            $table->unsignedBigInteger('signed_by')->nullable();
            $table->text('catatan')->nullable();

            $table->foreign('signed_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('region_id')->references('id')->on('region')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verifikasi_permintaan');
    }
};
