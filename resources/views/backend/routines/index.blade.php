@extends('layouts.portal')

@section('title', 'Schedule')
@section('page_title', 'Class Schedule')
@section('page_subtitle', 'Manage daily classes, timings, topics, and instructor assignments')

@section('content')
<div class="content-panel">
  <div class="panel-header">
    <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
      <form method="GET" action="{{ route('admin.routines.index') }}" style="display: flex; gap: 10px; align-items: center;">
        <select name="batch_id" class="form-control" style="width: 200px;" onchange="this.form.submit()">
          <option value="">All Batches</option>
          @foreach($batches as $b)
            <option value="{{ $b->id }}" {{ request('batch_id') == $b->id ? 'selected' : '' }}>{{ $b->batch_code }} - {{ $b->batch_name }}</option>
          @endforeach
        </select>
        <input type="date" name="date" value="{{ request('date') }}" class="form-control" style="width: 160px;" onchange="this.form.submit()">
        @if(request()->anyFilled(['batch_id', 'date']))
          <a href="{{ route('admin.routines.index') }}" class="btn-secondary" style="color: #ef4444; padding: 8px 12px;">Reset</a>
        @endif
      </form>
    </div>

    <button type="button" onclick="openModal('addClassModal')" class="btn-primary">
      <i class="fa-solid fa-plus"></i> Add Class
    </button>
  </div>

  <div class="table-responsive">
    <table class="tactical-table">
      <thead>
        <tr>
          <th>Date & Day</th>
          <th>Time</th>
          <th>Subject</th>
          <th>Batch</th>
          <th>Room</th>
          <th>Instructor</th>
          <th>Type</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        @forelse($routines as $r)
          <tr>
            <td>
              <strong style="display: block;">{{ $r->class_date->format('d M, Y') }}</strong>
              <small style="color: var(--text-muted);">{{ $r->day_of_week }}</small>
            </td>
            <td>
              <span class="badge badge-blue">
                {{ date('h:i A', strtotime($r->start_time)) }} - {{ date('h:i A', strtotime($r->end_time)) }}
              </span>
            </td>
            <td>
              <strong style="display: block; color: var(--brand-deep);">{{ $r->subject }}</strong>
              <small style="color: var(--text-muted);">{{ $r->topic ?? 'Standard session' }}</small>
            </td>
            <td><span class="badge badge-emerald">{{ $r->batch->batch_code ?? 'Batch' }}</span></td>
            <td><i class="fa-solid fa-door-open" style="color: var(--brand-mint);"></i> {{ $r->room }}</td>
            <td>{{ $r->instructor->user->name ?? 'Command Assessor' }}</td>
            <td><span class="badge badge-amber">{{ ucfirst($r->class_type) }}</span></td>
            <td>
              <span class="badge {{ $r->status === 'completed' ? 'badge-emerald' : ($r->status === 'scheduled' ? 'badge-blue' : 'badge-red') }}">
                {{ ucfirst($r->status) }}
              </span>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" style="text-align: center; padding: 30px; color: var(--text-muted);">
              No routine schedules found matching the filter.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div style="margin-top: 20px;">
    {{ $routines->links() }}
  </div>
</div>

<!-- Modal: Add Class Schedule -->
<div id="addClassModal" class="modal-backdrop" style="display: none;">
  <div class="modal-dialog">
    <div class="panel-header">
      <h3>Schedule Training Class</h3>
      <button type="button" onclick="closeModal('addClassModal')" style="background: none; border: none; font-size: 18px; cursor: pointer;">&times;</button>
    </div>
    <form action="{{ route('admin.routines.store') }}" method="POST">
      @csrf
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
        <div class="form-group">
          <label class="form-label">Course *</label>
          <select name="course_id" required class="form-control">
            @foreach($courses as $c)
              <option value="{{ $c->id }}">{{ $c->title }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Batch *</label>
          <select name="batch_id" required class="form-control">
            @foreach($batches as $b)
              <option value="{{ $b->id }}">{{ $b->batch_code }} - {{ $b->batch_name }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div style="display: grid; grid-template-columns: 1.3fr 0.7fr; gap: 14px;">
        <div class="form-group">
          <label class="form-label">Subject *</label>
          <input type="text" name="subject" required class="form-control" placeholder="e.g. Progressive Ground Tasks">
        </div>
        <div class="form-group">
          <label class="form-label">Training Room / Field *</label>
          <input type="text" name="room" required class="form-control" value="Hall Alpha">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Topic / Specific Drill</label>
        <input type="text" name="topic" class="form-control" placeholder="e.g. Bridging cantilever obstacles with rope & plank">
      </div>
      <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px;">
        <div class="form-group">
          <label class="form-label">Date *</label>
          <input type="date" name="class_date" required class="form-control" value="{{ date('Y-m-d') }}">
        </div>
        <div class="form-group">
          <label class="form-label">Start Time *</label>
          <input type="time" name="start_time" required class="form-control" value="09:30">
        </div>
        <div class="form-group">
          <label class="form-label">End Time *</label>
          <input type="time" name="end_time" required class="form-control" value="11:00">
        </div>
      </div>
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
        <div class="form-group">
          <label class="form-label">Instructor Assessor</label>
          <select name="instructor_id" class="form-control">
            <option value="">-- Assign Instructor --</option>
            @foreach($instructors as $inst)
              <option value="{{ $inst->id }}">{{ $inst->user->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Class Type *</label>
          <select name="class_type" class="form-control">
            <option value="theory">Theory Lecture</option>
            <option value="practical">Practical Obstacle Drill</option>
            <option value="physical">Physical Endurance</option>
            <option value="mock_viva">Mock Interview / Viva</option>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Status *</label>
        <select name="status" class="form-control">
          <option value="scheduled">Scheduled</option>
          <option value="completed">Completed</option>
          <option value="rescheduled">Rescheduled</option>
        </select>
      </div>
      <button type="submit" class="btn-primary" style="width: 100%; justify-content: center; padding: 12px;">Publish Class Schedule</button>
    </form>
  </div>
</div>

<script>
  function openModal(id) { document.getElementById(id).style.display = 'grid'; }
  function closeModal(id) { document.getElementById(id).style.display = 'none'; }
</script>
@endsection
