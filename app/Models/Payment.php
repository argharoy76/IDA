<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_number',
        'invoice_id',
        'student_id',
        'user_id',
        'amount',
        'payment_method',
        'transaction_reference',
        'payment_proof_file',
        'payment_date',
        'verification_status',
        'verified_by',
        'verified_at',
        'rejection_reason',
        'admin_notes',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'verified_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function transaction()
    {
        return $this->hasOne(FinancialTransaction::class);
    }
}
