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
        Schema::table('garasi_participants', function (Blueprint $table) {
            $table->unique(['garasi_activity_id', 'child_id'], 'garasi_participants_activity_child_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('garasi_participants', function (Blueprint $table) {
            $table->dropUnique('garasi_participants_activity_child_unique');
        });
    }
};
