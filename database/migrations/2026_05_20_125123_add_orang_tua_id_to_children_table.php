<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('children', function (Blueprint $table) {

            $table->foreignId('orang_tua_id')
                ->nullable()
                ->after('id')
                ->constrained('orang_tuas')
                ->nullOnDelete();

        });
    }

    public function down(): void
    {
        Schema::table('children', function (Blueprint $table) {

            $table->dropForeign([
                'orang_tua_id'
            ]);

            $table->dropColumn(
                'orang_tua_id'
            );

        });
    }
};