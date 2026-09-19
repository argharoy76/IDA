@extends('layouts.portal')

@section('title', 'Enroll New Cadet')
@section('page_title', 'Cadet Admission & Enrollment')
@section('page_subtitle', 'Register candidate and generate official academy identification and roll number')

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
  <div class="content-panel">
    <div class="panel-header">
      <h3><i class="fa-solid fa-user-plus" style="color: var(--brand-emerald);"></i> New Cadet Admission Form</h3>
      <a href="{{ route('admin.students.index') }}" class="btn-secondary" style="padding: 6px 12px; font-size: 12px;">
        <i class="fa-solid fa-arrow-left"></i> Back to Registry
      </a>
    </div>

    <form method="POST" action="{{ route('admin.students.store') }}">
      @csrf

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
        <div class="form-group">
          <label class="form-label">Full Name *</label>
          <input type="text" name="name" required class="form-control" placeholder="Cadet full name" value="{{ old('name') }}">
        </div>

        <div class="form-group">
          <label class="form-label">Email Address *</label>
          <input type="email" name="email" required class="form-control" placeholder="cadet@example.com" value="{{ old('email') }}">
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
        <div class="form-group">
          <label class="form-label">Phone Number *</label>
          <input type="text" name="phone" required class="form-control" placeholder="017XXXXXXXX" value="{{ old('phone') }}">
        </div>

        <div class="form-group">
          <label class="form-label">Emergency Contact Number</label>
          <input type="text" name="emergency_contact" class="form-control" placeholder="Guardian phone" value="{{ old('emergency_contact') }}">
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px;">
        <div class="form-group">
          <label class="form-label">Student Category *</label>
          <select name="student_type" class="form-control">
            <option value="academic">Academic Cadet (Regular)</option>
            <option value="external">External Candidate</option>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label">Target Defence Wing *</label>
          <select name="target_wing" class="form-control">
            <option value="Army">Army (BMA)</option>
            <option value="Navy">Navy (BNA)</option>
            <option value="Airforce">Air Force (BAFA)</option>
            <option value="General">General ISSB</option>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label">Gender *</label>
          <select name="gender" class="form-control">
            <option value="male">Male</option>
            <option value="female">Female</option>
          </select>
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
        <div class="form-group">
          <label class="form-label">Select Course Program</label>
          <select name="course_id" class="form-control">
            <option value="">-- Assign Course --</option>
            @foreach($courses as $c)
              <option value="{{ $c->id }}">{{ $c->title }} ({{ $c->category }})</option>
            @endforeach
          </select>
        </div>

        <div class="form-group">
          <label class="form-label">Allocate Initial Squad / Batch</label>
          <select name="batch_id" class="form-control">
            <option value="">-- Assign Batch --</option>
            @foreach($batches as $b)
              <option value="{{ $b->id }}">{{ $b->batch_name }} ({{ $b->batch_code }})</option>
            @endforeach
          </select>
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px;">
        <div class="form-group">
          <label class="form-label">Father's Name</label>
          <input type="text" name="father_name" class="form-control" placeholder="Father's name">
        </div>

        <div class="form-group">
          <label class="form-label">Mother's Name</label>
          <input type="text" name="mother_name" class="form-control" placeholder="Mother's name">
        </div>

        <div class="form-group">
          <label class="form-label">Blood Group</label>
          <select name="blood_group" class="form-control">
            <option value="">-- Select --</option>
            <option value="A+">A+</option>
            <option value="A-">A-</option>
            <option value="B+">B+</option>
            <option value="B-">B-</option>
            <option value="O+">O+</option>
            <option value="O-">O-</option>
            <option value="AB+">AB+</option>
            <option value="AB-">AB-</option>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Residential Address</label>
        <textarea name="address" rows="2" class="form-control" placeholder="Present address in Khulna / Home district"></textarea>
      </div>

      <button type="submit" class="btn-primary" style="width: 100%; justify-content: center; padding: 13px; font-size: 14px; margin-top: 10px;">
        <i class="fa-solid fa-id-badge"></i> Confirm Admission & Generate Official ID
      </button>
    </form>
  </div>
</div>
@endsection
