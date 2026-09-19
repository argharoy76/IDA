<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add age and is_offline to students table if not exists
        if (!Schema::hasColumn('students', 'age')) {
            Schema::table('students', function (Blueprint $table) {
                $table->unsignedSmallInteger('age')->nullable()->after('gender');
            });
        }

        // 2. Create course_student pivot table for multi-course enrollment
        if (!Schema::hasTable('course_student')) {
            Schema::create('course_student', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
                $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
                $table->timestamp('enrolled_at')->useCurrent();
                $table->string('status', 30)->default('active'); // active, completed, suspended
                $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
                $table->string('payment_status', 30)->default('paid'); // paid, pending, free, waived
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->unique(['student_id', 'course_id']);
            });

            // Backfill existing student current_course_id
            $existing = DB::table('students')->whereNotNull('current_course_id')->get();
            foreach ($existing as $st) {
                DB::table('course_student')->insertOrIgnore([
                    'student_id' => $st->id,
                    'course_id' => $st->current_course_id,
                    'enrolled_at' => now(),
                    'status' => 'active',
                    'payment_status' => 'paid',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_student');

        if (Schema::hasColumn('students', 'age')) {
            Schema::table('students', function (Blueprint $table) {
                $table->dropColumn('age');
            });
        }
    }
};
