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

        Schema::table('garasi_referrals', function (Blueprint $table) {
            $table->date('referral_date')->nullable()->change();
            $table->text('reason')->nullable()->change();
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

        Schema::table('garasi_referrals', function (Blueprint $table) {
            $table->date('referral_date')->nullable(false)->change();
            $table->text('reason')->nullable(false)->change();
        });
    }
};
