<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'pemeriksaan_matas',
            function (Blueprint $table) {

                $table->dropColumn([

                    'screen_time',

                    'membaca_sambil_tiduran',

                    'pencahayaan_belajar',

                ]);

            }
        );
    }

    public function down(): void
    {
        Schema::table(
            'pemeriksaan_matas',
            function (Blueprint $table) {

                $table->string(
                    'screen_time'
                )->nullable();

                $table->string(
                    'membaca_sambil_tiduran'
                )->nullable();

                $table->string(
                    'pencahayaan_belajar'
                )->nullable();

            }
        );
    }
};