@extends('layouts.portal')

@section('title', 'Instructors')
@section('page_title', 'Faculty & Instructors')
@section('page_subtitle', 'Manage academy faculty, instructors, and evaluators')

@section('content')
<div class="content-panel">
  <div class="panel-header">
    <h3><i class="fa-solid fa-user-tie" style="color: var(--brand-emerald);"></i> Instructors Directory</h3>
    <button type="button" onclick="openModal('addInstructorModal')" class="btn-primary">
      <i class="fa-solid fa-user-plus"></i> Add New Instructor
    </button>
  </div>

  <div class="table-responsive">
    <table class="tactical-table">
      <thead>
        <tr>
          <th>Instructor</th>
          <th>Designation</th>
          <th>Specialization</th>
          <th>Phone</th>
          <th>Assigned Batches</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        @foreach($instructors as $inst)
          <tr>
            <td>
              <strong style="font-size: 14px; color: var(--brand-deep); display: block;">{{ $inst->user->name }}</strong>
              <span class="badge badge-emerald" style="font-size: 10px;">{{ $inst->instructor_code }}</span>
            </td>
            <td><strong>{{ $inst->designation }}</strong></td>
            <td><span class="badge badge-blue">{{ $inst->specialization }}</span></td>
            <td>{{ $inst->phone }}</td>
            <td>
              @foreach($inst->batches as $b)
                <span class="badge badge-amber" style="margin-bottom: 2px;">{{ $b->batch_code }}</span>
              @endforeach
            </td>
            <td><span class="badge badge-emerald">{{ ucfirst($inst->status) }}</span></td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

<!-- Modal: Add Instructor -->
<div id="addInstructorModal" class="modal-backdrop" style="display: none;">
  <div class="modal-dialog">
    <div class="panel-header">
      <h3>Register Military Assessor / Faculty</h3>
      <button type="button" onclick="closeModal('addInstructorModal')" style="background: none; border: none; font-size: 18px; cursor: pointer;">&times;</button>
    </div>
    <form action="{{ route('admin.instructors.store') }}" method="POST">
      @csrf
      <div style="display: grid; grid-template-columns: 1.3fr 0.7fr; gap: 14px;">
        <div class="form-group">
          <label class="form-label">Full Name & Rank *</label>
          <input type="text" name="name" required class="form-control" placeholder="e.g. Major (Retd.) Tariqul Islam">
        </div>
        <div class="form-group">
          <label class="form-label">Instructor Code *</label>
          <input type="text" name="instructor_code" required class="form-control" placeholder="INST-IDA-04">
        </div>
      </div>
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
        <div class="form-group">
          <label class="form-label">Email Address *</label>
          <input type="email" name="email" required class="form-control" placeholder="assessor@ida.com">
        </div>
        <div class="form-group">
          <label class="form-label">Phone Number *</label>
          <input type="text" name="phone" required class="form-control" placeholder="017XXXXXXXX">
        </div>
      </div>
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
        <div class="form-group">
          <label class="form-label">Designation *</label>
          <input type="text" name="designation" required class="form-control" placeholder="e.g. Senior Military GTO Assessor">
        </div>
        <div class="form-group">
          <label class="form-label">Specialization Area *</label>
          <input type="text" name="specialization" required class="form-control" placeholder="e.g. Ground Tasks / PGT">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Military Background & Bio</label>
        <textarea name="bio" rows="3" class="form-control" placeholder="Service background in Bangladesh Army / Navy / Air Force..."></textarea>
      </div>
      <button type="submit" class="btn-primary" style="width: 100%; justify-content: center; padding: 12px;">Create Faculty Profile</button>
    </form>
  </div>
</div>

<script>
  function openModal(id) { document.getElementById(id).style.display = 'grid'; }
  function closeModal(id) { document.getElementById(id).style.display = 'none'; }
</script>
@endsection
