<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstructorObservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'instructor_id',
        'observation_date',
        'category',
        'observation_text',
        'rating',
        'recommended_action',
        'follow_up_date',
        'visibility', // student_visible, instructor_only, admin_only
    ];

    protected $casts = [
        'observation_date' => 'date',
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
}
