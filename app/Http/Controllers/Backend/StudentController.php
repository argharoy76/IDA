<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Student;
use App\Models\User;
use App\Models\Course;
use App\Models\Batch;
use App\Models\BatchStudent;
use App\Models\StudentDossier;
use App\Models\PerformanceTimeline;
use App\Models\Instructor;
use Carbon\Carbon;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with(['user', 'currentCourse', 'currentBatch']);

        if ($request->filled('type')) {
            $query->where('student_type', $request->type);
        }

        if ($request->filled('wing')) {
            $query->where('target_wing', $request->wing);
        }

        if ($request->filled('batch_id')) {
            $query->where('current_batch_id', $request->batch_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('student_id_code', 'like', "%{$search}%")
                  ->orWhere('roll_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        $students = $query->orderBy('id', 'desc')->paginate(15);
        $batches = Batch::where('status', 'active')->get();

        return view('backend.students.index', compact('students', 'batches'));
    }

    public function create()
    {
        $courses = Course::where('admission_status', '!=', 'closed')->get();
        $batches = Batch::where('status', 'active')->get();
        return view('backend.students.create', compact('courses', 'batches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'password' => 'nullable|string|min:6',
            'student_type' => 'required|in:academic,external',
            'target_wing' => 'required|string',
            'course_id' => 'nullable|exists:courses,id',
            'batch_id' => 'nullable|exists:batches,id',
            'father_name' => 'nullable|string',
            'mother_name' => 'nullable|string',
            'gender' => 'required|in:male,female',
            'blood_group' => 'nullable|string|max:5',
            'dob' => 'nullable|date',
            'address' => 'nullable|string',
            'emergency_contact' => 'nullable|string',
        ]);

        $role = ($validated['student_type'] === 'external') ? 'external_student' : 'academic_student';

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password'] ?? 'password'),
            'role' => $role,
            'phone' => $validated['phone'],
            'status' => 'active',
        ]);

        // Auto-generate Unique Student ID & Roll Number
        $year = date('Y');
        $prefix = ($validated['student_type'] === 'external') ? 'EXT' : 'IDA';
        $count = Student::where('student_type', $validated['student_type'])->count() + 1;
        $studentIdCode = sprintf('%s-%s-%03d', $prefix, $year, $count);
        $rollNumber = sprintf('%02d', $count);

        $student = Student::create([
            'user_id' => $user->id,
            'student_id_code' => $studentIdCode,
            'roll_number' => $rollNumber,
            'student_type' => $validated['student_type'],
            'father_name' => $validated['father_name'] ?? null,
            'mother_name' => $validated['mother_name'] ?? null,
            'gender' => $validated['gender'],
            'blood_group' => $validated['blood_group'] ?? null,
            'dob' => $validated['dob'] ?? null,
            'address' => $validated['address'] ?? null,
            'emergency_contact' => $validated['emergency_contact'] ?? null,
            'target_wing' => $validated['target_wing'],
            'current_course_id' => $validated['course_id'] ?? null,
            'current_batch_id' => $validated['batch_id'] ?? null,
            'admission_date' => Carbon::today(),
            'status' => 'active',
        ]);

        if (!empty($validated['batch_id'])) {
            BatchStudent::create([
                'student_id' => $student->id,
                'batch_id' => $validated['batch_id'],
                'course_id' => $validated['course_id'] ?? null,
                'joined_at' => Carbon::today(),
                'status' => 'active',
            ]);
        }

        // Initialize Student Dossier
        StudentDossier::create([
            'student_id' => $student->id,
            'readiness_score' => 60,
            'overall_status' => 'On Track',
        ]);

        PerformanceTimeline::create([
            'student_id' => $student->id,
            'event_type' => 'admission',
            'title' => 'Enrolled at Imperial Defence Academy',
            'description' => 'Admitted into ' . ($student->currentCourse->title ?? 'Academy Program') . '. ID: ' . $studentIdCode,
            'event_date' => Carbon::today(),
            'badge_color' => 'emerald',
            'icon' => 'fa-id-badge',
        ]);

        return redirect()->route('admin.students.show', $student->id)->with('success', "Student {$student->user->name} enrolled successfully! Generated ID: {$studentIdCode}");
    }

    public function show($id)
    {
        $student = Student::with([
            'user',
            'currentCourse',
            'currentBatch.primaryInstructor.user',
            'dossier',
            'strengths',
            'weaknesses.improvementPlans',
            'observations.instructor.user',
            'performanceAssessments',
            'improvementPlans.responsibleInstructor.user',
            'timelines',
            'attendances' => function ($q) {
                $q->orderBy('date', 'desc')->take(10);
            },
            'invoices.payments',
            'payments.verifier',
            'examAttempts.exam',
            'batchHistory.batch'
        ])->findOrFail($id);

        $instructors = Instructor::with('user')->where('status', 'active')->get();
        $batches = Batch::where('status', 'active')->get();

        return view('backend.students.show', compact('student', 'instructors', 'batches'));
    }

    public function edit($id)
    {
        $student = Student::with('user')->findOrFail($id);
        $courses = Course::all();
        $batches = Batch::where('status', 'active')->get();
        return view('backend.students.edit', compact('student', 'courses', 'batches'));
    }

    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'target_wing' => 'required|string',
            'father_name' => 'nullable|string',
            'mother_name' => 'nullable|string',
            'blood_group' => 'nullable|string|max:5',
            'dob' => 'nullable|date',
            'address' => 'nullable|string',
            'emergency_contact' => 'nullable|string',
            'status' => 'required|in:active,inactive,archived',
        ]);

        $student->user->update([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
        ]);

        $student->update([
            'target_wing' => $validated['target_wing'],
            'father_name' => $validated['father_name'] ?? null,
            'mother_name' => $validated['mother_name'] ?? null,
            'blood_group' => $validated['blood_group'] ?? null,
            'dob' => $validated['dob'] ?? null,
            'address' => $validated['address'] ?? null,
            'emergency_contact' => $validated['emergency_contact'] ?? null,
            'status' => $validated['status'],
        ]);

        return redirect()->route('admin.students.show', $student->id)->with('success', 'Cadet profile updated successfully.');
    }

    public function transferBatch(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $validated = $request->validate([
            'new_batch_id' => 'required|exists:batches,id',
            'transfer_notes' => 'nullable|string',
        ]);

        if ($student->current_batch_id == $validated['new_batch_id']) {
            return back()->with('error', 'Cadet is already allocated to this batch.');
        }

        // Close old batch enrollment
        BatchStudent::where('student_id', $student->id)
            ->where('batch_id', $student->current_batch_id)
            ->where('status', 'active')
            ->update([
                'status' => 'transferred',
                'left_at' => Carbon::today(),
                'notes' => $validated['transfer_notes'] ?? 'Transferred by administration.',
            ]);

        $newBatch = Batch::findOrFail($validated['new_batch_id']);

        // Create new batch enrollment
        BatchStudent::create([
            'student_id' => $student->id,
            'batch_id' => $newBatch->id,
            'course_id' => $newBatch->course_id,
            'joined_at' => Carbon::today(),
            'status' => 'active',
            'notes' => $validated['transfer_notes'] ?? null,
        ]);

        // Update student pointer
        $student->update([
            'current_batch_id' => $newBatch->id,
            'current_course_id' => $newBatch->course_id,
        ]);

        // Add timeline milestone
        PerformanceTimeline::create([
            'student_id' => $student->id,
            'event_type' => 'milestone',
            'title' => 'Transferred to ' . $newBatch->batch_name,
            'description' => 'Cadet squad transferred to batch code ' . $newBatch->batch_code . '. ' . ($validated['transfer_notes'] ?? ''),
            'event_date' => Carbon::today(),
            'badge_color' => 'blue',
            'icon' => 'fa-arrows-rotate',
        ]);

        return redirect()->route('admin.students.show', $student->id)->with('success', "Cadet successfully transferred to {$newBatch->batch_name}.");
    }
}
