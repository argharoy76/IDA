<?php

namespace App\Http\Controllers\Cadet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Invoice;
use App\Models\Payment;
use Carbon\Carbon;

class FeeController extends Controller
{
    public function index()
    {
        $student = Student::where('user_id', auth()->id())->first();
        if (!$student && in_array(auth()->user()->role, ['super_admin', 'admin', 'instructor'])) {
            $student = Student::first();
        }
        if (!$student) {
            abort(404, 'No cadet student record found.');
        }

        $invoices = Invoice::with(['feeType', 'payments'])
            ->where('student_id', $student->id)
            ->orderBy('due_date', 'desc')
            ->get();

        $payments = Payment::with('invoice')
            ->where('student_id', $student->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $totalDue = $invoices->whereIn('status', ['pending', 'partially_paid', 'overdue'])->sum('due_amount');
        $totalPaid = $invoices->sum('paid_amount');

        return view('cadet.fees.index', compact('student', 'invoices', 'payments', 'totalDue', 'totalPaid'));
    }

    public function submitPayment(Request $request)
    {
        $student = Student::where('user_id', auth()->id())->firstOrFail();

        $validated = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'amount' => 'required|numeric|min:10',
            'payment_method' => 'required|in:bkash,nagad,rocket,bank_transfer,cash',
            'transaction_reference' => 'required|string|max:100',
            'payment_proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
        ]);

        $invoice = Invoice::where('id', $validated['invoice_id'])->where('student_id', $student->id)->firstOrFail();

        $proofFileName = null;
        if ($request->hasFile('payment_proof')) {
            $proofFileName = time() . '_' . $request->file('payment_proof')->getClientOriginalName();
            $request->file('payment_proof')->move(public_path('uploads/payments'), $proofFileName);
        }

        $count = Payment::count() + 1;
        $payNumber = sprintf('PAY-%s-%04d', date('Y'), $count);

        Payment::create([
            'payment_number' => $payNumber,
            'invoice_id' => $invoice->id,
            'student_id' => $student->id,
            'user_id' => auth()->id(),
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'transaction_reference' => $validated['transaction_reference'],
            'payment_proof_file' => $proofFileName,
            'payment_date' => Carbon::today(),
            'verification_status' => 'pending',
        ]);

        return back()->with('success', "Payment {$payNumber} submitted successfully! The academy finance officer will verify your transaction shortly.");
    }
}
