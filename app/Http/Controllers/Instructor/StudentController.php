<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Instructor;
use App\Models\Student;
use App\Models\InstructorObservation;
use App\Models\StudentStrengthWeakness;
use App\Models\ImprovementPlan;
use App\Models\PerformanceTimeline;
use Carbon\Carbon;

class StudentController extends Controller
{
    public function index()
    {
        $instructor = Instructor::where('user_id', auth()->id())->first();
        $batchIds = $instructor ? $instructor->batches->pluck('id') : collect([]);

        $students = Student::with(['user', 'currentBatch', 'dossier'])
            ->whereIn('current_batch_id', $batchIds)
            ->paginate(15);

        return view('instructor.students.index', compact('students'));
    }

    public function show($id)
    {
        $instructor = Instructor::where('user_id', auth()->id())->firstOrFail();

        $student = Student::with([
            'user',
            'currentCourse',
            'currentBatch',
            'dossier',
            'strengths',
            'weaknesses',
            'observations' => function ($q) use ($instructor) {
                // Instructors can see their observations and student visible ones
                $q->where('instructor_id', $instructor->id ?? 0)
                  ->orWhere('visibility', '!=', 'admin_only');
            },
            'improvementPlans',
            'timelines',
            'attendances' => function ($q) {
                $q->orderBy('date', 'desc')->take(10);
            },
            'examAttempts.exam'
        ])->findOrFail($id);

        if (!$instructor->batches->pluck('id')->contains($student->current_batch_id)) {
            abort(403, 'Unauthorized access to cadet dossier. Cadet does not belong to your assigned batches.');
        }

        return view('instructor.students.show', compact('student', 'instructor'));
    }

    public function storeObservation(Request $request)
    {
        $instructor = Instructor::where('user_id', auth()->id())->firstOrFail();

        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'category' => 'required|string',
            'observation_text' => 'required|string|min:5',
            'rating' => 'required|integer|min:1|max:5',
            'recommended_action' => 'nullable|string',
            'follow_up_date' => 'nullable|date',
            'visibility' => 'required|in:student_visible,instructor_only,admin_only',
        ]);

        $student = Student::findOrFail($validated['student_id']);
        if (!$instructor->batches->pluck('id')->contains($student->current_batch_id)) {
            abort(403, 'Unauthorized. Cadet does not belong to your assigned batches.');
        }

        $obs = InstructorObservation::create([
            'student_id' => $validated['student_id'],
            'instructor_id' => $instructor->id,
            'observation_date' => Carbon::today(),
            'category' => $validated['category'],
            'observation_text' => $validated['observation_text'],
            'rating' => $validated['rating'],
            'recommended_action' => $validated['recommended_action'] ?? null,
            'follow_up_date' => $validated['follow_up_date'] ?? null,
            'visibility' => $validated['visibility'],
        ]);

        PerformanceTimeline::create([
            'student_id' => $validated['student_id'],
            'event_type' => 'observation',
            'title' => 'Observation by ' . auth()->user()->name,
            'description' => "{$validated['category']}: Rating {$validated['rating']}/5",
            'event_date' => Carbon::today(),
            'badge_color' => 'purple',
            'icon' => 'fa-eye',
            'source_id' => $obs->id,
        ]);

        return back()->with('success', 'Observation successfully recorded.');
    }

    public function storeStrengthWeakness(Request $request)
    {
        $instructor = Instructor::where('user_id', auth()->id())->firstOrFail();

        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'type' => 'required|in:strength,weakness',
            'category' => 'required|string',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,critical',
            'status' => 'required|in:identified,improving,improved,ongoing,resolved',
            'follow_up_date' => 'nullable|date',
        ]);

        $student = Student::findOrFail($validated['student_id']);
        if (!$instructor->batches->pluck('id')->contains($student->current_batch_id)) {
            abort(403, 'Unauthorized. Cadet does not belong to your assigned batches.');
        }

        StudentStrengthWeakness::create(array_merge($validated, [
            'identified_date' => Carbon::today(),
            'instructor_id' => $instructor->id,
        ]));

        return back()->with('success', ucfirst($validated['type']) . ' updated in cadet dossier.');
    }

    public function storeImprovementPlan(Request $request)
    {
        $instructor = Instructor::where('user_id', auth()->id())->firstOrFail();

        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'problem_description' => 'required|string|max:255',
            'target_objective' => 'required|string|max:255',
            'recommended_activity' => 'nullable|string',
            'assigned_task' => 'nullable|string',
            'deadline' => 'required|date',
            'progress_percentage' => 'nullable|integer|min:0|max:100',
            'status' => 'required|in:pending,in_progress,completed,deferred',
        ]);

        $student = Student::findOrFail($validated['student_id']);
        if (!$instructor->batches->pluck('id')->contains($student->current_batch_id)) {
            abort(403, 'Unauthorized. Cadet does not belong to your assigned batches.');
        }

        ImprovementPlan::create(array_merge($validated, [
            'responsible_instructor_id' => $instructor->id,
            'follow_up_date' => $validated['deadline'],
        ]));

        return back()->with('success', 'Target improvement task assigned to cadet.');
    }
}
