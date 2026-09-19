<?php

namespace App\Http\Controllers\Cadet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Routine;
use Carbon\Carbon;

class RoutineController extends Controller
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

        $todayDay = Carbon::today()->format('l');
        $todayClasses = Routine::with(['instructor.user'])
            ->where('batch_id', $student->current_batch_id)
            ->where(function ($q) use ($todayDay) {
                $q->whereDate('class_date', Carbon::today())
                  ->orWhere('day_of_week', $todayDay);
            })
            ->orderBy('start_time')
            ->get();

        $weekRoutines = Routine::with(['instructor.user'])
            ->where('batch_id', $student->current_batch_id)
            ->whereBetween('class_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->orderBy('start_time')
            ->get();

        if ($weekRoutines->isEmpty()) {
            $weekRoutines = Routine::with(['instructor.user'])
                ->where('batch_id', $student->current_batch_id)
                ->orderBy('start_time')
                ->take(10)
                ->get();
        }

        return view('cadet.routine.index', compact('student', 'todayClasses', 'weekRoutines'));
    }
}
