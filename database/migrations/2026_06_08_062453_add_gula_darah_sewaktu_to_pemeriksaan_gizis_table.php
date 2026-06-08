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
        Schema::table('pemeriksaan_gizis', function (Blueprint $table) {
            $table->decimal('gula_darah_sewaktu', 5, 1)->nullable()->after('status_gizi');
            $table->string('status_gula')->nullable()->after('gula_darah_sewaktu');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pemeriksaan_gizis', function (Blueprint $table) {
            $table->dropColumn(['gula_darah_sewaktu', 'status_gula']);
        });
    }
};
