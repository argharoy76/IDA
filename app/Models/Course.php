<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'duration',
        'eligibility',
        'fee',
        'category',
        'features',
        'syllabus',
        'admission_status',
        'schedule_info',
        'image',
        'is_featured',
    ];

    protected $casts = [
        'features' => 'array',
        'syllabus' => 'array',
        'is_featured' => 'boolean',
        'fee' => 'decimal:2',
    ];

    public function batches()
    {
        return $this->hasMany(Batch::class);
    }

    public function activeBatches()
    {
        return $this->hasMany(Batch::class)->where('status', 'active');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'current_course_id');
    }

    public function enrolledStudents()
    {
        return $this->belongsToMany(Student::class, 'course_student')
            ->withPivot(['enrolled_at', 'status', 'assigned_by', 'payment_status', 'notes'])
            ->withTimestamps();
    }

    public function routines()
    {
        return $this->hasMany(Routine::class);
    }

    /**
     * Scope courses for a specific military or police sector.
     */
    public function scopeForBranch($query, string $branch)
    {
        return $query->where(function ($q) use ($branch) {
            if ($branch === 'army') {
                $q->where('category', 'like', '%army%')
                  ->orWhere('title', 'like', '%army%')
                  ->orWhere('title', 'like', '%bma%');
            } elseif ($branch === 'navy') {
                $q->where('category', 'like', '%navy%')
                  ->orWhere('title', 'like', '%navy%')
                  ->orWhere('title', 'like', '%bns%')
                  ->orWhere('title', 'like', '%bna%');
            } elseif ($branch === 'air_force' || $branch === 'airforce') {
                $q->where('category', 'like', '%air%')
                  ->orWhere('title', 'like', '%air%')
                  ->orWhere('title', 'like', '%bafa%');
            } elseif ($branch === 'police') {
                $q->where('category', 'like', '%police%')
                  ->orWhere('title', 'like', '%police%');
            }
        });
    }

    /**
     * Get the branch key (army, navy, air_force, police, general) for this course.
     */
    public function getBranchKeyAttribute(): string
    {
        $cat = strtolower($this->category ?? '');
        $title = strtolower($this->title ?? '');

        if (str_contains($cat, 'army') || str_contains($title, 'army') || str_contains($title, 'bma')) {
            return 'army';
        }
        if (str_contains($cat, 'navy') || str_contains($title, 'navy') || str_contains($title, 'bns') || str_contains($title, 'bna')) {
            return 'navy';
        }
        if (str_contains($cat, 'air') || str_contains($title, 'air') || str_contains($title, 'bafa')) {
            return 'air_force';
        }
        if (str_contains($cat, 'police') || str_contains($title, 'police')) {
            return 'police';
        }
        return 'general';
    }

    /**
     * Get the program / track key (preliminary, issb, constable, si, asi).
     */
    public function getProgramTrackAttribute(): string
    {
        $cat = strtolower($this->category ?? '');
        $title = strtolower($this->title ?? '');

        if (str_contains($cat, 'constable') || str_contains($title, 'constable') || str_contains($title, 'con')) {
            return 'constable';
        }
        if (str_contains($cat, 'asi') || str_contains($title, 'asi') || str_contains($title, 'assistant sub-inspector') || str_contains($cat, 'assistant sub-inspector')) {
            return 'asi';
        }
        if (str_contains($cat, 'sub-inspector') || str_contains($title, 'sub-inspector') || str_contains($title, ' si ') || str_contains($title, 'si &') || str_contains($title, 'si/')) {
            return 'si';
        }
        if (str_contains($cat, 'issb') || str_contains($title, 'issb')) {
            return 'issb';
        }
        if (str_contains($cat, 'prelim') || str_contains($title, 'prelim') || str_contains($title, 'regular') || str_contains($title, 'cadet prep')) {
            return 'preliminary';
        }
        return 'preliminary';
    }
}
