<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImprovementPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'strength_weakness_id',
        'problem_description',
        'target_objective',
        'recommended_activity',
        'assigned_task',
        'responsible_instructor_id',
        'deadline',
        'progress_percentage',
        'status',
        'follow_up_date',
        'review_notes',
    ];

    protected $casts = [
        'deadline' => 'date',
        'follow_up_date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function weakness()
    {
        return $this->belongsTo(StudentStrengthWeakness::class, 'strength_weakness_id');
    }

    public function responsibleInstructor()
    {
        return $this->belongsTo(Instructor::class, 'responsible_instructor_id');
    }
}
