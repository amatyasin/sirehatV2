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
        Schema::table('students', function (Blueprint $table) {
            $table->string('nama_orang_tua')->nullable()->after('alamat');
            $table->string('nik_orang_tua')->nullable()->after('nama_orang_tua');
            $table->string('no_hp_orang_tua')->nullable()->after('nik_orang_tua');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['nama_orang_tua', 'nik_orang_tua', 'no_hp_orang_tua']);
        });
    }
};
