<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Course;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            if (!Schema::hasColumn('courses', 'branch')) {
                $table->string('branch', 50)->nullable()->index()->after('category');
            }
            if (!Schema::hasColumn('courses', 'target_track')) {
                $table->string('target_track', 50)->nullable()->index()->after('branch');
            }
        });

        // Backfill existing courses
        try {
            $courses = Course::all();
            foreach ($courses as $c) {
                $cat = strtolower($c->category ?? '');
                $title = strtolower($c->title ?? '');

                // Determine branch
                $branch = 'army';
                if (str_contains($cat, 'navy') || str_contains($title, 'navy') || str_contains($title, 'bna') || str_contains($title, 'bns')) {
                    $branch = 'navy';
                } elseif (str_contains($cat, 'police') || str_contains($title, 'police')) {
                    $branch = 'police';
                } elseif (str_contains($cat, 'air') || str_contains($title, 'air') || str_contains($title, 'bafa')) {
                    $branch = 'air_force';
                } elseif (str_contains($cat, 'army') || str_contains($title, 'army') || str_contains($title, 'bma')) {
                    $branch = 'army';
                }

                // Determine target_track
                if ($branch === 'police') {
                    if (str_contains($cat, 'constable') || str_contains($title, 'constable') || str_contains($title, 'con')) {
                        $track = 'constable';
                    } elseif (str_contains($cat, 'asi') || str_contains($title, 'asi') || str_contains($title, 'assistant')) {
                        $track = 'asi';
                    } else {
                        $track = 'si';
                    }
                } else {
                    if (str_contains($cat, 'issb') || str_contains($title, 'issb')) {
                        $track = 'issb';
                    } elseif (str_contains($cat, 'soldier') || str_contains($title, 'soldier') || str_contains($title, 'sainik') || str_contains($title, 'sailor') || str_contains($title, 'airman')) {
                        $track = 'soldier';
                    } else {
                        $track = 'preliminary';
                    }
                }

                $c->branch = $branch;
                $c->target_track = $track;
                $c->save();
            }
        } catch (\Throwable $e) {}
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            if (Schema::hasColumn('courses', 'target_track')) {
                $table->dropColumn('target_track');
            }
            if (Schema::hasColumn('courses', 'branch')) {
                $table->dropColumn('branch');
            }
        });
    }
};
