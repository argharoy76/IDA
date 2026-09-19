<?php

namespace App\Http\Controllers\Cadet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Attendance;

class AttendanceController extends Controller
{
    public function index()
    {
        $student = Student::with('currentBatch')->where('user_id', auth()->id())->first();
        if (!$student && in_array(auth()->user()->role, ['super_admin', 'admin', 'instructor'])) {
            $student = Student::with('currentBatch')->first();
        }
        if (!$student) {
            abort(404, 'No cadet student record found.');
        }

        $attendances = Attendance::with(['routine.instructor.user'])
            ->where('student_id', $student->id)
            ->orderBy('date', 'desc')
            ->paginate(20);

        $totalSessions = Attendance::where('student_id', $student->id)->count();
        $presentCount = Attendance::where('student_id', $student->id)->whereIn('status', ['present', 'late'])->count();
        $absentCount = Attendance::where('student_id', $student->id)->where('status', 'absent')->count();
        $lateCount = Attendance::where('student_id', $student->id)->where('status', 'late')->count();

        $percentage = $totalSessions > 0 ? round(($presentCount / $totalSessions) * 100, 1) : 100.0;

        return view('cadet.attendance.index', compact('student', 'attendances', 'totalSessions', 'presentCount', 'absentCount', 'lateCount', 'percentage'));
    }
}
