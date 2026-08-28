<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posyandu_monthly_examinations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('posyandu_id')->constrained('posyandus')->cascadeOnDelete();
            $table->date('examination_date');
            $table->unsignedTinyInteger('month');
            $table->unsignedSmallInteger('year');
            $table->string('location')->nullable();
            $table->string('status')->default('scheduled');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['posyandu_id', 'month', 'year'], 'posyandu_monthly_exam_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posyandu_monthly_examinations');
    }
};
