@extends('layouts.portal')

@section('title', 'Cadets')
@section('page_title', 'Cadet Directory')
@section('page_subtitle', 'Manage enrolled academy cadets and online assessment candidates')

@section('content')
<div class="content-panel">
  <!-- Search & Filter Controls -->
  <form method="GET" action="{{ route('admin.students.index') }}" style="display: flex; gap: 14px; flex-wrap: wrap; margin-bottom: 20px; align-items: center; justify-content: space-between;">
    <div style="display: flex; gap: 10px; flex-wrap: wrap; flex: 1;">
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, cadet ID, roll, phone..." class="form-control" style="max-width: 280px;">

      <select name="type" class="form-control" style="max-width: 170px;" onchange="this.form.submit()">
        <option value="">All Student Types</option>
        <option value="academic" {{ request('type') === 'academic' ? 'selected' : '' }}>Academic Cadets</option>
        <option value="external" {{ request('type') === 'external' ? 'selected' : '' }}>External Candidates</option>
      </select>

      <select name="wing" class="form-control" style="max-width: 170px;" onchange="this.form.submit()">
        <option value="">All Target Wings</option>
        <option value="Army" {{ request('wing') === 'Army' ? 'selected' : '' }}>Army (BMA)</option>
        <option value="Navy" {{ request('wing') === 'Navy' ? 'selected' : '' }}>Navy (BNA)</option>
        <option value="Airforce" {{ request('wing') === 'Airforce' ? 'selected' : '' }}>Airforce (BAFA)</option>
        <option value="General" {{ request('wing') === 'General' ? 'selected' : '' }}>General ISSB</option>
      </select>

      <button type="submit" class="btn-secondary" style="padding: 9px 16px;">
        <i class="fa-solid fa-filter"></i> Filter
      </button>

      @if(request()->anyFilled(['search', 'type', 'wing', 'batch_id']))
        <a href="{{ route('admin.students.index') }}" class="btn-secondary" style="color: #ef4444;">
          <i class="fa-solid fa-xmark"></i> Reset
        </a>
      @endif
    </div>

    <a href="{{ route('admin.students.create') }}" class="btn-primary">
      <i class="fa-solid fa-user-plus"></i> Add New Cadet
    </a>
  </form>

  <!-- Students Table -->
  <div class="table-responsive">
    <table class="tactical-table">
      <thead>
        <tr>
          <th>Cadet</th>
          <th>Roll No</th>
          <th>Type & Wing</th>
          <th>Batch</th>
          <th>Attendance</th>
          <th>Due Fees</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        @forelse($students as $st)
          <tr>
            <td>
              <div style="display: flex; align-items: center; gap: 10px;">
                <div style="width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, var(--brand-deep), var(--accent-navy)); color: #fff; display: grid; place-items: center; font-weight: 700; font-size: 13px;">
                  {{ strtoupper(substr($st->user->name, 0, 1)) }}
                </div>
                <div>
                  <strong style="display: block; font-size: 13.5px;">{{ $st->user->name }}</strong>
                  <span class="badge badge-emerald" style="font-size: 10px;">{{ $st->student_id_code }}</span>
                </div>
              </div>
            </td>
            <td><strong>#{{ $st->roll_number ?? '--' }}</strong></td>
            <td>
              <span class="badge {{ $st->student_type === 'academic' ? 'badge-blue' : 'badge-amber' }}">
                {{ ucfirst($st->student_type) }}
              </span>
              <small style="display: block; color: var(--text-muted); font-size: 11px; margin-top: 2px;">{{ $st->target_wing }}</small>
            </td>
            <td>
              <strong>{{ $st->currentBatch->batch_code ?? 'None' }}</strong>
              <small style="display: block; color: var(--text-muted); font-size: 11px;">{{ Str::limit($st->currentCourse->title ?? '', 22) }}</small>
            </td>
            <td>
              @if($st->student_type === 'academic')
                <strong style="color: {{ $st->attendance_percentage < 75 ? '#ef4444' : 'var(--brand-emerald)' }};">
                  {{ $st->attendance_percentage }}%
                </strong>
              @else
                <span style="color: var(--text-muted);">N/A</span>
              @endif
            </td>
            <td>
              @if($st->total_due_fee > 0)
                <span style="color: #ef4444; font-weight: 800;">৳{{ number_format($st->total_due_fee, 0) }}</span>
              @else
                <span class="badge badge-emerald" style="font-size: 10px;">Clear</span>
              @endif
            </td>
            <td>
              <span class="badge {{ $st->status === 'active' ? 'badge-emerald' : 'badge-amber' }}">
                {{ ucfirst($st->status) }}
              </span>
            </td>
            <td>
              <a href="{{ route('admin.students.show', $st->id) }}" class="btn-primary" style="padding: 6px 12px; font-size: 12px;">
                <i class="fa-solid fa-address-card"></i> View Dossier
              </a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" style="text-align: center; padding: 30px; color: var(--text-muted);">
              No cadet records found matching the active search filters.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div style="margin-top: 20px;">
    {{ $students->links() }}
  </div>
</div>
@endsection
