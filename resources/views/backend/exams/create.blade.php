@extends('layouts.portal')

@section('title', 'Create Assessment Module')
@section('page_title', 'Create Assessment Examination Module')
@section('page_subtitle', 'Configure parameters, exam type (IQ MCQ or WAT), time limits, and negative marking')

@section('topbar_actions')
  <a href="{{ route('admin.exams.index') }}" class="btn-tactical btn-tactical-outline">
    <i class="fa-solid fa-arrow-left"></i> Back to Exam Bank
  </a>
@endsection

@section('content')
<div style="max-width: 860px; margin: 0 auto;">
  <div class="tactical-card">
    <form action="{{ route('admin.exams.store') }}" method="POST">
      @csrf

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <div style="grid-column: span 2;">
          <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Module Title *</label>
          <input type="text" name="title" value="{{ old('title') }}" placeholder="e.g. Officer Cadets IQ Screening Test - Phase 1" class="form-tactical" required>
          @error('title') <span style="color: #ef4444; font-size: 11px;">{{ $message }}</span> @enderror
        </div>

        <div>
          <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Exam Engine Type *</label>
          <select name="exam_type" class="form-tactical" required>
            <option value="iq_mcq" {{ old('exam_type') === 'iq_mcq' ? 'selected' : '' }}>IQ Multiple Choice Question (MCQ)</option>
            <option value="word_association" {{ old('exam_type') === 'word_association' ? 'selected' : '' }}>Word Association Test (WAT - 15s Flash)</option>
            <option value="non_verbal_iq" {{ old('exam_type') === 'non_verbal_iq' ? 'selected' : '' }}>Non-Verbal Matrix IQ Test</option>
            <option value="general_aptitude" {{ old('exam_type') === 'general_aptitude' ? 'selected' : '' }}>General Academic Aptitude</option>
          </select>
          @error('exam_type') <span style="color: #ef4444; font-size: 11px;">{{ $message }}</span> @enderror
        </div>

        <div>
          <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">
            <i class="fa-solid fa-shield-halved" style="color: #10b981;"></i> Branch *
          </label>
          <select name="branch" class="form-tactical" required>
            <option value="">— Select Branch —</option>
            <option value="army" {{ old('branch') === 'army' ? 'selected' : '' }}>🛡️ Bangladesh Army</option>
            <option value="navy" {{ old('branch') === 'navy' ? 'selected' : '' }}>⚓ Bangladesh Navy</option>
            <option value="air_force" {{ old('branch') === 'air_force' ? 'selected' : '' }}>✈️ Bangladesh Air Force</option>
            <option value="police" {{ old('branch') === 'police' ? 'selected' : '' }}>🛡️ Bangladesh Police</option>
          </select>
          @error('branch') <span style="color: #ef4444; font-size: 11px;">{{ $message }}</span> @enderror
        </div>

        <div>
          <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Category / Domain *</label>
          <input type="text" name="category" value="{{ old('category', 'Military Intelligence & ISSB') }}" class="form-tactical" required>
          @error('category') <span style="color: #ef4444; font-size: 11px;">{{ $message }}</span> @enderror
        </div>

        <div>
          <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Duration (Minutes) *</label>
          <input type="number" name="duration_minutes" value="{{ old('duration_minutes', 45) }}" min="1" class="form-tactical" required>
          @error('duration_minutes') <span style="color: #ef4444; font-size: 11px;">{{ $message }}</span> @enderror
        </div>

        <div>
          <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Status *</label>
          <select name="status" class="form-tactical" required>
            <option value="open" {{ old('status') === 'open' ? 'selected' : '' }}>Open (Live for Candidates)</option>
            <option value="scheduled" {{ old('status') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
            <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft (Admin Only)</option>
            <option value="closed" {{ old('status') === 'closed' ? 'selected' : '' }}>Closed</option>
          </select>
          @error('status') <span style="color: #ef4444; font-size: 11px;">{{ $message }}</span> @enderror
        </div>

        <div>
          <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Total Marks *</label>
          <input type="number" step="0.5" name="total_marks" value="{{ old('total_marks', 50) }}" min="1" class="form-tactical" required>
          @error('total_marks') <span style="color: #ef4444; font-size: 11px;">{{ $message }}</span> @enderror
        </div>

        <div>
          <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">
            Minimum Result for Qualification (Pass Marks) *
          </label>
          <input type="number" step="0.5" name="pass_marks" value="{{ old('pass_marks', 25) }}" min="0" class="form-tactical" required>
          <small style="color: #94a3b8; font-size: 11px; margin-top: 3px; display: block;">Score &ge; this mark &rarr; QUALIFIED; below &rarr; NOT QUALIFIED</small>
          @error('pass_marks') <span style="color: #ef4444; font-size: 11px;">{{ $message }}</span> @enderror
        </div>

        <div>
          <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Negative Marking Per Wrong Answer</label>
          <input type="number" step="0.05" name="negative_marking_per_wrong" value="{{ old('negative_marking_per_wrong', 0.25) }}" min="0" class="form-tactical">
        </div>

        <div>
          <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Fee for External Candidates (৳)</label>
          <input type="number" step="1" name="fee" value="{{ old('fee', 0) }}" min="0" class="form-tactical">
        </div>

        <div style="grid-column: span 2;">
          <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Module Description</label>
          <textarea name="description" rows="3" class="form-tactical" placeholder="Brief summary of test domain, target military branch, and focus areas...">{{ old('description') }}</textarea>
        </div>

        <div style="grid-column: span 2;">
          <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Exam Instructions for Candidates</label>
          <textarea name="instructions" rows="3" class="form-tactical" placeholder="Instructions shown prior to start button (e.g. You cannot pause the timer. Negative marking applies.)">{{ old('instructions', 'Read each question carefully. Unanswered questions do not deduct marks. Each incorrect answer deducts 0.25 marks.') }}</textarea>
        </div>
      </div>

      <div style="margin-top: 24px; padding-top: 16px; border-top: 1px solid var(--border-soft); display: flex; justify-content: flex-end; gap: 12px;">
        <a href="{{ route('admin.exams.index') }}" class="btn-tactical btn-tactical-outline">Cancel</a>
        <button type="submit" class="btn-tactical btn-tactical-primary">
          <i class="fa-solid fa-check"></i> Create Exam & Setup Questions
        </button>
      </div>
    </form>
  </div>
</div>
@endsection
