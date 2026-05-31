<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'pemeriksaan_gizis',
            function (Blueprint $table) {

                $table->dropColumn([

                    'sarapan',
                    'konsumsi_sayur',
                    'minuman_manis',
                    'fast_food',
                    'aktivitas_fisik',
                    'screen_time',

                ]);

            }
        );
    }

    public function down(): void
    {
        Schema::table(
            'pemeriksaan_gizis',
            function (Blueprint $table) {

                $table->string(
                    'sarapan'
                )->nullable();

                $table->string(
                    'konsumsi_sayur'
                )->nullable();

                $table->string(
                    'minuman_manis'
                )->nullable();

                $table->string(
                    'fast_food'
                )->nullable();

                $table->string(
                    'aktivitas_fisik'
                )->nullable();

                $table->string(
                    'screen_time'
                )->nullable();

            }
        );
    }
};