<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'exam_type',
        'category',
        'branch',
        'description',
        'duration_minutes',
        'total_marks',
        'pass_marks',
        'random_question_count',
        'negative_marking_per_wrong',
        'fee',
        'is_paid_for_external',
        'is_public_for_external',
        'access_type',
        'status',
        'instructions',
        'schedule_start',
        'schedule_end',
    ];

    protected $casts = [
        'is_paid_for_external' => 'boolean',
        'is_public_for_external' => 'boolean',
        'total_marks' => 'decimal:2',
        'pass_marks' => 'decimal:2',
        'random_question_count' => 'integer',
        'negative_marking_per_wrong' => 'decimal:2',
        'fee' => 'decimal:2',
        'schedule_start' => 'datetime',
        'schedule_end' => 'datetime',
    ];

    public function branchLabel(): string
    {
        return match($this->branch) {
            'army' => 'Bangladesh Army',
            'navy' => 'Bangladesh Navy',
            'air_force' => 'Bangladesh Air Force',
            'police' => 'Bangladesh Police',
            default => 'Armed Forces General',
        };
    }

    public function branchIcon(): string
    {
        return match($this->branch) {
            'army' => 'fa-shield-halved',
            'navy' => 'fa-anchor',
            'air_force' => 'fa-jet-fighter',
            'police' => 'fa-user-shield',
            default => 'fa-award',
        };
    }

    public function branchColor(): string
    {
        return match($this->branch) {
            'army' => '#059669', // Emerald
            'navy' => '#1e3a8a', // Deep Navy
            'air_force' => '#0284c7', // Sky Blue
            'police' => '#ea580c', // Police Orange
            default => '#10b981',
        };
    }

    public function accessTypeLabel(): string
    {
        return match($this->access_type) {
            'free' => 'Free Exam',
            'both' => 'Both (Cadet & Free)',
            default => 'Cadet Exam',
        };
    }

    public function isScheduledFuture(): bool
    {
        if (in_array($this->status, ['open', 'closed', 'archived'])) {
            return false;
        }
        if ($this->status === 'scheduled') {
            if ($this->schedule_start) {
                return $this->schedule_start->isFuture();
            }
            return false;
        }
        return false;
    }

    public function isAvailableNow(): bool
    {
        if (in_array($this->status, ['closed', 'archived'])) {
            return false;
        }
        if ($this->schedule_end && $this->schedule_end->isPast()) {
            return false;
        }
        if ($this->status === 'open') {
            return true;
        }
        if ($this->status === 'scheduled') {
            if (!$this->schedule_start || $this->schedule_start->isPast()) {
                return true;
            }
        }
        return false;
    }

    public function isEnded(): bool
    {
        if (in_array($this->status, ['closed', 'archived'])) {
            return true;
        }
        if ($this->schedule_end && $this->schedule_end->isPast()) {
            return true;
        }
        return false;
    }

    public function isRankPublished(): bool
    {
        return $this->isEnded();
    }

    public function isScorecardAvailable(): bool
    {
        return $this->isEnded();
    }

    protected static array $leaderboardCache = [];

    public static function clearLeaderboardCache(): void
    {
        static::$leaderboardCache = [];
    }

    public function getLeaderboard(): \Illuminate\Support\Collection
    {
        if (isset(static::$leaderboardCache[$this->id])) {
            return static::$leaderboardCache[$this->id];
        }

        $attemptsQuery = ExamAttempt::with(['user', 'student'])
            ->where('exam_id', $this->id)
            ->whereIn('status', ['submitted', 'auto_submitted']);

        // Check if genuine student examinees exist
        $hasStudentAttempts = (clone $attemptsQuery)->whereHas('user', function ($q) {
            $q->whereNotIn('role', ['super_admin', 'admin', 'instructor']);
        })->exists();

        // If student examinees exist, exclude administrator/staff preview attempts
        if ($hasStudentAttempts) {
            $attemptsQuery->whereHas('user', function ($q) {
                $q->whereNotIn('role', ['super_admin', 'admin', 'instructor']);
            });
        }

        $allAttempts = $attemptsQuery->get();

        if ($allAttempts->isEmpty()) {
            return static::$leaderboardCache[$this->id] = collect();
        }

        // Group by candidate: coalesce student_id or user_id
        $grouped = $allAttempts->groupBy(function ($att) {
            return $att->student_id ? 'student_' . $att->student_id : 'user_' . $att->user_id;
        });

        // Pick each candidate's best attempt (highest score, tie-break by lower time_taken or lower ID)
        $candidates = $grouped->map(function ($attempts) {
            return $attempts->sort(function ($a, $b) {
                if ((float)$b->score !== (float)$a->score) {
                    return (float)$b->score <=> (float)$a->score;
                }
                return $a->id <=> $b->id;
            })->first();
        });

        // Sort descending by score
        $sortedCandidates = $candidates->sortByDesc(function ($att) {
            return (float) $att->score;
        })->values();

        // Assign standard ranking positions (1, 2, 3...)
        $leaderboard = collect();
        $currentRank = 1;
        $previousScore = null;

        foreach ($sortedCandidates as $index => $candidateAttempt) {
            $score = (float) $candidateAttempt->score;
            if ($previousScore !== null && $score < $previousScore) {
                $currentRank = $index + 1;
            }
            $previousScore = $score;

            $displayName = 'Candidate';
            if ($candidateAttempt->student && !empty($candidateAttempt->student->full_name)) {
                $displayName = $candidateAttempt->student->full_name;
            } elseif ($candidateAttempt->user && !empty($candidateAttempt->user->name)) {
                $displayName = $candidateAttempt->user->name;
            }

            $leaderboard->push((object)[
                'rank' => $currentRank,
                'attempt_id' => $candidateAttempt->id,
                'user_id' => $candidateAttempt->user_id,
                'student_id' => $candidateAttempt->student_id,
                'name' => $displayName,
                'score' => $score,
                'total_marks' => (float) ($candidateAttempt->total_marks ?? $this->total_marks),
                'percentage' => (float) $candidateAttempt->percentage,
                'result_status' => $candidateAttempt->result_status,
                'is_passed' => (bool) $candidateAttempt->is_passed,
                'completed_at' => $candidateAttempt->completed_at,
            ]);
        }

        return static::$leaderboardCache[$this->id] = $leaderboard;
    }

    public function isLiveNow(): bool
    {
        if ($this->isEnded()) {
            return false;
        }
        if ($this->isScheduledFuture()) {
            return false;
        }
        if ($this->status === 'open') {
            return true;
        }
        if ($this->status === 'scheduled') {
            if (!$this->schedule_start || $this->schedule_start->isPast()) {
                return true;
            }
        }
        return false;
    }

    public function conditionStatus(): string
    {
        if ($this->isEnded()) {
            return 'ENDED';
        }
        if ($this->isScheduledFuture()) {
            return 'SCHEDULED';
        }
        if ($this->isLiveNow()) {
            return 'LIVE';
        }
        return strtoupper($this->status);
    }

    public function getSecondsUntilStart(): int
    {
        if (!$this->schedule_start || $this->schedule_start->isPast()) {
            return 0;
        }
        return (int) max(0, \Carbon\Carbon::now()->diffInSeconds($this->schedule_start, false));
    }

    public function questions()
    {
        return $this->hasMany(Question::class)->orderBy('order_seq');
    }

    public function watWords()
    {
        return $this->hasMany(WatWord::class)->orderBy('order_seq');
    }

    public function attempts()
    {
        return $this->hasMany(ExamAttempt::class);
    }
}
