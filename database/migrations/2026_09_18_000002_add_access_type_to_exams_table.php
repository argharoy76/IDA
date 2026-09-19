<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            if (!Schema::hasColumn('exams', 'access_type')) {
                $table->string('access_type', 20)->default('paid')->after('is_paid_for_external');
            }
        });

        DB::table('exams')->where('is_paid_for_external', false)->update(['access_type' => 'free']);
        DB::table('exams')->where('is_paid_for_external', true)->update(['access_type' => 'paid']);
    }

    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            if (Schema::hasColumn('exams', 'access_type')) {
                $table->dropColumn('access_type');
            }
        });
    }
};
