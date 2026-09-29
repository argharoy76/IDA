<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_code',
        'title',
        'slug',
        'description',
        'duration',
        'eligibility',
        'fee',
        'category',
        'branch',
        'target_track',
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
     * Get the branch key (navy, police, army, air_force, general) for this course.
     */
    public function getBranchKeyAttribute(): string
    {
        if (!empty($this->attributes['branch'])) {
            return $this->attributes['branch'];
        }

        $cat = strtolower($this->category ?? '');
        $title = strtolower($this->title ?? '');

        if (str_contains($cat, 'navy') || str_contains($title, 'navy') || str_contains($title, 'bns') || str_contains($title, 'bna')) {
            return 'navy';
        }
        if (str_contains($cat, 'police') || str_contains($title, 'police')) {
            return 'police';
        }
        if (str_contains($cat, 'army') || str_contains($title, 'army') || str_contains($title, 'bma')) {
            return 'army';
        }
        if (str_contains($cat, 'air') || str_contains($title, 'air') || str_contains($title, 'bafa')) {
            return 'air_force';
        }
        return 'general';
    }

    /**
     * Get the program / track key (preliminary, issb, constable, si, asi, soldier).
     */
    public function getProgramTrackAttribute(): string
    {
        if (!empty($this->attributes['target_track'])) {
            return $this->attributes['target_track'];
        }

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
        if (str_contains($cat, 'soldier') || str_contains($title, 'soldier') || str_contains($title, 'sainik') || str_contains($title, 'sailor') || str_contains($title, 'airman')) {
            return 'soldier';
        }
        return 'preliminary';
    }

    public function branchLabel(): string
    {
        return match($this->branch_key) {
            'navy' => 'Navy (BNA)',
            'police' => 'Police Service',
            'army' => 'Army (BMA)',
            'air_force' => 'Air Force (BAFA)',
            default => 'Armed Forces General',
        };
    }

    public function branchIcon(): string
    {
        return match($this->branch_key) {
            'navy' => 'fa-anchor',
            'police' => 'fa-shield-halved',
            'army' => 'fa-person-military-rifle',
            'air_force' => 'fa-jet-fighter',
            default => 'fa-award',
        };
    }

    public function branchColor(): string
    {
        return match($this->branch_key) {
            'navy' => '#1e3a8a',
            'police' => '#7c3aed',
            'army' => '#059669',
            'air_force' => '#0284c7',
            default => '#10b981',
        };
    }

    public function isOfficer(): bool
    {
        return in_array($this->program_track, ['preliminary', 'issb', 'si', 'asi'], true);
    }

    public function isPrelim(): bool
    {
        return $this->program_track === 'preliminary';
    }

    public function isIssb(): bool
    {
        return $this->program_track === 'issb';
    }

    public function isPolice(): bool
    {
        return $this->branch_key === 'police' || in_array($this->program_track, ['constable', 'si', 'asi'], true);
    }

    public function trackLabel(): string
    {
        return match($this->program_track) {
            'preliminary' => 'Preliminary (Officer 1)',
            'issb' => 'ISSB Masterclass (Officer 2)',
            'constable' => 'Constable',
            'si' => 'Sub-Inspector (SI)',
            'asi' => 'Assistant Sub-Inspector (ASI)',
            'soldier' => 'Soldier / Sailor / Airman',
            default => 'General Program',
        };
    }

    public function trackBadgeStyle(): string
    {
        return match($this->program_track) {
            'preliminary' => 'background: rgba(59,130,246,0.15); color: #60a5fa; border: 1px solid rgba(59,130,246,0.3);',
            'issb' => 'background: rgba(234,179,8,0.18); color: #facc15; border: 1.5px solid rgba(234,179,8,0.45);',
            'constable' => 'background: rgba(168,85,247,0.15); color: #c084fc; border: 1px solid rgba(168,85,247,0.3);',
            'si' => 'background: rgba(168,85,247,0.15); color: #c084fc; border: 1px solid rgba(168,85,247,0.3);',
            'asi' => 'background: rgba(168,85,247,0.15); color: #c084fc; border: 1px solid rgba(168,85,247,0.3);',
            'soldier' => 'background: rgba(249,115,22,0.15); color: #fb923c; border: 1px solid rgba(249,115,22,0.3);',
            default => 'background: rgba(255,255,255,0.06); color: #cbd5e1; border: 1px solid rgba(255,255,255,0.1);',
        };
    }
}
