<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Student;
use App\Models\Instructor;
use App\Models\AuditLog;
use Carbon\Carbon;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['student', 'instructor']);

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('account_id', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhereHas('student', function ($sq) use ($search) {
                      $sq->where('student_id_code', 'like', "%{$search}%");
                  })
                  ->orWhereHas('instructor', function ($iq) use ($search) {
                      $iq->where('instructor_code', 'like', "%{$search}%");
                  });
            });
        }

        $users = $query->orderBy('id', 'desc')->paginate(20);

        return view('backend.accounts.index', compact('users'));
    }

    public function updateId(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'account_id' => 'required|string|max:50|unique:users,account_id,' . $user->id,
        ]);

        $oldId = $user->account_id;
        $newId = strtoupper(trim($validated['account_id']));

        $user->update(['account_id' => $newId]);

        // Sync with linked Student record if any
        if ($user->student) {
            $user->student->update(['student_id_code' => $newId]);
        }

        // Sync with linked Instructor record if any
        if ($user->instructor) {
            $user->instructor->update(['instructor_code' => $newId]);
        }

        AuditLog::log('update_account_id', 'User', $user->id, null, [
            'old_id' => $oldId,
            'new_id' => $newId,
            'updated_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', "Account ID for {$user->name} successfully changed to '{$newId}'.");
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:super_admin,admin,finance_manager,instructor,academic_student,external_student',
            'account_id' => 'nullable|string|max:50|unique:users,account_id',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:6',
            'status' => 'required|in:active,inactive',
        ]);

        $role = $validated['role'];
        $accountId = !empty($validated['account_id']) ? strtoupper(trim($validated['account_id'])) : null;

        if (empty($accountId)) {
            $prefix = match($role) {
                'super_admin', 'admin' => 'ADM',
                'finance_manager' => 'FIN',
                'instructor' => 'INS',
                'academic_student' => 'IDA',
                'external_student' => 'EXT',
                default => 'USR',
            };
            $count = User::where('role', $role)->count() + 1;
            $accountId = sprintf('%s-%s-%03d', $prefix, date('Y'), $count);

            // Ensure uniqueness
            $candidateId = $accountId;
            $suffix = 1;
            while (User::where('account_id', $candidateId)->exists()) {
                $candidateId = $accountId . '-' . $suffix;
                $suffix++;
            }
            $accountId = $candidateId;
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'account_id' => $accountId,
            'password' => Hash::make($validated['password']),
            'role' => $role,
            'phone' => $validated['phone'] ?? null,
            'status' => $validated['status'],
        ]);

        // If academic or external student, create linked Student profile
        if (in_array($role, ['academic_student', 'external_student'])) {
            $studentType = ($role === 'academic_student') ? 'academic' : 'external';
            Student::create([
                'user_id' => $user->id,
                'student_id_code' => $accountId,
                'roll_number' => sprintf('%02d', Student::where('student_type', $studentType)->count() + 1),
                'student_type' => $studentType,
                'admission_date' => Carbon::today(),
                'status' => 'active',
            ]);
        } elseif ($role === 'instructor') {
            Instructor::create([
                'user_id' => $user->id,
                'instructor_code' => $accountId,
                'designation' => 'Staff Instructor',
                'specialization' => 'Defence Training',
                'phone' => $validated['phone'] ?? null,
                'status' => 'active',
            ]);
        }

        AuditLog::log('create_account', 'User', $user->id, null, [
            'account_id' => $accountId,
            'role' => $role,
            'created_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', "Account '{$user->name}' created successfully with ID: {$accountId}.");
    }

    public function updatePassword(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        AuditLog::log('reset_password', 'User', $user->id, null, [
            'reset_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', "Password for {$user->name} (ID: {$user->account_id}) has been updated.");
    }
}
