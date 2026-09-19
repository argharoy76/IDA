<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_dossiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->unique()->constrained('students')->onDelete('cascade');
            $table->integer('readiness_score')->default(65); // 0-100%
            $table->string('overall_status')->default('On Track'); // On Track, Needs Attention, Critical Improvement, High Potential
            $table->string('physical_grade')->default('B+');
            $table->string('communication_grade')->default('B');
            $table->string('leadership_grade')->default('B+');
            $table->string('iq_grade')->default('A-');
            $table->string('discipline_grade')->default('A');
            $table->text('summary_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('student_strengths_weaknesses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->string('type'); // strength, weakness
            $table->string('category'); // Leadership, Communication, Mathematics, English, IQ, Physical, Discipline, General Knowledge, Viva/Interview
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('priority')->default('medium'); // low, medium, high, critical
            $table->string('status')->default('identified'); // identified, improving, improved, ongoing, resolved
            $table->date('identified_date');
            $table->date('follow_up_date')->nullable();
            $table->foreignId('instructor_id')->nullable()->constrained('instructors')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('instructor_observations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('instructor_id')->constrained('instructors')->onDelete('cascade');
            $table->date('observation_date');
            $table->string('category'); // Leadership, Command Task, Group Discussion, Lecturette, Physical Stamina, Psychological Aptitude
            $table->text('observation_text');
            $table->integer('rating')->default(3); // 1-5 scale
            $table->text('recommended_action')->nullable();
            $table->date('follow_up_date')->nullable();
            $table->string('visibility')->default('student_visible'); // student_visible, instructor_only, admin_only
            $table->timestamps();
        });

        Schema::create('performance_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('instructor_id')->nullable()->constrained('instructors')->nullOnDelete();
            $table->date('assessment_date');
            $table->string('period_label'); // e.g. "January Baseline", "Mid-Term Review", "Pre-ISSB Final Assessment"
            $table->string('category')->default('Academic'); // Academic, Communication, Leadership & Personality, ISSB Preparation, Physical
            $table->json('ratings')->nullable(); // detailed key-value sub scores
            $table->integer('overall_rating')->default(3); // 1-5 scale
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('improvement_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('strength_weakness_id')->nullable()->constrained('student_strengths_weaknesses')->nullOnDelete();
            $table->string('problem_description');
            $table->string('target_objective');
            $table->text('recommended_activity')->nullable();
            $table->text('assigned_task')->nullable();
            $table->foreignId('responsible_instructor_id')->nullable()->constrained('instructors')->nullOnDelete();
            $table->date('deadline');
            $table->integer('progress_percentage')->default(0); // 0-100%
            $table->string('status')->default('in_progress'); // pending, in_progress, completed, deferred
            $table->date('follow_up_date')->nullable();
            $table->text('review_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('performance_timelines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->string('event_type'); // admission, assessment, weakness_identified, milestone, exam_completed, observation
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('event_date');
            $table->string('badge_color')->default('emerald'); // emerald, blue, amber, red, purple
            $table->string('icon')->default('fa-flag');
            $table->unsignedBigInteger('source_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('performance_timelines');
        Schema::dropIfExists('improvement_plans');
        Schema::dropIfExists('performance_assessments');
        Schema::dropIfExists('instructor_observations');
        Schema::dropIfExists('student_strengths_weaknesses');
        Schema::dropIfExists('student_dossiers');
    }
};
