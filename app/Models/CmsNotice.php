<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CmsNotice extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'category',
        'attachment',
        'is_urgent',
        'is_pinned',
        'is_published',
        'publish_date',
    ];

    protected $casts = [
        'publish_date' => 'date',
        'is_urgent' => 'boolean',
        'is_pinned' => 'boolean',
        'is_published' => 'boolean',
    ];
}
