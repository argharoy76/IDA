<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            if (!Schema::hasColumn('exams', 'branch')) {
                $table->string('branch', 50)->nullable()->index()->after('category');
            }
            if (!Schema::hasColumn('exams', 'random_question_count')) {
                $table->integer('random_question_count')->nullable()->after('pass_marks');
            }
        });

        Schema::table('questions', function (Blueprint $table) {
            if (!Schema::hasColumn('questions', 'branch')) {
                $table->string('branch', 50)->nullable()->index()->after('exam_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            if (Schema::hasColumn('exams', 'random_question_count')) {
                $table->dropColumn('random_question_count');
            }
            if (Schema::hasColumn('exams', 'branch')) {
                $table->dropColumn('branch');
            }
        });

        Schema::table('questions', function (Blueprint $table) {
            if (Schema::hasColumn('questions', 'branch')) {
                $table->dropColumn('branch');
            }
        });
    }
};
