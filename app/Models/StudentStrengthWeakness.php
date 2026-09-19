<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentStrengthWeakness extends Model
{
    use HasFactory;

    protected $table = 'student_strengths_weaknesses';

    protected $fillable = [
        'student_id',
        'type', // strength, weakness
        'category',
        'title',
        'description',
        'priority', // low, medium, high, critical
        'status', // identified, improving, improved, ongoing, resolved
        'identified_date',
        'follow_up_date',
        'instructor_id',
        'notes',
    ];

    protected $casts = [
        'identified_date' => 'date',
        'follow_up_date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }

    public function improvementPlans()
    {
        return $this->hasMany(ImprovementPlan::class, 'strength_weakness_id');
    }
}
