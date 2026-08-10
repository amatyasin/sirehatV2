<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::table(
            'student_class_histories',
            function (Blueprint $table) {
                $table->index('student_id', 'temp_student_id_idx');
            }
        );

        Schema::table(
            'student_class_histories',

            function (Blueprint $table) {

                $table->dropUnique('student_history_unique');

            }
        );

        Schema::table(
            'student_class_histories',

            function (Blueprint $table) {

                $table->dropColumn(
                    'semester_id'
                );

            }
        );

        /*
        |--------------------------------------------------------------------------
        | ADD SEMESTER STRING
        |--------------------------------------------------------------------------
        */

        Schema::table(
            'student_class_histories',

            function (Blueprint $table) {

                $table->string(
                    'semester'
                )->after(
                    'academic_year_id'
                );

            }
        );

        /*
        |--------------------------------------------------------------------------
        | UNIQUE BARU
        |--------------------------------------------------------------------------
        */

        Schema::table(
            'student_class_histories',

            function (Blueprint $table) {

                $table->unique([

                    'student_id',

                    'school_id',

                    'school_class_id',

                    'academic_year_id',

                    'semester',

                ], 'student_history_unique');

            }
        );

        Schema::table(
            'student_class_histories',
            function (Blueprint $table) {
                $table->dropIndex('temp_student_id_idx');
            }
        );

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::table(
            'student_class_histories',
            function (Blueprint $table) {
                $table->index('student_id', 'temp_student_id_idx');
            }
        );

        /*
        |--------------------------------------------------------------------------
        | DROP UNIQUE BARU
        |--------------------------------------------------------------------------
        */

        DB::statement("
            ALTER TABLE student_class_histories
            DROP INDEX student_history_unique
        ");

        /*
        |--------------------------------------------------------------------------
        | DROP COLUMN SEMESTER
        |--------------------------------------------------------------------------
        */

        Schema::table(
            'student_class_histories',

            function (Blueprint $table) {

                $table->dropColumn(
                    'semester'
                );

            }
        );

        /*
        |--------------------------------------------------------------------------
        | KEMBALIKAN SEMESTER_ID
        |--------------------------------------------------------------------------
        */

        Schema::table(
            'student_class_histories',

            function (Blueprint $table) {

                $table->foreignId(
                    'semester_id'
                )

                ->nullable()

                ->constrained()

                ->nullOnDelete();

            }
        );

        /*
        |--------------------------------------------------------------------------
        | UNIQUE LAMA
        |--------------------------------------------------------------------------
        */

        Schema::table(
            'student_class_histories',

            function (Blueprint $table) {

                $table->unique([

                    'student_id',

                    'school_id',

                    'school_class_id',

                    'academic_year_id',

                ], 'student_history_unique');

            }
        );

        Schema::table(
            'student_class_histories',
            function (Blueprint $table) {
                $table->dropIndex('temp_student_id_idx');
            }
        );

        Schema::enableForeignKeyConstraints();
    }
};