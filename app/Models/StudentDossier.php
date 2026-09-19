<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentDossier extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'readiness_score',
        'overall_status',
        'physical_grade',
        'communication_grade',
        'leadership_grade',
        'iq_grade',
        'discipline_grade',
        'summary_notes',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
