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
        Schema::table('garasi_treatments', function (Blueprint $table) {
            if (!Schema::hasColumn('garasi_treatments', 'denisa')) {
                $table->boolean('denisa')->default(false)->after('education');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('garasi_treatments', function (Blueprint $table) {
            if (Schema::hasColumn('garasi_treatments', 'denisa')) {
                $table->dropColumn('denisa');
            }
        });
    }
};
