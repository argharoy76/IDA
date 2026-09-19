<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class FinancialTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_number',
        'type',
        'category_id',
        'amount',
        'transaction_date',
        'source_payee',
        'payment_method',
        'reference_no',
        'description',
        'payment_id',
        'receipt_attachment',
        'recorded_by',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(FinanceCategory::class, 'category_id');
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    // Calculated Cash Balance Methods (Strictly derived, never hard-coded)
    public static function getTotalInflow(): float
    {
        return (float) static::where('type', 'inflow')->sum('amount');
    }

    public static function getTotalOutflow(): float
    {
        return (float) static::where('type', 'outflow')->sum('amount');
    }

    public static function getCurrentBalance(float $openingBalance = 0.0): float
    {
        return $openingBalance + static::getTotalInflow() - static::getTotalOutflow();
    }

    public static function getTodayInflow(): float
    {
        return (float) static::where('type', 'inflow')
            ->whereDate('transaction_date', Carbon::today())
            ->sum('amount');
    }

    public static function getTodayOutflow(): float
    {
        return (float) static::where('type', 'outflow')
            ->whereDate('transaction_date', Carbon::today())
            ->sum('amount');
    }

    public static function getMonthlyInflow(int $month = null, int $year = null): float
    {
        $month = $month ?? Carbon::now()->month;
        $year = $year ?? Carbon::now()->year;
        return (float) static::where('type', 'inflow')
            ->whereMonth('transaction_date', $month)
            ->whereYear('transaction_date', $year)
            ->sum('amount');
    }

    public static function getMonthlyOutflow(int $month = null, int $year = null): float
    {
        $month = $month ?? Carbon::now()->month;
        $year = $year ?? Carbon::now()->year;
        return (float) static::where('type', 'outflow')
            ->whereMonth('transaction_date', $month)
            ->whereYear('transaction_date', $year)
            ->sum('amount');
    }
}
