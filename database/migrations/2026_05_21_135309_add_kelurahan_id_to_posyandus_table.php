<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posyandus', function (Blueprint $table) {

            $table->foreignId(
                'kelurahan_id'
            )
                ->nullable()
                ->after('instansi_id')
                ->constrained()
                ->cascadeOnDelete();

        });
    }

    public function down(): void
    {
        Schema::table('posyandus', function (Blueprint $table) {

            $table->dropForeign([
                'kelurahan_id'
            ]);

            $table->dropColumn(
                'kelurahan_id'
            );

        });
    }
};