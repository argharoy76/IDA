<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceAssessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'instructor_id',
        'assessment_date',
        'period_label',
        'category',
        'ratings',
        'overall_rating',
        'remarks',
    ];

    protected $casts = [
        'assessment_date' => 'date',
        'ratings' => 'array',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }
}
