<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->foreignId('instansi_id')
                ->nullable()
                ->after('id')
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('school_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('posyandu_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->dropForeign(['instansi_id']);
            $table->dropForeign(['school_id']);
            $table->dropForeign(['posyandu_id']);

            $table->dropColumn([
                'instansi_id',
                'school_id',
                'posyandu_id'
            ]);

        });
    }
};