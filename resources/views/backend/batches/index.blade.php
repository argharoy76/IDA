@extends('layouts.portal')

@section('title', 'Batches')
@section('page_title', 'Batches Management')
@section('page_subtitle', 'Organize batches, assign lead instructors, and manage schedules')

@section('content')
<div class="content-panel">
  <div class="panel-header">
    <h3><i class="fa-solid fa-layer-group" style="color: var(--brand-emerald);"></i> Active Batches</h3>
    <button type="button" onclick="openModal('addBatchModal')" class="btn-primary">
      <i class="fa-solid fa-plus"></i> Create Batch
    </button>
  </div>

  <div class="table-responsive">
    <table class="tactical-table">
      <thead>
        <tr>
          <th>Batch</th>
          <th>Course</th>
          <th>Instructor</th>
          <th>Schedule</th>
          <th>Enrolled Cadets</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        @foreach($batches as $b)
          <tr>
            <td>
              <strong style="font-size: 14px; color: var(--brand-deep); display: block;">{{ $b->batch_name }}</strong>
              <span class="badge badge-emerald" style="font-size: 10px;">{{ $b->batch_code }}</span>
            </td>
            <td>{{ $b->course->title ?? 'N/A' }}</td>
            <td>
              <strong>{{ $b->primaryInstructor->user->name ?? 'Unassigned' }}</strong>
              <small style="display: block; color: var(--text-muted); font-size: 11px;">{{ $b->primaryInstructor->designation ?? '' }}</small>
            </td>
            <td><span style="font-size: 12.5px;">{{ $b->schedule_summary ?? 'Schedule pending' }}</span></td>
            <td>
              <strong>{{ $b->students_count }} / {{ $b->max_students }}</strong>
              <div style="width: 80px; height: 6px; background: #e2e8f0; border-radius: 3px; overflow: hidden; margin-top: 4px;">
                <div style="width: {{ min(100, round(($b->students_count / max(1, $b->max_students)) * 100)) }}%; height: 100%; background: var(--brand-emerald);"></div>
              </div>
            </td>
            <td>
              <span class="badge {{ $b->status === 'active' ? 'badge-emerald' : 'badge-amber' }}">
                {{ ucfirst($b->status) }}
              </span>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

<!-- Modal: Add Batch -->
<div id="addBatchModal" class="modal-backdrop" style="display: none;">
  <div class="modal-dialog">
    <div class="panel-header">
      <h3>Create Batch</h3>
      <button type="button" onclick="closeModal('addBatchModal')" style="background: none; border: none; font-size: 18px; cursor: pointer;">&times;</button>
    </div>
    <form action="{{ route('admin.batches.store') }}" method="POST">
      @csrf
      <div class="form-group">
        <label class="form-label">Course *</label>
        <select name="course_id" required class="form-control">
          @foreach($courses as $c)
            <option value="{{ $c->id }}">{{ $c->title }} ({{ $c->category }})</option>
          @endforeach
        </select>
      </div>
      <div style="display: grid; grid-template-columns: 1.3fr 0.7fr; gap: 14px;">
        <div class="form-group">
          <label class="form-label">Batch Name *</label>
          <input type="text" name="batch_name" required class="form-control" placeholder="e.g. BMA 95th Long Course Squad Alpha">
        </div>
        <div class="form-group">
          <label class="form-label">Batch Code *</label>
          <input type="text" name="batch_code" required class="form-control" placeholder="IDA-BMA-95A">
        </div>
      </div>
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
        <div class="form-group">
          <label class="form-label">Primary Assessor / Instructor</label>
          <select name="primary_instructor_id" class="form-control">
            <option value="">-- Assign Instructor --</option>
            @foreach($instructors as $inst)
              <option value="{{ $inst->id }}">{{ $inst->user->name }} ({{ $inst->designation }})</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Max Squad Strength *</label>
          <input type="number" name="max_students" value="35" class="form-control">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Routine Schedule Summary</label>
        <input type="text" name="schedule_summary" class="form-control" placeholder="e.g. Sun, Tue, Thu | 09:00 AM - 12:30 PM">
      </div>
      <div class="form-group">
        <label class="form-label">Status *</label>
        <select name="status" class="form-control">
          <option value="active">Active</option>
          <option value="upcoming">Upcoming</option>
          <option value="completed">Completed</option>
        </select>
      </div>
      <button type="submit" class="btn-primary" style="width: 100%; justify-content: center; padding: 12px;">Create Squad Batch</button>
    </form>
  </div>
</div>

<script>
  function openModal(id) { document.getElementById(id).style.display = 'grid'; }
  function closeModal(id) { document.getElementById(id).style.display = 'none'; }
</script>
@endsection
