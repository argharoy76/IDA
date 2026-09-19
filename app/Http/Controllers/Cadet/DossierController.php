<?php

namespace App\Http\Controllers\Cadet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;

class DossierController extends Controller
{
    public function index()
    {
        $student = Student::with([
            'user',
            'currentCourse',
            'currentBatch.primaryInstructor.user',
            'dossier',
            'strengths',
            'weaknesses',
            'observations' => function ($q) {
                $q->where('visibility', 'student_visible')->orderBy('observation_date', 'desc');
            },
            'performanceAssessments',
            'improvementPlans' => function ($q) {
                $q->orderBy('deadline', 'asc');
            },
            'timelines' => function ($q) {
                $q->orderBy('event_date', 'desc');
            }
        ])->where('user_id', auth()->id())->first();

        if (!$student && in_array(auth()->user()->role, ['super_admin', 'admin', 'instructor'])) {
            $student = Student::with([
                'user',
                'currentCourse',
                'currentBatch.primaryInstructor.user',
                'dossier',
                'strengths',
                'weaknesses',
                'observations' => function ($q) {
                    $q->where('visibility', 'student_visible')->orderBy('observation_date', 'desc');
                },
                'performanceAssessments',
                'improvementPlans' => function ($q) {
                    $q->orderBy('deadline', 'asc');
                },
                'timelines' => function ($q) {
                    $q->orderBy('event_date', 'desc');
                }
            ])->first();
        }

        if (!$student) {
            abort(404, 'No cadet student record found.');
        }

        return view('cadet.dossier.index', compact('student'));
    }
}
