@extends('layouts.public')

@section('title', 'Assessment Scorecard: ' . ($attempt->exam->title ?? 'Exam'))

@section('content')
<style>
  @media (max-width: 768px) {
    .scorecard-banner-right {
      flex-direction: column !important;
      align-items: flex-start !important;
      border-top: 1px solid rgba(255, 255, 255, 0.15) !important;
      padding-top: 16px !important;
      width: 100% !important;
      gap: 12px !important;
    }
    .scorecard-banner-right > div:first-child {
      text-align: left !important;
    }
    .scorecard-badge-wrap {
      padding-left: 0 !important;
      border-left: none !important;
    }
  }
</style>

<!-- Scorecard Hero Header -->
<section style="background: linear-gradient(135deg, #022c22 0%, #064e3b 100%); color: #ffffff; padding: 48px 20px 40px 20px; border-bottom: 3px solid var(--accent-gold);">
  <div style="max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 24px;">
    <div>
      <div style="display: flex; gap: 8px; align-items: center; margin-bottom: 10px;">
        <span class="badge" style="background: {{ $attempt->exam->branchColor() ?? '#059669' }}; color: #ffffff; font-size: 11px; padding: 4px 10px; border-radius: 6px; font-weight: 700;">
          <i class="fa-solid {{ $attempt->exam->branchIcon() ?? 'fa-award' }}"></i> {{ strtoupper($attempt->exam->branch ?? 'TEST') }}
        </span>
        <span class="badge" style="background: rgba(255,255,255,0.15); color: #fff; font-size: 11px; padding: 4px 10px; border-radius: 6px;">
          {{ strtoupper(str_replace('_', ' ', $attempt->exam->exam_type ?? 'mcq')) }}
        </span>
      <div style="font-size: 11px; text-transform: uppercase; color: var(--accent-gold); font-weight: 700; letter-spacing: 0.5px; margin-bottom: 2px;">
        Assessment Scorecard
      </div>
      <h1 style="font-size: 26px; font-weight: 800; margin: 0 0 6px 0; color: #ffffff; font-family: 'Poppins', sans-serif;">
        Assessment Scorecard: {{ $attempt->exam->title ?? 'Assessment Test' }}
      </h1>
      <p style="font-size: 13.5px; color: #93c5fd; margin: 0;">
        Candidate: <strong>{{ auth()->user()->name }}</strong> | Attempt #{{ $attempt->attempt_number }} | Submitted on {{ $attempt->completed_at ? \Carbon\Carbon::parse($attempt->completed_at)->format('d M Y, h:i A') : 'Completed' }}
      </p>
    </div>

    <div class="scorecard-banner-right" style="display: flex; gap: 20px; align-items: center;">
      <div style="text-align: right;">
        <div style="font-size: 11px; text-transform: uppercase; color: #94a3b8; font-weight: 700; letter-spacing: 0.5px;">Your Final Score</div>
        <div style="font-size: 36px; font-family: 'Poppins', monospace; font-weight: 800; color: {{ $attempt->is_passed ? '#34d399' : '#f87171' }}; line-height: 1;">
          {{ number_format($attempt->score, 2) }} <span style="font-size: 20px; color: #cbd5e1;">/ {{ $attempt->total_marks }}</span>
        </div>
        <div style="font-size: 12px; color: #cbd5e1; margin-top: 4px;">
          Pass Mark: {{ $attempt->exam->pass_marks }} (Score: {{ round($attempt->percentage) }}%)
        </div>
      </div>

      <div class="scorecard-badge-wrap" style="padding-left: 20px; border-left: 1px solid rgba(255,255,255,0.2);">
        @if($attempt->exam->exam_type === 'word_association')
          <span style="background: #fef3c7; color: #92400e; padding: 10px 18px; font-size: 14px; font-weight: 800; border-radius: 8px; display: inline-flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-clock"></i> SUBMITTED
          </span>
        @elseif($attempt->is_passed)
          <span style="background: #ecfdf5; color: #047857; padding: 10px 18px; font-size: 14px; font-weight: 800; border-radius: 8px; display: inline-flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-check"></i> QUALIFIED
          </span>
        @else
          <span style="background: #fef2f2; color: #b91c1c; padding: 10px 18px; font-size: 14px; font-weight: 800; border-radius: 8px; display: inline-flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-xmark"></i> NOT QUALIFIED
          </span>
        @endif
      </div>
    </div>
  </div>
