<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Instructor;
use App\Models\Batch;
use App\Models\Student;
use App\Models\Routine;
use App\Models\InstructorObservation;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $instructor = Instructor::with(['batches', 'user'])->where('user_id', $user->id)->first();

        $assignedBatchIds = $instructor ? $instructor->batches->pluck('id') : collect([]);

        $assignedStudents = Student::with(['user', 'currentBatch', 'dossier', 'weaknesses'])
            ->whereIn('current_batch_id', $assignedBatchIds)
            ->where('status', 'active')
            ->get();

        $studentsRequiringAttention = $assignedStudents->filter(function ($s) {
            return $s->requires_attention;
        });

        $todayClasses = Routine::with(['course', 'batch'])
            ->where('instructor_id', $instructor->id ?? 0)
            ->whereDate('class_date', Carbon::today())
            ->orderBy('start_time')
            ->get();

        $recentObservations = InstructorObservation::with('student.user')
            ->where('instructor_id', $instructor->id ?? 0)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('instructor.dashboard', compact(
            'instructor',
            'assignedStudents',
            'studentsRequiringAttention',
            'todayClasses',
            'recentObservations'
        ));
    }
}
