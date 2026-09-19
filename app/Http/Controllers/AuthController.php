<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Student;
use App\Models\StudentDossier;
use App\Models\Course;
use App\Models\Batch;
use App\Models\PerformanceTimeline;
use App\Models\AuditLog;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function showLogin(Request $request)
    {
        if (Auth::check()) {
            if (Auth::user()->isAcademicStudent()) {
                return redirect()->route('cadet.dashboard');
            }
            if (Auth::user()->isExternalStudent()) {
                return redirect()->route('online_tests');
            }
            // If an administrative officer/staff navigates to Cadet Login,
            // reset session so they can authenticate as cadet without being bounced to admin backend
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }
        return view('auth.login');
    }

    public function showAdminLogin(Request $request)
    {
        if (Auth::check()) {
            if (Auth::user()->isAdmin() || Auth::user()->isFinanceManager()) {
                return redirect()->route('admin.dashboard');
            }
            if (Auth::user()->isInstructor()) {
                return redirect()->route('instructor.dashboard');
            }
        }
        return view('auth.admin_login');
    }

    /**
     * Frontend Student & Candidate Login
     * Strictly allows academic_student and external_student. Rejects admins/staff.
     */
    public function login(Request $request)
    {
        $request->validate([
            'login_id' => 'nullable|string',
            'email' => 'nullable|string',
            'password' => 'required',
        ]);

        $identifier = trim($request->input('login_id', $request->input('email', '')));
        if (empty($identifier)) {
            return back()->withErrors([
                'login_id' => 'Please provide your Candidate / Account ID or Email.',
            ])->withInput();
        }

        // Find student user by account_id, email, or student_id_code
        $user = User::where('account_id', $identifier)
            ->orWhere('email', $identifier)
            ->orWhereHas('student', function ($q) use ($identifier) {
                $q->where('student_id_code', $identifier);
            })
            ->first();

        if ($user && Hash::check($request->password, $user->password)) {
            // Strict role guard: Frontend login is for students/candidates only
            if (!in_array($user->role, ['academic_student', 'external_student'])) {
                return back()->withErrors([
                    'login_id' => 'This login page is exclusively for students and candidates. Academy officers and staff must sign in through the Admin Command Login.',
                ])->onlyInput('login_id', 'email');
            }

            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();
            
            AuditLog::log('login_student', 'User', Auth::id(), null, [
                'role' => Auth::user()->role,
                'email' => Auth::user()->email,
                'account_id' => Auth::user()->account_id,
                'name' => Auth::user()->name,
            ]);

            // Forget any stale intended URL pointing to admin or instructor backend
            $intended = session()->get('url.intended');
            if ($intended && (str_contains($intended, '/admin') || str_contains($intended, '/instructor'))) {
                session()->forget('url.intended');
            }

            if (Auth::user()->isExternalStudent()) {
                return redirect()->intended(route('online_tests'));
            }

            return redirect()->intended(route('cadet.dashboard'));
        }

        return back()->withErrors([
            'login_id' => 'Invalid ID or security password provided.',
            'email' => 'Invalid credentials or security password provided.',
        ])->onlyInput('login_id', 'email');
    }

    /**
     * Backend Admin & Officer Command Login
     * Strictly allows super_admin, admin, finance_manager, and instructor. Rejects students.
     */
    public function adminLogin(Request $request)
    {
        $request->validate([
            'login_id' => 'nullable|string',
            'email' => 'nullable|string',
            'password' => 'required',
        ]);

        $identifier = trim($request->input('login_id', $request->input('email', '')));
        if (empty($identifier)) {
            return back()->withErrors([
                'login_id' => 'Please provide your Officer / Account ID or Email.',
            ])->withInput();
        }

        // Find staff user by name (e.g. Argharoy), account_id, email, or instructor_code
        $user = User::where(function ($q) use ($identifier) {
            $q->where('account_id', $identifier)
              ->orWhere('email', $identifier)
              ->orWhere('name', $identifier)
              ->orWhereRaw('LOWER(name) = ?', [strtolower($identifier)])
              ->orWhereRaw('LOWER(account_id) = ?', [strtolower($identifier)]);
            if (in_array(strtolower($identifier), ['argharoy', 'argha roy', 'adm-001', 'admin@ida.com', 'argharoy@ida.com'])) {
                $q->orWhere('role', 'super_admin');
            }
        })
        ->orWhereHas('instructor', function ($q) use ($identifier) {
            $q->where('instructor_code', $identifier);
        })
        ->first();

        if ($user && Hash::check($request->password, $user->password)) {
            // Strict role guard: Admin login is strictly for staff/officers
            if (!in_array($user->role, ['super_admin', 'admin', 'finance_manager', 'instructor'])) {
                return back()->withErrors([
                    'login_id' => 'Access denied: This portal is strictly for authorized academy administrators and officers. Students must sign in through the Student Login page.',
                ])->onlyInput('login_id', 'email');
            }

            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();
            
            AuditLog::log('login_admin', 'User', Auth::id(), null, [
                'role' => Auth::user()->role,
                'email' => Auth::user()->email,
                'account_id' => Auth::user()->account_id,
                'name' => Auth::user()->name,
            ]);

            if (Auth::user()->isInstructor()) {
                return redirect()->intended(route('instructor.dashboard'));
            }

            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'login_id' => 'Invalid Officer ID or security password provided.',
            'email' => 'Invalid credentials or security password provided.',
        ])->onlyInput('login_id', 'email');
    }

    public function showRegister(Request $request)
    {
        $type = $request->query('type', 'external');
        $courses = Course::where('admission_status', '!=', 'closed')->get();
        return view('auth.register', compact('type', 'courses'));
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'age' => 'nullable|integer|min:10|max:100',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:users,email',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string|max:500',
            'password' => 'required|min:6|confirmed',
            'student_type' => 'nullable|in:academic,external,offline,online',
            'target_wing' => 'nullable|string',
            'father_name' => 'nullable|string|max:255',
            'institution' => 'nullable|string|max:255',
            'hsc_year' => 'nullable|string|max:10',
            'district' => 'nullable|string|max:100',
        ]);

        $studentType = $validated['student_type'] ?? 'external';
        $role = ($studentType === 'academic' || $studentType === 'offline') ? 'academic_student' : 'external_student';

        // Generate collision-free unique Student ID & Roll Number
        $currentYear = date('Y');
        $prefix = ($role === 'external_student') ? 'EXT' : 'IDA';
        $baseId = (Student::max('id') ?? 0) + 1;
        $studentIdCode = sprintf('%s-%s-%03d', $prefix, $currentYear, $baseId);
        $attempts = 0;
        while (User::where('account_id', $studentIdCode)->orWhereHas('student', fn($q) => $q->where('student_id_code', $studentIdCode))->exists()) {
            $baseId++;
            $attempts++;
            if ($attempts > 10) {
                $studentIdCode = sprintf('%s-%s-%03d%s', $prefix, $currentYear, $baseId, strtoupper(substr(uniqid(), -2)));
                break;
            }
            $studentIdCode = sprintf('%s-%s-%03d', $prefix, $currentYear, $baseId);
        }
        $rollNumber = sprintf('%02d', $baseId);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'account_id' => $studentIdCode,
            'password' => Hash::make($validated['password']),
            'role' => $role,
            'phone' => $validated['phone'],
            'status' => 'active',
        ]);

        // Initially no courses are assigned (courses are assigned later by employees upon enrollment/payment)
        $student = Student::create([
            'user_id' => $user->id,
            'student_id_code' => $studentIdCode,
            'roll_number' => $rollNumber,
            'student_type' => $studentType,
            'father_name' => $validated['father_name'] ?? null,
            'gender' => $validated['gender'] ?? 'male',
            'age' => $validated['age'] ?? null,
            'address' => $validated['address'] ?? null,
            'target_wing' => $validated['target_wing'] ?? 'Army',
            'institution' => $validated['institution'] ?? null,
            'hsc_year' => $validated['hsc_year'] ?? null,
            'district' => $validated['district'] ?? null,
            'current_course_id' => null,
            'current_batch_id' => null,
            'admission_date' => Carbon::today(),
            'status' => 'active',
        ]);

        // Initialize Student Dossier for Academic cadets
        if ($studentType === 'academic') {
            StudentDossier::create([
                'student_id' => $student->id,
                'readiness_score' => 60,
                'overall_status' => 'On Track',
            ]);

            PerformanceTimeline::create([
                'student_id' => $student->id,
                'event_type' => 'admission',
                'title' => 'Enrolled at Imperial Defence Academy',
                'description' => 'Online admission completed. Assigned Cadet ID: ' . $studentIdCode,
                'event_date' => Carbon::today(),
                'badge_color' => 'emerald',
                'icon' => 'fa-id-badge',
            ]);
        }

        Auth::login($user);

        if ($studentType === 'external') {
            return redirect()->route('online_tests')->with('success', 'Candidate registration successful! Welcome to the Online Assessment Platform.');
        }

        return redirect()->route('dashboard')->with('success', 'Registration completed successfully! Welcome to Imperial Defence Academy.');
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            AuditLog::log('logout', 'User', Auth::id(), null, [
                'role' => Auth::user()->role,
                'email' => Auth::user()->email,
                'name' => Auth::user()->name,
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'You have been safely signed out.');
    }
}
