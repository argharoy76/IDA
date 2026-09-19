@extends('layouts.portal')

@section('title', 'Online Tests - Web Management')
@section('page_title', 'Online Tests Portal Editor')
@section('page_subtitle', 'Manage test portal banner texts, examination protocols, question banks, and candidate evaluations')

@section('content')
<div style="display: flex; flex-direction: column; gap: 20px;">

  @include('backend.cms.partials.nav')

  @if(session('success'))
    <div class="alert alert-success" style="background: rgba(16, 185, 129, 0.15); border: 1px solid var(--brand-mint); color: #065f46; padding: 12px 16px; border-radius: var(--radius-sm); font-size: 13px; font-weight: 600;">
      <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
    </div>
  @endif

  <!-- Page Header & Protocol Settings -->
  <form action="{{ route('admin.cms.settings') }}" method="POST">
    @csrf
    <div class="tactical-card" style="margin-bottom: 24px;">
      <div style="border-bottom: 1px solid var(--border-soft); padding-bottom: 14px; margin-bottom: 20px;">
        <h3 style="font-size: 17px; font-weight: 800; color: var(--brand-deep); margin: 0 0 6px 0;">
          <i class="fa-solid fa-crosshairs" style="color: var(--brand-emerald);"></i> Online Test Page Header & Protocol Statement
        </h3>
        <p style="font-size: 12.5px; color: var(--text-muted); margin: 0;">
          Configure the title, description, and examination standards displayed on <code>/online-tests</code>.
        </p>
      </div>

      <div style="display: flex; flex-direction: column; gap: 16px;">
        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 16px;">
          <div>
            <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Eyebrow Badge</label>
            <input type="text" name="online_tests_badge" value="{{ cms('online_tests_badge', cms('tests_page_badge', 'DEFENCE ASSESSMENT GATEWAY')) }}" class="form-tactical">
          </div>
          <div>
            <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Page Heading</label>
            <input type="text" name="online_tests_title" value="{{ cms('online_tests_title', cms('tests_page_title', 'IDA Online Assessment Platform')) }}" class="form-tactical">
          </div>
        </div>

        <div>
          <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Page Subtitle</label>
          <textarea name="online_tests_subtitle" rows="2" class="form-tactical">{{ cms('online_tests_subtitle', cms('tests_page_subtitle', 'A high-precision, server-timed examination engine simulating Bangladesh Armed Forces Preliminary Screening, Verbal/Non-Verbal Intelligence, and Psychological assessment batteries.')) }}</textarea>
        </div>

        <div>
          <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Four-Step Protocol Section Heading</label>
          <input type="text" name="online_tests_step_heading" value="{{ cms('online_tests_step_heading', cms('tests_protocol_title', 'Four-Step Assessment Protocol for External Candidates')) }}" class="form-tactical">
        </div>

        <!-- 4 Steps Management -->
        <div style="background: var(--surface-subtle); border: 1px solid var(--border-soft); border-radius: var(--radius-sm); padding: 16px;">
          <h4 style="font-size: 13px; font-weight: 800; text-transform: uppercase; margin: 0 0 12px 0; color: var(--brand-deep);">
            <i class="fa-solid fa-list-ol"></i> 4-Step Examination Protocol Blocks
          </h4>
          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 14px;">
            <div>
              <label style="display: block; font-size: 11px; font-weight: 700; margin-bottom: 4px;">Step 1</label>
              <input type="text" name="online_tests_step1_title" value="{{ cms('online_tests_step1_title', 'Free Registration') }}" class="form-tactical" style="margin-bottom: 6px;">
              <textarea name="online_tests_step1_desc" rows="2" class="form-tactical">{{ cms('online_tests_step1_desc', 'Create your profile in 60 seconds') }}</textarea>
            </div>
            <div>
              <label style="display: block; font-size: 11px; font-weight: 700; margin-bottom: 4px;">Step 2</label>
              <input type="text" name="online_tests_step2_title" value="{{ cms('online_tests_step2_title', 'Select Mock Test') }}" class="form-tactical" style="margin-bottom: 6px;">
              <textarea name="online_tests_step2_desc" rows="2" class="form-tactical">{{ cms('online_tests_step2_desc', 'Choose Army, Navy, Air Force, or Police') }}</textarea>
            </div>
            <div>
              <label style="display: block; font-size: 11px; font-weight: 700; margin-bottom: 4px;">Step 3</label>
              <input type="text" name="online_tests_step3_title" value="{{ cms('online_tests_step3_title', 'Wait for Start Timer') }}" class="form-tactical" style="margin-bottom: 6px;">
              <textarea name="online_tests_step3_desc" rows="2" class="form-tactical">{{ cms('online_tests_step3_desc', 'Countdown unlocks test access at scheduled time') }}</textarea>
            </div>
            <div>
              <label style="display: block; font-size: 11px; font-weight: 700; margin-bottom: 4px;">Step 4</label>
              <input type="text" name="online_tests_step4_title" value="{{ cms('online_tests_step4_title', 'Live Timed Test') }}" class="form-tactical" style="margin-bottom: 6px;">
              <textarea name="online_tests_step4_desc" rows="2" class="form-tactical">{{ cms('online_tests_step4_desc', 'Instant scorecards & detailed solutions') }}</textarea>
            </div>
          </div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 10px;">
          <a href="{{ route('online_tests') }}" target="_blank" class="btn-tactical btn-tactical-outline">
            <i class="fa-solid fa-eye"></i> View Live Tests Page
          </a>
          <button type="submit" class="btn-tactical btn-tactical-primary">
            <i class="fa-solid fa-floppy-disk"></i> Save Online Test Settings
          </button>
        </div>
      </div>
    </div>
  </form>

  <!-- Quick Exam Engine Shortcuts -->
  <div class="tactical-card">
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-soft); padding-bottom: 14px; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
      <div>
        <h3 style="font-size: 17px; font-weight: 800; color: var(--brand-deep); margin: 0 0 4px 0;">
          <i class="fa-solid fa-list-check" style="color: var(--accent-gold);"></i> Online Assessments & Question Bank
        </h3>
        <p style="font-size: 12.5px; color: var(--text-muted); margin: 0;">
          Direct administrative shortcuts to configure tests, questions, WAT word flashcards, and examine candidate attempts.
        </p>
      </div>
      <div style="display: flex; gap: 8px;">
        <a href="{{ route('admin.exam_management.index') }}" class="btn-tactical btn-tactical-primary" style="font-size: 12px;">
          <i class="fa-solid fa-gear"></i> Exam Management Hub
        </a>
        <a href="{{ route('admin.exams.attempts') }}" class="btn-tactical btn-tactical-outline" style="font-size: 12px;">
          <i class="fa-solid fa-square-poll-vertical"></i> View Exam Results
        </a>
      </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px;">
      @foreach($exams as $e)
        <div style="background: var(--surface-subtle); border: 1px solid var(--border-soft); border-radius: var(--radius-sm); padding: 16px;">
          <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
            <strong style="color: var(--brand-deep); font-size: 14px;">{{ $e->title }}</strong>
            <span class="badge badge-info">{{ $e->questions_count }} Qs</span>
          </div>
          <p style="font-size: 12px; color: var(--text-muted); margin: 0 0 12px 0;">
            Type: <strong>{{ strtoupper(str_replace('_', ' ', $e->exam_type)) }}</strong> • Time: {{ $e->duration_minutes }} Mins
          </p>
          <div style="display: flex; gap: 8px;">
            @if($e->exam_type === 'word_association')
              <a href="{{ route('admin.exams.wat_words', $e->id) }}" class="btn-tactical btn-tactical-outline" style="font-size: 11px; padding: 4px 10px;">
                <i class="fa-solid fa-font"></i> Manage Words
              </a>
            @else
              <a href="{{ route('admin.exams.questions', $e->id) }}" class="btn-tactical btn-tactical-outline" style="font-size: 11px; padding: 4px 10px;">
                <i class="fa-solid fa-file-circle-question"></i> Questions
              </a>
            @endif
            <a href="{{ route('admin.exam_management.edit', $e->id) }}" class="btn-tactical btn-tactical-outline" style="font-size: 11px; padding: 4px 10px;">
              <i class="fa-solid fa-pen-to-square"></i> Edit
            </a>
          </div>
        </div>
      @endforeach
    </div>
  </div>

</div>
@endsection
