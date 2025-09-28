<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detail_barang', function (Blueprint $table) {
            // 1. Drop foreign key kode_region (kalau ada)
            $table->dropForeign(['kode_region']);

            // 4. Pastikan kolom 'vendor' cocok dengan vendor.nama_vendor
            $table->dropColumn(['vendor']);

            $table->unsignedBigInteger('vendor_id')->nullable()->after('tipe_id');

            $table->foreign('vendor_id')
                ->references('id')
                ->on('vendor')
                ->onDelete('set null');
        });
    }

public function down(): void
{
    Schema::table('detail_barang', function (Blueprint $table) {
        // Drop foreign key vendor_id dulu sebelum drop kolomnya
        $table->dropForeign(['vendor_id']);
        
        // Drop kolom vendor_id
        $table->dropColumn('vendor_id');
        
        // Tambah kolom vendor lagi (sesuaikan tipe datanya, misal string 100)
        $table->string('vendor')->nullable()->after('spk');
        
        
        // Tambah kembali foreign key kode_region
        $table->foreign('kode_region')
            ->references('kode_region')
            ->on('region')
            ->onDelete('cascade');
    });
}

};