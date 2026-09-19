<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'user_id',
        'student_id',
        'attempt_number',
        'started_at',
        'completed_at',
        'time_spent_seconds',
        'score',
        'total_marks',
        'percentage',
        'result_status',
        'status',
        'answers_summary',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'score' => 'decimal:2',
        'total_marks' => 'decimal:2',
        'percentage' => 'decimal:2',
        'answers_summary' => 'array',
    ];

    protected static function booted(): void
    {
        static::saved(function () {
            static::clearRankCache();
        });
        static::deleted(function () {
            static::clearRankCache();
        });
    }

    // ─── Accessor: is_passed ─────────────────────────────────────
    // Used by result views and dashboards to determine pass/fail badge
    public function getIsPassedAttribute(): bool
    {
        return $this->result_status === 'passed';
    }

    // ─── Accessor: correct_answers ───────────────────────────────
    // Returns count of correct answers from the answers_summary JSON
    public function getCorrectAnswersAttribute(): int
    {
        return (int) ($this->answers_summary['correct'] ?? 0);
    }

    // ─── Accessor: wrong_answers ─────────────────────────────────
    // Returns count of incorrect answers from the answers_summary JSON
    public function getWrongAnswersAttribute(): int
    {
        return (int) ($this->answers_summary['incorrect'] ?? 0);
    }

    // ─── Accessor: negative_marks_deducted ───────────────────────
    // Calculates total negative marks deducted from individual answers
    public function getNegativeMarksDeductedAttribute(): float
    {
        if (isset($this->answers_summary['negative_marks'])) {
            return (float) $this->answers_summary['negative_marks'];
        }

        // If answers are loaded, compute from them
        if ($this->relationLoaded('answers')) {
            return (float) abs($this->answers->where('marks_awarded', '<', 0)->sum('marks_awarded'));
        }

        // Fallback: calculate from answers_summary
        $wrongCount = $this->wrong_answers;
        $exam = $this->exam;
        if ($exam && $wrongCount > 0) {
            return (float) round($wrongCount * ($exam->negative_marking_per_wrong ?? 0.25), 2);
        }

        return 0.0;
    }

    // ─── Ranking and Candidate Calculations ───────────────────────
    protected static array $rankCache = [];

    public static function clearRankCache(): void
    {
        static::$rankCache = [];
        Exam::clearLeaderboardCache();
    }

    public function isRankPublished(): bool
    {
        return $this->exam ? $this->exam->isRankPublished() : false;
    }

    public function isScorecardAvailable(): bool
    {
        return $this->exam ? $this->exam->isScorecardAvailable() : false;
    }

    public function getRankInfo(): array
    {
        $examId = $this->exam_id;
        $score = (float) $this->score;

        if (!isset(static::$rankCache[$examId])) {
            $baseQuery = static::where('exam_id', $examId)
                ->whereIn('status', ['submitted', 'auto_submitted']);

            // If real student examinees exist, exclude administrator/staff preview attempts from candidate ranking
            $hasStudentAttempts = (clone $baseQuery)->whereHas('user', function ($q) {
                $q->whereNotIn('role', ['super_admin', 'admin', 'instructor']);
            })->exists();

            if ($hasStudentAttempts) {
                $baseQuery->whereHas('user', function ($q) {
                    $q->whereNotIn('role', ['super_admin', 'admin', 'instructor']);
                });
            }

            static::$rankCache[$examId] = $baseQuery
                ->selectRaw('COALESCE(student_id, user_id) as candidate_key, MAX(score) as best_score')
                ->groupBy('candidate_key')
                ->get();
        }

        $candidates = static::$rankCache[$examId];
        $totalCandidates = max(1, $candidates->count());
        $betterCandidates = $candidates->filter(function ($c) use ($score) {
            return (float) $c->best_score > $score;
        })->count();

        return [
            'rank' => $betterCandidates + 1,
            'total_candidates' => $totalCandidates,
        ];
    }

    public function getRankAttribute(): int
    {
        return $this->getRankInfo()['rank'];
    }

    public function getTotalCandidatesAttribute(): int
    {
        return $this->getRankInfo()['total_candidates'];
    }

    // ─── Accessor: submitted_at (alias for completed_at) ─────────
    // Some views reference submitted_at instead of completed_at
    public function getSubmittedAtAttribute()
    {
        return $this->completed_at;
    }

    // ─── Relationships ───────────────────────────────────────────

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function answers()
    {
        return $this->hasMany(ExamAttemptAnswer::class, 'attempt_id');
    }
}
