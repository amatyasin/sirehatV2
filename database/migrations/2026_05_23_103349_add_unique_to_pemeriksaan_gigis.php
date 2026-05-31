<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pemeriksaan_gigis', function (
            Blueprint $table
        ) {

            /*
            |--------------------------------------------------------------------------
            | CEGAH DUPLIKAT
            |--------------------------------------------------------------------------
            */

            $table->unique(
                ['student_class_history_id'],
                'pemeriksaan_gigi_unique'
            );

            /*
            |--------------------------------------------------------------------------
            | INDEX
            |--------------------------------------------------------------------------
            */

            $table->index(
                'tanggal_pemeriksaan'
            );

            $table->index(
                'dirujuk_ke_fasyankes'
            );

        });
    }

    public function down(): void
    {
        Schema::table('pemeriksaan_gigis', function (
            Blueprint $table
        ) {

            $table->dropUnique(
                'pemeriksaan_gigi_unique'
            );

            $table->dropIndex([
                'tanggal_pemeriksaan',
            ]);

            $table->dropIndex([
                'dirujuk_ke_fasyankes',
            ]);

        });
    }
};