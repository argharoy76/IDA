<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'designation',
        'bio',
        'photo',
        'display_category',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_category', 'asc')
                     ->orderBy('display_order', 'asc');
    }

    public function getPhotoUrl(): string
    {
        if (!empty($this->photo)) {
            if (str_starts_with($this->photo, 'http://') || str_starts_with($this->photo, 'https://')) {
                return $this->photo;
            }
            if (file_exists(public_path($this->photo))) {
                return asset($this->photo);
            }
        }
        $name = urlencode($this->name ?: 'IDA Leader');
        return "https://ui-avatars.com/api/?name={$name}&background=85c9cc&color=082d2f&size=512&font-size=0.36&bold=true";
    }

    public function getCategoryLabel()
    {
        return match ($this->display_category) {
            1 => 'Featured',
            2 => 'Standard',
            3 => 'Compact',
            4 => 'Mini',
            default => 'Unknown',
        };
    }
}
