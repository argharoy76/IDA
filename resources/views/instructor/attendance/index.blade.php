@extends('layouts.portal')

@section('title', 'Muster Roll Call')
@section('page_title', 'Squadron Muster Roll Call')
@section('page_subtitle', 'Mark daily parade, drill, and classroom attendance for assigned cadets')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

  <!-- Batch & Date Selector -->
  <div class="tactical-card" style="padding: 16px;">
    <form action="{{ route('instructor.attendance.index') }}" method="GET" style="display: flex; gap: 14px; align-items: flex-end; flex-wrap: wrap;">
      <div style="min-width: 220px;">
        <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 6px;">Select Squadron / Batch</label>
        <select name="batch_id" class="form-tactical" style="height: 40px;" onchange="this.form.submit()">
          @foreach($batches as $b)
            <option value="{{ $b->id }}" {{ $selectedBatchId == $b->id ? 'selected' : '' }}>
              {{ $b->name }} ({{ $b->code }})
            </option>
          @endforeach
        </select>
      </div>

      <div style="width: 180px;">
        <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 6px;">Muster Date</label>
        <input type="date" name="date" value="{{ $selectedDate }}" class="form-tactical" style="height: 40px;" onchange="this.form.submit()">
      </div>

      <button type="submit" class="btn-tactical btn-tactical-outline" style="height: 40px;">
        <i class="fa-solid fa-arrows-rotate"></i> Load Roster
      </button>
    </form>
  </div>

  <!-- Attendance Roll Call Sheet -->
  @if($selectedBatchId && count($students) > 0)
    <form action="{{ route('instructor.attendance.store') }}" method="POST">
      @csrf
      <input type="hidden" name="batch_id" value="{{ $selectedBatchId }}">
      <input type="hidden" name="date" value="{{ $selectedDate }}">

      <div class="tactical-card" style="padding: 0; overflow: hidden;">
        <div style="padding: 16px 20px; border-bottom: 1px solid var(--border-soft); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
          <div>
            <h3 style="font-size: 15px; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 8px;">
              <i class="fa-solid fa-clipboard-check" style="color: var(--accent-gold);"></i> Parade Muster: {{ date('l, d M Y', strtotime($selectedDate)) }}
            </h3>
            <span style="font-size: 12px; color: var(--text-muted);">{{ count($students) }} Cadets Enrolled</span>
          </div>

          <!-- Quick Bulk Actions -->
          <div style="display: flex; gap: 8px;">
            <button type="button" class="btn-tactical" style="background: rgba(16, 185, 129, 0.15); color: #10b981; padding: 4px 10px; font-size: 11px;" onclick="setAllAttendance('present')">
              <i class="fa-solid fa-check"></i> All Present
            </button>
            <button type="button" class="btn-tactical" style="background: rgba(239, 68, 68, 0.15); color: #ef4444; padding: 4px 10px; font-size: 11px;" onclick="setAllAttendance('absent')">
              <i class="fa-solid fa-xmark"></i> All Absent
            </button>
          </div>
        </div>

        <table class="tactical-table">
          <thead>
            <tr>
              <th>Cadet ID</th>
              <th>Cadet Name</th>
              <th>Roll #</th>
              <th>Attendance Status</th>
              <th>Remarks / Reason</th>
            </tr>
          </thead>
          <tbody>
            @foreach($students as $s)
              @php
                $att = $existingAttendance[$s->id] ?? null;
                $currentStatus = $att ? $att->status : 'present';
              @endphp
              <tr>
                <td>
                  <span style="font-family: 'Plus Jakarta Sans', monospace; font-weight: 700; color: var(--accent-gold);">{{ $s->student_id_code }}</span>
                </td>
                <td>
                  <strong style="color: var(--text-main);">{{ $s->user->name ?? 'Cadet' }}</strong>
                </td>
                <td>
                  <span style="font-family: 'Plus Jakarta Sans', monospace;">{{ $s->roll_number }}</span>
                </td>
                <td>
                  <div style="display: flex; gap: 12px; align-items: center;">
                    <label style="display: flex; align-items: center; gap: 4px; font-size: 12px; cursor: pointer; color: #10b981;">
                      <input type="radio" name="attendance[{{ $s->id }}][status]" value="present" class="att-radio-present" {{ $currentStatus === 'present' ? 'checked' : '' }}>
                      Present
                    </label>

                    <label style="display: flex; align-items: center; gap: 4px; font-size: 12px; cursor: pointer; color: #ef4444;">
                      <input type="radio" name="attendance[{{ $s->id }}][status]" value="absent" class="att-radio-absent" {{ $currentStatus === 'absent' ? 'checked' : '' }}>
                      Absent
                    </label>

                    <label style="display: flex; align-items: center; gap: 4px; font-size: 12px; cursor: pointer; color: #f59e0b;">
                      <input type="radio" name="attendance[{{ $s->id }}][status]" value="late" {{ $currentStatus === 'late' ? 'checked' : '' }}>
                      Late
                    </label>

                    <label style="display: flex; align-items: center; gap: 4px; font-size: 12px; cursor: pointer; color: #60a5fa;">
                      <input type="radio" name="attendance[{{ $s->id }}][status]" value="excused" {{ $currentStatus === 'excused' ? 'checked' : '' }}>
                      Excused
                    </label>
                  </div>
                </td>
                <td>
                  <input type="text" name="attendance[{{ $s->id }}][remarks]" value="{{ $att->remarks ?? '' }}" class="form-tactical" placeholder="e.g. Medical leave, guard duty" style="height: 32px; font-size: 12px; padding: 4px 10px;">
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>

        <div style="padding: 16px 20px; border-top: 1px solid var(--border-soft); display: flex; justify-content: flex-end;">
          <button type="submit" class="btn-tactical btn-tactical-primary">
            <i class="fa-solid fa-floppy-disk"></i> Commit Muster Roll
          </button>
        </div>
      </div>
    </form>
  @else
    <div class="tactical-card" style="text-align: center; padding: 48px; color: var(--text-muted);">
      <i class="fa-solid fa-users-slash" style="font-size: 36px; margin-bottom: 12px; display: block; opacity: 0.4;"></i>
      No active cadets found in the selected batch.
    </div>
  @endif

</div>

<script>
  function setAllAttendance(status) {
    if (status === 'present') {
      document.querySelectorAll('.att-radio-present').forEach(r => r.checked = true);
    } else if (status === 'absent') {
      document.querySelectorAll('.att-radio-absent').forEach(r => r.checked = true);
    }
  }
</script>
@endsection
