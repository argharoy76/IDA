@extends('layouts.portal')

@section('title', 'MCQ PDF Import Verification')
@section('page_title', 'MCQ PDF Import Verification')
@section('page_subtitle', 'Review extracted questions, options, and answer keys before committing to the ' . $branchName . ' Question Bank')

@section('topbar_actions')
  <a href="{{ route('admin.exams.branch', $branch) }}" class="btn-tactical btn-tactical-outline">
    <i class="fa-solid fa-arrow-left"></i> Cancel & Return
  </a>
@endsection

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

  <!-- Summary Banner -->
  <div class="tactical-card" style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.08) 0%, rgba(59, 130, 246, 0.08) 100%); border-color: rgba(16, 185, 129, 0.3); padding: 22px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
      <div>
        <span class="badge badge-emerald" style="margin-bottom: 6px;">PARSING COMPLETE</span>
        <h2 style="font-size: 20px; font-weight: 800; color: #ffffff; margin: 0 0 6px 0;">
          {{ $parseResult['total_detected'] }} MCQs Extracted for {{ $branchName }}
        </h2>
        <p style="margin: 0; font-size: 13px; color: #94a3b8;">
          <strong style="color: #10b981;">{{ $parseResult['valid_count'] }}</strong> questions are 100% complete with answer keys and options.
          @if($parseResult['flagged_count'] > 0)
            <span style="color: #f59e0b; font-weight: 700;">{{ $parseResult['flagged_count'] }} questions flagged for incomplete keys or options.</span>
          @endif
        </p>
      </div>

      <form action="{{ route('admin.exams.confirm_import', $branch) }}" method="POST">
        @csrf
        <input type="hidden" name="questions_json" value="{{ json_encode($parseResult['questions']) }}">
        <button type="submit" class="btn-tactical btn-tactical-primary" style="padding: 10px 22px; font-size: 14px;">
          <i class="fa-solid fa-circle-check"></i> Confirm & Import {{ $parseResult['valid_count'] }} Questions to Bank
        </button>
      </form>
    </div>
  </div>

  <!-- Detected Questions List -->
  <div class="tactical-card" style="padding: 0; overflow: hidden;">
    <div style="padding: 16px 20px; border-bottom: 1px solid var(--border-soft); display: flex; justify-content: space-between; align-items: center;">
      <h3 style="font-size: 15px; font-weight: 700; margin: 0; color: #ffffff;">
        <i class="fa-solid fa-list-ol" style="color: var(--accent-gold);"></i> Extracted Questions Preview
      </h3>
      <span style="font-size: 12px; color: #94a3b8;">Showing all {{ count($parseResult['questions']) }} extracted items</span>
    </div>

    <div style="padding: 20px; display: flex; flex-direction: column; gap: 16px; max-height: 800px; overflow-y: auto;">
      @foreach($parseResult['questions'] as $q)
        <div style="background: var(--surface-subtle); border: 1.5px solid {{ $q['is_valid'] ? 'var(--border-soft)' : '#fde68a' }}; border-radius: 8px; padding: 16px;">
          <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
            <div style="display: flex; align-items: center; gap: 8px;">
              <span style="font-weight: 800; color: #ffffff; font-size: 14px;">#{{ $q['index'] }}</span>
              @if($q['is_valid'])
                <span class="badge badge-emerald" style="font-size: 10.5px;"><i class="fa-solid fa-check"></i> VALID</span>
              @else
                <span class="badge badge-gold" style="font-size: 10.5px;"><i class="fa-solid fa-triangle-exclamation"></i> REVIEW NEEDED</span>
              @endif
            </div>

            <div>
              <span style="font-size: 12px; color: #94a3b8;">Correct Answer:</span>
              <strong style="color: {{ $q['correct_answer'] ? '#10b981' : '#ef4444' }}; font-size: 14px; background: rgba(16, 185, 129, 0.1); border: 1px solid {{ $q['correct_answer'] ? '#10b981' : '#ef4444' }}; padding: 2px 8px; border-radius: 4px;">
                {{ $q['correct_answer'] ?? 'NOT FOUND' }}
              </strong>
            </div>
          </div>

          <p style="font-weight: 700; font-size: 14px; margin: 0 0 12px 0; color: #ffffff; line-height: 1.5;">
            {{ $q['question_text'] }}
          </p>

          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 8px; margin-bottom: 10px; font-size: 12.5px;">
            <div style="padding: 8px 12px; border-radius: 6px; background: {{ $q['correct_answer'] === 'A' ? '#ecfdf5' : '#ffffff' }}; border: 1.5px solid {{ $q['correct_answer'] === 'A' ? '#059669' : 'var(--border-soft)' }};">
              <strong style="color: {{ $q['correct_answer'] === 'A' ? '#059669' : 'var(--brand-deep)' }};">A)</strong> {{ $q['option_a'] }}
            </div>
            <div style="padding: 8px 12px; border-radius: 6px; background: {{ $q['correct_answer'] === 'B' ? '#ecfdf5' : '#ffffff' }}; border: 1.5px solid {{ $q['correct_answer'] === 'B' ? '#059669' : 'var(--border-soft)' }};">
              <strong style="color: {{ $q['correct_answer'] === 'B' ? '#059669' : 'var(--brand-deep)' }};">B)</strong> {{ $q['option_b'] }}
            </div>
            <div style="padding: 8px 12px; border-radius: 6px; background: {{ $q['correct_answer'] === 'C' ? '#ecfdf5' : '#ffffff' }}; border: 1.5px solid {{ $q['correct_answer'] === 'C' ? '#059669' : 'var(--border-soft)' }};">
              <strong style="color: {{ $q['correct_answer'] === 'C' ? '#059669' : 'var(--brand-deep)' }};">C)</strong> {{ $q['option_c'] }}
            </div>
            <div style="padding: 8px 12px; border-radius: 6px; background: {{ $q['correct_answer'] === 'D' ? '#ecfdf5' : '#ffffff' }}; border: 1.5px solid {{ $q['correct_answer'] === 'D' ? '#059669' : 'var(--border-soft)' }};">
              <strong style="color: {{ $q['correct_answer'] === 'D' ? '#059669' : 'var(--brand-deep)' }};">D)</strong> {{ $q['option_d'] }}
            </div>
          </div>

          @if($q['explanation'])
            <div style="font-size: 12px; color: #92400e; background: #fffbeb; border: 1px solid #fde68a; padding: 8px 12px; border-radius: 6px;">
              <strong>Explanation:</strong> {{ $q['explanation'] }}
            </div>
          @endif

          @if(!empty($q['errors']))
            <div style="margin-top: 8px; font-size: 11.5px; color: #dc2626;">
              <i class="fa-solid fa-circle-exclamation"></i> {{ implode(', ', $q['errors']) }}
            </div>
          @endif
        </div>
      @endforeach
    </div>

    <!-- Bottom Confirm Action -->
    <div style="padding: 16px 20px; border-top: 1px solid var(--border-soft); background: var(--surface-subtle); display: flex; justify-content: space-between; align-items: center;">
      <a href="{{ route('admin.exams.branch', $branch) }}" class="btn-tactical btn-tactical-outline">
        <i class="fa-solid fa-arrow-left"></i> Discard & Back
      </a>

      <form action="{{ route('admin.exams.confirm_import', $branch) }}" method="POST">
        @csrf
        <input type="hidden" name="questions_json" value="{{ json_encode($parseResult['questions']) }}">
        <button type="submit" class="btn-tactical btn-tactical-primary">
          <i class="fa-solid fa-circle-check"></i> Commit & Import All {{ count($parseResult['questions']) }} Questions
        </button>
      </form>
    </div>
  </div>

</div>
@endsection
