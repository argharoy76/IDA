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
        'institution',
        'hsc_year',
        'district',
        'current_course_id',
        'current_batch_id',
        'admission_date',
        'status',
        'documents',
    ];

    protected $casts = [
        'dob' => 'date',
        'age' => 'integer',
        'admission_date' => 'date',
        'documents' => 'array',
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
    public function isEnrolledInBranch(string $branch): bool
    {
        return in_array($branch, $this->getEnrolledBranches(), true);
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
