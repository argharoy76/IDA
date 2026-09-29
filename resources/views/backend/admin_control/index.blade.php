@extends('layouts.portal')

@section('title', 'Admin Control Command Center')
@section('page_title', 'Admin Control Panel')
@section('page_subtitle', 'Manage all administrative accounts, roles, and platform access permissions.')

@section('topbar_actions')
  <button type="button" class="btn-tactical btn-tactical-primary" onclick="openAddAdminModal()">
    <i class="fa-solid fa-user-plus"></i> Add New Administrator
  </button>
@endsection

@section('content')
<style>
  .admin-control-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 16px;
    margin-bottom: 24px;
  }
  .stat-badge-chip {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 11px;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 6px;
  }
  .perm-chip {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 11px;
    font-weight: 600;
    padding: 2px 8px;
    border-radius: 4px;
    background: #f1f5f9;
    color: #334155;
    border: 1px solid rgba(255,255,255,0.1);
    margin: 2px 0;
  }
  .perm-chip.active {
    background: #ecfdf5;
    color: #065f46;
    border-color: #a7f3d0;
  }
  .dev-crown-badge {
    background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);
    color: #ffffff;
    font-size: 10px;
    font-weight: 800;
    padding: 2px 8px;
    border-radius: 4px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);
  }
  .modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.7);
    backdrop-filter: blur(4px);
    z-index: 9999;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 20px;
  }
  .modal-overlay.active {
    display: flex;
  }
  .tactical-modal-card {
    background: #ffffff;
    border-radius: 18px;
    width: 100%;
    max-width: 620px;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 25px 60px rgba(0,0,0,0.3);
    border: 1px solid rgba(255,255,255,0.1);
    font-family: inherit;
  }
  .tactical-modal-header {
    padding: 20px 24px;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #f8fafc;
    border-top-left-radius: 18px;
    border-top-right-radius: 18px;
  }
  .tactical-modal-body {
    padding: 24px;
  }
  .tactical-modal-footer {
    padding: 16px 24px;
    border-top: 1px solid #e2e8f0;
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    background: #f8fafc;
    border-bottom-left-radius: 18px;
    border-bottom-right-radius: 18px;
  }
</style>

