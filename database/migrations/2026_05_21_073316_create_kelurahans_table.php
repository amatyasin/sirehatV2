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
        Schema::create(
            'kelurahans',
            function (Blueprint $table) {

                $table->id();

                /*
                |--------------------------------------------------------------------------
                | RELASI KECAMATAN
                |--------------------------------------------------------------------------
                */

                $table->foreignId(
                    'kecamatan_id'
                )

                    ->constrained()

                    ->cascadeOnDelete();

                /*
                |--------------------------------------------------------------------------
                | DATA KELURAHAN
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'nama_kelurahan'
                );

                $table->boolean(
                    'aktif'
                )->default(true);

                $table->timestamps();

                /*
                |--------------------------------------------------------------------------
                | UNIQUE
                |--------------------------------------------------------------------------
                |
                | supaya tidak duplicate:
                | Loa Janan bisa ada di kecamatan lain
                |--------------------------------------------------------------------------
                */

                $table->unique([
                    'kecamatan_id',
                    'nama_kelurahan',
                ]);

            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(
            'kelurahans'
        );
    }
};