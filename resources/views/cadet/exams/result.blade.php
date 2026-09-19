@extends('layouts.public')

@section('title', 'Exam Details: ' . ($attempt->exam->title ?? 'Exam'))

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

<!-- Exam Details Hero Header -->
<section style="background: linear-gradient(135deg, #022c22 0%, #064e3b 100%); color: #ffffff; padding: 48px 20px 40px 20px; border-bottom: 3px solid var(--accent-gold);">
  <div style="max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 24px;">
    <div>
      <div style="display: flex; gap: 8px; align-items: center; margin-bottom: 10px;">
        <span class="badge" style="background: {{ $attempt->exam->branchColor() ?? '#059669' }}; color: #ffffff; font-size: 11px; padding: 4px 10px; border-radius: 6px; font-weight: 700;">
          <i class="fa-solid {{ $attempt->exam->branchIcon() ?? 'fa-award' }}"></i> {{ strtoupper($attempt->exam->branch ?? 'CADET') }}
        </span>
        <span class="badge" style="background: rgba(255,255,255,0.15); color: #fff; font-size: 11px; padding: 4px 10px; border-radius: 6px;">
          {{ strtoupper(str_replace('_', ' ', $attempt->exam->exam_type ?? 'mcq')) }}
        </span>
      </div>
      <div style="font-size: 11px; text-transform: uppercase; color: var(--accent-gold); font-weight: 700; letter-spacing: 0.5px; margin-bottom: 2px;">
        <i class="fa-solid fa-file-lines"></i> Exam Details &amp; Assessment Scorecard Review
      </div>
      <h1 style="font-size: 26px; font-weight: 800; margin: 0 0 6px 0; color: #ffffff; font-family: 'Poppins', sans-serif;">
        Exam Details: {{ $attempt->exam->title ?? 'Assessment Examination' }}
      </h1>
      <p style="font-size: 13.5px; color: #93c5fd; margin: 0;">
        Cadet: <strong>{{ auth()->user()->name }}</strong> | Attempt #{{ $attempt->attempt_number }} |
        @if(!$attempt->isRankPublished())
          <span style="color: #93c5fd; font-weight: 700;"><i class="fa-regular fa-clock"></i> Rank: Pending Exam Schedule Conclusion</span>
        @else
          <span style="color: #fef08a; font-weight: 700;"><i class="fa-solid fa-trophy"></i> Rank #{{ $attempt->rank }} of {{ $attempt->total_candidates }} Candidates</span>
        @endif
        | Submitted on {{ $attempt->completed_at ? \Carbon\Carbon::parse($attempt->completed_at)->format('d M Y, h:i A') : 'Completed' }}
      </p>
    </div>

    <div class="scorecard-banner-right" style="display: flex; gap: 20px; align-items: center;">
      <div style="text-align: right;">
        <div style="font-size: 11px; text-transform: uppercase; color: #94a3b8; font-weight: 700; letter-spacing: 0.5px;">Mark Achieved</div>
        <div style="font-size: 36px; font-family: 'Poppins', monospace; font-weight: 800; color: {{ $attempt->is_passed ? '#34d399' : '#f87171' }}; line-height: 1;">
          {{ (fmod($attempt->score, 1) == 0 ? (int)$attempt->score : number_format($attempt->score, 2)) }} <span style="font-size: 20px; color: #cbd5e1;">/ {{ (int)$attempt->total_marks }}</span>
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

