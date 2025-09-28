<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('list_barang', function (Blueprint $table) {
            try {
                $table->dropForeign(['kode_region']);
            } catch (\Exception $e) {
                // Handle exception jika foreign key tidak ada
            }

            $table->dropColumn('kode_region');
        });
    }

    public function down(): void
    {
        Schema::table('list_barang', function (Blueprint $table) {
            $table->string('kode_region')->nullable();

            $table->foreign('kode_region')
                ->references('kode_region')
                ->on('region')
                ->onDelete('set null');
        });
    }
};