@extends('layouts.portal')

@section('title', 'Edit Administrator: ' . $admin->name)
@section('page_title', 'Edit Administrator Profile')

@php
  $isArghaRoy = ($admin->isDeveloperAdmin() || strtolower(trim($admin->account_id ?? '')) === 'argharoy' || strtolower(trim($admin->name ?? '')) === 'argharoy' || $admin->id === 1);
@endphp

@section('content')
<div style="max-width: 1000px; margin: 0 auto;">

  <!-- Top Action Bar -->
  <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 24px;">
    <div style="display: flex; gap: 12px; align-items: center;">
      <a href="{{ route('admin.admin_control.index') }}" class="btn-tactical" style="background: rgba(255,255,255,0.06); color: #94a3b8; border: 1px solid rgba(255,255,255,0.12); padding: 9px 18px; font-size: 13px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; border-radius: 8px;">
        <i class="fa-solid fa-arrow-left"></i> Back to Admin Roster
      </a>
      @if($isArghaRoy)
        <span style="background: rgba(239,68,68,0.18); border: 1px solid rgba(239,68,68,0.4); color: #f87171; font-size: 8.5px; font-weight: 800; padding: 2px 6px; border-radius: 4px; display: inline-flex; align-items: center; gap: 4px; letter-spacing: 0.5px;">
          <i class="fa-solid fa-code" style="font-size: 7.5px;"></i> DEV
        </span>
      @endif
      <span style="font-size: 12.5px; color: #64748b;">
        Identity: <strong style="color: #cbd5e1; font-family: monospace;">{{ $admin->account_id }}</strong>
      </span>
    </div>

    <div>
      <button type="submit" form="updateAdminForm" class="btn-tactical btn-tactical-primary" style="padding: 9px 22px; font-size: 13px; font-weight: 700; display: inline-flex; align-items: center; gap: 8px; border-radius: 8px;">
        <i class="fa-solid fa-save"></i> Save Profile Changes
      </button>
    </div>
  </div>

  @if(session('success'))
    <div style="background: rgba(16,185,129,0.12); border: 1px solid rgba(16,185,129,0.35); border-radius: 10px; padding: 14px 18px; margin-bottom: 24px; color: #34d399; font-size: 13.5px; display: flex; align-items: center; gap: 10px;">
      <i class="fa-solid fa-circle-check" style="font-size: 16px;"></i>
      <span>{{ session('success') }}</span>
    </div>
  @endif

  @if(session('error'))
    <div style="background: rgba(239,68,68,0.12); border: 1px solid rgba(239,68,68,0.35); border-radius: 10px; padding: 14px 18px; margin-bottom: 24px; color: #f87171; font-size: 13.5px; display: flex; align-items: center; gap: 10px;">
      <i class="fa-solid fa-triangle-exclamation" style="font-size: 16px;"></i>
      <span>{{ session('error') }}</span>
    </div>
  @endif

  @if(isset($errors) && $errors->any())
    <div style="background: rgba(239,68,68,0.12); border: 1px solid rgba(239,68,68,0.35); border-radius: 10px; padding: 16px 20px; margin-bottom: 24px; color: #fca5a5; font-size: 13px;">
      <strong style="display: block; margin-bottom: 6px;"><i class="fa-solid fa-triangle-exclamation"></i> Please resolve the following errors:</strong>
      <ul style="margin: 0; padding-left: 20px; line-height: 1.6;">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <!-- Update Admin Form -->
  <form method="POST" action="{{ route('admin.admin_control.update', $admin->id) }}" id="updateAdminForm" autocomplete="off">
    @csrf
    @method('PUT')

    <!-- Browser Autofill Traps to guarantee new password field remains completely empty -->
    <input type="text" name="prevent_autofill_username" style="display:none;" tabindex="-1" autocomplete="off">
    <input type="password" name="prevent_autofill_pwd" style="display:none;" tabindex="-1" autocomplete="off">

    <!-- Section 1: Account Identity & Role Authority -->
    <div class="content-panel" style="margin-bottom: 24px; background: #0f121a; border: 1px solid rgba(255,255,255,0.08); border-radius: 14px; padding: 22px;">
      <div class="panel-header" style="margin-bottom: 18px; border-bottom: 1px solid rgba(255,255,255,0.06); padding-bottom: 14px;">
        <h3 style="font-size: 15px; font-weight: 800; color: #ffffff; margin: 0; display: flex; align-items: center; gap: 10px;">
          <i class="fa-solid fa-id-card-clip" style="color: #ff5757;"></i>
          <span>Account Identity & Role Authority</span>
        </h3>
      </div>

      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 18px; margin-bottom: 18px;">
        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">
            Administrator Full Name *
          </label>
          <input type="text" name="name" required value="{{ old('name', $admin->name) }}"
                 class="form-control"
                 style="background: #131722; border: 1px solid rgba(255,255,255,0.1); color: #ffffff; padding: 11px 14px; border-radius: 8px; font-size: 14px;"
                 placeholder="e.g. Major Tareq Rahman">
        </div>

        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">
            Identity *
          </label>
          <input type="text" name="account_id" required value="{{ old('account_id', $admin->account_id) }}"
                 class="form-control"
                 style="background: #131722; border: 1px solid rgba(255,255,255,0.1); color: #ffffff; font-family: monospace; font-size: 14px; font-weight: 700; padding: 11px 14px; border-radius: 8px;"
                 placeholder="e.g. ArghaRoy or ADM-005"
                 {{ $isArghaRoy ? 'readonly' : '' }}>
        </div>
      </div>

      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 18px;">
        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">
            Clearance Role *
          </label>
          @if($isArghaRoy)
            <!-- Developer Account Locked Role -->
            <input type="hidden" name="role" value="super_admin">
            <div style="background: linear-gradient(135deg, rgba(239, 68, 68, 0.12) 0%, rgba(185, 28, 28, 0.2) 100%); border: 1.5px solid #ef4444; border-radius: 8px; padding: 11px 14px; display: flex; align-items: center; justify-content: space-between; min-height: 44px; box-sizing: border-box;">
              <div style="display: flex; align-items: center; gap: 8px;">
                <span style="background: rgba(239, 68, 68, 0.2); border: 1px solid rgba(239, 68, 68, 0.4); color: #f87171; padding: 2px 6px; border-radius: 4px; font-size: 8.5px; font-weight: 800; display: inline-flex; align-items: center; gap: 4px; letter-spacing: 0.5px;">
                  <i class="fa-solid fa-code" style="font-size: 7.5px;"></i> DEV
                </span>
                <span style="font-size: 14px; font-weight: 700; color: #ffffff;">Developer</span>
              </div>
              <span style="font-size: 11.5px; color: #fca5a5; font-weight: 600; display: flex; align-items: center; gap: 4px;">
                <i class="fa-solid fa-lock"></i> Permanent
              </span>
            </div>
          @else
            <!-- Standard Role Selection (Bug-free clean options) -->
            <select name="role" id="admin_role_select" required onchange="toggleProAdminPasswordBox(this.value)" class="form-control" style="background: #131722; border: 1px solid rgba(255,255,255,0.1); color: #ffffff; padding: 11px 14px; border-radius: 8px; font-size: 14px; font-weight: 600; width: 100%;">
              <option value="super_admin" {{ old('role', $admin->role) === 'super_admin' ? 'selected' : '' }} style="background-color: #131722; color: #ffffff;">Super Admin</option>
              <option value="pro_admin" {{ old('role', $admin->role) === 'pro_admin' ? 'selected' : '' }} style="background-color: #131722; color: #ffffff;">Pro Admin</option>
              <option value="admin" {{ old('role', $admin->role) === 'admin' ? 'selected' : '' }} style="background-color: #131722; color: #ffffff;">Normal Admin</option>
              <option value="finance_manager" {{ old('role', $admin->role) === 'finance_manager' ? 'selected' : '' }} style="background-color: #131722; color: #ffffff;">Account Manager</option>
            </select>
          @endif
        </div>

        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">
            Account Status *
          </label>
          <select name="status" required class="form-control" style="background: #131722; border: 1px solid rgba(255,255,255,0.1); color: #ffffff; padding: 11px 14px; border-radius: 8px; font-size: 14px; width: 100%;">
            <option value="active" {{ old('status', $admin->status) === 'active' ? 'selected' : '' }} style="background-color: #131722; color: #ffffff;">Active</option>
            <option value="inactive" {{ old('status', $admin->status) === 'inactive' ? 'selected' : '' }} style="background-color: #131722; color: #ffffff;">Inactive</option>
            <option value="suspended" {{ old('status', $admin->status) === 'suspended' ? 'selected' : '' }} style="background-color: #131722; color: #ffffff;">Suspended</option>
          </select>
        </div>
      </div>

      @php
        $selectedRole = old('role', $admin->role);
        $hasCadetPwAccess = in_array('can_access_cadet_passwords', old('permissions', $admin->permissions ?? [])) || ($admin->role === 'super_admin');
      @endphp
      <div id="proAdminPasswordAccessBox" style="display: {{ $selectedRole === 'pro_admin' ? 'block' : 'none' }}; margin-top: 18px; background: rgba(99, 102, 241, 0.06); border: 1.5px solid rgba(99, 102, 241, 0.3); border-radius: 10px; padding: 14px 18px;">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
          <div>
            <div style="font-size: 13.5px; font-weight: 800; color: #ffffff; display: flex; align-items: center; gap: 8px;">
              <i class="fa-solid fa-key" style="color: #818cf8;"></i>
              <span>Cadet Password Access</span>
              <span style="font-size: 10px; background: rgba(99,102,241,0.25); color: #c7d2fe; padding: 2px 7px; border-radius: 4px; font-weight: 700;">PRO ADMIN CLEARANCE</span>
            </div>
            <div style="font-size: 12px; color: #94a3b8; margin-top: 4px;">
              Can this Pro Admin view and watch cadet current passwords in Cadet Management?
            </div>
          </div>
          <div style="display: flex; align-items: center; gap: 16px;">
            <label style="display: inline-flex; align-items: center; gap: 6px; cursor: pointer; color: #ffffff; font-size: 13px; font-weight: 700;">
              <input type="radio" name="can_access_cadet_passwords_opt" value="1" {{ $hasCadetPwAccess ? 'checked' : '' }} style="accent-color: #6366f1; width: 16px; height: 16px;">
              <span>Yes (Can Access)</span>
            </label>
            <label style="display: inline-flex; align-items: center; gap: 6px; cursor: pointer; color: #94a3b8; font-size: 13px; font-weight: 600;">
              <input type="radio" name="can_access_cadet_passwords_opt" value="0" {{ !$hasCadetPwAccess ? 'checked' : '' }} style="accent-color: #6366f1; width: 16px; height: 16px;">
              <span>No (Restricted)</span>
            </label>
          </div>
        </div>
      </div>
    </div>

    <!-- Section 2: Contact & Authentication Details -->
    <div class="content-panel" style="margin-bottom: 24px; background: #0f121a; border: 1px solid rgba(255,255,255,0.08); border-radius: 14px; padding: 22px;">
      <div class="panel-header" style="margin-bottom: 18px; border-bottom: 1px solid rgba(255,255,255,0.06); padding-bottom: 14px;">
        <h3 style="font-size: 15px; font-weight: 800; color: #ffffff; margin: 0; display: flex; align-items: center; gap: 10px;">
          <i class="fa-solid fa-address-book" style="color: #60a5fa;"></i>
          <span>Official Contact Information</span>
        </h3>
      </div>

      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 18px;">
        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 6px;">Official Email Address *</label>
          <input type="email" name="email" required value="{{ old('email', $admin->email) }}"
                 class="form-control"
                 style="background: #131722; border: 1px solid rgba(255,255,255,0.1); color: #ffffff; padding: 11px 14px; border-radius: 8px; font-size: 14px;"
                 placeholder="admin@ida.com">
        </div>

        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 6px;">Registered Phone Number *</label>
          <input type="text" name="phone" required value="{{ old('phone', $admin->phone) }}"
                 class="form-control"
                 style="background: #131722; border: 1px solid rgba(255,255,255,0.1); color: #ffffff; padding: 11px 14px; border-radius: 8px; font-size: 14px;"
                 placeholder="e.g. 01711002233">
        </div>
      </div>
    </div>

    <!-- Section 3: Password Management (With Show/Hide & Previous Password) -->
    <div class="content-panel" style="margin-bottom: 24px; background: #0f121a; border: 1px solid rgba(255,255,255,0.08); border-radius: 14px; padding: 22px;">
      <div class="panel-header" style="margin-bottom: 18px; border-bottom: 1px solid rgba(255,255,255,0.06); padding-bottom: 14px;">
        <h3 style="font-size: 15px; font-weight: 800; color: #ffffff; margin: 0; display: flex; align-items: center; gap: 10px;">
          <i class="fa-solid fa-key" style="color: #f59e0b;"></i>
          <span>Password Credentials</span>
        </h3>
      </div>

      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 18px;">
        <!-- Current / Previous Password Box -->
        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 6px;">
            Current / Saved Password
          </label>
          <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; padding: 11px 14px; color: #cbd5e1; font-family: monospace; font-size: 14px; min-height: 44px; box-sizing: border-box; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-lock" style="color: #64748b;"></i>
            <span>{{ $admin->plain_password ?? 'Encrypted (Stored as secure hash)' }}</span>
          </div>
        </div>

        <!-- New Password Field with Watch Eye Toggle (Strictly Empty & Protected from Autofill) -->
        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 6px;">
            New Security Password
          </label>
          <div style="position: relative;">
            <input type="password" id="admin_edit_password" name="password" minlength="6"
                   value="" autocomplete="new-password"
                   class="form-control"
                   style="background: #131722; border: 1px solid rgba(255,255,255,0.1); color: #ffffff; padding: 11px 44px 11px 14px; border-radius: 8px; font-size: 14px;"
                   placeholder="Leave blank to keep current">
            <button type="button" onclick="togglePasswordVisibility('admin_edit_password', this)"
                    style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #94a3b8; cursor: pointer; padding: 4px; font-size: 15px;"
                    title="Watch Password">
              <i class="fa-solid fa-eye"></i>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Section 4: Granted Operational Module Permissions -->
    <div class="content-panel" style="margin-bottom: 28px; background: #0f121a; border: 1px solid rgba(255,255,255,0.08); border-radius: 14px; padding: 22px;">
      <div class="panel-header" style="margin-bottom: 18px; border-bottom: 1px solid rgba(255,255,255,0.06); padding-bottom: 14px;">
        <h3 style="font-size: 15px; font-weight: 800; color: #ffffff; margin: 0; display: flex; align-items: center; gap: 10px;">
          <i class="fa-solid fa-shield-halved" style="color: #10b981;"></i>
          <span>Granted Operational Module Permissions</span>
        </h3>
      </div>

      @php
        $currentPerms = old('permissions', $admin->permissions ?? []);
        if (!is_array($currentPerms)) {
          $currentPerms = [];
        }
      @endphp

      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 14px;">
        @foreach($availablePermissions as $permKey => $pData)
          @php
            $isChecked = in_array($permKey, $currentPerms) || ($admin->role === 'super_admin');
          @endphp
          <label style="display: flex; align-items: flex-start; gap: 12px; background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06); border-radius: 10px; padding: 14px 16px; cursor: pointer; transition: all 0.15s;"
                 onmouseover="this.style.borderColor='rgba(255,87,87,0.3)'"
                 onmouseout="this.style.borderColor='rgba(255,255,255,0.06)'">
            <input type="checkbox" name="permissions[]" value="{{ $permKey }}"
                   {{ $isChecked ? 'checked' : '' }}
                   style="margin-top: 3px; accent-color: #ff5757; width: 17px; height: 17px; cursor: pointer;">
            <div>
              <div style="font-size: 13.5px; font-weight: 700; color: #ffffff; margin-bottom: 3px;">
                {{ $pData['label'] }}
              </div>
              <div style="font-size: 12px; color: #94a3b8; line-height: 1.4;">
                {{ $pData['description'] }}
              </div>
            </div>
          </label>
        @endforeach
      </div>
    </div>

    <!-- Bottom Submit Toolbar -->
    <div style="display: flex; justify-content: flex-end; align-items: center; gap: 14px; background: #131722; border: 1px solid rgba(255,255,255,0.08); border-radius: 14px; padding: 16px 24px; margin-bottom: 40px;">
      <a href="{{ route('admin.admin_control.index') }}" class="btn-tactical" style="background: rgba(255,255,255,0.06); color: #94a3b8; border: 1px solid rgba(255,255,255,0.12); padding: 10px 20px; font-size: 13px; text-decoration: none; border-radius: 8px;">
        Cancel
      </a>
      <button type="submit" class="btn-tactical" style="background: #ff5757; color: #ffffff; padding: 10px 28px; font-size: 13.5px; font-weight: 700; border: none; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(255,87,87,0.25);">
        <i class="fa-solid fa-check"></i> Save Profile Changes
      </button>
    </div>

  </form>

</div>

<script>
  function togglePasswordVisibility(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
      input.type = 'text';
      icon.classList.remove('fa-eye');
      icon.classList.add('fa-eye-slash');
      icon.style.color = '#ff5757';
    } else {
      input.type = 'password';
      icon.classList.remove('fa-eye-slash');
      icon.classList.add('fa-eye');
      icon.style.color = '#94a3b8';
    }
  }

  function toggleProAdminPasswordBox(role) {
    var box = document.getElementById('proAdminPasswordAccessBox');
    if (box) {
      box.style.display = (role === 'pro_admin') ? 'block' : 'none';
    }
  }

  // Ensure New Security Password space remains strictly empty & clears any browser autofill
  function clearNewPasswordField() {
    const input = document.getElementById('admin_edit_password');
    if (input) {
      input.value = '';
    }
  }

  document.addEventListener('DOMContentLoaded', clearNewPasswordField);
  window.addEventListener('load', clearNewPasswordField);
  setTimeout(clearNewPasswordField, 50);
  setTimeout(clearNewPasswordField, 150);
  setTimeout(clearNewPasswordField, 300);
  setTimeout(clearNewPasswordField, 600);
</script>
@endsection
