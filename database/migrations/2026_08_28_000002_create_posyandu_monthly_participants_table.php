<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posyandu_monthly_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('posyandu_monthly_examination_id')->constrained('posyandu_monthly_examinations', indexName: 'fk_pos_m_part_exam')->cascadeOnDelete();
            $table->foreignId('child_id')->constrained('children')->cascadeOnDelete();
            $table->foreignId('orang_tua_id')->nullable()->constrained('orang_tuas')->nullOnDelete();
            $table->boolean('attendance')->default(false);

            // Antropometri
            $table->decimal('weight', 5, 2)->nullable();
            $table->decimal('height', 5, 2)->nullable();
            $table->decimal('bmi', 5, 2)->nullable();
            $table->string('bmi_category')->nullable();
            $table->decimal('height_for_age_zscore', 5, 2)->nullable();
            $table->string('stunting_status')->nullable();
            $table->decimal('head_circumference', 5, 2)->nullable();
            $table->string('head_circumference_result')->nullable();

            // Skrining Kesehatan
            $table->enum('exclusive_breastfeeding', ['Y', 'T'])->nullable();
            $table->enum('mp_asi', ['Y', 'T'])->nullable();

            // Skrining TBC
            $table->enum('tb_cough', ['Y', 'T'])->default('T');
            $table->enum('tb_fever', ['Y', 'T'])->default('T');
            $table->enum('tb_weight_problem', ['Y', 'T'])->default('T');
            $table->enum('tb_close_contact', ['Y', 'T'])->default('T');
            $table->string('tb_screening_result')->nullable();

            // Status & Tindak Lanjut
            $table->string('examination_status')->default('Belum Diperiksa');
            $table->text('follow_up_recommendation')->nullable();
            $table->string('follow_up_status')->nullable();
            $table->text('notes')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['posyandu_monthly_examination_id', 'child_id'], 'posyandu_monthly_participant_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posyandu_monthly_participants');
    }
};
