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
        Schema::table(

            'pemeriksaan_gigis',

            function (Blueprint $table) {

                $table->unique(

                    'student_class_history_id',

                    'unique_pemeriksaan_gigi'

                );

            }

        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(

            'pemeriksaan_gigis',

            function (Blueprint $table) {

                $table->dropUnique(
                    'unique_pemeriksaan_gigi'
                );

            }

        );
    }
};