<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('permintaan', function (Blueprint $table) {
            $table->text('catatan_admin')->nullable()->after('approved_by_admin');
            $table->text('catatan_super_admin')->nullable()->after('catatan_admin');
        });
    }

    public function down()
    {
        Schema::table('permintaan', function (Blueprint $table) {
            $table->dropColumn(['catatan_admin', 'catatan_super_admin']);
        });
    }
};