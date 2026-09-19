<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('exam_type')->default('iq_mcq'); // iq_mcq, word_association, non_verbal_iq, general_aptitude
            $table->string('category')->default('Verbal IQ'); // Verbal IQ, Non-Verbal IQ, WAT, Psychological, ISSB Mock
            $table->text('description')->nullable();
            $table->integer('duration_minutes')->default(30);
            $table->decimal('total_marks', 8, 2)->default(50.00);
            $table->decimal('pass_marks', 8, 2)->default(25.00);
            $table->decimal('negative_marking_per_wrong', 8, 2)->default(0.25);
            $table->decimal('fee', 10, 2)->default(0.00); // For external candidates
            $table->boolean('is_paid_for_external')->default(false);
            $table->boolean('is_public_for_external')->default(true);
            $table->string('status')->default('open'); // draft, scheduled, open, closed, archived
            $table->text('instructions')->nullable();
            $table->dateTime('schedule_start')->nullable();
            $table->dateTime('schedule_end')->nullable();
            $table->timestamps();
        });

        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->nullable()->constrained('exams')->nullOnDelete();
            $table->string('exam_type')->default('iq_mcq');
            $table->text('question_text');
            $table->string('question_image')->nullable();
            $table->json('options')->nullable(); // Array of {key: 'A', text: 'Option 1'}
            $table->string('correct_answer', 10)->nullable(); // e.g. "B"
            $table->text('explanation')->nullable();
            $table->decimal('marks', 6, 2)->default(1.00);
            $table->decimal('negative_marks', 6, 2)->default(0.25);
            $table->integer('order_seq')->default(1);
            $table->string('difficulty')->default('medium'); // easy, medium, hard
            $table->timestamps();
        });

        Schema::create('wat_words', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            $table->string('word');
            $table->integer('display_seconds')->default(15); // Standard ISSB WAT presentation time (10-15s)
            $table->integer('order_seq')->default(1);
            $table->timestamps();
        });

        Schema::create('exam_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->integer('attempt_number')->default(1);
            $table->dateTime('started_at');
            $table->dateTime('completed_at')->nullable();
            $table->integer('time_spent_seconds')->default(0);
            $table->decimal('score', 8, 2)->default(0.00);
            $table->decimal('total_marks', 8, 2)->default(0.00);
            $table->decimal('percentage', 6, 2)->default(0.00);
            $table->string('result_status')->default('under_evaluation'); // passed, failed, under_evaluation
            $table->string('status')->default('in_progress'); // in_progress, submitted, auto_submitted, timed_out
            $table->json('answers_summary')->nullable();
            $table->timestamps();
        });

        Schema::create('exam_attempt_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')->constrained('exam_attempts')->onDelete('cascade');
            $table->foreignId('question_id')->nullable()->constrained('questions')->nullOnDelete();
            $table->foreignId('wat_word_id')->nullable()->constrained('wat_words')->nullOnDelete();
            $table->text('user_answer')->nullable();
            $table->integer('response_time_seconds')->default(0);
            $table->boolean('is_correct')->nullable();
            $table->decimal('marks_awarded', 6, 2)->default(0.00);
            $table->text('instructor_feedback')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_attempt_answers');
        Schema::dropIfExists('exam_attempts');
        Schema::dropIfExists('wat_words');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('exams');
    }
};
