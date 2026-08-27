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
        // 1. Add extra columns to garasi_participants
        Schema::table('garasi_participants', function (Blueprint $table) {
            if (!Schema::hasColumn('garasi_participants', 'status')) {
                $table->string('status')->default('pending')->after('orang_tua_id');
            }
            if (!Schema::hasColumn('garasi_participants', 'mother_accompanied_brushing')) {
                $table->boolean('mother_accompanied_brushing')->default(false)->after('mother_accompanied');
            }
            if (!Schema::hasColumn('garasi_participants', 'follow_up_scheduled_date')) {
                $table->date('follow_up_scheduled_date')->nullable()->after('brushing_before_bed');
            }
            if (!Schema::hasColumn('garasi_participants', 'notes')) {
                $table->text('notes')->nullable()->after('follow_up_scheduled_date');
            }
        });

        // 2. garasi_brushing_practices
        if (!Schema::hasTable('garasi_brushing_practices')) {
            Schema::create('garasi_brushing_practices', function (Blueprint $table) {
                $table->id();
                $table->foreignId('garasi_participant_id')->unique()->constrained('garasi_participants')->cascadeOnDelete();
                $table->boolean('together_brushing')->default(false);
                $table->string('practice_ability')->nullable();
                $table->string('brushing_frequency')->nullable();
                $table->boolean('brushing_before_bed')->default(false);
                $table->string('mother_accompaniment_frequency')->nullable();
                $table->string('use_toothpaste')->nullable();
                $table->string('toothpaste_brand')->nullable();
                $table->string('tool_used')->nullable();
                $table->string('tool_other_description')->nullable();
                $table->timestamps();
            });
        }

        // 3. Extra columns for garasi_educations
        Schema::table('garasi_educations', function (Blueprint $table) {
            if (!Schema::hasColumn('garasi_educations', 'child_toothbrush_selection')) {
                $table->boolean('child_toothbrush_selection')->default(false)->after('fluoride_education');
            }
            if (!Schema::hasColumn('garasi_educations', 'mother_toothbrush_selection')) {
                $table->boolean('mother_toothbrush_selection')->default(false)->after('child_toothbrush_selection');
            }
        });

        // 4. Extra columns for garasi_screenings
        Schema::table('garasi_screenings', function (Blueprint $table) {
            if (!Schema::hasColumn('garasi_screenings', 'complaint_other')) {
                $table->boolean('complaint_other')->default(false)->after('chewing_difficulty');
            }
            if (!Schema::hasColumn('garasi_screenings', 'complaint_other_description')) {
                $table->string('complaint_other_description')->nullable()->after('complaint_other');
            }
            if (!Schema::hasColumn('garasi_screenings', 'tartar')) {
                $table->boolean('tartar')->default(false)->after('oral_hygiene');
            }
            if (!Schema::hasColumn('garasi_screenings', 'poor_oral_hygiene')) {
                $table->boolean('poor_oral_hygiene')->default(false)->after('swollen_gums_observed');
            }
            if (!Schema::hasColumn('garasi_screenings', 'finding_other')) {
                $table->boolean('finding_other')->default(false)->after('poor_oral_hygiene');
            }
            if (!Schema::hasColumn('garasi_screenings', 'finding_other_description')) {
                $table->string('finding_other_description')->nullable()->after('finding_other');
            }
        });

        // 5. garasi_dental_indices
        if (!Schema::hasTable('garasi_dental_indices')) {
            Schema::create('garasi_dental_indices', function (Blueprint $table) {
                $table->id();
                $table->foreignId('garasi_participant_id')->unique()->constrained('garasi_participants')->cascadeOnDelete();
                $table->string('dentition_type')->default('mixed'); // sulung, permanen, mixed
                $table->integer('decay_perm_D')->default(0);
                $table->integer('missing_perm_M')->default(0);
                $table->integer('filling_perm_F')->default(0);
                $table->integer('dmft_score')->default(0);
                $table->integer('decay_prim_d')->default(0);
                $table->integer('extracted_prim_e')->default(0);
                $table->integer('filled_prim_f')->default(0);
                $table->integer('deft_score')->default(0);
                $table->timestamps();
            });
        }

        // 6. garasi_dental_findings (Odontogram)
        if (!Schema::hasTable('garasi_dental_findings')) {
            Schema::create('garasi_dental_findings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('garasi_participant_id')->constrained('garasi_participants')->cascadeOnDelete();
                $table->string('tooth_number');
                $table->string('condition'); // normal, decay, filling, missing, broken, other
                $table->string('notes')->nullable();
                $table->timestamps();
            });
        }

        // 7. garasi_treatments
        if (!Schema::hasTable('garasi_treatments')) {
            Schema::create('garasi_treatments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('garasi_participant_id')->unique()->constrained('garasi_participants')->cascadeOnDelete();
                $table->boolean('education')->default(false);
                $table->boolean('observation')->default(false);
                $table->boolean('filling')->default(false);
                $table->boolean('extraction')->default(false);
                $table->boolean('scaling')->default(false);
                $table->boolean('root_canal')->default(false);
                $table->boolean('prosthesis')->default(false);
                $table->boolean('treatment_other')->default(false);
                $table->string('treatment_other_description')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // 8. Extra columns for garasi_referrals
        Schema::table('garasi_referrals', function (Blueprint $table) {
            if (!Schema::hasColumn('garasi_referrals', 'referral_needed')) {
                $table->boolean('referral_needed')->default(false)->after('garasi_participant_id');
            }
            if (!Schema::hasColumn('garasi_referrals', 'reason_other')) {
                $table->string('reason_other')->nullable()->after('reason');
            }
            if (!Schema::hasColumn('garasi_referrals', 'destination_other')) {
                $table->string('destination_other')->nullable()->after('destination');
            }
            if (!Schema::hasColumn('garasi_referrals', 'recommended_actions')) {
                $table->json('recommended_actions')->nullable()->after('destination_other');
            }
        });

        // 9. garasi_follow_ups
        if (!Schema::hasTable('garasi_follow_ups')) {
            Schema::create('garasi_follow_ups', function (Blueprint $table) {
                $table->id();
                $table->foreignId('garasi_participant_id')->constrained('garasi_participants')->cascadeOnDelete();
                $table->foreignId('previous_participant_id')->nullable()->constrained('garasi_participants')->nullOnDelete();
                $table->date('follow_up_date');
                $table->string('behavior_change'); // ada_perubahan, tidak_ada_perubahan
                $table->text('behavior_change_description')->nullable();
                $table->string('mother_accompaniment_change')->nullable(); // meningkat, tetap, menurun
                $table->string('dental_condition_change')->nullable(); // membaik, tetap, memburuk
                $table->string('referral_status')->nullable(); // sudah_dilakukan, belum_dilakukan, tidak_diperlukan
                $table->text('notes')->nullable();
                $table->foreignId('evaluator_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('garasi_follow_ups');
        Schema::dropIfExists('garasi_treatments');
        Schema::dropIfExists('garasi_dental_findings');
        Schema::dropIfExists('garasi_dental_indices');
        Schema::dropIfExists('garasi_brushing_practices');

        Schema::table('garasi_referrals', function (Blueprint $table) {
            $table->dropColumn(['referral_needed', 'reason_other', 'destination_other', 'recommended_actions']);
        });

        Schema::table('garasi_screenings', function (Blueprint $table) {
            $table->dropColumn(['complaint_other', 'complaint_other_description', 'tartar', 'poor_oral_hygiene', 'finding_other', 'finding_other_description']);
        });

        Schema::table('garasi_educations', function (Blueprint $table) {
            $table->dropColumn(['child_toothbrush_selection', 'mother_toothbrush_selection']);
        });

        Schema::table('garasi_participants', function (Blueprint $table) {
            $table->dropColumn(['status', 'mother_accompanied_brushing', 'follow_up_scheduled_date', 'notes']);
        });
    }
};
