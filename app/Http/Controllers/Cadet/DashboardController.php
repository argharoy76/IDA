<?php

namespace App\Http\Controllers\Cadet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Routine;
use App\Models\Invoice;
use App\Models\ExamAttempt;
use App\Models\ImprovementPlan;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $student = Student::with([
            'currentCourse',
            'currentBatch.primaryInstructor.user',
            'dossier',
            'weaknesses',
            'strengths',
            'timelines' => function ($q) {
                $q->take(5);
            }
        ])->where('user_id', $user->id)->first();

        // Graceful preview fallback for administrators inspecting the cadet portal
        if (!$student && in_array($user->role, ['super_admin', 'admin', 'instructor'])) {
            $student = Student::with([
                'currentCourse',
                'currentBatch.primaryInstructor.user',
                'dossier',
                'weaknesses',
                'strengths',
                'timelines' => function ($q) {
                    $q->take(5);
                }
            ])->first();
        }

        if (!$student) {
            $student = Student::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'student_id_code' => 'IDA-CADET-' . str_pad((string)$user->id, 4, '0', STR_PAD_LEFT),
                    'admission_date' => now()->toDateString(),
                    'status' => 'active',
                    'target_wing' => 'General',
                ]
            );
        }

        $todayClasses = Routine::with(['instructor.user'])
            ->where('batch_id', $student->current_batch_id)
            ->whereDate('class_date', Carbon::today())
            ->orderBy('start_time')
            ->get();

        $upcomingClasses = Routine::with(['instructor.user'])
            ->where('batch_id', $student->current_batch_id)
            ->whereDate('class_date', '>', Carbon::today())
            ->orderBy('class_date')
            ->orderBy('start_time')
            ->take(3)
            ->get();

        $pendingInvoices = Invoice::where('student_id', $student->id)
            ->whereIn('status', ['pending', 'partially_paid', 'overdue'])
            ->get();

        $activeImprovementPlan = ImprovementPlan::with('responsibleInstructor.user')
            ->where('student_id', $student->id)
            ->where('status', 'in_progress')
            ->latest()
            ->first();

        $recentExamAttempts = ExamAttempt::with('exam')
            ->where('student_id', $student->id)
            ->orderBy('completed_at', 'desc')
            ->take(3)
            ->get();

        return view('cadet.dashboard.index', compact(
            'student',
            'todayClasses',
            'upcomingClasses',
            'pendingInvoices',
            'activeImprovementPlan',
            'recentExamAttempts'
        ));
    }

    public function updateDetails(Request $request)
    {
        $user = auth()->user();
        $student = Student::where('user_id', $user->id)->first();
        if (!$student && in_array($user->role, ['super_admin', 'admin', 'instructor'])) {
            $student = Student::first();
        }

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'phone' => 'nullable|string|max:25',
            'email' => 'required|email|max:150|unique:users,email,' . $user->id,
            'institution' => 'nullable|string|max:150',
            'hsc_year' => 'nullable|string|max:10',
            'district' => 'nullable|string|max:100',
            'target_wing' => 'nullable|string|in:Army,Navy,Air Force,General',
            'password' => 'nullable|string|min:6',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        if (!empty($validated['phone'])) {
            $user->phone = $validated['phone'];
        }
        if (!empty($validated['password'])) {
            $user->password = bcrypt($validated['password']);
        }
        $user->save();

        if ($student) {
            $student->update([
                'institution' => $validated['institution'] ?? $student->institution,
                'hsc_year' => $validated['hsc_year'] ?? $student->hsc_year,
                'district' => $validated['district'] ?? $student->district,
                'target_wing' => $validated['target_wing'] ?? $student->target_wing,
            ]);
        }

        return back()->with('success', 'Your candidate profile details have been updated successfully.');
    }
}
