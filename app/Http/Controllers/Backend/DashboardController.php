<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Course;
use App\Models\Batch;
use App\Models\Instructor;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\FinancialTransaction;
use App\Models\Routine;
use App\Models\ExamAttempt;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalStudents = Student::count();
        $academicStudents = Student::where('student_type', 'academic')->count();
        $externalStudents = Student::where('student_type', 'external')->count();
        $activeBatches = Batch::where('status', 'active')->count();
        $totalCourses = Course::count();
        $totalInstructors = Instructor::count();

        // Financial derived indicators
        $cashBalance = FinancialTransaction::getCurrentBalance();
        $totalInflow = FinancialTransaction::getTotalInflow();
        $totalOutflow = FinancialTransaction::getTotalOutflow();
        $todayIncome = FinancialTransaction::getTodayInflow();
        $todayExpense = FinancialTransaction::getTodayOutflow();
        $monthlyIncome = FinancialTransaction::getMonthlyInflow();
        $monthlyExpense = FinancialTransaction::getMonthlyOutflow();
        $outstandingFees = (float) Invoice::whereIn('status', ['pending', 'partially_paid', 'overdue'])->sum('due_amount');
        $pendingPaymentsCount = Payment::where('verification_status', 'pending')->count();

        // Early Warning System: Cadets requiring urgent attention
        $students = Student::with(['user', 'attendances', 'weaknesses', 'examAttempts', 'currentBatch'])
            ->where('student_type', 'academic')
            ->where('status', 'active')
            ->get();

        $studentsRequiringAttention = $students->filter(function ($student) {
            return $student->requires_attention;
        })->take(5);

        // Upcoming Today's Classes
        $todayClasses = Routine::with(['course', 'batch', 'instructor.user'])
            ->whereDate('class_date', Carbon::today())
            ->orderBy('start_time')
            ->get();

        // Recent Payments
        $recentPayments = Payment::with(['student.user', 'user', 'invoice'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Recent Financial Transactions
        $recentTransactions = FinancialTransaction::with('category')
            ->orderBy('transaction_date', 'desc')
            ->take(5)
            ->get();

        return view('backend.dashboard.index', compact(
            'totalStudents',
            'academicStudents',
            'externalStudents',
            'activeBatches',
            'totalCourses',
            'totalInstructors',
            'cashBalance',
            'totalInflow',
            'totalOutflow',
            'todayIncome',
            'todayExpense',
            'monthlyIncome',
            'monthlyExpense',
            'outstandingFees',
            'pendingPaymentsCount',
            'studentsRequiringAttention',
            'todayClasses',
            'recentPayments',
            'recentTransactions'
        ));
    }
}
