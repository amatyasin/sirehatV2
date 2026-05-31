<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('children', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | TAMBAH ORANG TUA
            |--------------------------------------------------------------------------
            */

            if (! Schema::hasColumn('children', 'orang_tua_id')) {

                $table->foreignId('orang_tua_id')
                    ->nullable()
                    ->after('posyandu_id')
                    ->constrained('orang_tuas')
                    ->nullOnDelete();

            }

            /*
            |--------------------------------------------------------------------------
            | RENAME NAMA
            |--------------------------------------------------------------------------
            */

            if (
                Schema::hasColumn('children', 'nama_anak') &&
                ! Schema::hasColumn('children', 'nama_lengkap')
            ) {

                $table->renameColumn(
                    'nama_anak',
                    'nama_lengkap'
                );

            }

        });
    }

    public function down(): void
    {
        //
    }
};