<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'posyandus',
            function (Blueprint $table) {

                $table->string(
                    'no_wa'
                )
                ->nullable()
                ->after(
                    'penanggung_jawab'
                );

            }
        );
    }

    public function down(): void
    {
        Schema::table(
            'posyandus',
            function (Blueprint $table) {

                $table->dropColumn(
                    'no_wa'
                );

            }
        );
    }
};