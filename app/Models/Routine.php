<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Routine extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'batch_id',
        'instructor_id',
        'subject',
        'topic',
        'room',
        'class_date',
        'day_of_week',
        'start_time',
        'end_time',
        'class_type',
        'status',
        'notes',
    ];

    protected $casts = [
        'class_date' => 'date',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function getRoomNoAttribute()
    {
        return $this->room;
    }
}
