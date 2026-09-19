<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\FeeType;
use App\Models\Student;
use App\Models\Batch;
use Carbon\Carbon;

class FeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with(['student.user', 'feeType', 'payments']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('student', function ($sq) use ($search) {
                      $sq->where('student_id_code', 'like', "%{$search}%")
                         ->orWhereHas('user', function ($uq) use ($search) {
                             $uq->where('name', 'like', "%{$search}%");
                         });
                  });
            });
        }

        $invoices = $query->orderBy('id', 'desc')->paginate(15);
        $feeTypes = FeeType::all();
        $totalOutstanding = Invoice::whereIn('status', ['pending', 'partially_paid', 'overdue'])->sum('due_amount');
        $totalCollected = Invoice::sum('paid_amount');

        return view('backend.fees.index', compact('invoices', 'feeTypes', 'totalOutstanding', 'totalCollected'));
    }

    public function createInvoice()
    {
        $students = Student::with('user')->where('status', 'active')->get();
        $feeTypes = FeeType::all();
        return view('backend.fees.create_invoice', compact('students', 'feeTypes'));
    }

    public function storeInvoice(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'fee_type_id' => 'required|exists:fee_types,id',
            'title' => 'required|string|max:255',
            'gross_amount' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'waiver_amount' => 'nullable|numeric|min:0',
            'due_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $gross = (float) $validated['gross_amount'];
        $discount = (float) ($validated['discount_amount'] ?? 0);
        $waiver = (float) ($validated['waiver_amount'] ?? 0);
        $net = max(0, $gross - $discount - $waiver);

        $count = Invoice::count() + 1;
        $invNumber = sprintf('INV-%s-%04d', date('Y'), $count);

        Invoice::create([
            'invoice_number' => $invNumber,
            'student_id' => $validated['student_id'],
            'fee_type_id' => $validated['fee_type_id'],
            'title' => $validated['title'],
            'gross_amount' => $gross,
            'discount_amount' => $discount,
            'waiver_amount' => $waiver,
            'net_amount' => $net,
            'paid_amount' => 0.00,
            'due_amount' => $net,
            'due_date' => $validated['due_date'],
            'status' => 'pending',
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('admin.fees.index')->with('success', "Invoice {$invNumber} generated successfully.");
    }
}
