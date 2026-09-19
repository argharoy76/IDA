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
        Schema::table('users', function (Blueprint $table) {
            $table->string('account_id', 60)->nullable()->unique()->after('email');
        });

        // Backfill account_id for existing users
        $users = \App\Models\User::with(['student', 'instructor'])->get();
        foreach ($users as $user) {
            $accountId = null;
            if ($user->student && !empty($user->student->student_id_code)) {
                $accountId = $user->student->student_id_code;
            } elseif ($user->instructor && !empty($user->instructor->instructor_code)) {
                $accountId = $user->instructor->instructor_code;
            } elseif ($user->email === 'admin@ida.com' || $user->role === 'super_admin' || strtolower($user->name) === 'argharoy') {
                $accountId = 'Argharoy';
            } elseif ($user->email === 'ops@ida.com') {
                $accountId = 'ADM-002';
            } elseif ($user->email === 'finance@ida.com') {
                $accountId = 'FIN-001';
            } else {
                $rolePrefix = match($user->role) {
                    'super_admin', 'admin' => 'ADM',
                    'finance_manager' => 'FIN',
                    'instructor' => 'INS',
                    'academic_student' => 'IDA',
                    'external_student' => 'EXT',
                    default => 'USR',
                };
                $accountId = sprintf('%s-%s-%03d', $rolePrefix, date('Y'), $user->id);
            }

            $candidateId = $accountId;
            $suffix = 1;
            while (\App\Models\User::where('account_id', $candidateId)->where('id', '!=', $user->id)->exists()) {
                $candidateId = $accountId . '-' . $suffix;
                $suffix++;
            }

            $user->account_id = $candidateId;
            $user->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('account_id');
        });
    }
};
