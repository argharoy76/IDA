<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Batch;
use App\Models\Course;
use App\Models\Instructor;

class BatchController extends Controller
{
    public function index()
    {
        $batches = Batch::with(['course', 'primaryInstructor.user'])->withCount('students')->get();
        $courses = Course::all();
        $instructors = Instructor::with('user')->where('status', 'active')->get();
        return view('backend.batches.index', compact('batches', 'courses', 'instructors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'batch_name' => 'required|string|max:255',
            'batch_code' => 'required|string|unique:batches,batch_code',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'primary_instructor_id' => 'nullable|exists:instructors,id',
            'max_students' => 'required|integer|min:5|max:100',
            'schedule_summary' => 'nullable|string',
            'status' => 'required|in:upcoming,active,completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        Batch::create($validated);

        return redirect()->route('admin.batches.index')->with('success', 'Squad / Batch created successfully.');
    }

    public function update(Request $request, $id)
    {
        $batch = Batch::findOrFail($id);

        $validated = $request->validate([
            'batch_name' => 'required|string|max:255',
            'primary_instructor_id' => 'nullable|exists:instructors,id',
            'max_students' => 'required|integer|min:5',
            'schedule_summary' => 'nullable|string',
            'status' => 'required|in:upcoming,active,completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        $batch->update($validated);

        return redirect()->route('admin.batches.index')->with('success', 'Squad / Batch updated successfully.');
    }
}
