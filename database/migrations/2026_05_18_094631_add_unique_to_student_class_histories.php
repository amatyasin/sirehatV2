<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'student_class_histories',
            function (Blueprint $table) {

                $table->unique([

                    'student_id',
                    'school_id',
                    'school_class_id',
                    'academic_year_id',
                    'semester_id',

                ], 'student_history_unique');

            }
        );
    }

    public function down(): void
    {
        Schema::table(
            'student_class_histories',
            function (Blueprint $table) {

                $table->dropUnique(
                    'student_history_unique'
                );

            }
        );
    }
};