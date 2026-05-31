<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'kelurahans',
            function (Blueprint $table) {

                $table->foreignId(
                    'instansi_id'
                )

                ->nullable()

                ->after(
                    'kecamatan_id'
                )

                ->constrained(
                    'instansis'
                )

                ->nullOnDelete();

            }
        );
    }

    public function down(): void
    {
        Schema::table(
            'kelurahans',
            function (Blueprint $table) {

                $table->dropForeign([
                    'instansi_id'
                ]);

                $table->dropColumn(
                    'instansi_id'
                );

            }
        );
    }
};