<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'schools',
            function (Blueprint $table) {

                $table->foreignId(
                    'kecamatan_id'
                )

                ->nullable()

                ->after(
                    'instansi_id'
                )

                ->constrained(
                    'kecamatans'
                )

                ->nullOnDelete();

                $table->foreignId(
                    'kelurahan_id'
                )

                ->nullable()

                ->after(
                    'kecamatan_id'
                )

                ->constrained(
                    'kelurahans'
                )

                ->nullOnDelete();

            }
        );
    }

    public function down(): void
    {
        Schema::table(
            'schools',
            function (Blueprint $table) {

                $table->dropForeign([
                    'kecamatan_id'
                ]);

                $table->dropForeign([
                    'kelurahan_id'
                ]);

                $table->dropColumn([

                    'kecamatan_id',

                    'kelurahan_id',

                ]);

            }
        );
    }
};