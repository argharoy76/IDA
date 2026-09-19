<?php

namespace App\Http\Controllers\External;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\Payment;
use App\Models\Student;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $student = Student::where('user_id', $user->id)->first();

        $availableTests = Exam::where('is_public_for_external', true)->where('status', 'open')->get();

        $myAttempts = ExamAttempt::with('exam')
            ->where('user_id', $user->id)
            ->orderBy('id', 'desc')
            ->get();

        $myPayments = Payment::where('user_id', $user->id)
            ->orderBy('id', 'desc')
            ->get();

        return view('external.dashboard', compact('user', 'student', 'availableTests', 'myAttempts', 'myPayments'));
    }
}