<!-- Details Body Content -->
<div style="max-width: 1200px; margin: 30px auto 60px auto; padding: 0 20px; display: flex; flex-direction: column; gap: 24px;">

  <!-- Quick Action Strip -->
  <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
    <div style="display: flex; gap: 10px;">
      <a href="{{ route('online_tests') }}" class="btn-secondary" style="padding: 9px 18px; font-size: 13px; text-decoration: none;">
        <i class="fa-solid fa-arrow-left"></i> Online Tests Catalog
      </a>
      <a href="{{ route('cadet.exams.index') }}" class="btn-secondary" style="padding: 9px 18px; font-size: 13px; text-decoration: none;">
        <i class="fa-solid fa-graduation-cap"></i> Cadet Exams
      </a>
      <a href="{{ route('cadet.exams.history') }}" class="btn-secondary" style="padding: 9px 18px; font-size: 13px; text-decoration: none;">
        <i class="fa-solid fa-clock-rotate-left"></i> Exam History
      </a>
    </div>

    <button onclick="window.print()" class="btn-primary" style="padding: 9px 18px; font-size: 13px; cursor: pointer;">
      <i class="fa-solid fa-print"></i> Print Exam Details
    </button>
  </div>

  <!-- Performance Metrics Cards -->
  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 14px;">
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.03);">
      <div style="font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: 700; margin-bottom: 4px;">Total Questions</div>
      <div style="font-size: 22px; font-weight: 800; color: #0f172a; font-family: 'Poppins', sans-serif;">
        {{ $attempt->exam->exam_type === 'word_association' ? $attempt->exam->watWords->count() : $attempt->exam->questions->count() }}
      </div>
    </div>

    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.03);">
      <div style="font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: 700; margin-bottom: 4px;">Correct Answers</div>
      <div style="font-size: 22px; font-weight: 800; color: #059669; font-family: 'Poppins', sans-serif;">
        {{ $attempt->correct_answers }}
      </div>
    </div>

    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.03);">
      <div style="font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: 700; margin-bottom: 4px;">Incorrect Answers</div>
      <div style="font-size: 22px; font-weight: 800; color: #dc2626; font-family: 'Poppins', sans-serif;">
        {{ $attempt->wrong_answers }}
      </div>
    </div>

    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.03);">
      <div style="font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: 700; margin-bottom: 4px;">Minus Marking Deducted</div>
      <div style="font-size: 22px; font-weight: 800; color: #dc2626; font-family: 'Poppins', sans-serif;">
        {{ $attempt->negative_marks_deducted > 0 ? '-' . number_format($attempt->negative_marks_deducted, 2) : '0.00' }}
      </div>
    </div>

    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.03);">
      <div style="font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: 700; margin-bottom: 4px;">Mark Achieved</div>
      <div style="font-size: 22px; font-weight: 800; color: {{ $attempt->is_passed ? '#059669' : '#dc2626' }}; font-family: 'Poppins', monospace;">
        {{ (fmod($attempt->score, 1) == 0 ? (int)$attempt->score : number_format($attempt->score, 2)) }}/{{ (int)$attempt->total_marks }}
      </div>
      <div style="font-size: 10px; color: #64748b; margin-top: 2px;">({{ round($attempt->percentage) }}%)</div>
    </div>

    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.03);">
      <div style="font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: 700; margin-bottom: 4px;">Candidate Rank</div>
      <div style="font-size: {{ $attempt->isRankPublished() ? '22px' : '16px' }}; font-weight: 800; color: {{ $attempt->isRankPublished() ? '#d97706' : '#64748b' }}; font-family: 'Poppins', sans-serif;">
        @if($attempt->isRankPublished())
          Rank #{{ $attempt->rank }}
        @else
          Pending
        @endif
      </div>
      @if(!$attempt->isRankPublished() && optional($attempt->exam)->schedule_end)
        <div style="font-size: 10px; color: #94a3b8; margin-top: 3px; font-weight: 500;">
          After {{ $attempt->exam->schedule_end->format('h:i A') }}
        </div>
      @endif
    </div>

    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.03);">
      <div style="font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: 700; margin-bottom: 4px;">Total Candidates</div>
      <div style="font-size: {{ $attempt->isRankPublished() ? '22px' : '16px' }}; font-weight: 800; color: {{ $attempt->isRankPublished() ? '#2563eb' : '#64748b' }}; font-family: 'Poppins', sans-serif;">
        @if($attempt->isRankPublished())
          {{ $attempt->total_candidates }}
        @else
          Pending
        @endif
      </div>
      @if(!$attempt->isRankPublished())
        <div style="font-size: 10px; color: #94a3b8; margin-top: 3px; font-weight: 500;">
          After schedule
        </div>
      @endif
    </div>
  </div>

  <!-- Candidate Comparison: All Candidates Scorecard & Position for this Exam -->
  @if($attempt->isRankPublished() || auth()->user()->isAdmin() || in_array(auth()->user()->role, ['super_admin', 'admin', 'instructor']))
    @php
      $leaderboard = $leaderboard ?? ($attempt->exam ? $attempt->exam->getLeaderboard() : collect());
    @endphp
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; overflow: hidden; box-shadow: 0 3px 12px rgba(0,0,0,0.04);">
      <div style="padding: 18px 24px; border-bottom: 1px solid #e2e8f0; background: #f8fafc; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <div>
          <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; color: #2563eb; letter-spacing: 0.6px; margin-bottom: 2px;">
            <i class="fa-solid fa-ranking-star"></i> Examination Standings
          </div>
          <h3 style="font-size: 17px; font-weight: 800; margin: 0; color: #0f172a; font-family: 'Poppins', sans-serif;">
            All Candidates Scorecard &amp; Merit Position
          </h3>
          <p style="font-size: 12px; color: #64748b; margin: 3px 0 0 0;">
            Comparative standing of all candidates who appeared for <strong>{{ $attempt->exam->title }}</strong> only.
          </p>
        </div>

        <div style="font-size: 12px; font-weight: 700; color: #475569; background: #ffffff; border: 1px solid #cbd5e1; padding: 6px 14px; border-radius: 999px; display: inline-flex; align-items: center; gap: 6px;">
          <i class="fa-solid fa-users" style="color: #2563eb;"></i>
          <span><strong>{{ $leaderboard->count() }}</strong> {{ $leaderboard->count() === 1 ? 'Candidate' : 'Total Candidates' }}</span>
        </div>
      </div>

      <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; font-size: 13px; text-align: left;">
          <thead>
            <tr style="background: #f1f5f9; border-bottom: 1px solid #e2e8f0; color: #475569; font-size: 11.5px; text-transform: uppercase; letter-spacing: 0.5px;">
              <th style="padding: 12px 18px; font-weight: 800; text-align: center; width: 95px;">Position</th>
              <th style="padding: 12px 18px; font-weight: 800;">Candidate Name</th>
              <th style="padding: 12px 18px; font-weight: 800; text-align: center;">Mark Obtained</th>
              <th style="padding: 12px 18px; font-weight: 800; text-align: center;">Percentage</th>
              <th style="padding: 12px 18px; font-weight: 800; text-align: center;">Result Status</th>
              <th style="padding: 12px 18px; font-weight: 800; text-align: right;">Submission Time</th>
            </tr>
          </thead>
          <tbody>
            @forelse($leaderboard as $cand)
              @php
                $isCurrentUser = (auth()->check() && ($cand->user_id === auth()->id() || ($student && $cand->student_id === $student->id)));
                $rowBg = $isCurrentUser ? '#eff6ff' : '#ffffff';
                $borderLeft = $isCurrentUser ? '3px solid #2563eb' : '3px solid transparent';
              @endphp
              <tr style="background: {{ $rowBg }}; border-bottom: 1px solid #f1f5f9; border-left: {{ $borderLeft }}; transition: background 0.15s ease;">
                {{-- Position / Rank --}}
                <td style="padding: 14px 18px; text-align: center;">
                  @if($cand->rank === 1)
                    <span style="background: #fef3c7; color: #b45309; border: 1px solid #fde68a; font-weight: 800; font-size: 11px; padding: 3px 9px; border-radius: 999px; display: inline-flex; align-items: center; gap: 4px;">
                      🥇 Rank #1
                    </span>
                  @elseif($cand->rank === 2)
                    <span style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; font-weight: 800; font-size: 11px; padding: 3px 9px; border-radius: 999px; display: inline-flex; align-items: center; gap: 4px;">
                      🥈 Rank #2
                    </span>
                  @elseif($cand->rank === 3)
                    <span style="background: #fff7ed; color: #c2410c; border: 1px solid #fed7aa; font-weight: 800; font-size: 11px; padding: 3px 9px; border-radius: 999px; display: inline-flex; align-items: center; gap: 4px;">
                      🥉 Rank #3
                    </span>
                  @else
                    <span style="background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0; font-weight: 800; font-size: 11px; padding: 3px 8px; border-radius: 999px;">
                      Rank #{{ $cand->rank }}
                    </span>
                  @endif
                </td>

                {{-- Candidate Name --}}
                <td style="padding: 14px 18px;">
                  <div style="display: flex; align-items: center; gap: 8px;">
                    <strong style="color: #0f172a; font-size: 13.5px;">{{ $cand->name }}</strong>
                    @if($isCurrentUser)
                      <span style="background: #2563eb; color: #ffffff; font-size: 9.5px; font-weight: 800; padding: 2px 7px; border-radius: 999px; text-transform: uppercase; letter-spacing: 0.5px;">YOU</span>
                    @endif
                  </div>
                </td>

                {{-- Mark Obtained --}}
                <td style="padding: 14px 18px; text-align: center;">
                  <strong style="font-size: 13.5px; font-family: monospace; color: {{ $cand->is_passed ? '#047857' : '#dc2626' }};">
                    {{ number_format($cand->score, 2) }}
                  </strong>
                  <span style="font-size: 11px; color: #64748b;"> / {{ (int)$cand->total_marks }}</span>
                </td>

                {{-- Percentage --}}
                <td style="padding: 14px 18px; text-align: center;">
                  <span style="font-weight: 700; color: #334155; font-size: 12.5px;">{{ round($cand->percentage) }}%</span>
                </td>

                {{-- Result Status --}}
                <td style="padding: 14px 18px; text-align: center;">
                  @if($cand->result_status === 'passed')
                    <span style="background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; padding: 3px 9px; border-radius: 999px; font-size: 10px; font-weight: 800; display: inline-flex; align-items: center; gap: 4px;">
                      <i class="fa-solid fa-circle-check"></i> QUALIFIED
                    </span>
                  @elseif($cand->result_status === 'failed')
                    <span style="background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; padding: 3px 9px; border-radius: 999px; font-size: 10px; font-weight: 800; display: inline-flex; align-items: center; gap: 4px;">
                      <i class="fa-solid fa-circle-xmark"></i> NOT QUALIFIED
                    </span>
                  @else
                    <span style="background: #fffbeb; color: #b45309; border: 1px solid #fde68a; padding: 3px 9px; border-radius: 999px; font-size: 10px; font-weight: 800; display: inline-flex; align-items: center; gap: 4px;">
                      <i class="fa-solid fa-clock"></i> SUBMITTED
                    </span>
                  @endif
                </td>

                {{-- Submission Time --}}
                <td style="padding: 14px 18px; text-align: right; color: #64748b; font-size: 12px;">
                  {{ $cand->completed_at ? \Carbon\Carbon::parse($cand->completed_at)->format('d M Y, h:i A') : 'Completed' }}
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" style="text-align: center; padding: 24px; color: #64748b;">
                  No candidate standings available for this examination.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div style="padding: 12px 20px; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px; font-size: 11.5px; color: #64748b;">
        <div>
          <i class="fa-solid fa-circle-info" style="color: #2563eb;"></i> Rankings are compiled by comparing candidate marks for <strong>{{ $attempt->exam->title }}</strong>.
        </div>
        <div>
          Minimum Result for Qualification: <strong>{{ $attempt->exam->pass_marks }} marks</strong>
        </div>
      </div>
    </div>
  @else
    <!-- Ranking Pending Notice (While Exam Schedule Is Live) -->
    <div style="background: #ffffff; border: 1.5px solid #fde68a; border-left: 5px solid #f59e0b; border-radius: 14px; padding: 18px 22px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px; box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
      <div style="display: flex; align-items: center; gap: 14px;">
        <div style="width: 44px; height: 44px; border-radius: 50%; background: #fffbeb; color: #d97706; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; border: 1px solid #fde68a;">
          <i class="fa-solid fa-clock-rotate-left"></i>
        </div>
        <div>
          <div style="font-size: 14.5px; font-weight: 800; color: #0f172a; font-family: 'Poppins', sans-serif;">
            Examination is Currently Live &bull; Candidate Ranking Pending
          </div>
          <div style="font-size: 12.5px; color: #64748b; margin-top: 3px; line-height: 1.5;">
            @if(optional($attempt->exam)->schedule_end)
              Official merit rankings and the full candidate scorecard will be released after the examination schedule concludes at <strong>{{ $attempt->exam->schedule_end->format('h:i A, M d, Y') }}</strong>.
            @else
              Official merit rankings and the candidate scorecard will be released after the examination concludes.
            @endif
            Your complete question paper, your selected choices, corrected answers, and minus markings are detailed below.
          </div>
        </div>
      </div>
    </div>
  @endif

  <!-- Review Section: Question Paper, Selected Answers & Minus Marking -->
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
      <div style="padding: 18px 24px; border-bottom: 1px solid #e2e8f0; background: #f8fafc; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
        <h3 style="font-size: 16px; font-weight: 800; margin: 0; display: flex; align-items: center; gap: 8px; color: #0f172a; font-family: 'Poppins', sans-serif;">
          <i class="fa-solid fa-file-signature" style="color: #2563eb;"></i> Question Paper, Answer Evaluation &amp; Explanations
        </h3>
        <div style="font-size: 12px; color: #64748b; font-weight: 600;">
          Showing {{ $attempt->exam->questions->count() }} {{ Str::plural('Question', $attempt->exam->questions->count()) }} &bull; Total Marks: {{ (int)$attempt->total_marks }}
        </div>
      </div>

      <div style="padding: 20px; display: flex; flex-direction: column; gap: 20px;">
        @php
          $answersByQuestionId = $attempt->answers->keyBy('question_id');
        @endphp
        @foreach($attempt->exam->questions as $idx => $q)
          @php
            $ans = $answersByQuestionId->get($q->id);
            $selectedChoice = $ans ? $ans->selected_option : null;
            $isCorrect = $ans ? $ans->is_correct : null;
            $marksAwarded = $ans ? (float)$ans->marks_awarded : 0.0;
            $marksDeducted = $ans ? $ans->marks_deducted : 0.0;
            $cardBorder = $isCorrect === true ? '#a7f3d0' : ($isCorrect === false ? '#fecaca' : '#e2e8f0');
          @endphp
          <div style="background: #ffffff; border: 1.5px solid {{ $cardBorder }}; border-radius: 12px; padding: 18px 20px; box-shadow: 0 1px 4px rgba(0,0,0,0.02);">
            
            <!-- Question Card Top Header -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; flex-wrap: wrap; gap: 8px;">
              <div style="display: flex; align-items: center; gap: 8px;">
                <span style="font-weight: 800; color: #0f172a; font-size: 14.5px;">Question {{ $idx + 1 }}</span>
                <span style="font-size: 11px; font-weight: 700; color: #64748b; background: #f1f5f9; padding: 2px 7px; border-radius: 4px;">
                  {{ (fmod($q->marks, 1) == 0 ? (int)$q->marks : number_format($q->marks, 2)) }} {{ (float)$q->marks === 1.0 ? 'Mark' : 'Marks' }}
                </span>
              </div>
              <div>
                @if($isCorrect === true)
                  <span style="background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; padding: 4px 10px; border-radius: 6px; font-size: 11.5px; font-weight: 700; display: inline-flex; align-items: center; gap: 5px;">
                    <i class="fa-solid fa-check"></i> Correct (+{{ (fmod($marksAwarded, 1) == 0 ? (int)$marksAwarded : number_format($marksAwarded, 2)) }})
                  </span>
                @elseif($selectedChoice)
                  <span style="background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; padding: 4px 10px; border-radius: 6px; font-size: 11.5px; font-weight: 700; display: inline-flex; align-items: center; gap: 5px;">
                    <i class="fa-solid fa-xmark"></i> Incorrect (-{{ number_format($marksDeducted, 2) }})
                  </span>
                @else
                  <span style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; padding: 4px 10px; border-radius: 6px; font-size: 11.5px; font-weight: 700; display: inline-flex; align-items: center; gap: 5px;">
                    <i class="fa-solid fa-minus"></i> Skipped / Not Attempted (0.00)
                  </span>
                @endif
              </div>
            </div>

            <!-- Detailed Evaluation Summary Bar -->
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 9px 14px; margin-bottom: 14px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; font-size: 12px;">
              <div>
                <span style="color: #64748b; font-weight: 600;">Your Selection:</span>
                @if($selectedChoice)
                  @if($isCorrect === true)
                    <strong style="color: #047857; margin-left: 4px;"><i class="fa-solid fa-circle-check"></i> Option {{ $selectedChoice }} (Correct)</strong>
                  @else
                    <strong style="color: #b91c1c; margin-left: 4px;"><i class="fa-solid fa-circle-xmark"></i> Option {{ $selectedChoice }} (Wrong)</strong>
                  @endif
                @else
                  <span style="color: #64748b; font-style: italic; margin-left: 4px;">None (Skipped)</span>
                @endif
              </div>
              <div>
                <span style="color: #64748b; font-weight: 600;">Corrected Answer:</span>
                <strong style="color: #047857; margin-left: 4px;"><i class="fa-solid fa-check"></i> Option {{ $q->correct_answer }}</strong>
              </div>
              <div>
                <span style="color: #64748b; font-weight: 600;">Minus Marking Got:</span>
                <strong style="color: {{ $marksDeducted > 0 ? '#dc2626' : '#64748b' }}; margin-left: 4px;">
                  {{ $marksDeducted > 0 ? '-' . number_format($marksDeducted, 2) : '0.00' }}
                </strong>
              </div>
              <div>
                <span style="color: #64748b; font-weight: 600;">Mark Earned:</span>
                <strong style="color: {{ $isCorrect === true ? '#047857' : ($selectedChoice ? '#dc2626' : '#64748b') }}; margin-left: 4px;">
                  {{ $isCorrect === true ? '+' . (fmod($marksAwarded, 1) == 0 ? (int)$marksAwarded : number_format($marksAwarded, 2)) : ($selectedChoice ? '-' . number_format($marksDeducted, 2) : '0.00') }}
                </strong>
              </div>
            </div>

            <!-- Question Text (Question Paper) -->
            <div style="font-weight: 600; font-size: 15px; color: #0f172a; margin: 0 0 14px 0; line-height: 1.6;">
              {!! App\Services\McqPdfParserService::renderStemContent($q->question_text) !!}
            </div>

            @if($q->question_image)
              <div style="margin: 10px 0 14px 0;">
                <img src="{{ asset($q->question_image) }}" alt="Question Diagram" style="max-width: 100%; max-height: 280px; border-radius: 8px; border: 1px solid #e2e8f0;">
              </div>
            @endif

            <!-- Options Grid -->
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
                  $isUser = $selectedChoice === $opt['key'];
                  $isRight = $q->correct_answer === $opt['key'];
                  if ($isRight && $isUser) {
                    $border = '#059669';
                    $bg = '#ecfdf5';
                  } elseif ($isRight) {
                    $border = '#10b981';
                    $bg = '#f0fdf4';
                  } elseif ($isUser) {
                    $border = '#dc2626';
                    $bg = '#fef2f2';
                  } else {
                    $border = '#e2e8f0';
                    $bg = '#ffffff';
                  }
                @endphp
                <div style="padding: 10px 14px; border-radius: 8px; font-size: 13px; background: {{ $bg }}; border: 1.5px solid {{ $border }}; display: flex; justify-content: space-between; align-items: center; color: #1e293b;">
                  <div><strong style="color: #d97706; margin-right: 4px;">{{ $opt['key'] }}:</strong> {!! App\Services\McqPdfParserService::renderStemContent($opt['text']) !!}</div>
                  @if($isRight && $isUser)
                    <span style="background: #059669; color: #ffffff; padding: 3px 8px; border-radius: 5px; font-size: 10.5px; font-weight: 800; display: inline-flex; align-items: center; gap: 4px; white-space: nowrap;">
                      <i class="fa-solid fa-circle-check"></i> Correct Key &amp; Your Selection
                    </span>
                  @elseif($isRight)
                    <span style="background: #dcfce7; color: #047857; border: 1px solid #86efac; padding: 3px 8px; border-radius: 5px; font-size: 10.5px; font-weight: 800; display: inline-flex; align-items: center; gap: 4px; white-space: nowrap;">
                      <i class="fa-solid fa-check"></i> Correct Key
                    </span>
                  @elseif($isUser)
                    <span style="background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; padding: 3px 8px; border-radius: 5px; font-size: 10.5px; font-weight: 800; display: inline-flex; align-items: center; gap: 4px; white-space: nowrap;">
                      <i class="fa-solid fa-circle-xmark"></i> Your Selection (Wrong: -{{ number_format($marksDeducted, 2) }})
                    </span>
                  @endif
                </div>
              @endforeach
            </div>

            <!-- Explanation Hint -->
            @if($q->explanation)
              <div style="font-size: 13px; color: #92400e; background: #fffbeb; border: 1px solid #fde68a; padding: 12px 16px; border-radius: 8px; line-height: 1.5; margin-top: 10px;">
                <strong style="color: #92400e; display: flex; align-items: center; gap: 6px; margin-bottom: 3px;"><i class="fa-solid fa-lightbulb"></i> Solution Explanation:</strong>
                {!! App\Services\McqPdfParserService::renderStemContent($q->explanation) !!}
              </div>
            @endif
          </div>
        @endforeach
      </div>
    </div>
  @endif

</div>
@endsection

@section('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    if (window.typesetMathJax) {
      window.typesetMathJax(document.body);
    }
  });
  if (document.readyState === 'complete' || document.readyState === 'interactive') {
    if (window.typesetMathJax) {
      window.typesetMathJax(document.body);
    }
  }
</script>
@endsection
