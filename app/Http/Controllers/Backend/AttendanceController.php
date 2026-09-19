<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Routine;
use App\Models\Batch;
use App\Models\Student;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $batches = Batch::where('status', 'active')->get();
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

        // Existing attendance map for today
        $existingAttendance = Attendance::where('batch_id', $selectedBatchId)
            ->whereDate('date', $selectedDate)
            ->get()
            ->keyBy('student_id');

        return view('backend.attendance.index', compact('batches', 'selectedBatchId', 'selectedDate', 'routines', 'students', 'existingAttendance'));
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
            // Find or create default daily routine entry
            $routine = Routine::where('batch_id', $validated['batch_id'])
                ->whereDate('class_date', $validated['date'])
                ->first();
            if (!$routine) {
                $batch = Batch::findOrFail($validated['batch_id']);
                $routine = Routine::create([
                    'course_id' => $batch->course_id,
                    'batch_id' => $batch->id,
                    'subject' => 'General Training / Muster Parade',
                    'class_date' => $validated['date'],
                    'start_time' => '09:00:00',
                    'end_time' => '10:00:00',
                    'status' => 'completed',
                ]);
            }
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

        return back()->with('success', 'Squad attendance recorded successfully! Student dossiers updated.');
    }
}
