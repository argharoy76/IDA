<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamAttemptAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'attempt_id',
        'question_id',
        'wat_word_id',
        'user_answer',
        'response_time_seconds',
        'is_correct',
        'marks_awarded',
        'instructor_feedback',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'marks_awarded' => 'decimal:2',
    ];

    // ─── Accessor: selected_option (alias for user_answer) ───────
    // Views reference $ans->selected_option for MCQ choice letter (A/B/C/D)
    public function getSelectedOptionAttribute(): ?string
    {
        return $this->user_answer;
    }

    // ─── Accessor: text_answer (alias for user_answer) ───────────
    // Views reference $ans->text_answer for WAT sentence responses
    public function getTextAnswerAttribute(): ?string
    {
        return $this->user_answer;
    }

    // ─── Accessor: marks_deducted ────────────────────────────────
    // Returns the absolute value of negative marks awarded (for wrong answers)
    public function getMarksDeductedAttribute(): float
    {
        return (float) abs(min(0, (float) $this->marks_awarded));
    }

    // ─── Relationships ───────────────────────────────────────────

    public function attempt()
    {
        return $this->belongsTo(ExamAttempt::class, 'attempt_id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function watWord()
    {
        return $this->belongsTo(WatWord::class);
    }
}
