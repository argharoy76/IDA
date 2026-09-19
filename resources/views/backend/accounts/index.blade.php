@extends('layouts.portal')

@section('title', 'Account & ID Control')
@section('page_title', 'Account & ID Management')
@section('page_subtitle', 'View, create, and assign customized login IDs for administrators, instructors, cadets, and candidates')

@section('content')
<div class="content-panel">

  <!-- Header Action Toolbar -->
  <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px; margin-bottom: 22px;">
    
    <!-- Search & Filter Controls -->
    <form method="GET" action="{{ route('admin.accounts.index') }}" style="display: flex; gap: 10px; flex-wrap: wrap; flex: 1;">
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, ID, email, phone..." class="form-control" style="max-width: 280px;">

      <select name="role" class="form-control" style="max-width: 180px;" onchange="this.form.submit()">
        <option value="">All Account Roles</option>
        <option value="super_admin" {{ request('role') === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Operations Admin</option>
        <option value="finance_manager" {{ request('role') === 'finance_manager' ? 'selected' : '' }}>Finance Manager</option>
        <option value="instructor" {{ request('role') === 'instructor' ? 'selected' : '' }}>Instructor</option>
        <option value="academic_student" {{ request('role') === 'academic_student' ? 'selected' : '' }}>Academic Cadet</option>
        <option value="external_student" {{ request('role') === 'external_student' ? 'selected' : '' }}>External Candidate</option>
      </select>

      <button type="submit" class="btn-secondary" style="padding: 9px 16px;">
        <i class="fa-solid fa-filter"></i> Filter
      </button>

      @if(request()->anyFilled(['search', 'role']))
        <a href="{{ route('admin.accounts.index') }}" class="btn-secondary" style="color: #ef4444;">
          <i class="fa-solid fa-xmark"></i> Reset
        </a>
      @endif
    </form>

    <!-- Create New Account Button -->
    <button type="button" onclick="openModal('createAccountModal')" class="btn-primary">
      <i class="fa-solid fa-user-plus"></i> Add New Account
    </button>
  </div>

  <!-- Accounts Table -->
  <div class="table-responsive">
    <table class="tactical-table">
      <thead>
        <tr>
          <th>User Account</th>
          <th>Assigned Login ID</th>
          <th>Email Address</th>
          <th>Role</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($users as $u)
          <tr>
            <td>
              <div style="display: flex; align-items: center; gap: 10px;">
                <div style="width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, var(--brand-deep), var(--accent-navy)); color: #fff; display: grid; place-items: center; font-weight: 700; font-size: 13px;">
                  {{ strtoupper(substr($u->name, 0, 1)) }}
                </div>
                <div>
                  <strong style="display: block; font-size: 13.5px; color: #ffffff;">{{ $u->name }}</strong>
                  <small style="color: #8c96a8; font-size: 11px;">Joined: {{ $u->created_at ? $u->created_at->format('d M, Y') : 'N/A' }}</small>
                </div>
              </div>
            </td>
            <td>
              <div style="display: flex; align-items: center; gap: 8px;">
                <span class="badge badge-emerald" style="font-size: 12px; font-weight: 800; letter-spacing: 0.5px; padding: 5px 10px;">
                  <i class="fa-solid fa-id-card-clip" style="margin-right: 4px;"></i> {{ $u->account_id ?? 'UNSET' }}
                </span>
                <button type="button" onclick="openEditIdModal('{{ $u->id }}', '{{ addslashes($u->name) }}', '{{ $u->account_id }}')" 
                        class="btn-secondary" style="padding: 3px 8px; font-size: 11px;" title="Change ID">
                  <i class="fa-solid fa-pen-to-square"></i>
                </button>
              </div>
            </td>
            <td>
              <span style="color: #cbd5e1; font-size: 13px;">{{ $u->email }}</span>
              @if($u->phone)
                <small style="display: block; color: #8c96a8; font-size: 11px;">{{ $u->phone }}</small>
              @endif
            </td>
            <td>
              @php
                $badgeClass = match($u->role) {
                  'super_admin' => 'badge-red',
                  'admin' => 'badge-amber',
                  'finance_manager' => 'badge-blue',
                  'instructor' => 'badge-purple',
                  'academic_student' => 'badge-emerald',
                  default => 'badge-gray',
                };
                $roleLabel = match($u->role) {
                  'super_admin' => 'Super Admin',
                  'admin' => 'Admin',
                  'finance_manager' => 'Finance',
                  'instructor' => 'Instructor',
                  'academic_student' => 'Cadet',
                  'external_student' => 'Candidate',
                  default => ucfirst($u->role),
                };
              @endphp
              <span class="badge {{ $badgeClass }}">
                {{ $roleLabel }}
              </span>
            </td>
            <td>
              @if($u->status === 'active')
                <span class="badge badge-emerald" style="font-size: 10px;">Active</span>
              @else
                <span class="badge badge-red" style="font-size: 10px;">{{ ucfirst($u->status) }}</span>
              @endif
            </td>
            <td>
              <div style="display: flex; gap: 6px;">
                <button type="button" onclick="openEditIdModal('{{ $u->id }}', '{{ addslashes($u->name) }}', '{{ $u->account_id }}')" 
                        class="btn-secondary" style="padding: 5px 10px; font-size: 11.5px;">
                  <i class="fa-solid fa-id-card"></i> Set ID
                </button>
                <button type="button" onclick="openResetPasswordModal('{{ $u->id }}', '{{ addslashes($u->name) }}')" 
                        class="btn-secondary" style="padding: 5px 10px; font-size: 11.5px;">
                  <i class="fa-solid fa-key"></i> Password
                </button>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" style="text-align: center; padding: 40px; color: #8c96a8;">
              <i class="fa-solid fa-users-slash" style="font-size: 32px; margin-bottom: 10px; display: block;"></i>
              No accounts matching your criteria found.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div style="margin-top: 20px;">
    {{ $users->links() }}
  </div>
</div>

<!-- Modal 1: Edit Account ID -->
<div id="editIdModal" class="modal-backdrop" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center;">
  <div class="classical-card" style="background: #131926; border: 1px solid rgba(255, 255, 255, 0.12); border-radius: 16px; width: 100%; max-width: 460px; padding: 28px; box-shadow: 0 20px 40px rgba(0,0,0,0.6);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px;">
      <h3 style="margin: 0; font-size: 18px; color: #ffffff; font-weight: 700;">
        <i class="fa-solid fa-id-card" style="color: #10b981; margin-right: 8px;"></i> Set Account Login ID
      </h3>
      <button type="button" onclick="closeModal('editIdModal')" style="background: none; border: none; color: #8c96a8; font-size: 18px; cursor: pointer;">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>

    <form id="editIdForm" method="POST" action="">
      @csrf
      <p style="font-size: 13px; color: #94a3b8; margin-bottom: 16px;">
        Assign a custom login ID for <strong id="editIdUserName" style="color: #ffffff;"></strong>. This ID can be used on the login page instead of their email.
      </p>

      <div class="form-group" style="margin-bottom: 20px;">
        <label class="form-label" style="font-size: 12px; font-weight: 700; color: #cbd5e1; text-transform: uppercase; margin-bottom: 6px; display: block;">
          Login ID *
        </label>
        <input type="text" name="account_id" id="editIdInput" required class="form-control" 
               style="width: 100%; padding: 11px 14px; background: #0c111a; border: 1px solid rgba(255,255,255,0.15); border-radius: 8px; color: #ffffff; font-size: 14px; font-weight: 700; letter-spacing: 0.5px;"
               placeholder="e.g. ADM-001, IDA-2026-001, EXT-2026-101">
        <small style="color: #8c96a8; font-size: 11px; display: block; margin-top: 5px;">
          Must be unique. Automatically converted to uppercase.
        </small>
      </div>

      <div style="display: flex; justify-content: flex-end; gap: 10px;">
        <button type="button" onclick="closeModal('editIdModal')" class="btn-secondary" style="padding: 9px 18px;">
          Cancel
        </button>
        <button type="submit" class="btn-primary" style="padding: 9px 20px;">
          <i class="fa-solid fa-check"></i> Save ID
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Modal 2: Create New Account with ID -->
<div id="createAccountModal" class="modal-backdrop" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center;">
  <div class="classical-card" style="background: #131926; border: 1px solid rgba(255, 255, 255, 0.12); border-radius: 16px; width: 100%; max-width: 520px; padding: 28px; box-shadow: 0 20px 40px rgba(0,0,0,0.6);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px;">
      <h3 style="margin: 0; font-size: 18px; color: #ffffff; font-weight: 700;">
        <i class="fa-solid fa-user-plus" style="color: #10b981; margin-right: 8px;"></i> Create New Account
      </h3>
      <button type="button" onclick="closeModal('createAccountModal')" style="background: none; border: none; color: #8c96a8; font-size: 18px; cursor: pointer;">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>

    <form method="POST" action="{{ route('admin.accounts.store') }}">
      @csrf

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px;">
        <div>
          <label class="form-label" style="font-size: 11.5px; font-weight: 700; color: #cbd5e1; display: block; margin-bottom: 4px;">Full Name *</label>
          <input type="text" name="name" required class="form-control" placeholder="Account full name" style="width: 100%;">
        </div>
        <div>
          <label class="form-label" style="font-size: 11.5px; font-weight: 700; color: #cbd5e1; display: block; margin-bottom: 4px;">Email Address *</label>
          <input type="email" name="email" required class="form-control" placeholder="user@ida.com" style="width: 100%;">
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px;">
        <div>
          <label class="form-label" style="font-size: 11.5px; font-weight: 700; color: #cbd5e1; display: block; margin-bottom: 4px;">Account Role *</label>
          <select name="role" required class="form-control" style="width: 100%;">
            <option value="super_admin">Super Admin</option>
            <option value="admin">Operations Admin</option>
            <option value="finance_manager">Finance Manager</option>
            <option value="instructor">Instructor</option>
            <option value="academic_student">Academic Cadet</option>
            <option value="external_student">External Candidate</option>
          </select>
        </div>
        <div>
          <label class="form-label" style="font-size: 11.5px; font-weight: 700; color: #cbd5e1; display: block; margin-bottom: 4px;">Custom Login ID (Optional)</label>
          <input type="text" name="account_id" class="form-control" placeholder="e.g. ADM-005, INS-005" style="width: 100%;">
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px;">
        <div>
          <label class="form-label" style="font-size: 11.5px; font-weight: 700; color: #cbd5e1; display: block; margin-bottom: 4px;">Password *</label>
          <input type="password" name="password" required class="form-control" placeholder="Min 6 characters" style="width: 100%;">
        </div>
        <div>
          <label class="form-label" style="font-size: 11.5px; font-weight: 700; color: #cbd5e1; display: block; margin-bottom: 4px;">Phone Number</label>
          <input type="text" name="phone" class="form-control" placeholder="017XXXXXXXX" style="width: 100%;">
        </div>
      </div>

      <div style="margin-bottom: 20px;">
        <label class="form-label" style="font-size: 11.5px; font-weight: 700; color: #cbd5e1; display: block; margin-bottom: 4px;">Account Status *</label>
        <select name="status" class="form-control" style="width: 100%;">
          <option value="active">Active (Permitted to login)</option>
          <option value="inactive">Inactive</option>
        </select>
      </div>

      <div style="display: flex; justify-content: flex-end; gap: 10px;">
        <button type="button" onclick="closeModal('createAccountModal')" class="btn-secondary" style="padding: 9px 18px;">
          Cancel
        </button>
        <button type="submit" class="btn-primary" style="padding: 9px 20px;">
          <i class="fa-solid fa-user-plus"></i> Create Account
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Modal 3: Reset Password -->
<div id="resetPasswordModal" class="modal-backdrop" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center;">
  <div class="classical-card" style="background: #131926; border: 1px solid rgba(255, 255, 255, 0.12); border-radius: 16px; width: 100%; max-width: 440px; padding: 28px; box-shadow: 0 20px 40px rgba(0,0,0,0.6);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px;">
      <h3 style="margin: 0; font-size: 18px; color: #ffffff; font-weight: 700;">
        <i class="fa-solid fa-key" style="color: #f59e0b; margin-right: 8px;"></i> Reset Account Password
      </h3>
      <button type="button" onclick="closeModal('resetPasswordModal')" style="background: none; border: none; color: #8c96a8; font-size: 18px; cursor: pointer;">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>

    <form id="resetPasswordForm" method="POST" action="">
      @csrf
      <p style="font-size: 13px; color: #94a3b8; margin-bottom: 16px;">
        Set a new password for <strong id="resetPwdUserName" style="color: #ffffff;"></strong>.
      </p>

      <div class="form-group" style="margin-bottom: 14px;">
        <label class="form-label" style="font-size: 11.5px; font-weight: 700; color: #cbd5e1; display: block; margin-bottom: 4px;">New Password *</label>
        <input type="password" name="password" required class="form-control" placeholder="Min 6 characters" style="width: 100%;">
      </div>

      <div class="form-group" style="margin-bottom: 20px;">
        <label class="form-label" style="font-size: 11.5px; font-weight: 700; color: #cbd5e1; display: block; margin-bottom: 4px;">Confirm New Password *</label>
        <input type="password" name="password_confirmation" required class="form-control" placeholder="Repeat new password" style="width: 100%;">
      </div>

      <div style="display: flex; justify-content: flex-end; gap: 10px;">
        <button type="button" onclick="closeModal('resetPasswordModal')" class="btn-secondary" style="padding: 9px 18px;">
          Cancel
        </button>
        <button type="submit" class="btn-primary" style="padding: 9px 20px;">
          <i class="fa-solid fa-check"></i> Update Password
        </button>
      </div>
    </form>
  </div>
</div>

<script>
  function openModal(id) {
    const el = document.getElementById(id);
    if (el) {
      el.style.display = 'flex';
    }
  }

  function closeModal(id) {
    const el = document.getElementById(id);
    if (el) {
      el.style.display = 'none';
    }
  }

  function openEditIdModal(userId, userName, currentId) {
    document.getElementById('editIdUserName').innerText = userName;
    document.getElementById('editIdInput').value = currentId || '';
    document.getElementById('editIdForm').action = '/admin/accounts/' + userId + '/update-id';
    openModal('editIdModal');
  }

  function openResetPasswordModal(userId, userName) {
    document.getElementById('resetPwdUserName').innerText = userName;
    document.getElementById('resetPasswordForm').action = '/admin/accounts/' + userId + '/password';
    openModal('resetPasswordModal');
  }
</script>
@endsection