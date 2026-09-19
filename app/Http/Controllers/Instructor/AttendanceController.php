<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Instructor;
use App\Models\Batch;
use App\Models\Student;
use App\Models\Routine;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $instructor = Instructor::where('user_id', auth()->id())->first();
        $batches = $instructor ? $instructor->batches : collect([]);

        $selectedBatchId = $request->get('batch_id', $batches->first()->id ?? null);
        $selectedDate = $request->get('date', Carbon::today()->format('Y-m-d'));

        $routines = Routine::where('batch_id', $selectedBatchId)
            ->whereDate('class_date', $selectedDate)
            ->get();

        $students = [];
        if ($selectedBatchId) {
            $students = Student::with('user')
                ->where('current_batch_id', $selectedBatchId)
                ->where('status', 'active')
                ->get();
        }

        $existingAttendance = Attendance::where('batch_id', $selectedBatchId)
            ->whereDate('date', $selectedDate)
            ->get()
            ->keyBy('student_id');

        return view('instructor.attendance.index', compact('batches', 'selectedBatchId', 'selectedDate', 'routines', 'students', 'existingAttendance'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'batch_id' => 'required|exists:batches,id',
            'date' => 'required|date',
            'routine_id' => 'nullable|exists:routines,id',
            'attendance' => 'required|array',
            'attendance.*.status' => 'required|in:present,absent,late,excused',
            'attendance.*.remarks' => 'nullable|string',
        ]);

        $routineId = $validated['routine_id'] ?? null;
        if (!$routineId) {
            $batch = Batch::findOrFail($validated['batch_id']);
            $routine = Routine::firstOrCreate(
                [
                    'batch_id' => $batch->id,
                    'class_date' => $validated['date'],
                ],
                [
                    'course_id' => $batch->course_id,
                    'subject' => 'Instructor Training Session',
                    'start_time' => '09:30:00',
                    'end_time' => '11:00:00',
                    'status' => 'completed',
                ]
            );
            $routineId = $routine->id;
        }

        foreach ($validated['attendance'] as $studentId => $data) {
            Attendance::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'batch_id' => $validated['batch_id'],
                    'date' => $validated['date'],
                ],
                [
                    'routine_id' => $routineId,
                    'status' => $data['status'],
                    'remarks' => $data['remarks'] ?? null,
                    'marked_by' => auth()->id(),
                ]
            );
        }

        return back()->with('success', 'Cadet muster attendance recorded successfully.');
    }
}
