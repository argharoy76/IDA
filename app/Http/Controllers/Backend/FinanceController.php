<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FinancialTransaction;
use App\Models\FinanceCategory;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\AuditLog;
use Carbon\Carbon;

class FinanceController extends Controller
{
    public function index(Request $request)
    {
        $query = FinancialTransaction::with(['category', 'payment', 'recorder']);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('transaction_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('transaction_date', '<=', $request->to_date);
        }

        $transactions = $query->orderBy('transaction_date', 'desc')->orderBy('id', 'desc')->paginate(15);
        $categories = FinanceCategory::all();

        // Derived Cash Balance Metrics
        $totalInflow = FinancialTransaction::getTotalInflow();
        $totalOutflow = FinancialTransaction::getTotalOutflow();
        $currentBalance = FinancialTransaction::getCurrentBalance();
        $todayIncome = FinancialTransaction::getTodayInflow();
        $todayExpense = FinancialTransaction::getTodayOutflow();
        $monthlyIncome = FinancialTransaction::getMonthlyInflow();
        $monthlyExpense = FinancialTransaction::getMonthlyOutflow();
        $outstandingFees = (float) Invoice::whereIn('status', ['pending', 'partially_paid', 'overdue'])->sum('due_amount');
        $pendingPayments = (float) Payment::where('verification_status', 'pending')->sum('amount');

        return view('backend.finance.index', compact(
            'transactions',
            'categories',
            'totalInflow',
            'totalOutflow',
            'currentBalance',
            'todayIncome',
            'todayExpense',
            'monthlyIncome',
            'monthlyExpense',
            'outstandingFees',
            'pendingPayments'
        ));
    }

    public function storeTransaction(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:inflow,outflow',
            'category_id' => 'required|exists:finance_categories,id',
            'amount' => 'required|numeric|min:1',
            'transaction_date' => 'required|date',
            'source_payee' => 'required|string|max:255',
            'payment_method' => 'required|string',
            'reference_no' => 'nullable|string|max:100',
            'description' => 'required|string|min:5',
        ]);

        $txnCount = FinancialTransaction::count() + 1;
        $txnNumber = sprintf('TXN-%s-%04d', date('Y'), $txnCount);

        $txn = FinancialTransaction::create(array_merge($validated, [
            'transaction_number' => $txnNumber,
            'recorded_by' => auth()->id(),
        ]));

        AuditLog::log('created', 'FinancialTransaction', $txn->id, null, [
            'type' => $validated['type'],
            'amount' => $validated['amount'],
            'source_payee' => $validated['source_payee'],
        ]);

        return back()->with('success', "Financial {$validated['type']} of ৳" . number_format($validated['amount'], 2) . " recorded in ledger. Current cash balance recalculated.");
    }

    public function reports(Request $request)
    {
        $year = $request->get('year', Carbon::now()->year);
        $month = $request->get('month', Carbon::now()->month);

        $monthlyInflow = FinancialTransaction::getMonthlyInflow($month, $year);
        $monthlyOutflow = FinancialTransaction::getMonthlyOutflow($month, $year);
        $netCashFlow = $monthlyInflow - $monthlyOutflow;

        $inflowByCategory = FinancialTransaction::where('type', 'inflow')
            ->whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $month)
            ->selectRaw('category_id, sum(amount) as total')
            ->groupBy('category_id')
            ->with('category')
            ->get();

        $outflowByCategory = FinancialTransaction::where('type', 'outflow')
            ->whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $month)
            ->selectRaw('category_id, sum(amount) as total')
            ->groupBy('category_id')
            ->with('category')
            ->get();

        return view('backend.finance.reports', compact('year', 'month', 'monthlyInflow', 'monthlyOutflow', 'netCashFlow', 'inflowByCategory', 'outflowByCategory'));
    }
}