<div style="display: flex; flex-direction: column; gap: 24px;">

  <!-- Messages -->
  @if(session('success'))
    <div style="background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 12px; padding: 14px 18px; color: #065f46; font-size: 13.5px; display: flex; align-items: center; gap: 10px;">
      <i class="fa-solid fa-circle-check" style="color: #059669; font-size: 18px;"></i>
      <span>{{ session('success') }}</span>
    </div>
  @endif

  @if(session('error'))
    <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 12px; padding: 14px 18px; color: #991b1b; font-size: 13.5px; display: flex; align-items: center; gap: 10px;">
      <i class="fa-solid fa-circle-exclamation" style="color: #ef4444; font-size: 18px;"></i>
      <span>{{ session('error') }}</span>
    </div>
  @endif

  @if($errors->any())
    <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 12px; padding: 14px 18px; color: #991b1b; font-size: 13.5px;">
      <div style="font-weight: 700; margin-bottom: 4px; display: flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-triangle-exclamation" style="color: #ef4444;"></i> Action Encountered Validation Errors:
      </div>
      <ul style="margin: 0; padding-left: 20px;">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

    @if(isset($pendingRequests) && $pendingRequests->count() > 0)
    <div style="background: rgba(245, 158, 11, 0.08); border: 1.5px solid rgba(245, 158, 11, 0.35); border-radius: 14px; padding: 16px 20px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px; margin-bottom: 20px;">
      <div style="display: flex; align-items: center; gap: 12px;">
        <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(245, 158, 11, 0.2); color: #f59e0b; display: grid; place-items: center; font-size: 18px;">
          <i class="fa-solid fa-bell"></i>
        </div>
        <h4 style="font-size: 14.5px; font-weight: 800; color: #fbbf24; margin: 0;">
          {{ $pendingRequests->count() }} Pending Admin Request(s)
        </h4>
      </div>
      <button type="button" onclick="switchAdminTab('requests')" class="btn-tactical" style="background: #f59e0b; color: #000000; font-weight: 700; padding: 8px 16px; font-size: 12.5px; border-radius: 8px; border: none; cursor: pointer;">
        <i class="fa-solid fa-user-check"></i> Review Requests
      </button>
    </div>
  @endif

  <!-- Key Statistics Grid -->
  <div class="admin-control-grid">
    <div class="stat-card" style="background: #0f121a; border: 1px solid rgba(255,255,255,0.06); box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
      <div class="stat-icon" style="background: rgba(239, 68, 68, 0.12); color: #ef4444;">
        <i class="fa-solid fa-user-shield"></i>
      </div>
      <div>
        <div class="stat-label" style="color: #94a3b8;">Total Admins</div>
        <div class="stat-value" style="color: #ffffff;">{{ $stats['total_admins'] }}</div>
      </div>
    </div>

    <div class="stat-card" style="background: #0f121a; border: 1px solid rgba(255,255,255,0.06); box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
      <div class="stat-icon" style="background: rgba(245, 158, 11, 0.12); color: #f59e0b;">
        <i class="fa-solid fa-crown"></i>
      </div>
      <div>
        <div class="stat-label" style="color: #94a3b8;">Super Admin</div>
        <div class="stat-value" style="color: #ffffff;">{{ $stats['super_admins'] }}</div>
      </div>
    </div>

    <div class="stat-card" style="background: #0f121a; border: 1px solid rgba(255,255,255,0.06); box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
      <div class="stat-icon" style="background: rgba(99, 102, 241, 0.12); color: #6366f1;">
        <i class="fa-solid fa-award"></i>
      </div>
      <div>
        <div class="stat-label" style="color: #94a3b8;">Pro Admin</div>
        <div class="stat-value" style="color: #ffffff;">{{ $stats['pro_admins'] ?? 0 }}</div>
      </div>
    </div>
    
    <div class="stat-card" style="background: #0f121a; border: 1px solid rgba(255,255,255,0.06); box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
      <div class="stat-icon" style="background: rgba(14, 165, 233, 0.12); color: #0ea5e9;">
        <i class="fa-solid fa-user-shield"></i>
      </div>
      <div>
        <div class="stat-label" style="color: #94a3b8;">Normal Admin</div>
        <div class="stat-value" style="color: #ffffff;">{{ $stats['standard_admins'] }}</div>
      </div>
    </div>

    <div class="stat-card" style="background: #0f121a; border: 1px solid rgba(255,255,255,0.06); box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
      <div class="stat-icon" style="background: rgba(16, 185, 129, 0.12); color: #10b981;">
        <i class="fa-solid fa-vault"></i>
      </div>
      <div>
        <div class="stat-label" style="color: #94a3b8;">Account Manager</div>
        <div class="stat-value" style="color: #ffffff;">{{ $stats['finance_managers'] }}</div>
      </div>
    </div>

    <div class="stat-card" style="background: #0f121a; border: 1px solid {{ $stats['pending_requests'] > 0 ? 'rgba(245, 158, 11, 0.4)' : 'rgba(255,255,255,0.06)' }}; box-shadow: 0 4px 12px rgba(0,0,0,0.2); cursor: pointer;" onclick="switchAdminTab('requests')">
      <div class="stat-icon" style="background: rgba(245, 158, 11, 0.15); color: #f59e0b;">
        <i class="fa-solid fa-user-clock"></i>
      </div>
      <div>
        <div class="stat-label" style="color: #94a3b8;">Pending Admin</div>
        <div class="stat-value" style="color: {{ $stats['pending_requests'] > 0 ? '#fbbf24' : '#ffffff' }}; display: flex; align-items: center; gap: 8px;">
          {{ $stats['pending_requests'] }}
          @if($stats['pending_requests'] > 0)
            <span style="font-size: 10px; background: #f59e0b; color: #000; padding: 2px 6px; border-radius: 4px; font-weight: 800;">PENDING</span>
          @endif
        </div>
      </div>
    </div>
  </div>

  <!-- Tab Navigation -->
  <div style="display: flex; gap: 10px; margin-bottom: 16px; border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 12px;">
    <button type="button" id="tabBtnRoster" onclick="switchAdminTab('roster')" class="btn-tactical" style="background: rgba(255,87,87,0.15); border: 1.5px solid #ff5757; color: #ffffff; padding: 9px 18px; font-size: 13px; font-weight: 700; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
      <i class="fa-solid fa-users" style="color: #ff5757;"></i> Admin Account ({{ $admins->count() }})
    </button>
    <button type="button" id="tabBtnRequests" onclick="switchAdminTab('requests')" class="btn-tactical" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.1); color: #94a3b8; padding: 9px 18px; font-size: 13px; font-weight: 700; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
      <i class="fa-solid fa-user-clock" style="color: #f59e0b;"></i> Pending Admin ({{ $pendingRequests->count() }})
      @if($pendingRequests->count() > 0)
        <span style="background: #f59e0b; color: #000; font-size: 10px; padding: 1px 6px; border-radius: 10px; font-weight: 800;">{{ $pendingRequests->count() }}</span>
      @endif
    </button>
  </div>

  <!-- SECTION 1: Admin Accounts -->
  <div id="rosterSection" class="tactical-card" style="padding: 0; overflow: hidden; margin-bottom: 24px;">
    <div style="padding: 20px 24px; border-bottom: 1px solid var(--border-soft); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px;">
      <div>
        <h3 style="font-size: 16px; font-weight: 800; color: #ffffff; margin: 0; display: flex; align-items: center; gap: 8px;">
          <i class="fa-solid fa-users-gear" style="color: #ef4444;"></i> Admin Accounts
        </h3>
      </div>

      <!-- Search & Filters -->
      <form method="GET" action="{{ route('admin.admin_control.index') }}" style="display: flex; align-items: center; gap: 10px;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, ID, phone..." class="input-tactical" style="width: 220px; font-size: 12.5px; padding: 7px 12px;">
        <select name="role" class="input-tactical" style="width: 155px; font-size: 12.5px; padding: 7px 10px;" onchange="this.form.submit()">
          <option value="">All Roles</option>
          <option value="super_admin" {{ request('role') === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
          <option value="pro_admin" {{ request('role') === 'pro_admin' ? 'selected' : '' }}>Pro Admin</option>
          <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Normal Admin</option>
          <option value="finance_manager" {{ request('role') === 'finance_manager' ? 'selected' : '' }}>Account Manager</option>
        </select>
        <button type="submit" class="btn-tactical btn-tactical-outline" style="padding: 7px 14px; font-size: 12.5px;">
          <i class="fa-solid fa-magnifying-glass"></i>
        </button>
      </form>
    </div>

    <!-- Table -->
    <div style="overflow-x: auto;">
      <table class="table-tactical" style="width: 100%; border-collapse: collapse;">
        <thead>
          <tr style="background: rgba(255,255,255,0.03); border-bottom: 1px solid rgba(255,255,255,0.06);">
            <th style="padding: 14px 20px; text-align: left; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Administrator</th>
            <th style="padding: 14px 20px; text-align: left; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Identity</th>
            <th style="padding: 14px 20px; text-align: left; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Registered Phone</th>
            <th style="padding: 14px 20px; text-align: left; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Authority / Role</th>
            <th style="padding: 14px 20px; text-align: right; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Management Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($admins as $adm)
            @php
              $isDev = ($adm->isDeveloperAdmin() || strtolower(trim($adm->account_id ?? '')) === 'argharoy' || $adm->id === 1);
              $userPerms = $adm->permissions ?? [];

              if ($isDev) {
                  $roleName = 'Developer';
                  $roleGradient = 'linear-gradient(135deg, #ef4444 0%, #991b1b 100%)';
                  $roleBorder = 'rgba(239, 68, 68, 0.5)';
                  $roleShadow = '0 4px 14px rgba(239, 68, 68, 0.4)';
                  $roleColor = '#f87171';
                  $roleBg = 'rgba(239, 68, 68, 0.15)';
                  $roleIcon = 'fa-solid fa-code';
              } elseif ($adm->role === 'super_admin') {
                  $roleName = 'Super Admin';
                  $roleGradient = 'linear-gradient(135deg, #f59e0b 0%, #b45309 100%)';
                  $roleBorder = 'rgba(245, 158, 11, 0.5)';
                  $roleShadow = '0 4px 14px rgba(245, 158, 11, 0.35)';
                  $roleColor = '#fbbf24';
                  $roleBg = 'rgba(245, 158, 11, 0.15)';
                  $roleIcon = 'fa-solid fa-crown';
              } elseif ($adm->role === 'pro_admin') {
                  $roleName = 'Pro Admin';
                  $roleGradient = 'linear-gradient(135deg, #6366f1 0%, #4338ca 100%)';
                  $roleBorder = 'rgba(99, 102, 241, 0.5)';
                  $roleShadow = '0 4px 14px rgba(99, 102, 241, 0.35)';
                  $roleColor = '#a5b4fc';
                  $roleBg = 'rgba(99, 102, 241, 0.15)';
                  $roleIcon = 'fa-solid fa-award';
              } elseif ($adm->role === 'admin') {
                  $roleName = 'Normal Admin';
                  $roleGradient = 'linear-gradient(135deg, #0284c7 0%, #0369a1 100%)';
                  $roleBorder = 'rgba(2, 132, 199, 0.5)';
                  $roleShadow = '0 4px 14px rgba(2, 132, 199, 0.35)';
                  $roleColor = '#38bdf8';
                  $roleBg = 'rgba(2, 132, 199, 0.15)';
                  $roleIcon = 'fa-solid fa-user-shield';
              } else {
                  $roleName = 'Account Manager';
                  $roleGradient = 'linear-gradient(135deg, #10b981 0%, #047857 100%)';
                  $roleBorder = 'rgba(16, 185, 129, 0.5)';
                  $roleShadow = '0 4px 14px rgba(16, 185, 129, 0.35)';
                  $roleColor = '#34d399';
                  $roleBg = 'rgba(16, 185, 129, 0.15)';
                  $roleIcon = 'fa-solid fa-vault';
              }
            @endphp
            <tr style="border-bottom: 1px solid rgba(255,255,255,0.05); transition: background 0.15s;" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background='transparent'">
              <!-- Name & Email with Dedicated Role Logo -->
              <td style="padding: 16px 20px;">
                <div style="display: flex; align-items: center; gap: 14px;">
                  @if(!empty($adm->avatar))
                    <div style="position: relative; width: 42px; height: 42px; flex-shrink: 0;">
                      <img src="{{ asset($adm->avatar) }}" alt="{{ $adm->name }}" style="width: 42px; height: 42px; border-radius: 12px; object-fit: cover; border: 1.5px solid {{ $roleBorder }};">
                      <div style="position: absolute; bottom: -3px; right: -3px; width: 20px; height: 20px; border-radius: 6px; background: {{ $roleGradient }}; display: grid; place-items: center; border: 2px solid #0f172a; font-size: 9px; color: #ffffff; box-shadow: {{ $roleShadow }};" title="{{ $roleName }}">
                        <i class="{{ $roleIcon }}"></i>
                      </div>
                    </div>
                  @else
                    <div style="width: 42px; height: 42px; border-radius: 12px; background: {{ $roleGradient }}; border: 1.5px solid {{ $roleBorder }}; color: #ffffff; display: grid; place-items: center; font-size: 17px; flex-shrink: 0; box-shadow: {{ $roleShadow }};" title="{{ $roleName }}">
                      <i class="{{ $roleIcon }}"></i>
                    </div>
                  @endif
                  <div>
                    <div style="font-size: 14.5px; font-weight: 700; color: #ffffff; display: flex; align-items: center; gap: 6px; white-space: nowrap;">
                      <span>{{ $adm->name }}</span>
                      @if($isDev)
                        <span style="background: rgba(239,68,68,0.2); border: 1px solid rgba(239,68,68,0.45); color: #f87171; font-size: 8.5px; font-weight: 800; padding: 1.5px 5.5px; border-radius: 4px; display: inline-flex; align-items: center; gap: 3.5px; letter-spacing: 0.5px; vertical-align: middle;">
                          <i class="fa-solid fa-code" style="font-size: 7.5px;"></i> DEV
                        </span>
                      @endif
                    </div>
                    <div style="font-size: 12px; color: #64748b;">{{ $adm->email }}</div>
                  </div>
                </div>
              </td>

              <!-- Identity -->
              <td style="padding: 16px 20px;">
                <code style="background: rgba(255,255,255,0.05); color: #ffffff; padding: 3px 8px; border-radius: 5px; font-size: 12.5px; font-weight: 700; border: 1px solid rgba(255,255,255,0.1);">
                  {{ $adm->account_id }}
                </code>
              </td>

              <!-- Phone -->
              <td style="padding: 16px 20px;">
                <div style="display: flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 600; color: #cbd5e1;">
                  <i class="fa-solid fa-phone" style="color: #059669; font-size: 11px;"></i>
                  <span>{{ $adm->phone ?? 'Not Registered' }}</span>
                </div>
              </td>

              <!-- Authority / Role with Graphic Icon & Badge -->
              <td style="padding: 16px 20px;">
                <span class="stat-badge-chip" style="background: {{ $roleBg }}; color: {{ $roleColor }}; border: 1px solid {{ $roleBorder }}; font-weight: 700; display: inline-flex; align-items: center; gap: 7px; padding: 5px 12px; border-radius: 6px; font-size: 12.5px;">
                  <i class="{{ $roleIcon }}" style="font-size: 12px;"></i> {{ $roleName }}
                </span>
                @if($adm->role === 'pro_admin')
                  <div style="margin-top: 5px;">
                    @if($adm->canAccessCadetPasswords())
                      <span style="font-size: 10.5px; color: #818cf8; background: rgba(99,102,241,0.15); border: 1px solid rgba(99,102,241,0.3); padding: 2px 7px; border-radius: 4px; display: inline-flex; align-items: center; gap: 4px; font-weight: 700;">
                        <i class="fa-solid fa-key" style="font-size: 9px;"></i> Cadet Passwords: Yes
                      </span>
                    @else
                      <span style="font-size: 10.5px; color: #94a3b8; background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); padding: 2px 7px; border-radius: 4px; display: inline-flex; align-items: center; gap: 4px; font-weight: 600;">
                        <i class="fa-solid fa-lock" style="font-size: 9px;"></i> Cadet Passwords: No
                      </span>
                    @endif
                  </div>
                @endif
              </td>

              <!-- Actions -->
              <td style="padding: 16px 20px; text-align: right;">
                <div style="display: flex; align-items: center; justify-content: flex-end; gap: 8px;">
                  @php
                    $currentUser = auth()->user();
                    $isTargetDev = $isDev;
                    $canEditTarget = true;
                    $lockTitle = '';

                    // Rule 1: No super admin can edit the details of a developer (except developer himself)
                    if ($isTargetDev && !$currentUser->isDeveloperAdmin()) {
                        $canEditTarget = false;
                        $lockTitle = 'Developer account details cannot be edited by any other administrator.';
                    }
                    // Rule 2: No one can edit the details of a super admin except a super admin
                    elseif ($adm->role === 'super_admin' && $currentUser->role !== 'super_admin') {
                        $canEditTarget = false;
                        $lockTitle = 'Only a Super Administrator can edit a Super Administrator account.';
                    }
                  @endphp

                  @if($canEditTarget)
                    <!-- Edit Profile Button (Full Page) -->
                    <a href="{{ route('admin.admin_control.edit', $adm->id) }}" class="btn-tactical btn-tactical-outline" style="padding: 6px 14px; font-size: 12px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; border-radius: 6px;">
                      <i class="fa-solid fa-user-pen"></i> Edit Profile
                    </a>
                  @else
                    <!-- Locked / Protected Indicator -->
                    <span class="btn-tactical" style="padding: 6px 12px; font-size: 12px; opacity: 0.45; cursor: not-allowed; background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); color: #94a3b8; border-radius: 6px; display: inline-flex; align-items: center; gap: 6px;" title="{{ $lockTitle }}">
                      <i class="fa-solid fa-lock"></i> Locked
                    </span>
                  @endif

                  <!-- Remove Button -->
                  @if($isDev || $adm->id === auth()->id() || ($adm->role === 'super_admin' && $currentUser->role !== 'super_admin'))
                    <button type="button" class="btn-tactical" disabled style="padding: 6px 12px; font-size: 12px; opacity: 0.4; cursor: not-allowed; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: #94a3b8; border-radius: 6px;" title="{{ $isDev ? 'Developer account cannot be removed' : ($adm->id === auth()->id() ? 'Current account' : 'Only Super Administrators can remove a Super Administrator') }}">
                      <i class="fa-solid fa-lock"></i> Protected
                    </button>
                  @else
                    <button type="button" class="btn-tactical btn-tactical-danger" style="padding: 6px 12px; font-size: 12px; border-radius: 6px;"
                            onclick="confirmDeleteAdmin({{ $adm->id }}, '{{ addslashes($adm->name) }}', '{{ addslashes($adm->account_id) }}')">
                      <i class="fa-solid fa-trash-can"></i> Remove
                    </button>
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" style="padding: 36px; text-align: center; color: #64748b;">
                No administrators match your current filter.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <!-- SECTION 2: Account Creation Requests Deck -->
  <div id="requestsSection" class="tactical-card" style="padding: 0; overflow: hidden; display: none; margin-bottom: 24px;">
    <div style="padding: 20px 24px; border-bottom: 1px solid var(--border-soft); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px;">
      <div>
        <h3 style="font-size: 16px; font-weight: 800; color: #fbbf24; margin: 0; display: flex; align-items: center; gap: 8px;">
          <i class="fa-solid fa-user-clock" style="color: #f59e0b;"></i> Pending Admin
        </h3>
      </div>
      <div>
        <span style="font-size: 12px; font-weight: 700; color: #f59e0b; background: rgba(245,158,11,0.12); border: 1px solid rgba(245,158,11,0.3); padding: 5px 12px; border-radius: 6px;">
          {{ $pendingRequests->count() }} Pending
        </span>
      </div>
    </div>

    <div style="overflow-x: auto;">
      <table class="table-tactical" style="width: 100%; border-collapse: collapse;">
        <thead>
          <tr style="background: rgba(255,255,255,0.03); border-bottom: 1px solid rgba(255,255,255,0.06);">
            <th style="padding: 14px 20px; text-align: left; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase;">Applicant Name</th>
            <th style="padding: 14px 20px; text-align: left; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase;">Requested Identity</th>
            <th style="padding: 14px 20px; text-align: left; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase;">Phone Number</th>
            <th style="padding: 14px 20px; text-align: left; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase;">Submission Date</th>
            <th style="padding: 14px 20px; text-align: right; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase;">Super Admin Decision</th>
          </tr>
        </thead>
        <tbody>
          @forelse($pendingRequests as $req)
            <tr style="border-bottom: 1px solid rgba(255,255,255,0.05); transition: background 0.15s;" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background='transparent'">
              <td style="padding: 16px 20px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                  <div style="width: 38px; height: 38px; border-radius: 10px; background: linear-gradient(135deg, rgba(245,158,11,0.2), rgba(217,119,6,0.2)); border: 1px solid rgba(245,158,11,0.35); color: #fbbf24; display: grid; place-items: center; font-size: 14px; box-shadow: 0 4px 12px rgba(245,158,11,0.15);" title="Pending Admin">
                    <i class="fa-solid fa-user-clock"></i>
                  </div>
                  <div>
                    <div style="font-size: 14px; font-weight: 700; color: #ffffff;">{{ $req->name }}</div>
                    <div style="font-size: 12px; color: #94a3b8;">{{ $req->email }}</div>
                  </div>
                </div>
              </td>
              <td style="padding: 16px 20px;">
                <code style="background: rgba(255,255,255,0.05); color: #ffffff; padding: 3px 8px; border-radius: 5px; font-size: 12.5px; font-weight: 700; border: 1px solid rgba(255,255,255,0.1);">
                  {{ $req->account_id }}
                </code>
              </td>
              <td style="padding: 16px 20px;">
                <span style="font-size: 13px; color: #cbd5e1; font-weight: 600;">{{ $req->phone }}</span>
              </td>
              <td style="padding: 16px 20px; font-size: 12px; color: #94a3b8;">
                {{ $req->created_at->format('d M Y, h:i A') }} ({{ $req->created_at->diffForHumans() }})
              </td>
              <td style="padding: 16px 20px; text-align: right;">
                <div style="display: flex; align-items: center; justify-content: flex-end; gap: 8px;">
                  <!-- Approve / Permit Form -->
                  <form method="POST" action="{{ route('admin.admin_control.approve', $req->id) }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn-tactical" style="background: #10b981; color: #ffffff; padding: 7px 14px; font-size: 12px; font-weight: 700; border: none; border-radius: 6px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                      <i class="fa-solid fa-check"></i> Permit Request
                    </button>
                  </form>

                  <!-- Edit Profile to Change Any Details -->
                  <a href="{{ route('admin.admin_control.edit', $req->id) }}" class="btn-tactical btn-tactical-outline" style="padding: 7px 14px; font-size: 12px; text-decoration: none; border-radius: 6px; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-user-pen"></i> Edit Profile
                  </a>

                  <!-- Reject Form -->
                  <form method="POST" action="{{ route('admin.admin_control.reject', $req->id) }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to reject and remove this account request?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-tactical btn-tactical-danger" style="padding: 7px 12px; font-size: 12px; border-radius: 6px; cursor: pointer;">
                      <i class="fa-solid fa-xmark"></i> Reject
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" style="padding: 40px; text-align: center; color: #64748b;">
                <i class="fa-solid fa-clipboard-check" style="font-size: 28px; color: #475569; display: block; margin-bottom: 8px;"></i>
                No pending administrator account creation requests at this time.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>

<!-- =============================================================== -->
<!-- ADD NEW ADMINISTRATOR MODAL                                     -->
<!-- =============================================================== -->
<div id="addAdminModal" class="modal-overlay">
  <div class="tactical-modal-card">
    <div class="tactical-modal-header">
      <div style="display: flex; align-items: center; gap: 10px;">
        <div style="width: 36px; height: 36px; border-radius: 8px; background: rgba(239, 68, 68, 0.15); color: #ef4444; display: grid; place-items: center; font-size: 16px;">
          <i class="fa-solid fa-user-plus"></i>
        </div>
        <div>
          <h3 style="font-size: 16px; font-weight: 800; color: #0f172a; margin: 0;">Add New Administrator</h3>
        </div>
      </div>
      <button type="button" onclick="closeModal('addAdminModal')" style="background: none; border: none; font-size: 18px; color: #94a3b8; cursor: pointer;">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>

    <form method="POST" action="{{ route('admin.admin_control.store') }}">
      @csrf
      <div class="tactical-modal-body">
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
          <div>
            <label class="form-label" style="display: block; font-size: 11.5px; font-weight: 700; color: #475569; margin-bottom: 6px;">
              Admin Full Name *
            </label>
            <input type="text" name="name" required placeholder="e.g. Major Tareq Rahman" class="input-tactical" style="width: 100%;">
          </div>

          <div>
            <label class="form-label" style="display: block; font-size: 11.5px; font-weight: 700; color: #475569; margin-bottom: 6px;">
              Identity *
            </label>
            <input type="text" name="account_id" required placeholder="e.g. ADM-005 or TareqAdmin" class="input-tactical" style="width: 100%;">
          </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
          <div>
            <label class="form-label" style="display: block; font-size: 11.5px; font-weight: 700; color: #475569; margin-bottom: 6px;">
              Official Email Address *
            </label>
            <input type="email" name="email" required placeholder="e.g. tareq@ida.com" class="input-tactical" style="width: 100%;">
          </div>

          <div>
            <label class="form-label" style="display: block; font-size: 11.5px; font-weight: 700; color: #475569; margin-bottom: 6px;">
              Registered Phone Number *
            </label>
            <input type="text" name="phone" required placeholder="e.g. 01711002233" class="input-tactical" style="width: 100%;">
          </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
          <div>
            <label class="form-label" style="display: block; font-size: 11.5px; font-weight: 700; color: #475569; margin-bottom: 6px;">
              Initial Password *
            </label>
            <div style="position: relative;">
              <input type="password" id="add_admin_password" name="password" required minlength="6" autocomplete="new-password" value="" placeholder="Min 6 characters" class="input-tactical" style="width: 100%; padding-right: 40px;">
              <button type="button" onclick="togglePasswordVisibility('add_admin_password', this)" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #64748b; cursor: pointer;">
                <i class="fa-solid fa-eye"></i>
              </button>
            </div>
          </div>

          <div>
            <label class="form-label" style="display: block; font-size: 11.5px; font-weight: 700; color: #475569; margin-bottom: 6px;">
              Account Role *
            </label>
            <select name="role" id="add_admin_role" required onchange="toggleAddProAdminPw(this.value)" class="input-tactical" style="width: 100%;">
              <option value="super_admin">Super Admin</option>
              <option value="pro_admin">Pro Admin</option>
              <option value="admin">Normal Admin</option>
              <option value="finance_manager">Account Manager</option>
            </select>
          </div>
        </div>

        <div id="addProAdminPwCard" style="display: none; background: rgba(99, 102, 241, 0.08); border: 1.5px solid rgba(99, 102, 241, 0.35); border-radius: 10px; padding: 12px 16px; margin-bottom: 16px;">
          <div style="font-size: 12px; font-weight: 800; color: #a5b4fc; margin-bottom: 8px; display: flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-key"></i> Cadet Password Access (Pro Admin)
          </div>
          <div style="display: flex; gap: 18px;">
            <label style="display: inline-flex; align-items: center; gap: 6px; cursor: pointer; font-size: 12.5px; color: #ffffff; font-weight: 700;">
              <input type="radio" name="can_access_cadet_passwords_opt" value="1" style="accent-color: #6366f1; width: 15px; height: 15px;">
              <span>Yes (Can Access)</span>
            </label>
            <label style="display: inline-flex; align-items: center; gap: 6px; cursor: pointer; font-size: 12.5px; color: #94a3b8; font-weight: 600;">
              <input type="radio" name="can_access_cadet_passwords_opt" value="0" checked style="accent-color: #6366f1; width: 15px; height: 15px;">
              <span>No (Restricted)</span>
            </label>
          </div>
        </div>

        <!-- Permissions Checkboxes -->
        <div style="background: #f8fafc; border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 16px;">
          <label style="display: block; font-size: 12px; font-weight: 800; color: #0f172a; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px;">
            <i class="fa-solid fa-list-check" style="color: #ef4444; margin-right: 6px;"></i> Module Permissions
          </label>
          <div style="display: flex; flex-direction: column; gap: 10px;">
            @foreach($availablePermissions as $permKey => $pData)
              <label style="display: flex; align-items: flex-start; gap: 10px; cursor: pointer; user-select: none;">
                <input type="checkbox" name="permissions[]" value="{{ $permKey }}" style="margin-top: 3px; accent-color: #ef4444; width: 16px; height: 16px;">
                <div>
                  <div style="font-size: 13px; font-weight: 700; color: #1e293b;">{{ $pData['label'] }}</div>
                  <div style="font-size: 11.5px; color: #64748b;">{{ $pData['description'] }}</div>
                </div>
              </label>
            @endforeach
          </div>
        </div>

      </div>
      <div class="tactical-modal-footer">
        <button type="button" class="btn-tactical btn-tactical-outline" onclick="closeModal('addAdminModal')">Cancel</button>
        <button type="submit" class="btn-tactical btn-tactical-primary">
          <i class="fa-solid fa-plus"></i> Initialize Administrator
        </button>
      </div>
    </form>
  </div>
</div>

<!-- =============================================================== -->
<!-- EDIT ADMINISTRATOR PERMISSIONS & DETAILS MODAL                  -->
<!-- =============================================================== -->
<div id="editAdminModal" class="modal-overlay">
  <div class="tactical-modal-card">
    <div class="tactical-modal-header">
      <div style="display: flex; align-items: center; gap: 10px;">
        <div style="width: 36px; height: 36px; border-radius: 8px; background: rgba(59, 130, 246, 0.15); color: #2563eb; display: grid; place-items: center; font-size: 16px;">
          <i class="fa-solid fa-user-pen"></i>
        </div>
        <div>
          <h3 style="font-size: 16px; font-weight: 800; color: #0f172a; margin: 0;">Edit Admin Clearance & Permissions</h3>
        </div>
      </div>
      <button type="button" onclick="closeModal('editAdminModal')" style="background: none; border: none; font-size: 18px; color: #94a3b8; cursor: pointer;">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>

    <form id="editAdminForm" method="POST" action="">
      @csrf
      <div class="tactical-modal-body">
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
          <div>
            <label class="form-label" style="display: block; font-size: 11.5px; font-weight: 700; color: #475569; margin-bottom: 6px;">
              Admin Full Name *
            </label>
            <input type="text" name="name" id="edit_name" required class="input-tactical" style="width: 100%;">
          </div>

          <div>
            <label class="form-label" style="display: block; font-size: 11.5px; font-weight: 700; color: #475569; margin-bottom: 6px;">
              Identity *
            </label>
            <input type="text" name="account_id" id="edit_account_id" required class="input-tactical" style="width: 100%;">
          </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
          <div>
            <label class="form-label" style="display: block; font-size: 11.5px; font-weight: 700; color: #475569; margin-bottom: 6px;">
              Official Email Address *
            </label>
            <input type="email" name="email" id="edit_email" required class="input-tactical" style="width: 100%;">
          </div>

          <div>
            <label class="form-label" style="display: block; font-size: 11.5px; font-weight: 700; color: #475569; margin-bottom: 6px;">
              Registered Phone Number *
            </label>
            <input type="text" name="phone" id="edit_phone" required class="input-tactical" style="width: 100%;">
          </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
          <div>
            <label class="form-label" style="display: block; font-size: 11.5px; font-weight: 700; color: #475569; margin-bottom: 6px;">
              Reset Security Password (leave blank to keep)
            </label>
            <input type="password" name="password" id="modal_edit_password" value="" autocomplete="new-password" minlength="8" placeholder="Enter new password if changing" class="input-tactical" style="width: 100%;">
          </div>

          <div>
            <label class="form-label" style="display: block; font-size: 11.5px; font-weight: 700; color: #475569; margin-bottom: 6px;">
              Account Role *
            </label>
            <select name="role" id="edit_role" required onchange="toggleEditProAdminPw(this.value)" class="input-tactical" style="width: 100%;">
              <option value="super_admin">Super Admin</option>
              <option value="pro_admin">Pro Admin</option>
              <option value="admin">Normal Admin</option>
              <option value="finance_manager">Account Manager</option>
            </select>
          </div>
        </div>

        <div id="editProAdminPwCard" style="display: none; background: rgba(99, 102, 241, 0.08); border: 1.5px solid rgba(99, 102, 241, 0.35); border-radius: 10px; padding: 12px 16px; margin-bottom: 16px;">
          <div style="font-size: 12px; font-weight: 800; color: #a5b4fc; margin-bottom: 8px; display: flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-key"></i> Cadet Password Access (Pro Admin)
          </div>
          <div style="display: flex; gap: 18px;">
            <label style="display: inline-flex; align-items: center; gap: 6px; cursor: pointer; font-size: 12.5px; color: #ffffff; font-weight: 700;">
              <input type="radio" name="can_access_cadet_passwords_opt" id="edit_pw_opt_yes" value="1" style="accent-color: #6366f1; width: 15px; height: 15px;">
              <span>Yes (Can Access)</span>
            </label>
            <label style="display: inline-flex; align-items: center; gap: 6px; cursor: pointer; font-size: 12.5px; color: #94a3b8; font-weight: 600;">
              <input type="radio" name="can_access_cadet_passwords_opt" id="edit_pw_opt_no" value="0" checked style="accent-color: #6366f1; width: 15px; height: 15px;">
              <span>No (Restricted)</span>
            </label>
          </div>
        </div>

        <!-- Permissions Checkboxes -->
        <div style="background: #f8fafc; border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 16px;">
          <label style="display: block; font-size: 12px; font-weight: 800; color: #0f172a; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px;">
            <i class="fa-solid fa-list-check" style="color: #ef4444; margin-right: 6px;"></i> Module Permissions
          </label>
          <div style="display: flex; flex-direction: column; gap: 10px;">
            @foreach($availablePermissions as $permKey => $pData)
              <label style="display: flex; align-items: flex-start; gap: 10px; cursor: pointer; user-select: none;">
                <input type="checkbox" name="permissions[]" value="{{ $permKey }}" id="edit_perm_{{ $permKey }}" style="margin-top: 3px; accent-color: #ef4444; width: 16px; height: 16px;">
                <div>
                  <div style="font-size: 13px; font-weight: 700; color: #1e293b;">{{ $pData['label'] }}</div>
                  <div style="font-size: 11.5px; color: #64748b;">{{ $pData['description'] }}</div>
                </div>
              </label>
            @endforeach
          </div>
        </div>

      </div>
      <div class="tactical-modal-footer">
        <button type="button" class="btn-tactical btn-tactical-outline" onclick="closeModal('editAdminModal')">Cancel</button>
        <button type="submit" class="btn-tactical btn-tactical-primary">
          <i class="fa-solid fa-save"></i> Save Clearance Changes
        </button>
      </div>
    </form>
  </div>
</div>

<!-- =============================================================== -->
<!-- DELETE CONFIRMATION MODAL                                       -->
<!-- =============================================================== -->
<div id="deleteAdminModal" class="modal-overlay">
  <div class="tactical-modal-card" style="max-width: 480px;">
    <div class="tactical-modal-header" style="background: #fef2f2; border-bottom: 1px solid #fecaca;">
      <div style="display: flex; align-items: center; gap: 10px;">
        <div style="width: 36px; height: 36px; border-radius: 8px; background: rgba(239, 68, 68, 0.2); color: #ef4444; display: grid; place-items: center; font-size: 16px;">
          <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <h3 style="font-size: 16px; font-weight: 800; color: #991b1b; margin: 0;">Revoke Admin Account</h3>
      </div>
      <button type="button" onclick="closeModal('deleteAdminModal')" style="background: none; border: none; font-size: 18px; color: #94a3b8; cursor: pointer;">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>

    <form id="deleteAdminForm" method="POST" action="">
      @csrf
      @method('DELETE')
      <div class="tactical-modal-body" style="text-align: center; padding: 28px 24px;">
        <p style="font-size: 14px; color: #334155; margin-bottom: 14px; line-height: 1.5;">
          Are you sure you want to permanently revoke administrative access for:
        </p>
        <div style="background: #f8fafc; border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; padding: 14px; margin-bottom: 16px;">
          <strong style="font-size: 16px; color: #0f172a;" id="delAdminName"></strong><br>
          <span style="font-size: 12.5px; color: #64748b;" id="delAdminId"></span>
        </div>
        <p style="font-size: 12px; color: #ef4444; margin: 0;">
          <i class="fa-solid fa-circle-exclamation"></i> This administrator will no longer be able to authenticate or access the Command Deck.
        </p>
      </div>
      <div class="tactical-modal-footer">
        <button type="button" class="btn-tactical btn-tactical-outline" onclick="closeModal('deleteAdminModal')">Cancel</button>
        <button type="submit" class="btn-tactical btn-tactical-danger">
          <i class="fa-solid fa-trash-can"></i> Permanently Revoke Account
        </button>
      </div>
    </form>
  </div>
</div>

<script>
  function openAddAdminModal() {
    document.getElementById('addAdminModal').classList.add('active');
  }

  function toggleAddProAdminPw(role) {
    var card = document.getElementById('addProAdminPwCard');
    if (card) {
      card.style.display = (role === 'pro_admin') ? 'block' : 'none';
    }
  }

  function toggleEditProAdminPw(role) {
    var card = document.getElementById('editProAdminPwCard');
    if (card) {
      card.style.display = (role === 'pro_admin') ? 'block' : 'none';
    }
  }

  function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('active');
  }

  function togglePasswordVisibility(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
      input.type = 'text';
      icon.classList.remove('fa-eye');
      icon.classList.add('fa-eye-slash');
      icon.style.color = '#ef4444';
    } else {
      input.type = 'password';
      icon.classList.remove('fa-eye-slash');
      icon.classList.add('fa-eye');
      icon.style.color = '#64748b';
    }
  }

  function confirmDeleteAdmin(id, name, accountId) {
    document.getElementById('delAdminName').textContent = name;
    document.getElementById('delAdminId').textContent = 'Identity: ' + accountId;
    const form = document.getElementById('deleteAdminForm');
    form.action = "{{ route('admin.admin_control.index') }}/" + id;
    document.getElementById('deleteAdminModal').classList.add('active');
  }

  // Close modals when clicking outside
  window.onclick = function(event) {
    if (event.target.classList.contains('modal-overlay')) {
      event.target.classList.remove('active');
    }
  }
  function switchAdminTab(tab) {
    const rosterSec = document.getElementById('rosterSection');
    const reqSec = document.getElementById('requestsSection');
    const tabRoster = document.getElementById('tabBtnRoster');
    const tabReq = document.getElementById('tabBtnRequests');

    if (tab === 'requests') {
      rosterSec.style.display = 'none';
      reqSec.style.display = 'block';
      tabReq.style.background = 'rgba(245, 158, 11, 0.18)';
      tabReq.style.borderColor = '#f59e0b';
      tabReq.style.color = '#ffffff';

      tabRoster.style.background = 'rgba(255,255,255,0.04)';
      tabRoster.style.borderColor = 'rgba(255,255,255,0.1)';
      tabRoster.style.color = '#94a3b8';
    } else {
      rosterSec.style.display = 'block';
      reqSec.style.display = 'none';
      tabRoster.style.background = 'rgba(255,87,87,0.15)';
      tabRoster.style.borderColor = '#ff5757';
      tabRoster.style.color = '#ffffff';

      tabReq.style.background = 'rgba(255,255,255,0.04)';
      tabReq.style.borderColor = 'rgba(255,255,255,0.1)';
      tabReq.style.color = '#94a3b8';
    }
  }

  // Auto switch to requests tab if url has #requests or ?tab=requests
  if (window.location.hash === '#requests' || window.location.search.includes('tab=requests')) {
    switchAdminTab('requests');
  }
</script>
@endsection
