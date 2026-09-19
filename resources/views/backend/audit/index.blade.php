@extends('layouts.portal')

@section('title', 'System Audit Logs')
@section('page_title', 'Security & System Audit Trail')
@section('page_subtitle', 'Chronological log of administrative actions, fee verification events, dossier notes, and security modifications')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

  <!-- Filter Bar -->
  <div class="tactical-card" style="padding: 16px;">
    <form action="{{ route('admin.audit.index') }}" method="GET" style="display: flex; gap: 14px; align-items: flex-end; flex-wrap: wrap;">
      <div style="width: 200px;">
        <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 6px;">Filter Action</label>
        <select name="action" class="form-tactical" style="height: 40px;">
          <option value="">All Actions</option>
          <option value="approved" {{ request('action') === 'approved' ? 'selected' : '' }}>Approved</option>
          <option value="rejected" {{ request('action') === 'rejected' ? 'selected' : '' }}>Rejected</option>
          <option value="created" {{ request('action') === 'created' ? 'selected' : '' }}>Created</option>
          <option value="updated" {{ request('action') === 'updated' ? 'selected' : '' }}>Updated</option>
          <option value="deleted" {{ request('action') === 'deleted' ? 'selected' : '' }}>Deleted</option>
        </select>
      </div>

      <button type="submit" class="btn-tactical btn-tactical-outline" style="height: 40px;">
        <i class="fa-solid fa-filter"></i> Apply Filter
      </button>

      @if(request()->filled('action'))
        <a href="{{ route('admin.audit.index') }}" class="btn-tactical" style="height: 40px; background: rgba(255,255,255,0.06); color: var(--text-muted);">
          <i class="fa-solid fa-xmark"></i> Clear
        </a>
      @endif
    </form>
  </div>

  <!-- Audit Table -->
  <div class="tactical-card" style="padding: 0; overflow: hidden;">
    <div style="padding: 16px 20px; border-bottom: 1px solid var(--border-soft); display: flex; justify-content: space-between; align-items: center;">
      <h3 style="font-size: 15px; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-shield-halved" style="color: var(--accent-gold);"></i> Audit Log Records
      </h3>
      <span style="font-size: 12px; color: var(--text-muted);">Total: {{ $logs->total() }} events</span>
    </div>

    <div style="overflow-x: auto;">
      <table class="tactical-table">
        <thead>
          <tr>
            <th>Timestamp</th>
            <th>Officer / User</th>
            <th>Action</th>
            <th>Module / Model</th>
            <th>Target ID</th>
            <th>Details & Metadata</th>
            <th>IP Address</th>
          </tr>
        </thead>
        <tbody>
          @forelse($logs as $log)
            <tr>
              <td>
                <div style="font-size: 12px; font-family: 'Plus Jakarta Sans', monospace; color: #fff;">
                  {{ $log->created_at->format('d M Y') }}
                </div>
                <div style="font-size: 11px; color: var(--text-muted);">{{ $log->created_at->format('h:i:s A') }}</div>
              </td>
              <td>
                <strong>{{ $log->user->name ?? 'System' }}</strong>
                <div style="font-size: 10.5px; color: var(--text-muted);">{{ $log->user ? str_replace('_', ' ', $log->user->role) : 'Automated Daemon' }}</div>
              </td>
              <td>
                @php
                  $badge = match(strtolower($log->action)) {
                    'approved' => 'badge-emerald',
                    'rejected', 'deleted' => 'badge-danger',
                    'created' => 'badge-gold',
                    default => 'badge-navy'
                  };
                @endphp
                <span class="badge {{ $badge }}">{{ strtoupper($log->action) }}</span>
              </td>
              <td>
                <span style="font-weight: 600; font-size: 12.5px;">{{ class_basename($log->auditable_type ?? 'System') }}</span>
              </td>
              <td>
                <span style="font-family: 'Plus Jakarta Sans', monospace; color: #60a5fa;">#{{ $log->auditable_id ?? 'N/A' }}</span>
              </td>
              <td>
                <div style="font-size: 11.5px; font-family: monospace; color: var(--text-muted); max-width: 320px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ json_encode($log->new_values ?? $log->old_values) }}">
                  {{ json_encode($log->new_values ?? $log->old_values) }}
                </div>
              </td>
              <td>
                <span style="font-family: 'Plus Jakarta Sans', monospace; font-size: 11.5px; color: var(--text-muted);">{{ $log->ip_address ?? '127.0.0.1' }}</span>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" style="text-align: center; padding: 48px; color: var(--text-muted);">
                No system audit events recorded.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($logs->hasPages())
      <div style="padding: 16px 20px; border-top: 1px solid var(--border-soft);">
        {{ $logs->links() }}
      </div>
    @endif
  </div>

</div>
@endsection
