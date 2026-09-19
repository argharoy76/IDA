@extends('layouts.portal')

@section('title', 'Attendance')
@section('page_title', 'Attendance')
@section('page_subtitle', 'Mark and monitor daily attendance by batch')

@section('content')
<div class="content-panel">
  <!-- Batch & Date Selector -->
  <form method="GET" action="{{ route('admin.attendance.index') }}" style="display: flex; gap: 14px; align-items: center; margin-bottom: 24px; flex-wrap: wrap; background: var(--surface-subtle); padding: 14px; border-radius: var(--radius-sm); border: 1px solid var(--border-soft);">
    <div>
      <label class="form-label">Batch</label>
      <select name="batch_id" class="form-control" style="width: 240px;" onchange="this.form.submit()">
        @foreach($batches as $b)
          <option value="{{ $b->id }}" {{ $selectedBatchId == $b->id ? 'selected' : '' }}>
            {{ $b->batch_code }} - {{ $b->batch_name }}
          </option>
        @endforeach
      </select>
    </div>

    <div>
      <label class="form-label">Date</label>
      <input type="date" name="date" value="{{ $selectedDate }}" class="form-control" style="width: 170px;" onchange="this.form.submit()">
    </div>

    <div style="margin-top: 20px;">
      <button type="submit" class="btn-secondary" style="padding: 9px 18px;">
        <i class="fa-solid fa-arrows-rotate"></i> Load Sheet
      </button>
    </div>
  </form>

  <!-- Attendance Marking Form -->
  @if(count($students) > 0)
    <form action="{{ route('admin.attendance.store') }}" method="POST">
      @csrf
      <input type="hidden" name="batch_id" value="{{ $selectedBatchId }}">
      <input type="hidden" name="date" value="{{ $selectedDate }}">

      @if($routines->isNotEmpty())
        <div class="form-group" style="max-width: 380px; margin-bottom: 20px;">
          <label class="form-label">Link to Scheduled Class (Optional)</label>
          <select name="routine_id" class="form-control">
            @foreach($routines as $rt)
              <option value="{{ $rt->id }}">{{ $rt->subject }} ({{ date('h:i A', strtotime($rt->start_time)) }})</option>
            @endforeach
          </select>
        </div>
      @endif

      <div class="table-responsive">
        <table class="tactical-table">
          <thead>
            <tr>
              <th>Roll</th>
              <th>Cadet Identity</th>
              <th>Target Wing</th>
              <th>Cumulative Attendance</th>
              <th>Muster Status</th>
              <th>Remarks / Reason</th>
            </tr>
          </thead>
          <tbody>
            @foreach($students as $st)
              @php
                $currentStatus = $existingAttendance[$st->id]->status ?? 'present';
                $currentRemarks = $existingAttendance[$st->id]->remarks ?? '';
              @endphp
              <tr>
                <td><strong>#{{ $st->roll_number ?? '00' }}</strong></td>
                <td>
                  <strong style="display: block; font-size: 14px;">{{ $st->user->name }}</strong>
                  <span class="badge badge-emerald" style="font-size: 10px;">{{ $st->student_id_code }}</span>
                </td>
                <td><span class="badge badge-blue">{{ $st->target_wing }}</span></td>
                <td>
                  <strong style="color: {{ $st->attendance_percentage < 75 ? '#ef4444' : 'var(--brand-emerald)' }};">
                    {{ $st->attendance_percentage }}%
                  </strong>
                </td>
                <td>
                  <div style="display: flex; gap: 12px; font-size: 13px;">
                    <label style="display: flex; align-items: center; gap: 4px; cursor: pointer; color: #059669; font-weight: 700;">
                      <input type="radio" name="attendance[{{ $st->id }}][status]" value="present" {{ $currentStatus === 'present' ? 'checked' : '' }}> Present
                    </label>
                    <label style="display: flex; align-items: center; gap: 4px; cursor: pointer; color: #ef4444; font-weight: 700;">
                      <input type="radio" name="attendance[{{ $st->id }}][status]" value="absent" {{ $currentStatus === 'absent' ? 'checked' : '' }}> Absent
                    </label>
                    <label style="display: flex; align-items: center; gap: 4px; cursor: pointer; color: #d97706; font-weight: 700;">
                      <input type="radio" name="attendance[{{ $st->id }}][status]" value="late" {{ $currentStatus === 'late' ? 'checked' : '' }}> Late
                    </label>
                    <label style="display: flex; align-items: center; gap: 4px; cursor: pointer; color: #64748b; font-weight: 700;">
                      <input type="radio" name="attendance[{{ $st->id }}][status]" value="excused" {{ $currentStatus === 'excused' ? 'checked' : '' }}> Excused
                    </label>
                  </div>
                </td>
                <td>
                  <input type="text" name="attendance[{{ $st->id }}][remarks]" value="{{ $currentRemarks }}" class="form-control" style="font-size: 12px; padding: 6px 10px;" placeholder="Optional remark...">
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div style="margin-top: 24px; display: flex; justify-content: flex-end;">
        <button type="submit" class="btn-primary" style="padding: 12px 28px; font-size: 14px;">
          <i class="fa-solid fa-cloud-arrow-up"></i> Save Muster Roll & Recalculate Dossiers
        </button>
      </div>
    </form>
  @else
    <div style="text-align: center; padding: 40px; color: var(--text-muted);">
      No cadets are currently enrolled in this squad batch.
    </div>
  @endif
</div>
@endsection
