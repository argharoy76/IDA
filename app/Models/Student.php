<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'student_id_code',
        'roll_number',
        'student_type',
        'father_name',
        'mother_name',
        'dob',
        'age',
        'gender',
        'blood_group',
        'address',
        'emergency_contact',
        'target_wing',
        'target_tracks',
        'institution',
        'hsc_year',
        'district',
        'current_course_id',
        'current_batch_id',
        'admission_date',
        'status',
        'documents',
        'plain_password',
    ];

    protected $casts = [
        'dob' => 'date',
        'age' => 'integer',
        'admission_date' => 'date',
        'documents' => 'array',
        'target_tracks' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_student')
            ->withPivot(['enrolled_at', 'status', 'assigned_by', 'payment_status', 'notes'])
            ->withTimestamps();
    }

    public function hasCourse($courseId): bool
    {
        return $this->courses()->where('courses.id', $courseId)->exists()
            || ($this->current_course_id == $courseId);
    }

    /**
     * Determine all branch sectors the cadet is enrolled in (e.g. ['army'], ['navy'], etc.).
     */
    public function getEnrolledBranches(): array
    {
        $branches = [];

        // 1. Check enrolled courses
        $courses = $this->courses;
        if ($courses->isEmpty() && $this->currentCourse) {
            $courses = collect([$this->currentCourse]);
        } elseif ($this->currentCourse && !$courses->contains('id', $this->current_course_id)) {
            $courses = $courses->push($this->currentCourse);
        }

        foreach ($courses as $course) {
            $cat = strtolower($course->category ?? '');
            $title = strtolower($course->title ?? '');

            if (str_contains($cat, 'army') || str_contains($title, 'army') || str_contains($title, 'bma')) {
                $branches[] = 'army';
            }
            if (str_contains($cat, 'navy') || str_contains($title, 'navy') || str_contains($title, 'bns')) {
                $branches[] = 'navy';
            }
            if (str_contains($cat, 'air') || str_contains($title, 'air') || str_contains($title, 'bafa')) {
                $branches[] = 'air_force';
            }
            if (str_contains($cat, 'police') || str_contains($title, 'police')) {
                $branches[] = 'police';
            }
            if (str_contains($cat, 'issb') || str_contains($title, 'issb')) {
                $branches[] = 'army';
                $branches[] = 'navy';
                $branches[] = 'air_force';
            }
        }

        // 2. If no branches found from courses, fallback to target_wing
        if (empty($branches) && !empty($this->target_wing)) {
            $wing = strtolower(trim($this->target_wing));
            if (str_contains($wing, 'army')) {
                $branches[] = 'army';
            } elseif (str_contains($wing, 'navy')) {
                $branches[] = 'navy';
            } elseif (str_contains($wing, 'air')) {
                $branches[] = 'air_force';
            } elseif (str_contains($wing, 'police')) {
                $branches[] = 'police';
            } elseif (str_contains($wing, 'general')) {
                $branches = ['army', 'navy', 'air_force'];
            }
        }

        return array_values(array_unique($branches));
    }

    /**
     * Check whether student is enrolled in a specific branch sector.
     */
    public function isEnrolledInBranch(?string $branch): bool
    {
        if (empty($branch) || $branch === 'general' || $branch === 'all') {
            return true;
        }
        return in_array($branch, $this->getEnrolledBranches(), true);
    }

    /**
     * Check if cadet has purchased/enrolled in ANY Tri-Services Military Course (Army, Navy, or Air Force).
     */
    public function hasMilitaryCourse(): bool
    {
        $branches = $this->getEnrolledBranches();
        return in_array('army', $branches, true) 
            || in_array('navy', $branches, true) 
            || in_array('air_force', $branches, true);
    }

    /**
     * Get array of all assigned program / category tracks (e.g. ['prelim', 'issb'], ['si', 'asi'], ['constable']).
     */
    public function getTargetTracks(): array
    {
        $tracks = [];

        // 1. From explicit target_tracks field
        if (!empty($this->target_tracks)) {
            $raw = is_array($this->target_tracks) ? $this->target_tracks : json_decode($this->target_tracks, true);
            if (is_array($raw)) {
                $tracks = array_merge($tracks, $raw);
            } elseif (is_string($this->target_tracks)) {
                $tracks = array_merge($tracks, array_map('trim', explode(',', $this->target_tracks)));
            }
        }

        // 2. From target_wing string
        if (!empty($this->target_wing)) {
            $w = strtolower($this->target_wing);
            if (str_contains($w, 'prelim') || str_contains($w, 'written')) {
                $tracks[] = 'prelim';
            }
            if (str_contains($w, 'issb')) {
                $tracks[] = 'issb';
            }
            if (str_contains($w, 'constable') || str_contains($w, 'con')) {
                $tracks[] = 'constable';
            }
            if (preg_match('/\b(asi|assistant sub-inspector|assistant sub inspector)\b/i', $w)) {
                $tracks[] = 'asi';
            }
            $withoutAsi = preg_replace('/\b(asi|assistant sub-inspector|assistant sub inspector|assistant)\b/i', '', $w);
            if (preg_match('/\b(si|sub-inspector|sub inspector)\b/i', $withoutAsi)) {
                $tracks[] = 'si';
            }
        }

        // 3. From enrolled courses
        $courses = $this->courses;
        if ($courses->isEmpty() && $this->currentCourse) {
            $courses = collect([$this->currentCourse]);
        }
        foreach ($courses as $c) {
            if (!empty($c->program_track)) {
                $tracks[] = $c->program_track;
            }
            $cat = strtolower($c->category ?? '');
            $title = strtolower($c->title ?? '');
            $text = $cat . ' ' . $title;
            if (str_contains($text, 'constable')) $tracks[] = 'constable';
            if (preg_match('/\b(asi|assistant sub-inspector|assistant sub inspector)\b/i', $text)) $tracks[] = 'asi';
            $withoutAsi = preg_replace('/\b(asi|assistant sub-inspector|assistant sub inspector|assistant)\b/i', '', $text);
            if (preg_match('/\b(si|sub-inspector|sub inspector)\b/i', $withoutAsi)) $tracks[] = 'si';
            if (str_contains($text, 'issb')) $tracks[] = 'issb';
            if (str_contains($text, 'prelim') || str_contains($text, 'bma') || str_contains($text, 'bna') || str_contains($text, 'bafa')) $tracks[] = 'prelim';
        }

        return array_values(array_unique(array_filter($tracks)));
    }

    /**
     * Check if cadet is authorized for Soldier/Non-Commissioned exams of the specified branch.
     */
    public function hasSoldierTrack(?string $branch = null): bool
    {
        $branch = $branch ?: 'army';
        if (!$this->isEnrolledInBranch($branch)) {
            return false;
        }

        $tracks = $this->getTargetTracks();
        if (!empty($tracks)) {
            if (in_array('soldier', $tracks, true)) {
                return true;
            }
            // If they only have officer tracks (prelim/issb), they don't get soldier exams
            if (in_array('prelim', $tracks, true) || in_array('issb', $tracks, true)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if cadet is authorized for Preliminary exams of the specified branch.
     */
    public function hasPrelimTrack(?string $branch = null): bool
    {
        $branch = $branch ?: 'army';
        if (!$this->isEnrolledInBranch($branch)) {
            return false;
        }

        $tracks = $this->getTargetTracks();
        // If cadet has designated tracks
        if (!empty($tracks)) {
            if (in_array('prelim', $tracks, true)) {
                return true;
            }
            // If they are strictly in Soldier or ISSB track, deny prelim
            if (in_array('soldier', $tracks, true) || in_array('issb', $tracks, true)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if cadet is authorized for ISSB exams across all branches.
     * Rule: Every student that has ISSB courses, or ISSB track, or military courses can conduct ISSB exams!
     */
    public function hasIssbTrack(): bool
    {
        // 1. Explicitly assigned ISSB track in target_tracks column
        $explicitTracks = [];
        if (!empty($this->target_tracks)) {
            $raw = is_array($this->target_tracks) ? $this->target_tracks : json_decode($this->target_tracks, true);
            if (is_array($raw)) {
                $explicitTracks = $raw;
            } elseif (is_string($this->target_tracks)) {
                $explicitTracks = array_map('trim', explode(',', $this->target_tracks));
            }
        }

        if (in_array('issb', $explicitTracks, true)) {
            return true;
        }

        // 2. Direct enrolled course with ISSB in title, track, or category
        $courses = $this->courses;
        if ($courses->isEmpty() && $this->currentCourse) {
            $courses = collect([$this->currentCourse]);
        }
        foreach ($courses as $c) {
            $text = strtolower(($c->title ?? '') . ' ' . ($c->category ?? '') . ' ' . ($c->program_track ?? ''));
            if (str_contains($text, 'issb')) {
                return true;
            }
        }

        // 3. Cadets with any military course (Army, Navy, Air Force)
        if ($this->hasMilitaryCourse()) {
            // New Rule: If they are strictly enrolled in the 'soldier' track, they do not get automatic ISSB access.
            if (in_array('soldier', $explicitTracks, true) && !in_array('issb', $explicitTracks, true) && !in_array('prelim', $explicitTracks, true)) {
                return false;
            }
            return true;
        }

        // 4. Check if target_wing includes ISSB
        if (!empty($this->target_wing) && str_contains(strtolower($this->target_wing), 'issb')) {
            return true;
        }

        return false;
    }

    /**
     * Check if cadet has access to a specific Police Track (Constable, SI, ASI).
     */
    public function hasPoliceTrack(string $track): bool
    {
        if (!$this->isEnrolledInBranch('police')) {
            return false;
        }

        $track = strtolower(trim($track));
        $tracks = $this->getTargetTracks();

        $policeTracks = array_intersect(['constable', 'si', 'asi'], $tracks);
        if (!empty($policeTracks)) {
            return in_array($track, $policeTracks, true);
        }

        // If enrolled in generic Police with no specific track restriction, allow access
        return true;
    }

    /**
     * Verify if cadet is authorized to conduct the specified exam.
     */
    public function canConductExam(Exam $exam): bool
    {
        return $exam->canCandidateAccess($this);
    }

    /**
     * Human-readable exam clearance overview.
     */
    public function getExamClearanceDescription(): string
    {
        $branches = $this->getEnrolledBranches();
        $clearances = [];

        if ($this->hasMilitaryCourse()) {
            $prelimWings = [];
            if (in_array('army', $branches, true)) $prelimWings[] = 'Army Prelim';
            if (in_array('navy', $branches, true)) $prelimWings[] = 'Navy Prelim';
            if (in_array('air_force', $branches, true)) $prelimWings[] = 'Air Force Prelim';

            if (!empty($prelimWings)) {
                $clearances[] = implode(', ', $prelimWings);
            }
            $clearances[] = 'All Tri-Services ISSB Masterclasses';
        }

        if (in_array('police', $branches, true)) {
            $clearances[] = 'Police Assessments (' . ($this->target_wing ?: 'Police Service') . ')';
        }

        return !empty($clearances) ? implode(' + ', $clearances) : 'General Assessments';
    }

    public function currentCourse()
    {
        return $this->belongsTo(Course::class, 'current_course_id');
    }

    public function currentBatch()
    {
        return $this->belongsTo(Batch::class, 'current_batch_id');
    }

    public function dossier()
    {
        return $this->hasOne(StudentDossier::class);
    }

    public function batchHistory()
    {
        return $this->hasMany(BatchStudent::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function strengthsWeaknesses()
    {
        return $this->hasMany(StudentStrengthWeakness::class);
    }

    public function strengths()
    {
        return $this->hasMany(StudentStrengthWeakness::class)->where('type', 'strength');
    }

    public function weaknesses()
    {
        return $this->hasMany(StudentStrengthWeakness::class)->where('type', 'weakness');
    }

    public function observations()
    {
        return $this->hasMany(InstructorObservation::class);
    }

    public function performanceAssessments()
    {
        return $this->hasMany(PerformanceAssessment::class)->orderBy('assessment_date', 'desc');
    }

    public function improvementPlans()
    {
        return $this->hasMany(ImprovementPlan::class);
    }

    public function timelines()
    {
        return $this->hasMany(PerformanceTimeline::class)->orderBy('event_date', 'desc');
    }

    public function examAttempts()
    {
        return $this->hasMany(ExamAttempt::class);
    }

    // Helper: Compute real-time Attendance percentage
    public function getAttendancePercentageAttribute(): float
    {
        $total = $this->attendances()->count();
        if ($total === 0) return 100.0;
        $present = $this->attendances()->whereIn('status', ['present', 'late'])->count();
        return round(($present / $total) * 100, 1);
    }

    // Helper: Total pending fee due
    public function getTotalDueFeeAttribute(): float
    {
        return (float) $this->invoices()->whereIn('status', ['pending', 'partially_paid', 'overdue'])->sum('due_amount');
    }

    // Helper: Check if student has early warning flags
    public function getRequiresAttentionAttribute(): bool
    {
        // Flag 1: Attendance below 75%
        if ($this->attendances()->count() >= 3 && $this->attendance_percentage < 75.0) {
            return true;
        }
        // Flag 2: Critical / High unresolved weaknesses
        $criticalWeaknesses = $this->weaknesses()
            ->whereIn('priority', ['high', 'critical'])
            ->whereIn('status', ['identified', 'ongoing'])
            ->count();
        if ($criticalWeaknesses > 0) {
            return true;
        }
        // Flag 3: Failed recent exam attempt
        $recentFailed = $this->examAttempts()
            ->where('result_status', 'failed')
            ->count();
        if ($recentFailed > 0) {
            return true;
        }
        return false;
    }
}
