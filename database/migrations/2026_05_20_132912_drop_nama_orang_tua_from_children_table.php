<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'children',
            function (Blueprint $table) {

                $table->dropColumn(
                    'nama_orang_tua'
                );

            }
        );
    }

    public function down(): void
    {
        Schema::table(
            'children',
            function (Blueprint $table) {

                $table->string(
                    'nama_orang_tua'
                )

                ->nullable();

            }
        );
    }
};