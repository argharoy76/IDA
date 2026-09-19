@extends('layouts.portal')

@section('title', 'Admin Control Command Center')
@section('page_title', 'Developer Admin Control Command Center')
@section('page_subtitle', 'Highest clearance authority management. Provision administrators, assign fine-grained operational permissions, and enforce 3-factor login credentials.')

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
    border: 1px solid #e2e8f0;
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
    border: 1px solid #e2e8f0;
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

  <!-- Developer Authority Banner -->
  <div style="background: linear-gradient(135deg, #1e1b4b 0%, #0f172a 100%); color: #ffffff; border-radius: 16px; padding: 22px 28px; border: 1px solid rgba(239, 68, 68, 0.4); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
    <div style="display: flex; align-items: center; gap: 16px;">
      <div style="width: 52px; height: 52px; border-radius: 14px; background: rgba(239, 68, 68, 0.2); border: 1.5px solid #ef4444; display: grid; place-items: center; font-size: 24px; color: #ef4444;">
        <i class="fa-solid fa-crown"></i>
      </div>
      <div>
        <div style="display: flex; align-items: center; gap: 10px;">
          <h2 style="font-size: 18px; font-weight: 800; color: #ffffff; margin: 0;">Master Developer Console &bull; ArghaRoy</h2>
          <span class="dev-crown-badge"><i class="fa-solid fa-shield-halved"></i> HIGHEST AUTHORITY</span>
        </div>
        <p style="font-size: 13px; color: #cbd5e1; margin: 4px 0 0 0;">
          Enforcing 3-Match Security: Username + Registered Phone + Password. Only your account has access to this Admin Control console.
        </p>
      </div>
    </div>
    <div style="display: flex; align-items: center; gap: 10px;">
      <span style="font-size: 12px; background: rgba(255,255,255,0.1); padding: 6px 14px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.15); color: #f8fafc;">
        <i class="fa-solid fa-user-shield" style="color: #ef4444; margin-right: 6px;"></i> Active Super Admin: <strong>{{ auth()->user()->name }}</strong>
      </span>
    </div>
  </div>

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

  <!-- Key Statistics Grid -->
  <div class="admin-control-grid">
    <div class="stat-card">
      <div class="stat-icon" style="background: rgba(239, 68, 68, 0.12); color: #ef4444;">
        <i class="fa-solid fa-user-shield"></i>
      </div>
      <div>
        <div class="stat-label">Total Officers / Admins</div>
        <div class="stat-value">{{ $stats['total_admins'] }}</div>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon" style="background: rgba(245, 158, 11, 0.12); color: #f59e0b;">
        <i class="fa-solid fa-crown"></i>
      </div>
      <div>
        <div class="stat-label">Super Administrators</div>
        <div class="stat-value">{{ $stats['super_admins'] }}</div>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon" style="background: rgba(59, 130, 246, 0.12); color: #3b82f6;">
        <i class="fa-solid fa-id-badge"></i>
      </div>
      <div>
        <div class="stat-label">Standard Admins</div>
        <div class="stat-value">{{ $stats['standard_admins'] }}</div>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon" style="background: rgba(16, 185, 129, 0.12); color: #10b981;">
        <i class="fa-solid fa-wallet"></i>
      </div>
      <div>
        <div class="stat-label">Finance Managers</div>
        <div class="stat-value">{{ $stats['finance_managers'] }}</div>
      </div>
    </div>
  </div>

  <!-- Administrators List Card -->
  <div class="tactical-card" style="padding: 0; overflow: hidden;">
    <div style="padding: 20px 24px; border-bottom: 1px solid var(--border-soft); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px;">
      <div>
        <h3 style="font-size: 16px; font-weight: 800; color: #0f172a; margin: 0 0 4px 0; display: flex; align-items: center; gap: 8px;">
          <i class="fa-solid fa-users-gear" style="color: #ef4444;"></i> Roster of Administrative Accounts
        </h3>
        <p style="font-size: 12.5px; color: #64748b; margin: 0;">
          All staff accounts configured to access the Command Deck. Each admin must match their login ID, registered phone number, and password to authenticate.
        </p>
      </div>

      <!-- Search & Filters -->
      <form method="GET" action="{{ route('admin.admin_control.index') }}" style="display: flex; align-items: center; gap: 10px;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, ID, phone..." class="input-tactical" style="width: 220px; font-size: 12.5px; padding: 7px 12px;">
        <select name="role" class="input-tactical" style="width: 140px; font-size: 12.5px; padding: 7px 10px;" onchange="this.form.submit()">
          <option value="">All Roles</option>
          <option value="super_admin" {{ request('role') === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
          <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
          <option value="finance_manager" {{ request('role') === 'finance_manager' ? 'selected' : '' }}>Finance Manager</option>
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
          <tr style="background: #f8fafc; border-bottom: 1.5px solid #e2e8f0;">
            <th style="padding: 14px 20px; text-align: left; font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px;">Administrator</th>
            <th style="padding: 14px 20px; text-align: left; font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px;">Officer / Login ID</th>
            <th style="padding: 14px 20px; text-align: left; font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px;">Registered Phone (Factor 2)</th>
            <th style="padding: 14px 20px; text-align: left; font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px;">Authority / Role</th>
            <th style="padding: 14px 20px; text-align: left; font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px;">Granted Permissions</th>
            <th style="padding: 14px 20px; text-align: right; font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px;">Management Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($admins as $adm)
            @php
              $isDev = $adm->isDeveloperAdmin();
              $userPerms = $adm->permissions ?? [];
            @endphp
            <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.15s;" onmouseover="this.style.background='#fbfcfd'" onmouseout="this.style.background='transparent'">
              <!-- Name & Email -->
              <td style="padding: 16px 20px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                  <div style="width: 40px; height: 40px; border-radius: 10px; background: {{ $isDev ? 'linear-gradient(135deg, #ef4444, #991b1b)' : '#eff6ff' }}; color: {{ $isDev ? '#ffffff' : '#3b82f6' }}; display: grid; place-items: center; font-weight: 800; font-size: 15px; flex-shrink: 0;">
                    @if($isDev)
                      <i class="fa-solid fa-crown" style="font-size: 14px;"></i>
                    @else
                      {{ strtoupper(substr($adm->name, 0, 1)) }}
                    @endif
                  </div>
                  <div>
                    <div style="font-size: 14px; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 6px;">
                      {{ $adm->name }}
                      @if($isDev)
                        <span class="dev-crown-badge">DEV MASTER</span>
                      @endif
                    </div>
                    <div style="font-size: 12px; color: #64748b;">{{ $adm->email }}</div>
                  </div>
                </div>
              </td>

              <!-- Account ID -->
              <td style="padding: 16px 20px;">
                <code style="background: #f1f5f9; color: #0f172a; padding: 3px 8px; border-radius: 5px; font-size: 12.5px; font-weight: 700; border: 1px solid #e2e8f0;">
                  {{ $adm->account_id }}
                </code>
              </td>

              <!-- Phone -->
              <td style="padding: 16px 20px;">
                <div style="display: flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 600; color: #334155;">
                  <i class="fa-solid fa-phone" style="color: #059669; font-size: 11px;"></i>
                  <span>{{ $adm->phone ?? 'Not Registered' }}</span>
                </div>
              </td>

              <!-- Role -->
              <td style="padding: 16px 20px;">
                @if($adm->role === 'super_admin')
                  <span class="stat-badge-chip" style="background: #fee2e2; color: #991b1b; border: 1px solid #fecaca;">
                    <i class="fa-solid fa-shield-halved"></i> Super Admin
                  </span>
                @elseif($adm->role === 'admin')
                  <span class="stat-badge-chip" style="background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe;">
                    <i class="fa-solid fa-user-gear"></i> Admin
                  </span>
                @else
                  <span class="stat-badge-chip" style="background: #dcfce7; color: #166534; border: 1px solid #bbf7d0;">
                    <i class="fa-solid fa-calculator"></i> Finance Manager
                  </span>
                @endif
              </td>

              <!-- Permissions -->
              <td style="padding: 16px 20px;">
                @if($adm->role === 'super_admin')
                  <span class="perm-chip active" style="font-weight: 700; background: #ecfdf5; color: #047857; border-color: #a7f3d0;">
                    <i class="fa-solid fa-unlock-keyhole"></i> Full Command Clearance (All Modules)
                  </span>
                @elseif(empty($userPerms))
                  <span style="font-size: 12px; color: #94a3b8; font-style: italic;">No specific permissions granted</span>
                @else
                  <div style="display: flex; flex-wrap: wrap; gap: 4px; max-width: 280px;">
                    @foreach($userPerms as $p)
                      @if(isset($availablePermissions[$p]))
                        <span class="perm-chip active">
                          <i class="fa-solid fa-check" style="font-size: 9px;"></i> {{ $availablePermissions[$p]['label'] }}
                        </span>
                      @endif
                    @endforeach
                  </div>
                @endif
              </td>

              <!-- Actions -->
              <td style="padding: 16px 20px; text-align: right;">
                <div style="display: flex; align-items: center; justify-content: flex-end; gap: 8px;">
                  <!-- Edit Permissions Button -->
                  <button type="button" class="btn-tactical btn-tactical-outline" style="padding: 6px 12px; font-size: 12px;"
                          onclick="openEditModal({{ json_encode($adm) }})">
                    <i class="fa-solid fa-user-pen"></i> Permissions
                  </button>

                  <!-- Remove Button (Locked for Dev Admin) -->
                  @if($isDev || $adm->id === auth()->id())
                    <button type="button" class="btn-tactical" disabled style="padding: 6px 12px; font-size: 12px; opacity: 0.4; cursor: not-allowed; background: #f1f5f9; border: 1px solid #cbd5e1; color: #94a3b8;" title="Developer Super Admin cannot be deleted">
                      <i class="fa-solid fa-lock"></i> Protected
                    </button>
                  @else
                    <button type="button" class="btn-tactical btn-tactical-danger" style="padding: 6px 12px; font-size: 12px;"
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
          <span style="font-size: 11.5px; color: #64748b;">Grant administrative clearance and assign access rights</span>
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
              Officer / Login ID *
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
              Registered Phone Number (3-Match Rule) *
            </label>
            <input type="text" name="phone" required placeholder="e.g. 01711002233" class="input-tactical" style="width: 100%;">
            <span style="font-size: 10.5px; color: #059669; margin-top: 3px; display: block;">
              <i class="fa-solid fa-shield-halved"></i> Must match exactly when logging in.
            </span>
          </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
          <div>
            <label class="form-label" style="display: block; font-size: 11.5px; font-weight: 700; color: #475569; margin-bottom: 6px;">
              Initial Password *
            </label>
            <input type="password" name="password" required minlength="8" placeholder="Min 8 characters" class="input-tactical" style="width: 100%;">
          </div>

          <div>
            <label class="form-label" style="display: block; font-size: 11.5px; font-weight: 700; color: #475569; margin-bottom: 6px;">
              Account Role *
            </label>
            <select name="role" required class="input-tactical" style="width: 100%;">
              <option value="admin">Admin (Operational)</option>
              <option value="finance_manager">Finance Manager</option>
              <option value="super_admin">Super Admin</option>
            </select>
          </div>
        </div>

        <!-- Permissions Checkboxes -->
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px;">
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
          <span style="font-size: 11.5px; color: #64748b;" id="editModalSub">Update credentials, role, and granted operational permissions</span>
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
              Officer / Login ID *
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
              Registered Phone Number (3-Match Factor) *
            </label>
            <input type="text" name="phone" id="edit_phone" required class="input-tactical" style="width: 100%;">
          </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
          <div>
            <label class="form-label" style="display: block; font-size: 11.5px; font-weight: 700; color: #475569; margin-bottom: 6px;">
              Reset Security Password (leave blank to keep)
            </label>
            <input type="password" name="password" minlength="8" placeholder="Enter new password if changing" class="input-tactical" style="width: 100%;">
          </div>

          <div>
            <label class="form-label" style="display: block; font-size: 11.5px; font-weight: 700; color: #475569; margin-bottom: 6px;">
              Account Role *
            </label>
            <select name="role" id="edit_role" required class="input-tactical" style="width: 100%;">
              <option value="admin">Admin (Operational)</option>
              <option value="finance_manager">Finance Manager</option>
              <option value="super_admin">Super Admin</option>
            </select>
          </div>
        </div>

        <!-- Permissions Checkboxes -->
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px;">
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
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px; margin-bottom: 16px;">
          <strong style="font-size: 16px; color: #0f172a;" id="delAdminName"></strong><br>
          <span style="font-size: 12.5px; color: #64748b;" id="delAdminId"></span>
        </div>
        <p style="font-size: 12px; color: #ef4444; margin: 0;">
          <i class="fa-solid fa-circle-exclamation"></i> This officer will no longer be able to authenticate or access the Command Deck.
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

  function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('active');
  }

  function openEditModal(admin) {
    document.getElementById('edit_name').value = admin.name || '';
    document.getElementById('edit_account_id').value = admin.account_id || '';
    document.getElementById('edit_email').value = admin.email || '';
    document.getElementById('edit_phone').value = admin.phone || '';
    document.getElementById('edit_role').value = admin.role || 'admin';

    // Is this developer admin?
    const isDev = (admin.account_id === 'ArghaRoy' || admin.name === 'ArghaRoy' || admin.id === 1);
    if (isDev) {
      document.getElementById('edit_account_id').readOnly = true;
      document.getElementById('edit_role').disabled = true;
    } else {
      document.getElementById('edit_account_id').readOnly = false;
      document.getElementById('edit_role').disabled = false;
    }

    // Reset checkboxes
    const perms = admin.permissions || [];
    ['can_manage_cms', 'can_manage_exams', 'can_manage_students', 'can_manage_finance', 'can_view_audit'].forEach(p => {
      const cb = document.getElementById('edit_perm_' + p);
      if (cb) {
        cb.checked = perms.includes(p) || (admin.role === 'super_admin');
      }
    });

    const form = document.getElementById('editAdminForm');
    form.action = "{{ route('admin.admin_control.index') }}/" + admin.id + "/permissions";

    document.getElementById('editAdminModal').classList.add('active');
  }

  function confirmDeleteAdmin(id, name, accountId) {
    document.getElementById('delAdminName').textContent = name;
    document.getElementById('delAdminId').textContent = 'Account ID: ' + accountId;
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
</script>
@endsection
