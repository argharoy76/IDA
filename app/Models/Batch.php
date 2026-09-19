<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'batch_name',
        'batch_code',
        'start_date',
        'end_date',
        'primary_instructor_id',
        'max_students',
        'schedule_summary',
        'status',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function primaryInstructor()
    {
        return $this->belongsTo(Instructor::class, 'primary_instructor_id');
    }

    public function instructor()
    {
        return $this->primaryInstructor();
    }

    public function getNameAttribute()
    {
        return $this->batch_name;
    }

    public function getCodeAttribute()
    {
        return $this->batch_code;
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'current_batch_id');
    }

    public function enrollments()
    {
        return $this->hasMany(BatchStudent::class);
    }

    public function routines()
    {
        return $this->hasMany(Routine::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
