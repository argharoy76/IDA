@extends('layouts.portal')

@section('title', 'Courses')
@section('page_title', 'Courses Catalog')
@section('page_subtitle', 'Manage preparatory programs, durations, and tuition fees')

@section('content')
<div class="content-panel">
  <div class="panel-header">
    <h3><i class="fa-solid fa-book-bookmark" style="color: var(--brand-emerald);"></i> Preparatory Programs</h3>
    <button type="button" onclick="openModal('addCourseModal')" class="btn-primary">
      <i class="fa-solid fa-plus"></i> Add New Course
    </button>
  </div>

  <div class="table-responsive">
    <table class="tactical-table">
      <thead>
        <tr>
          <th>Course Title</th>
          <th>Category</th>
          <th>Duration</th>
          <th>Tuition Fee</th>
          <th>Batches</th>
          <th>Students</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        @foreach($courses as $c)
          <tr>
            <td>
              <strong style="font-size: 14px; color: var(--brand-deep); display: block;">{{ $c->title }}</strong>
              <small style="color: var(--text-muted);">{{ Str::limit($c->description, 50) }}</small>
            </td>
            <td><span class="badge badge-emerald">{{ $c->category }}</span></td>
            <td>{{ $c->duration }}</td>
            <td><strong>৳{{ number_format($c->fee, 0) }}</strong></td>
            <td><span class="badge badge-blue">{{ $c->batches_count }} Batches</span></td>
            <td><span class="badge badge-emerald">{{ $c->students_count }} Cadets</span></td>
            <td>
              <span class="badge {{ $c->admission_status === 'open' ? 'badge-emerald' : 'badge-amber' }}">
                {{ ucfirst($c->admission_status) }}
              </span>
            </td>
            <td>
              <a href="{{ route('courses.detail', $c->slug) }}" target="_blank" class="btn-secondary" style="padding: 5px 10px; font-size: 11px;">
                <i class="fa-solid fa-arrow-up-right-from-square"></i> Preview
              </a>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

<!-- Modal: Add Course -->
<div id="addCourseModal" class="modal-backdrop" style="display: none;">
  <div class="modal-dialog">
    <div class="panel-header">
      <h3>Add New Preparatory Course</h3>
      <button type="button" onclick="closeModal('addCourseModal')" style="background: none; border: none; font-size: 18px; cursor: pointer;">&times;</button>
    </div>
    <form action="{{ route('admin.courses.store') }}" method="POST">
      @csrf
      <div class="form-group">
        <label class="form-label">Course Title *</label>
        <input type="text" name="title" required class="form-control" placeholder="e.g. BMA 95th Long Course Special">
      </div>
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
        <div class="form-group">
          <label class="form-label">Category *</label>
          <select name="category" class="form-control">
            <option value="Army">Army (BMA)</option>
            <option value="Navy">Navy (BNA)</option>
            <option value="Airforce">Air Force (BAFA)</option>
            <option value="ISSB Special">ISSB Special Masterclass</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Duration *</label>
          <input type="text" name="duration" required class="form-control" placeholder="e.g. 4 Months">
        </div>
      </div>
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
        <div class="form-group">
          <label class="form-label">Tuition Fee (BDT) *</label>
          <input type="number" name="fee" required class="form-control" placeholder="18500">
        </div>
        <div class="form-group">
          <label class="form-label">Admission Status *</label>
          <select name="admission_status" class="form-control">
            <option value="open">Open</option>
            <option value="upcoming">Upcoming</option>
            <option value="closed">Closed</option>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Eligibility Criteria</label>
        <input type="text" name="eligibility" class="form-control" placeholder="e.g. HSC with GPA 4.00, Age 17-21">
      </div>
      <div class="form-group">
        <label class="form-label">Description & Overview</label>
        <textarea name="description" rows="3" class="form-control" placeholder="Course syllabus overview..."></textarea>
      </div>
      <div style="margin-bottom: 16px;">
        <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600; cursor: pointer;">
          <input type="checkbox" name="is_featured" value="1" checked>
          <span>Feature on Homepage</span>
        </label>
      </div>
      <button type="submit" class="btn-primary" style="width: 100%; justify-content: center; padding: 12px;">Create Course</button>
    </form>
  </div>
</div>

<script>
  function openModal(id) { document.getElementById(id).style.display = 'grid'; }
  function closeModal(id) { document.getElementById(id).style.display = 'none'; }
</script>
@endsection
