@extends('layouts.portal')

@section('title', 'Exam Evaluation Paper')
@section('page_title', 'Assessment Evaluation: ' . ($attempt->exam->title ?? 'Exam'))
@section('page_subtitle', 'Detailed candidate response sheet, answer key comparison, and scoring audit')

@section('topbar_actions')
  <a href="{{ route('admin.exams.attempts') }}" class="btn-tactical btn-tactical-outline">
    <i class="fa-solid fa-arrow-left"></i> Back to Submissions
  </a>
@endsection

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

  <!-- Candidate & Score Hero Card -->
  <div class="tactical-card" style="background: linear-gradient(135deg, rgba(15, 23, 42, 0.9), rgba(6, 78, 59, 0.3)); border: 1px solid var(--border-soft);">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
      <div>
        <span class="badge badge-navy" style="margin-bottom: 8px;">
          {{ strtoupper(str_replace('_', ' ', $attempt->exam->exam_type ?? 'mcq')) }}
        </span>
        <h2 style="font-size: 20px; font-weight: 800; margin: 0 0 6px 0; color: #fff;">
          Candidate: {{ $attempt->user->name ?? 'Candidate' }}
        </h2>
        <div style="font-size: 13px; color: var(--text-muted);">
          @if($attempt->student)
            Cadet ID: <strong style="color: var(--accent-gold);">{{ $attempt->student->student_id_code }}</strong> (Roll: {{ $attempt->student->roll_number }}) | Squad: {{ $attempt->student->batch->name ?? 'Squadron' }}
          @else
            External Test Taker | Registered: {{ $attempt->user->email ?? 'N/A' }}
          @endif
        </div>
      </div>

      <!-- Score Capsule -->
      <div style="display: flex; gap: 16px; align-items: center;">
        <div style="text-align: right;">
          <div style="font-size: 11px; text-transform: uppercase; color: var(--text-muted); font-weight: 700;">Final Score</div>
          <div style="font-size: 32px; font-family: 'Plus Jakarta Sans', monospace; font-weight: 800; color: {{ $attempt->is_passed ? '#10b981' : '#ef4444' }};">
            {{ number_format($attempt->score, 2) }} / {{ $attempt->total_marks }}
          </div>
          <div style="font-size: 12px; color: var(--text-muted);">
            Pass Mark: {{ $attempt->exam->pass_marks ?? 0 }} (Achieved: {{ round($attempt->percentage) }}%)
          </div>
        </div>

        <div style="padding-left: 16px; border-left: 1px solid var(--border-soft);">
          @if($attempt->is_passed)
            <span class="badge badge-emerald" style="padding: 10px 16px; font-size: 14px; font-weight: 800;">
              <i class="fa-solid fa-check-double"></i> PASSED
            </span>
          @else
            <span class="badge badge-danger" style="padding: 10px 16px; font-size: 14px; font-weight: 800;">
              <i class="fa-solid fa-triangle-exclamation"></i> FAILED
            </span>
          @endif
        </div>
      </div>
    </div>
  </div>

  <!-- Breakdown Stats Strip -->
  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 14px;">
    <div class="stat-card" style="padding: 14px 18px;">
      <div>
        <div class="stat-label">Total Questions</div>
        <div class="stat-value" style="font-size: 20px;">{{ $attempt->exam->exam_type === 'word_association' ? $attempt->exam->watWords->count() : $attempt->exam->questions->count() }}</div>
      </div>
    </div>
    <div class="stat-card" style="padding: 14px 18px;">
      <div>
        <div class="stat-label">Correct Answers</div>
        <div class="stat-value" style="color: #10b981; font-size: 20px;">{{ $attempt->correct_answers }}</div>
      </div>
    </div>
    <div class="stat-card" style="padding: 14px 18px;">
      <div>
        <div class="stat-label">Incorrect Answers</div>
        <div class="stat-value" style="color: #ef4444; font-size: 20px;">{{ $attempt->wrong_answers }}</div>
      </div>
    </div>
    <div class="stat-card" style="padding: 14px 18px;">
      <div>
        <div class="stat-label">Negative Marks</div>
        <div class="stat-value" style="color: #ef4444; font-size: 20px;">-৳{{ number_format($attempt->negative_marks_deducted, 2) }}</div>
      </div>
    </div>
  </div>

  <!-- Detailed Question & Answer Breakdown -->
  @if($attempt->exam && $attempt->exam->exam_type === 'word_association')
    <!-- WAT Responses Table -->
    <div class="tactical-card" style="padding: 0; overflow: hidden;">
      <div style="padding: 16px 20px; border-bottom: 1px solid var(--border-soft);">
        <h3 style="font-size: 15px; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 8px;">
          <i class="fa-solid fa-bolt" style="color: var(--accent-gold);"></i> Word Association Responses
        </h3>
      </div>

      <table class="tactical-table">
        <thead>
          <tr>
            <th>Seq</th>
            <th>Flashed Word</th>
            <th>Candidate Formulated Sentence / Response</th>
            <th>Response Time</th>
          </tr>
        </thead>
        <tbody>
          @forelse($attempt->answers as $idx => $ans)
            <tr>
              <td><span style="font-family: 'Plus Jakarta Sans', monospace; font-weight: 700;">#{{ $idx + 1 }}</span></td>
              <td>
                <strong style="font-family: 'Plus Jakarta Sans', monospace; font-size: 15px; letter-spacing: 1px; color: #60a5fa;">
                  {{ $ans->watWord->word ?? 'WORD' }}
                </strong>
              </td>
              <td>
                <span style="font-size: 13.5px; color: #fff; font-weight: 500;">
                  {{ $ans->text_answer ?: '— [No response provided in 15 seconds] —' }}
                </span>
              </td>
              <td>
                <span class="badge badge-navy">{{ $ans->response_time_seconds ? $ans->response_time_seconds . 's' : 'Timed Out' }}</span>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="4" style="text-align: center; padding: 32px; color: var(--text-muted);">
                No individual answer logs recorded.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  @else
    <!-- MCQ Evaluation List -->
    <div class="tactical-card" style="padding: 0; overflow: hidden;">
      <div style="padding: 16px 20px; border-bottom: 1px solid var(--border-soft);">
        <h3 style="font-size: 15px; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 8px;">
          <i class="fa-solid fa-list-check" style="color: var(--accent-gold);"></i> Question-by-Question Evaluation
        </h3>
      </div>

      <div style="padding: 16px; display: flex; flex-direction: column; gap: 16px;">
        @forelse($attempt->answers as $idx => $ans)
          @php $q = $ans->question; @endphp
          @if($q)
            <div style="background: var(--surface-subtle); border: 1.5px solid {{ $ans->is_correct ? '#a7f3d0' : '#fecaca' }}; border-radius: 8px; padding: 16px;">
              <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                <div style="display: flex; align-items: center; gap: 8px;">
                  <span style="font-weight: 800; color: var(--brand-deep);">Q{{ $idx + 1 }}</span>
                  @if($ans->is_correct)
                    <span class="badge badge-emerald"><i class="fa-solid fa-check"></i> Correct (+{{ $ans->marks_awarded }})</span>
                  @elseif($ans->selected_option)
                    <span class="badge badge-danger"><i class="fa-solid fa-xmark"></i> Incorrect (-{{ $ans->marks_deducted }})</span>
                  @else
                    <span class="badge badge-navy">Skipped (0.00)</span>
                  @endif
                </div>

                <span style="font-size: 11px; color: var(--text-muted);">
                  Difficulty: {{ ucfirst($q->difficulty ?? 'medium') }}
                </span>
              </div>

              <p style="font-weight: 700; font-size: 14px; color: var(--text-main); margin: 0 0 12px 0;">
                {{ $q->question_text }}
              </p>

              <!-- Options -->
              <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 10px;">
                @php $opts = is_array($q->options) ? $q->options : json_decode($q->options, true) ?? []; @endphp
                @foreach($opts as $opt)
                  @php
                    $isUser = $ans->selected_option === $opt['key'];
                    $isRight = $q->correct_answer === $opt['key'];
                    $border = $isRight ? '#059669' : ($isUser ? '#ef4444' : 'var(--border-soft)');
                    $bg = $isRight ? '#ecfdf5' : ($isUser ? '#fef2f2' : '#ffffff');
                  @endphp
                  <div style="padding: 8px 12px; border-radius: 6px; font-size: 12.5px; background: {{ $bg }}; border: 1.5px solid {{ $border }}; display: flex; justify-content: space-between; align-items: center; color: var(--text-main);">
                    <div>
                      <strong>{{ $opt['key'] }}:</strong> {{ $opt['text'] }}
                    </div>
                    @if($isRight)
                      <span style="color: #059669; font-weight: 700; font-size: 11px;"><i class="fa-solid fa-check"></i> Key</span>
                    @elseif($isUser)
                      <span style="color: #ef4444; font-weight: 700; font-size: 11px;"><i class="fa-solid fa-xmark"></i> Chosen</span>
                    @endif
                  </div>
                @endforeach
              </div>

              @if($q->explanation)
                <div style="font-size: 12.5px; color: #92400e; background: #fffbeb; border: 1px solid #fde68a; padding: 10px 14px; border-radius: 6px;">
                  <strong style="color: #92400e;">Solution Hint:</strong> {{ $q->explanation }}
                </div>
              @endif
            </div>
          @endif
        @empty
          <div style="text-align: center; padding: 32px; color: var(--text-muted);">
            No individual question response details logged.
          </div>
        @endforelse
      </div>
    </div>
  @endif

</div>
@endsection
