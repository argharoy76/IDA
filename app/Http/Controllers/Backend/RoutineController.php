<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Routine;
use App\Models\Course;
use App\Models\Batch;
use App\Models\Instructor;
use Carbon\Carbon;

class RoutineController extends Controller
{
    public function index(Request $request)
    {
        $query = Routine::with(['course', 'batch', 'instructor.user']);

        if ($request->filled('batch_id')) {
            $query->where('batch_id', $request->batch_id);
        }

        if ($request->filled('date')) {
            $query->whereDate('class_date', $request->date);
        }

        $routines = $query->orderBy('class_date', 'desc')->orderBy('start_time')->paginate(20);
        $courses = Course::all();
        $batches = Batch::where('status', 'active')->get();
        $instructors = Instructor::with('user')->where('status', 'active')->get();

        return view('backend.routines.index', compact('routines', 'courses', 'batches', 'instructors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'batch_id' => 'required|exists:batches,id',
            'instructor_id' => 'nullable|exists:instructors,id',
            'subject' => 'required|string|max:255',
            'topic' => 'nullable|string|max:255',
            'room' => 'required|string|max:100',
            'class_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'class_type' => 'required|in:theory,practical,physical,drill,mock_viva',
            'status' => 'required|in:scheduled,completed,cancelled,rescheduled',
            'notes' => 'nullable|string',
        ]);

        $dayOfWeek = Carbon::parse($validated['class_date'])->format('l');

        Routine::create(array_merge($validated, [
            'day_of_week' => $dayOfWeek,
        ]));

        return redirect()->route('admin.routines.index')->with('success', 'Class schedule entry added.');
    }

    public function update(Request $request, $id)
    {
        $routine = Routine::findOrFail($id);

        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'topic' => 'nullable|string|max:255',
            'room' => 'required|string|max:100',
            'class_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'status' => 'required|in:scheduled,completed,cancelled,rescheduled',
            'notes' => 'nullable|string',
        ]);

        $dayOfWeek = Carbon::parse($validated['class_date'])->format('l');

        $routine->update(array_merge($validated, [
            'day_of_week' => $dayOfWeek,
        ]));

        return redirect()->route('admin.routines.index')->with('success', 'Class schedule updated.');
    }

    public function destroy($id)
    {
        $routine = Routine::findOrFail($id);
        $routine->delete();
        return redirect()->route('admin.routines.index')->with('success', 'Class schedule entry removed.');
    }
}
