<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\FinancialTransaction;
use App\Models\FinanceCategory;
use App\Models\AuditLog;
use App\Models\PerformanceTimeline;
use Carbon\Carbon;

class PaymentVerificationController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');
        $query = Payment::with(['student.user', 'user', 'invoice', 'verifier']);

        if ($status !== 'all') {
            $query->where('verification_status', $status);
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(15);
        $pendingCount = Payment::where('verification_status', 'pending')->count();
        $approvedCount = Payment::where('verification_status', 'approved')->count();

        return view('backend.payments.verification', compact('payments', 'status', 'pendingCount', 'approvedCount'));
    }

    public function approve(Request $request, $id)
    {
        $payment = Payment::with(['student.user', 'invoice'])->findOrFail($id);

        if ($payment->verification_status === 'approved') {
            return back()->with('error', 'Payment has already been verified and recorded.');
        }

        $adminNotes = $request->input('admin_notes', 'Verified with mobile financial service / bank statement.');

        // 1. Mark Payment as Approved
        $payment->update([
            'verification_status' => 'approved',
            'verified_by' => auth()->id(),
            'verified_at' => Carbon::now(),
            'admin_notes' => $adminNotes,
        ]);

        // 2. Update Invoice Amounts & Status
        if ($payment->invoice) {
            $invoice = $payment->invoice;
            $newPaid = (float) $invoice->paid_amount + (float) $payment->amount;
            $newDue = max(0, (float) $invoice->net_amount - $newPaid);
            $newStatus = ($newDue <= 0.01) ? 'paid' : 'partially_paid';

            $invoice->update([
                'paid_amount' => $newPaid,
                'due_amount' => $newDue,
                'status' => $newStatus,
            ]);
        }

        // 3. Automatically record Verified Inflow in Financial Ledger
        $category = FinanceCategory::firstOrCreate(
            ['name' => 'Student Course & Assessment Fees', 'type' => 'inflow'],
            ['description' => 'Verified student fees and external examination receipts']
        );

        $txnCount = FinancialTransaction::count() + 1;
        $txnNumber = sprintf('TXN-%s-%04d', date('Y'), $txnCount);
        $payeeName = $payment->student->user->name ?? ($payment->user->name ?? 'Student / Cadet');

        FinancialTransaction::create([
            'transaction_number' => $txnNumber,
            'type' => 'inflow',
            'category_id' => $category->id,
            'amount' => $payment->amount,
            'transaction_date' => Carbon::today(),
            'source_payee' => $payeeName . ' (' . ($payment->student->student_id_code ?? 'EXT') . ')',
            'payment_method' => $payment->payment_method,
            'reference_no' => $payment->transaction_reference,
            'description' => "Verified fee payment ({$payment->payment_number}) for " . ($payment->invoice->title ?? 'Academy fees'),
            'payment_id' => $payment->id,
            'recorded_by' => auth()->id(),
        ]);

        // 4. Audit Log & Timeline
        AuditLog::log('approved', 'Payment', $payment->id, null, ['amount' => $payment->amount, 'verified_by' => auth()->user()->name]);

        if ($payment->student) {
            PerformanceTimeline::create([
                'student_id' => $payment->student->id,
                'event_type' => 'payment_verified',
                'title' => 'Payment Verified: ৳' . number_format($payment->amount, 2),
                'description' => "Transaction ref: {$payment->transaction_reference} approved by finance. Invoice updated.",
                'event_date' => Carbon::today(),
                'badge_color' => 'emerald',
                'icon' => 'fa-receipt',
            ]);
        }

        return back()->with('success', "Payment {$payment->payment_number} approved! ৳{$payment->amount} added to verified Cash Ledger.");
    }

    public function reject(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);

        $validated = $request->validate([
            'rejection_reason' => 'required|string|min:5',
        ]);

        $payment->update([
            'verification_status' => 'rejected',
            'verified_by' => auth()->id(),
            'verified_at' => Carbon::now(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        AuditLog::log('rejected', 'Payment', $payment->id, null, ['reason' => $validated['rejection_reason']]);

        return back()->with('success', "Payment {$payment->payment_number} marked as rejected. Cadet will be notified.");
    }
}
