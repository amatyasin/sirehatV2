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
        Schema::table(
            'pemeriksaan_gigis',
            function (Blueprint $table) {

                $table->dropColumn([

                    'radang_gusi',

                    'susunan_gigi',

                    'gigi_hilang',

                ]);

            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(
            'pemeriksaan_gigis',
            function (Blueprint $table) {

                $table->enum(
                    'radang_gusi',
                    ['Y', 'N']
                )->nullable();

                $table->string(
                    'susunan_gigi'
                )->nullable();

                $table->enum(
                    'gigi_hilang',
                    ['Y', 'N']
                )->nullable();

            }
        );
    }
};