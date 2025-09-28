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
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengiriman_id')->nullable()->constrained('pengiriman')->onDelete('cascade');
            $table->string('tiket_pengiriman')->nullable()->index();
            $table->string('type')->nullable(); // 'img_gudang', 'img_user', 'dokumen', dll
            $table->string('filename');
            $table->string('path');
            $table->string('mime')->nullable();
            $table->integer('size')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
