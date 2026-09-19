<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\InstructorObservation;
use App\Models\StudentStrengthWeakness;
use App\Models\ImprovementPlan;
use App\Models\PerformanceAssessment;
use App\Models\PerformanceTimeline;
use Carbon\Carbon;

class DossierController extends Controller
{
    public function storeObservation(Request $request, $studentId)
    {
        $student = Student::findOrFail($studentId);

        $validated = $request->validate([
            'instructor_id' => 'required|exists:instructors,id',
            'category' => 'required|string',
            'observation_text' => 'required|string|min:5',
            'rating' => 'required|integer|min:1|max:5',
            'recommended_action' => 'nullable|string',
            'follow_up_date' => 'nullable|date',
            'visibility' => 'required|in:student_visible,instructor_only,admin_only',
        ]);

        $observation = InstructorObservation::create([
            'student_id' => $student->id,
            'instructor_id' => $validated['instructor_id'],
            'observation_date' => Carbon::today(),
            'category' => $validated['category'],
            'observation_text' => $validated['observation_text'],
            'rating' => $validated['rating'],
            'recommended_action' => $validated['recommended_action'] ?? null,
            'follow_up_date' => $validated['follow_up_date'] ?? null,
            'visibility' => $validated['visibility'],
        ]);

        PerformanceTimeline::create([
            'student_id' => $student->id,
            'event_type' => 'observation',
            'title' => 'Instructor Observation: ' . $validated['category'],
            'description' => "Rating: {$validated['rating']}/5. " . substr($validated['observation_text'], 0, 120) . '...',
            'event_date' => Carbon::today(),
            'badge_color' => 'purple',
            'icon' => 'fa-eye',
            'source_id' => $observation->id,
        ]);

        return back()->with('success', 'Instructor observation recorded and added to cadet dossier.');
    }

    public function storeStrengthWeakness(Request $request, $studentId)
    {
        $student = Student::findOrFail($studentId);

        $validated = $request->validate([
            'type' => 'required|in:strength,weakness',
            'category' => 'required|string',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,critical',
            'status' => 'required|in:identified,improving,improved,ongoing,resolved',
            'instructor_id' => 'nullable|exists:instructors,id',
            'follow_up_date' => 'nullable|date',
        ]);

        $sw = StudentStrengthWeakness::create([
            'student_id' => $student->id,
            'type' => $validated['type'],
            'category' => $validated['category'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'priority' => $validated['priority'],
            'status' => $validated['status'],
            'identified_date' => Carbon::today(),
            'follow_up_date' => $validated['follow_up_date'] ?? null,
            'instructor_id' => $validated['instructor_id'] ?? null,
        ]);

        $color = $validated['type'] === 'strength' ? 'emerald' : 'amber';
        PerformanceTimeline::create([
            'student_id' => $student->id,
            'event_type' => $validated['type'] . '_recorded',
            'title' => ucfirst($validated['type']) . ': ' . $validated['title'],
            'description' => "Category: {$validated['category']} | Status: " . ucfirst($validated['status']),
            'event_date' => Carbon::today(),
            'badge_color' => $color,
            'icon' => $validated['type'] === 'strength' ? 'fa-shield-heart' : 'fa-triangle-exclamation',
            'source_id' => $sw->id,
        ]);

        return back()->with('success', ucfirst($validated['type']) . ' recorded in cadet dossier.');
    }

    public function storeImprovementPlan(Request $request, $studentId)
    {
        $student = Student::findOrFail($studentId);

        $validated = $request->validate([
            'strength_weakness_id' => 'nullable|exists:student_strengths_weaknesses,id',
            'problem_description' => 'required|string|max:255',
            'target_objective' => 'required|string|max:255',
            'recommended_activity' => 'nullable|string',
            'assigned_task' => 'nullable|string',
            'responsible_instructor_id' => 'nullable|exists:instructors,id',
            'deadline' => 'required|date',
            'progress_percentage' => 'nullable|integer|min:0|max:100',
            'status' => 'required|in:pending,in_progress,completed,deferred',
        ]);

        ImprovementPlan::create([
            'student_id' => $student->id,
            'strength_weakness_id' => $validated['strength_weakness_id'] ?? null,
            'problem_description' => $validated['problem_description'],
            'target_objective' => $validated['target_objective'],
            'recommended_activity' => $validated['recommended_activity'] ?? null,
            'assigned_task' => $validated['assigned_task'] ?? null,
            'responsible_instructor_id' => $validated['responsible_instructor_id'] ?? null,
            'deadline' => $validated['deadline'],
            'progress_percentage' => $validated['progress_percentage'] ?? 0,
            'status' => $validated['status'],
            'follow_up_date' => $validated['deadline'],
        ]);

        PerformanceTimeline::create([
            'student_id' => $student->id,
            'event_type' => 'improvement_plan',
            'title' => 'New Improvement Plan: ' . $validated['target_objective'],
            'description' => "Target: {$validated['problem_description']}. Deadline: {$validated['deadline']}",
            'event_date' => Carbon::today(),
            'badge_color' => 'blue',
            'icon' => 'fa-bullseye',
        ]);

        return back()->with('success', 'Actionable improvement plan assigned to cadet.');
    }
}
