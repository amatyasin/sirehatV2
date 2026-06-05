<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pemeriksaan_gizis', function (Blueprint $table) {

            $table->decimal('hemoglobin', 5, 2)
                ->nullable()
                ->after('tanda_klinis_anemia');

            $table->string('status_anemia')
                ->nullable()
                ->after('hemoglobin');

        });
    }

    public function down(): void
    {
        Schema::table('pemeriksaan_gizis', function (Blueprint $table) {

            $table->dropColumn(['hemoglobin', 'status_anemia']);

        });
    }
};
