<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('duration')->nullable(); // e.g. "3 Months", "6 Months"
            $table->string('eligibility')->nullable(); // e.g. "HSC / Equivalent, Minimum GPA 4.5"
            $table->decimal('fee', 10, 2)->default(0.00);
            $table->string('category')->default('Long Course'); // Army, Navy, Airforce, ISSB Special
            $table->json('features')->nullable();
            $table->json('syllabus')->nullable();
            $table->string('admission_status')->default('open'); // open, upcoming, closed
            $table->string('schedule_info')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });

        Schema::create('instructors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('instructor_code')->unique();
            $table->string('designation')->default('Instructor'); // Major (Retd.), Captain (Retd.), Senior Faculty
            $table->string('specialization')->nullable(); // Psychology, Ground Tasks, General Knowledge, Viva Grooming
            $table->string('phone')->nullable();
            $table->text('bio')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->string('batch_name');
            $table->string('batch_code')->unique(); // e.g. "IDA-BMA-94"
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->foreignId('primary_instructor_id')->nullable()->constrained('instructors')->nullOnDelete();
            $table->integer('max_students')->default(40);
            $table->string('schedule_summary')->nullable();
            $table->string('status')->default('active'); // upcoming, active, completed, cancelled
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('student_id_code')->unique(); // e.g. "IDA-2026-001"
            $table->string('roll_number')->nullable();
            $table->string('student_type')->default('academic'); // academic, external
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->date('dob')->nullable();
            $table->string('gender')->default('male');
            $table->string('blood_group', 10)->nullable();
            $table->text('address')->nullable();
            $table->string('emergency_contact')->nullable();
            $table->string('target_wing')->default('Army'); // Army, Navy, Airforce, General
            $table->foreignId('current_course_id')->nullable()->constrained('courses')->nullOnDelete();
            $table->foreignId('current_batch_id')->nullable()->constrained('batches')->nullOnDelete();
            $table->date('admission_date')->nullable();
            $table->string('status')->default('active'); // active, inactive, archived
            $table->json('documents')->nullable();
            $table->timestamps();
        });

        Schema::create('batch_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('batch_id')->constrained('batches')->onDelete('cascade');
            $table->foreignId('course_id')->nullable()->constrained('courses')->nullOnDelete();
            $table->date('joined_at')->nullable();
            $table->date('left_at')->nullable();
            $table->string('status')->default('active'); // active, transferred, completed
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('routines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->foreignId('batch_id')->constrained('batches')->onDelete('cascade');
            $table->foreignId('instructor_id')->nullable()->constrained('instructors')->nullOnDelete();
            $table->string('subject');
            $table->string('topic')->nullable();
            $table->string('room')->default('Hall Alpha');
            $table->date('class_date');
            $table->string('day_of_week')->nullable();
            $table->time('start_time');
            $table->time('end_time');
            $table->string('class_type')->default('theory'); // theory, practical, physical, drill, mock_viva
            $table->string('status')->default('scheduled'); // scheduled, completed, cancelled, rescheduled
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('routine_id')->constrained('routines')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('batch_id')->constrained('batches')->onDelete('cascade');
            $table->date('date');
            $table->string('status')->default('present'); // present, absent, late, excused
            $table->string('remarks')->nullable();
            $table->foreignId('marked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('routines');
        Schema::dropIfExists('batch_student');
        Schema::dropIfExists('students');
        Schema::dropIfExists('batches');
        Schema::dropIfExists('instructors');
        Schema::dropIfExists('courses');
    }
};
