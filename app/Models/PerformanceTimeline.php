<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceTimeline extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'event_type',
        'title',
        'description',
        'event_date',
        'badge_color',
        'icon',
        'source_id',
    ];

    protected $casts = [
        'event_date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
