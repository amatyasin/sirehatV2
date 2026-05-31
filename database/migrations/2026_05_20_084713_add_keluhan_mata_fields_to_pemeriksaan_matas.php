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

                $table->enum(
                    'mata_bengkak',
                    ['Y', 'N']
                )

                ->nullable()

                ->after(
                    'gatal_mata'
                );

                $table->enum(
                    'mata_belekan',
                    ['Y', 'N']
                )

                ->nullable()

                ->after(
                    'mata_bengkak'
                );

            }
        );
    }

    public function down(): void
    {
        Schema::table(
            'pemeriksaan_matas',
            function (Blueprint $table) {

                $table->dropColumn([

                    'mata_bengkak',

                    'mata_belekan',

                ]);

            }
        );
    }
};