<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'branch',
        'exam_type',
        'question_text',
        'question_image',
        'options',
        'correct_answer',
        'explanation',
        'marks',
        'negative_marks',
        'order_seq',
        'difficulty',
    ];

    public function scopePool($query, ?string $branch = null)
    {
        $q = $query->whereNull('exam_id');
        if ($branch) {
            $q->where('branch', $branch);
        }
        return $q;
    }

    public function scopeForBranch($query, string $branch)
    {
        return $query->where('branch', $branch);
    }

    protected $casts = [
        'options' => 'array',
        'marks' => 'decimal:2',
        'negative_marks' => 'decimal:2',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }
}