</section>

<!-- Scorecard Body Content -->
<div style="max-width: 1200px; margin: 30px auto 60px auto; padding: 0 20px; display: flex; flex-direction: column; gap: 24px;">

  <!-- Quick Action Strip -->
  <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
    <a href="{{ route('online_tests') }}" class="btn-secondary" style="padding: 9px 18px; font-size: 13px; text-decoration: none;">
      <i class="fa-solid fa-arrow-left"></i> Back to Online Tests
    </a>

    <button onclick="window.print()" class="btn-primary" style="padding: 9px 18px; font-size: 13px; cursor: pointer;">
      <i class="fa-solid fa-print"></i> Print Official Scorecard
    </button>
  </div>

  <!-- Performance Metrics Cards -->
  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px; box-shadow: 0 2px 8px rgba(0,0,0,0.03);">
      <div style="font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: 700; margin-bottom: 4px;">Total Questions</div>
      <div style="font-size: 24px; font-weight: 800; color: #0f172a; font-family: 'Poppins', sans-serif;">
        {{ $attempt->exam->exam_type === 'word_association' ? $attempt->exam->watWords->count() : $attempt->exam->questions->count() }}
      </div>
    </div>

    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px; box-shadow: 0 2px 8px rgba(0,0,0,0.03);">
      <div style="font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: 700; margin-bottom: 4px;">Correct Answers</div>
      <div style="font-size: 24px; font-weight: 800; color: #059669; font-family: 'Poppins', sans-serif;">
        {{ $attempt->correct_answers }}
      </div>
    </div>

    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px; box-shadow: 0 2px 8px rgba(0,0,0,0.03);">
      <div style="font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: 700; margin-bottom: 4px;">Incorrect Answers</div>
      <div style="font-size: 24px; font-weight: 800; color: #dc2626; font-family: 'Poppins', sans-serif;">
        {{ $attempt->wrong_answers }}
      </div>
    </div>

    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px; box-shadow: 0 2px 8px rgba(0,0,0,0.03);">
      <div style="font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: 700; margin-bottom: 4px;">Negative Marks Deducted</div>
      <div style="font-size: 24px; font-weight: 800; color: #dc2626; font-family: 'Poppins', sans-serif;">
        -{{ number_format($attempt->negative_marks_deducted, 2) }}
      </div>
    </div>
  </div>

  <!-- Review Section: Solution Key & Detailed Explanations -->
  @if($attempt->exam && $attempt->exam->exam_type === 'word_association')
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.04);">
      <div style="padding: 16px 22px; border-bottom: 1px solid #e2e8f0; background: #f8fafc;">
        <h3 style="font-size: 16px; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 8px; color: #0f172a;">
          <i class="fa-solid fa-bolt" style="color: #d97706;"></i> Submitted Word Associations
        </h3>
      </div>

      <table style="width: 100%; border-collapse: collapse; font-size: 13.5px;">
        <thead>
          <tr style="background: #f1f5f9; border-bottom: 1px solid #e2e8f0; text-align: left;">
            <th style="padding: 12px 18px; font-weight: 700; color: #475569;">#</th>
            <th style="padding: 12px 18px; font-weight: 700; color: #475569;">Prompt Word</th>
            <th style="padding: 12px 18px; font-weight: 700; color: #475569;">Your Reaction Sentence</th>
            <th style="padding: 12px 18px; font-weight: 700; color: #475569;">Reaction Time</th>
          </tr>
        </thead>
        <tbody>
          @foreach($attempt->answers as $idx => $ans)
            <tr style="border-bottom: 1px solid #f1f5f9;">
              <td style="padding: 12px 18px; font-weight: 700; color: #64748b;">#{{ $idx + 1 }}</td>
              <td style="padding: 12px 18px;"><strong style="font-size: 15px; color: #0f172a; text-transform: uppercase;">{{ $ans->watWord->word ?? 'WORD' }}</strong></td>
              <td style="padding: 12px 18px; font-weight: 600; color: #1e293b;">{{ $ans->text_answer ?: '— [Timed out] —' }}</td>
              <td style="padding: 12px 18px;"><span class="badge badge-navy" style="font-size: 11px;">{{ $ans->response_time_seconds ? $ans->response_time_seconds . 's' : '15s' }}</span></td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @else
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.04);">
      <div style="padding: 18px 24px; border-bottom: 1px solid #e2e8f0; background: #f8fafc;">
        <h3 style="font-size: 16px; font-weight: 800; margin: 0; display: flex; align-items: center; gap: 8px; color: #0f172a;">
          <i class="fa-solid fa-list-check" style="color: #059669;"></i> Solution Key & Detailed Explanations
        </h3>
      </div>

      <div style="padding: 20px; display: flex; flex-direction: column; gap: 18px;">
        @foreach($attempt->answers as $idx => $ans)
          @php $q = $ans->question; @endphp
          @if($q)
            <div style="background: #ffffff; border: 1.5px solid {{ $ans->is_correct ? '#a7f3d0' : '#fecaca' }}; border-radius: 10px; padding: 18px; box-shadow: 0 1px 4px rgba(0,0,0,0.02);">
              <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                <div style="display: flex; align-items: center; gap: 8px;">
                  <span style="font-weight: 800; color: #0f172a; font-size: 14px;">Question {{ $idx + 1 }}</span>
                  @if($ans->is_correct)
                    <span style="background: #ecfdf5; color: #047857; padding: 3px 10px; border-radius: 6px; font-size: 11.5px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                      <i class="fa-solid fa-check"></i> Correct (+{{ $ans->marks_awarded }})
                    </span>
                  @elseif($ans->selected_option)
                    <span style="background: #fef2f2; color: #b91c1c; padding: 3px 10px; border-radius: 6px; font-size: 11.5px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                      <i class="fa-solid fa-xmark"></i> Incorrect (-{{ $ans->marks_deducted }})
                    </span>
                  @else
                    <span style="background: #f1f5f9; color: #475569; padding: 3px 10px; border-radius: 6px; font-size: 11.5px; font-weight: 700;">
                      Skipped
                    </span>
                  @endif
                </div>
              </div>

              <!-- Question Text -->
              <p style="font-weight: 600; font-size: 15px; color: #0f172a; margin: 0 0 14px 0; line-height: 1.6;">
                {{ $q->question_text }}
              </p>

              <!-- Options -->
              <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 12px;">
                @php
                  $rawOpts = is_array($q->options) ? $q->options : (json_decode($q->options, true) ?? []);
                  $opts = [];
                  foreach ($rawOpts as $k => $v) {
                      if (is_array($v) && isset($v['key'])) {
                          $opts[] = $v;
                      } else {
                          $opts[] = ['key' => is_string($k) ? $k : chr(65 + (int)$k), 'text' => $v];
                      }
                  }
                @endphp
                @foreach($opts as $opt)
                  @php
                    $isUser = $ans->selected_option === $opt['key'];
                    $isRight = $q->correct_answer === $opt['key'];
                    $border = $isRight ? '#059669' : ($isUser ? '#dc2626' : '#e2e8f0');
                    $bg = $isRight ? '#ecfdf5' : ($isUser ? '#fef2f2' : '#ffffff');
                  @endphp
                  <div style="padding: 10px 14px; border-radius: 8px; font-size: 13px; background: {{ $bg }}; border: 1.5px solid {{ $border }}; display: flex; justify-content: space-between; align-items: center; color: #1e293b;">
                    <div><strong style="color: #d97706; margin-right: 4px;">{{ $opt['key'] }}:</strong> {{ $opt['text'] }}</div>
                    @if($isRight) <span style="color: #059669; font-weight: 700; font-size: 11px;"><i class="fa-solid fa-check"></i> Correct Key</span> @endif
                    @if($isUser && !$isRight) <span style="color: #dc2626; font-weight: 700; font-size: 11px;"><i class="fa-solid fa-xmark"></i> Your Selection</span> @endif
                  </div>
                @endforeach
              </div>

              <!-- Explanation Hint -->
              @if($q->explanation)
                <div style="font-size: 13px; color: #92400e; background: #fffbeb; border: 1px solid #fde68a; padding: 10px 14px; border-radius: 8px; line-height: 1.5;">
                  <strong style="color: #92400e;"><i class="fa-solid fa-lightbulb"></i> Solution Explanation:</strong> {{ $q->explanation }}
                </div>
              @endif
            </div>
          @endif
        @endforeach
      </div>
    </div>
  @endif

</div>
@endsection
