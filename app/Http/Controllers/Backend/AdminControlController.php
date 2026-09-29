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
        if (!auth()->check() || auth()->user()->role !== 'super_admin') {
            abort(403, 'Unauthorized. Super Admin Clearance Required.');
        }
    }

    /**
     * Standard list of administrative permissions available on the platform.
     */
    public static function availablePermissions(): array
    {
        return [
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
            'can_access_cadet_passwords' => [
                'label' => 'Cadet Password Access',
                'description' => 'Permission to watch and reveal cadet current passwords in Cadet Management.'
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
    }

    /**
     * List all administrators, their roles, phone numbers, and permission sets.
     */
    public function index(Request $request)
    {
        $this->checkDeveloperAdmin();

        $pendingRequests = User::where('status', 'pending')->orderBy('created_at', 'desc')->get();

        $query = User::whereIn('role', ['super_admin', 'pro_admin', 'admin', 'finance_manager'])
            ->where('status', '!=', 'pending');

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

        $availablePermissions = self::availablePermissions();

        $stats = [
            'total_admins' => User::whereIn('role', ['super_admin', 'pro_admin', 'admin', 'finance_manager'])->where('status', '!=', 'pending')->count(),
            'super_admins' => User::where('role', 'super_admin')->where('status', '!=', 'pending')->count(),
            'pro_admins' => User::where('role', 'pro_admin')->where('status', '!=', 'pending')->count(),
            'standard_admins' => User::where('role', 'admin')->where('status', '!=', 'pending')->count(),
            'finance_managers' => User::where('role', 'finance_manager')->where('status', '!=', 'pending')->count(),
            'pending_requests' => $pendingRequests->count(),
        ];

        return view('backend.admin_control.index', compact('admins', 'availablePermissions', 'stats', 'pendingRequests'));
    }

    /**
     * Approve and permit an administrative account request.
     */
    public function approve(Request $request, $id)
    {
        $this->checkDeveloperAdmin();

        $user = User::findOrFail($id);
        $user->status = 'active';
        $user->save();

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'APPROVED_ADMIN_ACCOUNT',
            'details' => "Super Admin approved administrative request for: {$user->name} ({$user->account_id})",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => Carbon::now(),
        ]);

        return redirect()->route('admin.admin_control.index')
            ->with('success', "Administrative account request for '{$user->name}' has been permitted and activated successfully.");
    }

    /**
     * Reject and remove an administrative account request.
     */
    public function reject(Request $request, $id)
    {
        $this->checkDeveloperAdmin();

        $user = User::findOrFail($id);
        $name = $user->name;
        $accId = $user->account_id;

        $user->delete();

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'REJECTED_ADMIN_ACCOUNT',
            'details' => "Super Admin rejected administrative request: {$name} ({$accId})",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => Carbon::now(),
        ]);

        return redirect()->route('admin.admin_control.index')
            ->with('success', "Administrative account request for '{$name}' has been rejected and removed.");
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
            'role' => 'required|in:admin,pro_admin,finance_manager,super_admin',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
        ]);

        $permissions = $validated['permissions'] ?? [];
        if ($request->has('can_access_cadet_passwords_opt')) {
            if ($request->input('can_access_cadet_passwords_opt') == '1') {
                if (!in_array('can_access_cadet_passwords', $permissions)) {
                    $permissions[] = 'can_access_cadet_passwords';
                }
            } else {
                $permissions = array_values(array_diff($permissions, ['can_access_cadet_passwords']));
            }
        }

        $user = User::create([
            'name' => $validated['name'],
            'account_id' => $validated['account_id'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'plain_password' => $validated['password'],
            'role' => $validated['role'],
            'permissions' => $permissions,
            'status' => 'active',
        ]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'CREATED_ADMIN_ACCOUNT',
            'details' => "Super Admin created {$validated['role']} account: {$user->name} ({$user->account_id}) with phone {$user->phone}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => Carbon::now(),
        ]);

        return redirect()->route('admin.admin_control.index')
            ->with('success', "Administrator account for '{$user->name}' initialized successfully with specified clearance.");
    }

    /**
     * Show dedicated full page editor for an administrator.
     */
    public function edit($id)
    {
        $this->checkDeveloperAdmin();

        $admin = User::findOrFail($id);
        $currentUser = auth()->user();

        // Security Guard: No one can edit the details of a developer except the developer himself
        if ($admin->isDeveloperAdmin() && !$currentUser->isDeveloperAdmin()) {
            abort(403, 'Critical Security Protection: Developer account details cannot be edited or modified by any other administrator.');
        }

        // Security Guard: No one can edit the details of a super admin except a super admin
        if ($admin->role === 'super_admin' && $currentUser->role !== 'super_admin') {
            abort(403, 'Unauthorized: Only a Super Administrator can edit the profile of a Super Administrator.');
        }

        $availablePermissions = self::availablePermissions();

        return view('backend.admin_control.edit', compact('admin', 'availablePermissions'));
    }

    /**
     * Update an administrator's credentials, role, status, and permissions.
     */
    public function update(Request $request, $id)
    {
        $this->checkDeveloperAdmin();

        $user = User::findOrFail($id);
        $currentUser = auth()->user();

        // Security Guard: No one can edit the details of a developer except the developer himself
        if ($user->isDeveloperAdmin() && !$currentUser->isDeveloperAdmin()) {
            abort(403, 'Critical Security Protection: Developer account details cannot be edited or modified by any other administrator.');
        }

        // Security Guard: No one can edit the details of a super admin except a super admin
        if ($user->role === 'super_admin' && $currentUser->role !== 'super_admin') {
            abort(403, 'Unauthorized: Only a Super Administrator can edit the profile of a Super Administrator.');
        }

        // Security Guard: Only super admins can assign the super admin role
        if ($request->input('role') === 'super_admin' && $currentUser->role !== 'super_admin') {
            abort(403, 'Unauthorized: Only a Super Administrator can grant Super Administrator clearance.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'account_id' => 'required|string|max:50|unique:users,account_id,' . $user->id,
            'email' => 'required|email|max:100|unique:users,email,' . $user->id,
            'phone' => 'required|string|max:30',
            'role' => 'required|in:super_admin,pro_admin,admin,finance_manager',
            'status' => 'nullable|in:active,inactive,suspended',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
            'password' => 'nullable|string|min:6',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'];
        
        $isArghaRoy = ($user->isDeveloperAdmin() || strtolower(trim($user->account_id ?? '')) === 'argharoy' || $user->id === 1);
        if ($isArghaRoy) {
            $user->role = 'super_admin';
            $user->account_id = 'ArghaRoy';
            $user->permissions = ['can_manage_cms', 'can_manage_exams', 'can_manage_students', 'can_manage_finance', 'can_view_audit', 'can_access_cadet_passwords'];
        } else {
            $user->account_id = $validated['account_id'];
            $user->role = $validated['role'];
            $perms = $validated['permissions'] ?? [];
            if ($request->has('can_access_cadet_passwords_opt')) {
                if ($request->input('can_access_cadet_passwords_opt') == '1') {
                    if (!in_array('can_access_cadet_passwords', $perms)) {
                        $perms[] = 'can_access_cadet_passwords';
                    }
                } else {
                    $perms = array_values(array_diff($perms, ['can_access_cadet_passwords']));
                }
            }
            $user->permissions = $perms;
        }

        if (!empty($validated['status'])) {
            $user->status = $validated['status'];
        }

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
            $user->plain_password = $validated['password'];
        }

        $user->save();

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'UPDATED_ADMIN_ACCOUNT',
            'details' => "Super Admin updated details, credentials, and role ({$user->role}) for: {$user->name} ({$user->account_id})",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => Carbon::now(),
        ]);

        if ($request->input('submit_action') === 'save_update' || $request->input('action') === 'save_update') {
            return redirect()->route('admin.admin_control.edit', $user->id)
                ->with('success', "Administrator profile for '{$user->name}' updated successfully.");
        }

        return redirect()->route('admin.admin_control.index')
            ->with('success', "Administrator profile for '{$user->name}' updated successfully.");
    }

    /**
     * Alias for update for backwards compatibility.
     */
    public function updatePermissions(Request $request, $id)
    {
        return $this->update($request, $id);
    }

    /**
     * Delete an administrator account.
     */
    public function destroy(Request $request, $id)
    {
        $this->checkDeveloperAdmin();

        $user = User::findOrFail($id);
        $currentUser = auth()->user();

        // Security Guard: Critical protection against deleting Developer Admin ArghaRoy
        if ($user->isDeveloperAdmin() || strtolower($user->account_id) === 'argharoy' || $user->id === 1) {
            return redirect()->route('admin.admin_control.index')
                ->with('error', 'Critical Security Protection: The Developer account cannot be deleted or revoked.');
        }

        // Security Guard: Self-deletion guard
        if ($user->id === $currentUser->id) {
            return redirect()->route('admin.admin_control.index')
                ->with('error', 'Security Policy: You cannot delete your own active administrator account.');
        }

        // Security Guard: No one can remove a super admin except a super admin
        if ($user->role === 'super_admin' && $currentUser->role !== 'super_admin') {
            abort(403, 'Unauthorized: Only a Super Administrator can remove a Super Administrator.');
        }

        $adminName = $user->name;
        $adminId = $user->account_id;

        $user->delete();

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'DELETED_ADMIN_ACCOUNT',
            'details' => "Super Admin removed administrator account: {$adminName} ({$adminId})",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => Carbon::now(),
        ]);

        return redirect()->route('admin.admin_control.index')
            ->with('success', "Administrator account '{$adminName}' removed successfully.");
    }
}
