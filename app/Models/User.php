<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'account_id',
        'password',
        'role',
        'phone',
        'status',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['super_admin', 'admin']);
    }

    public function isFinanceManager(): bool
    {
        return in_array($this->role, ['super_admin', 'finance_manager']);
    }

    public function isInstructor(): bool
    {
        return $this->role === 'instructor';
    }

    public function isAcademicStudent(): bool
    {
        return $this->role === 'academic_student';
    }

    public function isExternalStudent(): bool
    {
        return $this->role === 'external_student';
    }

    public function hasRole(array|string $roles): bool
    {
        if (is_string($roles)) {
            $roles = explode(',', $roles);
        }
        $roles = array_map('trim', $roles);
        if ($this->role === 'super_admin') {
            return true;
        }
        return in_array($this->role, $roles);
    }

    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function instructor()
    {
        return $this->hasOne(Instructor::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function examAttempts()
    {
        return $this->hasMany(ExamAttempt::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}
