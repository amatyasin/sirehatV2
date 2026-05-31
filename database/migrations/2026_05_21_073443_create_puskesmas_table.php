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
            'puskesmas',
            function (Blueprint $table) {

                $table->id();

                /*
                |--------------------------------------------------------------------------
                | DATA PUSKESMAS
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'nama_puskesmas'
                )->unique();

                $table->text(
                    'alamat'
                )->nullable();

                $table->string(
                    'kepala_puskesmas'
                )->nullable();

                $table->string(
                    'telepon'
                )->nullable();

                $table->boolean(
                    'aktif'
                )->default(true);

                $table->timestamps();

            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(
            'puskesmas'
        );
    }
};