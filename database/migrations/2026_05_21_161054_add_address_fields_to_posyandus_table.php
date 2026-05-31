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
                    'rt'
                )
                ->nullable()
                ->after(
                    'no_wa'
                );

                $table->string(
                    'rw'
                )
                ->nullable()
                ->after(
                    'rt'
                );

                $table->string(
                    'kode_pos'
                )
                ->nullable()
                ->after(
                    'rw'
                );

            }
        );
    }

    public function down(): void
    {
        Schema::table(
            'posyandus',
            function (Blueprint $table) {

                $table->dropColumn([

                    'rt',

                    'rw',

                    'kode_pos',

                ]);

            }
        );
    }
};