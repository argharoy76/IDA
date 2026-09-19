<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WatWord extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'word',
        'display_seconds',
        'order_seq',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }
}
