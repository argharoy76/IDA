<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use App\Models\Exam;
use App\Models\Student;

class SyncDatabaseTracksCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ida:sync-tracks {--force : Force schema repair without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Safely synchronize target_track and target_tracks columns for exams and students without disrupting existing data.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting Safe Database Track Synchronization...');

        $stats = [
            'exam_col_added' => false,
            'student_col_added' => false,
            'exams_backfilled' => 0,
            'students_backfilled' => 0,
        ];

        // 1. Check and add target_track to exams table
        if (Schema::hasTable('exams')) {
            if (!Schema::hasColumn('exams', 'target_track')) {
                Schema::table('exams', function (Blueprint $table) {
                    $table->string('target_track', 50)->nullable()->index()->after('branch');
                });
                $stats['exam_col_added'] = true;
                $this->info("✓ Column 'target_track' added to 'exams' table.");
            } else {
                $this->line("• Column 'target_track' already exists on 'exams' table.");
            }

            // Backfill exams with null or empty target_track
            $exams = DB::table('exams')->whereNull('target_track')->orWhere('target_track', '')->get();
            foreach ($exams as $ex) {
                $text = strtolower(($ex->category ?? '') . ' ' . ($ex->title ?? '') . ' ' . ($ex->exam_type ?? ''));
                $branch = strtolower($ex->branch ?? 'army');
                $track = 'prelim';

                if (str_contains($text, 'issb') || str_contains($text, 'wat') || str_contains($text, 'word_association')) {
                    $track = 'issb';
                } elseif ($branch === 'police') {
                    if (str_contains($text, 'constable')) {
                        $track = 'constable';
                    } elseif (preg_match('/\b(asi|assistant)\b/', $text)) {
                        $track = 'asi';
                    } elseif (preg_match('/\b(si|sub-inspector)\b/', $text)) {
                        $track = 'si';
                    } else {
                        $track = 'si';
                    }
                } elseif (in_array($branch, ['army', 'navy', 'air_force'], true)) {
                    $track = 'prelim';
                } else {
                    $track = 'general';
                }

                DB::table('exams')->where('id', $ex->id)->update(['target_track' => $track]);
                $stats['exams_backfilled']++;
            }
        }

        // 2. Check and add target_tracks to students table
        if (Schema::hasTable('students')) {
            if (!Schema::hasColumn('students', 'target_tracks')) {
                Schema::table('students', function (Blueprint $table) {
                    $table->string('target_tracks', 150)->nullable()->after('target_wing');
                });
                $stats['student_col_added'] = true;
                $this->info("✓ Column 'target_tracks' added to 'students' table.");
            } else {
                $this->line("• Column 'target_tracks' already exists on 'students' table.");
            }

            // Backfill students with null or empty target_tracks
            $students = Student::with(['courses', 'currentCourse'])->whereNull('target_tracks')->orWhere('target_tracks', '')->orWhere('target_tracks', '[]')->get();
            foreach ($students as $stu) {
                $tracks = $stu->getTargetTracks();
                if (empty($tracks)) {
                    $wing = strtolower($stu->target_wing ?? '');
                    if (str_contains($wing, 'police')) {
                        $tracks = ['si'];
                    } else {
                        $tracks = ['prelim', 'issb'];
                    }
                }

                DB::table('students')->where('id', $stu->id)->update([
                    'target_tracks' => json_encode(array_values(array_unique($tracks)))
                ]);
                $stats['students_backfilled']++;
            }
        }

        $this->info("Synchronization Complete! Exams backfilled: {$stats['exams_backfilled']}, Students backfilled: {$stats['students_backfilled']}. Zero existing data altered.");
        return 0;
    }
}
