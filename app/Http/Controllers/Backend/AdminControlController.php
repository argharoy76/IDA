<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\AuditLog;
use Carbon\Carbon;

class AdminControlController extends Controller
{
    /**
     * Ensure only the Developer Super Admin (ArghaRoy) can access this controller.
     */
    private function checkDeveloperAdmin()
    {
        if (!auth()->check() || !auth()->user()->isDeveloperAdmin()) {
            abort(403, 'Unauthorized. Exclusive Developer Super Admin Clearance Required.');
        }
    }

    /**
     * List all administrators, their roles, phone numbers, and permission sets.
     */
    public function index(Request $request)
    {
        $this->checkDeveloperAdmin();

        $query = User::whereIn('role', ['super_admin', 'admin', 'finance_manager']);

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('account_id', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $admins = $query->orderBy('id', 'asc')->get();

        $availablePermissions = [
            'can_manage_cms' => [
                'label' => 'Web CMS & Landing Page',
                'description' => 'Can modify hero slides, notices, about, courses, and site branding.'
            ],
            'can_manage_exams' => [
                'label' => 'Exam Engine & Question Bank',
                'description' => 'Can create, edit, scan PDFs, and schedule exams across all branches.'
            ],
            'can_manage_students' => [
                'label' => 'Cadet & Student Management',
                'description' => 'Can manage student enrollments, batches, class schedules, and attendance.'
            ],
            'can_manage_finance' => [
                'label' => 'Fees & Financial Ledger',
                'description' => 'Can verify bKash/Nagad payments, issue tuition invoices, and view ledger.'
            ],
            'can_view_audit' => [
                'label' => 'Security Audit Logs',
                'description' => 'Can review security access records, login attempts, and system actions.'
            ],
        ];

        $stats = [
            'total_admins' => User::whereIn('role', ['super_admin', 'admin', 'finance_manager'])->count(),
            'super_admins' => User::where('role', 'super_admin')->count(),
            'standard_admins' => User::where('role', 'admin')->count(),
            'finance_managers' => User::where('role', 'finance_manager')->count(),
        ];

        return view('backend.admin_control.index', compact('admins', 'availablePermissions', 'stats'));
    }

    /**
     * Create a new administrator account with specific role, phone, and permissions.
     */
    public function store(Request $request)
    {
        $this->checkDeveloperAdmin();

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'account_id' => 'required|string|max:50|unique:users,account_id',
            'email' => 'required|email|max:100|unique:users,email',
            'phone' => 'required|string|max:30',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,finance_manager,super_admin',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'account_id' => $validated['account_id'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'permissions' => $validated['permissions'] ?? [],
            'status' => 'active',
        ]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'CREATED_ADMIN_ACCOUNT',
            'details' => "Developer Admin created {$validated['role']} account: {$user->name} ({$user->account_id}) with phone {$user->phone}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => Carbon::now(),
        ]);

        return redirect()->route('admin.admin_control.index')
            ->with('success', "Administrator account for '{$user->name}' initialized successfully with specified clearance.");
    }

    /**
     * Update an administrator's permissions, role, phone, or password.
     */
    public function updatePermissions(Request $request, $id)
    {
        $this->checkDeveloperAdmin();

        $user = User::findOrFail($id);

        $rules = [
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:users,email,' . $user->id,
            'phone' => 'required|string|max:30',
            'role' => 'required|in:super_admin,admin,finance_manager',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
            'password' => 'nullable|string|min:8',
        ];

        // Only allow changing account_id if not developer admin
        if (!$user->isDeveloperAdmin()) {
            $rules['account_id'] = 'required|string|max:50|unique:users,account_id,' . $user->id;
        }

        $validated = $request->validate($rules);

        // Security Guard: Prevent demoting Developer Super Admin ArghaRoy
        if ($user->isDeveloperAdmin()) {
            $validated['role'] = 'super_admin';
            // Developer Admin always has full permissions
            $validated['permissions'] = ['can_manage_cms', 'can_manage_exams', 'can_manage_students', 'can_manage_finance', 'can_view_audit'];
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'];
        $user->role = $validated['role'];
        $user->permissions = $validated['permissions'] ?? [];

        if (isset($validated['account_id']) && !$user->isDeveloperAdmin()) {
            $user->account_id = $validated['account_id'];
        }

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'UPDATED_ADMIN_PERMISSIONS',
            'details' => "Developer Admin updated clearance & permissions for: {$user->name} ({$user->account_id})",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => Carbon::now(),
        ]);

        return redirect()->route('admin.admin_control.index')
            ->with('success', "Permissions and credentials for '{$user->name}' updated successfully.");
    }

    /**
     * Delete an administrator account.
     */
    public function destroy(Request $request, $id)
    {
        $this->checkDeveloperAdmin();

        $user = User::findOrFail($id);

        // Security Guard: Critical protection against deleting Developer Admin ArghaRoy
        if ($user->isDeveloperAdmin() || strtolower($user->account_id) === 'argharoy' || $user->id === auth()->id() || $user->id === 1) {
            return redirect()->route('admin.admin_control.index')
                ->with('error', 'Critical Security Protection: The Developer Super Admin account cannot be deleted or revoked.');
        }

        $adminName = $user->name;
        $adminId = $user->account_id;

        $user->delete();

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'DELETED_ADMIN_ACCOUNT',
            'details' => "Developer Admin removed administrator account: {$adminName} ({$adminId})",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => Carbon::now(),
        ]);

        return redirect()->route('admin.admin_control.index')
            ->with('success', "Administrator account '{$adminName}' removed successfully.");
    }
}
