<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Instructor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'instructor_code',
        'designation',
        'specialization',
        'phone',
        'bio',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function batches()
    {
        return $this->hasMany(Batch::class, 'primary_instructor_id');
    }

    public function routines()
    {
        return $this->hasMany(Routine::class);
    }

    public function observations()
    {
        return $this->hasMany(InstructorObservation::class);
    }

    public function assignedImprovementPlans()
    {
        return $this->hasMany(ImprovementPlan::class, 'responsible_instructor_id');
    }
}
