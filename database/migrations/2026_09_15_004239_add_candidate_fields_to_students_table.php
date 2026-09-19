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
        Schema::table('students', function (Blueprint $table) {
            $table->string('institution')->nullable()->after('target_wing');
            $table->string('hsc_year', 10)->nullable()->after('institution');
            $table->string('district', 100)->nullable()->after('hsc_year');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['institution', 'hsc_year', 'district']);
        });
    }
};
