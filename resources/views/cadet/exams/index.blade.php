@extends('layouts.portal')

@section('title', 'Assessment Examinations')
@section('cadet_badge', 'Exam')
@section('page_title', 'Cadet Assessment & Mock Testing Portal')
@section('page_subtitle', 'Computerized ISSB Intelligence MCQ screening, Non-Verbal Matrix, and 15-second Word Association Tests')

@section('content')
<style>
  .cadet-exams-grid {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 20px;
    width: 100%;
    margin: 0 auto;
  }
  .cadet-exam-3d-card {
    position: relative;
    flex: 0 1 calc(25% - 16px);
    width: 100%;
    max-width: 320px;
    min-width: 260px;
    margin: 0;
    border-radius: 22px;
    padding: 22px 20px 18px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    overflow: hidden;
    color: #ffffff;
    box-shadow: 
      0 18px 36px -8px rgba(0, 0, 0, 0.24),
      0 6px 14px -3px rgba(0, 0, 0, 0.12),
      inset 0 2px 2.5px rgba(255, 255, 255, 0.55),
      inset 0 -4px 0 rgba(0, 0, 0, 0.22);
    border: 1.5px solid rgba(255, 255, 255, 0.35);
    transition: transform 0.28s cubic-bezier(0.2, 0.8, 0.2, 1), box-shadow 0.28s cubic-bezier(0.2, 0.8, 0.2, 1), filter 0.28s ease;
    box-sizing: border-box;
  }
  @media (max-width: 1200px) {
    .cadet-exam-3d-card {
      flex: 0 1 calc(50% - 16px);
      max-width: 340px;
    }
  }
  @media (max-width: 640px) {
    .cadet-exam-3d-card {
      flex: 1 1 100%;
      max-width: 100%;
    }
  }
  .cadet-exam-3d-card:hover {
    transform: translateY(-7px) scale(1.015);
    box-shadow: 
      0 26px 48px -10px rgba(0, 0, 0, 0.3),
      0 12px 22px -5px rgba(0, 0, 0, 0.16),
      inset 0 2.5px 3px rgba(255, 255, 255, 0.65),
      inset 0 -4.5px 0 rgba(0, 0, 0, 0.26);
    filter: brightness(1.03);
  }
  .cadet-exam-glow {
    position: absolute;
    top: -35px;
    right: -35px;
    width: 185px;
    height: 185px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.35) 0%, rgba(255, 255, 255, 0.1) 50%, rgba(255, 255, 255, 0) 72%);
    pointer-events: none;
    transition: transform 0.35s ease;
  }
  .cadet-exam-3d-card:hover .cadet-exam-glow {
    transform: scale(1.18);
  }
  .btn-3d-white {
    width: 100%;
    text-align: center;
    justify-content: center;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 13.5px;
    font-weight: 800;
    padding: 12px 18px;
    border-radius: 14px;
    background: #ffffff;
    border: none;
    box-shadow: 
      0 10px 20px -3px rgba(0, 0, 0, 0.25),
      inset 0 2px 1px rgba(255, 255, 255, 0.95),
      inset 0 -3px 0 rgba(0, 0, 0, 0.14);
    text-decoration: none;
    cursor: pointer;
    transition: all 0.2s cubic-bezier(0.2, 0.8, 0.2, 1);
  }
  .btn-3d-white:hover {
    transform: translateY(-2px);
    box-shadow: 
      0 14px 26px -4px rgba(0, 0, 0, 0.32),
      inset 0 2px 1px #ffffff,
      inset 0 -3px 0 rgba(0, 0, 0, 0.18);
    filter: brightness(1.03);
  }
  .btn-3d-white:active {
    transform: translateY(1px);
    box-shadow: 
      0 4px 10px -2px rgba(0, 0, 0, 0.2),
      inset 0 1px 1px rgba(255, 255, 255, 0.8),
      inset 0 -1.5px 0 rgba(0, 0, 0, 0.1);
  }
</style>

<div style="display: flex; flex-direction: column; gap: 24px;">

  <!-- Branch Switcher Navigation -->
  <div class="tactical-card" style="padding: 12px 18px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
    <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
      <span style="font-size: 11px; font-weight: 800; text-transform: uppercase; color: var(--text-muted); margin-right: 4px;">Branch Wing:</span>
      <a href="{{ route('cadet.exams.index') }}" class="badge {{ !$selectedBranch ? 'badge-emerald' : 'badge-navy' }}" style="text-decoration: none; padding: 7px 13px; font-size: 12px; display: inline-flex; align-items: center; gap: 5px;">
        <i class="fa-solid fa-layer-group"></i> All Branches
      </a>
      <a href="{{ route('cadet.exams.index', ['branch' => 'army']) }}" class="badge {{ $selectedBranch === 'army' ? 'badge-emerald' : 'badge-navy' }}" style="text-decoration: none; padding: 7px 13px; font-size: 12px; display: inline-flex; align-items: center; gap: 6px;">
        <i class="fa-solid fa-shield-halved"></i> Army
        @if(in_array('army', $enrolledBranches))
          <span style="font-size: 9.5px; background: rgba(16,185,129,0.3); color: #34d399; padding: 1px 6px; border-radius: 999px; font-weight: 800;">Enrolled</span>
        @else
          <span style="font-size: 9.5px; opacity: 0.8; background: rgba(255,255,255,0.15); padding: 1px 6px; border-radius: 999px;">Courses</span>
        @endif
      </a>
      <a href="{{ route('cadet.exams.index', ['branch' => 'navy']) }}" class="badge {{ $selectedBranch === 'navy' ? 'badge-emerald' : 'badge-navy' }}" style="text-decoration: none; padding: 7px 13px; font-size: 12px; display: inline-flex; align-items: center; gap: 6px;">
        <i class="fa-solid fa-anchor"></i> Navy
        @if(in_array('navy', $enrolledBranches))
          <span style="font-size: 9.5px; background: rgba(16,185,129,0.3); color: #34d399; padding: 1px 6px; border-radius: 999px; font-weight: 800;">Enrolled</span>
        @else
          <span style="font-size: 9.5px; opacity: 0.8; background: rgba(255,255,255,0.15); padding: 1px 6px; border-radius: 999px;">Courses</span>
        @endif
      </a>
      <a href="{{ route('cadet.exams.index', ['branch' => 'air_force']) }}" class="badge {{ $selectedBranch === 'air_force' ? 'badge-emerald' : 'badge-navy' }}" style="text-decoration: none; padding: 7px 13px; font-size: 12px; display: inline-flex; align-items: center; gap: 6px;">
        <i class="fa-solid fa-jet-fighter"></i> Air Force
        @if(in_array('air_force', $enrolledBranches))
          <span style="font-size: 9.5px; background: rgba(16,185,129,0.3); color: #34d399; padding: 1px 6px; border-radius: 999px; font-weight: 800;">Enrolled</span>
        @else
          <span style="font-size: 9.5px; opacity: 0.8; background: rgba(255,255,255,0.15); padding: 1px 6px; border-radius: 999px;">Courses</span>
        @endif
      </a>
      <a href="{{ route('cadet.exams.index', ['branch' => 'police']) }}" class="badge {{ $selectedBranch === 'police' ? 'badge-emerald' : 'badge-navy' }}" style="text-decoration: none; padding: 7px 13px; font-size: 12px; display: inline-flex; align-items: center; gap: 6px;">
        <i class="fa-solid fa-user-shield"></i> Police
        @if(in_array('police', $enrolledBranches))
          <span style="font-size: 9.5px; background: rgba(16,185,129,0.3); color: #34d399; padding: 1px 6px; border-radius: 999px; font-weight: 800;">Enrolled</span>
        @else
          <span style="font-size: 9.5px; opacity: 0.8; background: rgba(255,255,255,0.15); padding: 1px 6px; border-radius: 999px;">Courses</span>
        @endif
      </a>
    </div>

    <div>
      <a href="{{ route('cadet.exams.history') }}" class="btn-tactical btn-tactical-outline" style="padding: 7px 14px; font-size: 12px; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; color: #ffffff; border-color: rgba(255,255,255,0.25);">
        <i class="fa-solid fa-clock-rotate-left" style="color: var(--accent-gold);"></i> Exam History
      </a>
    </div>
  </div>

  @if(session('exam_submission_summary') || session('success'))
    @php
      $summary = session('exam_submission_summary');
    @endphp
    <div class="tactical-card" style="background: linear-gradient(135deg, #064e3b 0%, #022c22 100%); border: 1.5px solid #10b981; padding: 22px; border-radius: 18px; box-shadow: 0 12px 36px rgba(0, 0, 0, 0.35);">
      <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px; margin-bottom: 16px;">
        <div style="display: flex; align-items: center; gap: 14px;">
          <div style="width: 48px; height: 48px; border-radius: 14px; background: #10b981; color: #ffffff; display: grid; place-items: center; font-size: 22px; box-shadow: 0 4px 14px rgba(16, 185, 129, 0.45);">
            <i class="fa-solid fa-circle-check"></i>
          </div>
          <div>
            <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-bottom: 2px;">
              <span style="font-size: 11px; font-weight: 800; text-transform: uppercase; background: rgba(16, 185, 129, 0.25); color: #6ee7b7; padding: 2px 8px; border-radius: 6px;">
                Exam Completed
              </span>
              @if($summary && isset($summary['result_status']))
                @if($summary['result_status'] === 'passed')
                  <span style="font-size: 11px; font-weight: 800; background: rgba(16, 185, 129, 0.3); color: #34d399; border: 1px solid #10b981; padding: 2px 8px; border-radius: 6px;">
                    <i class="fa-solid fa-award"></i> QUALIFIED
                  </span>
                @elseif($summary['result_status'] === 'failed')
                  <span style="font-size: 11px; font-weight: 800; background: rgba(239, 68, 68, 0.25); color: #f87171; border: 1px solid #ef4444; padding: 2px 8px; border-radius: 6px;">
                    <i class="fa-solid fa-xmark"></i> NOT QUALIFIED
                  </span>
                @else
                  <span style="font-size: 11px; font-weight: 800; background: rgba(245, 158, 11, 0.25); color: #fbbf24; border: 1px solid #f59e0b; padding: 2px 8px; border-radius: 6px;">
                    <i class="fa-solid fa-clock"></i> SUBMITTED
                  </span>
                @endif
              @endif
            </div>
            <h2 style="font-size: 18px; font-weight: 800; color: #ffffff; margin: 0 0 2px 0;">
              {{ $summary['exam_title'] ?? session('success') }}
            </h2>
            <div style="font-size: 12.5px; color: #a7f3d0;">
              Assessment test submitted successfully! Your attempt metrics and ranking have been verified.
            </div>
          </div>
        </div>

        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
          @if(session('submitted_attempt_id'))
            <a href="{{ route('cadet.exams.result', session('submitted_attempt_id')) }}" class="btn-3d-white" style="width: auto; padding: 9px 18px; font-size: 12.5px; color: #047857;">
              <i class="fa-solid fa-square-poll-vertical"></i> View Scorecard
            </a>
          @endif
          <a href="{{ route('cadet.exams.history') }}" class="btn-tactical btn-tactical-outline" style="padding: 9px 16px; font-size: 12.5px; text-decoration: none; border-color: rgba(255,255,255,0.3); color: #ffffff;">
            <i class="fa-solid fa-clock-rotate-left"></i> Exam History
          </a>
        </div>
      </div>

      @if($summary)
        <!-- Key Metrics Strip: Mark, Minus Marking, Total Mark, Rank, Candidates -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 12px; background: rgba(0, 0, 0, 0.25); border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 12px; padding: 14px 18px;">
          <div>
            <div style="font-size: 10px; font-weight: 800; text-transform: uppercase; color: #94a3b8; letter-spacing: 0.5px;">Mark Achieved</div>
            <div style="font-size: 20px; font-weight: 800; font-family: 'Plus Jakarta Sans', monospace; color: #34d399;">
              {{ number_format($summary['score'], 2) }}
            </div>
          </div>

          <div>
            <div style="font-size: 10px; font-weight: 800; text-transform: uppercase; color: #94a3b8; letter-spacing: 0.5px;">Minus Marking</div>
            <div style="font-size: 20px; font-weight: 800; font-family: 'Plus Jakarta Sans', monospace; color: {{ ($summary['minus_marks'] ?? 0) > 0 ? '#f87171' : '#cbd5e1' }};">
              {{ ($summary['minus_marks'] ?? 0) > 0 ? '-' . number_format($summary['minus_marks'], 2) : '0.00' }}
            </div>
          </div>

          <div>
            <div style="font-size: 10px; font-weight: 800; text-transform: uppercase; color: #94a3b8; letter-spacing: 0.5px;">Total Marks</div>
            <div style="font-size: 20px; font-weight: 800; font-family: 'Plus Jakarta Sans', monospace; color: #fef08a;">
              {{ (int)$summary['total_marks'] }}
            </div>
          </div>

          <div>
            <div style="font-size: 10px; font-weight: 800; text-transform: uppercase; color: #94a3b8; letter-spacing: 0.5px;">Candidate Rank</div>
            <div style="font-size: 16px; font-weight: 800; font-family: 'Plus Jakarta Sans', sans-serif; color: #fbbf24;">
              @if(!empty($summary['rank_published']))
                Rank #{{ $summary['rank'] }}
              @else
                Pending (Live Exam)
              @endif
            </div>
          </div>

          <div>
            <div style="font-size: 10px; font-weight: 800; text-transform: uppercase; color: #94a3b8; letter-spacing: 0.5px;">Total Candidates</div>
            <div style="font-size: 16px; font-weight: 800; font-family: 'Plus Jakarta Sans', sans-serif; color: #e2e8f0;">
              @if(!empty($summary['rank_published']))
                {{ $summary['total_candidates'] }} {{ $summary['total_candidates'] === 1 ? 'Candidate' : 'Candidates' }}
              @else
                Pending Schedule
              @endif
            </div>
          </div>
        </div>
      @endif
    </div>
  @endif

  @if($isEnrolledInSelectedBranch)
    <!-- Available Assessment Exams Grid -->
    <div>
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 8px;">
        <h3 style="font-size: 16px; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 8px; color: var(--accent-gold);">
          <i class="fa-solid fa-crosshairs"></i> Available Assessment Modules
        </h3>
        @if(!$selectedBranch)
          <span style="font-size: 11.5px; font-weight: 700; color: #10b981; background: rgba(16, 185, 129, 0.12); border: 1px solid rgba(16, 185, 129, 0.3); padding: 4px 12px; border-radius: 999px; display: inline-flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-circle-check"></i> Enrolled Sector: {{ collect($enrolledBranches)->map(fn($b) => match($b){ 'army'=>'Army','navy'=>'Navy','air_force'=>'Air Force','police'=>'Police',default=>ucfirst($b) })->join(', ') }}
          </span>
        @endif
      </div>

      <div class="cadet-exams-grid">
      @forelse($exams as $exam)
        @php
          $isEnded = $exam->isEnded();
          $isFuture = !$isEnded && $exam->isScheduledFuture();
          $isLive = !$isEnded && !$isFuture;
          $remSeconds = $exam->getSecondsUntilStart();
          $examStartUrl = route('cadet.exams.start', $exam->id);

          $branchThemes = [
              'army' => [
                  'gradient' => 'linear-gradient(135deg, #10b981 0%, #059669 50%, #047857 100%)',
                  'btnColor' => '#047857',
              ],
              'navy' => [
                  'gradient' => 'linear-gradient(135deg, #2563eb 0%, #1d4ed8 50%, #1e3a8a 100%)',
                  'btnColor' => '#1e3a8a',
              ],
              'air_force' => [
                  'gradient' => 'linear-gradient(135deg, #38bdf8 0%, #0ea5e9 50%, #0284c7 100%)',
                  'btnColor' => '#0284c7',
              ],
              'police' => [
                  'gradient' => 'linear-gradient(135deg, #ff7348 0%, #ea580c 50%, #c2410c 100%)',
                  'btnColor' => '#c2410c',
              ],
          ];

          $themeOrder = ['army', 'navy', 'air_force', 'police'];
          $fallbackKey = $themeOrder[$loop->index % 4];
          $activeTheme = $branchThemes[$exam->branch] ?? $branchThemes[$fallbackKey];
          $cardGradient = $activeTheme['gradient'];
          $btnColor = $activeTheme['btnColor'];
        @endphp

        <div class="cadet-exam-3d-card {{ $isLive ? 'is-live-card' : '' }}" style="background: {{ $cardGradient }};" data-exam-id="{{ $exam->id }}">
          {{-- Ambient spotlight glow like dashboard tiles --}}
          <div class="cadet-exam-glow"></div>

          <div style="position: relative; z-index: 1;">
            {{-- Top Row: Exam Name (Left) & Condition Status (Right) --}}
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; gap: 8px;">
              {{-- Top Left: Exam Name & Branch Badge (No IQ MCQ) --}}
              <div style="display: flex; gap: 6px; align-items: center; flex-wrap: wrap;">
                <span style="background: rgba(255,255,255,0.24); color: #ffffff; padding: 5px 12px; border-radius: 999px; font-size: 11px; font-weight: 800; border: 1px solid rgba(255,255,255,0.38); box-shadow: 0 2px 6px rgba(0,0,0,0.15); display: inline-flex; align-items: center; gap: 6px; text-shadow: 0 1px 2px rgba(0,0,0,0.2);">
                  <i class="fa-solid {{ $exam->branchIcon() }}"></i> {{ $exam->branchLabel() }}
                </span>
                @if($exam->isIssb())
                  <span style="background: rgba(254, 240, 138, 0.28); color: #fef08a; padding: 4px 10px; border-radius: 999px; font-size: 10px; font-weight: 800; border: 1px solid rgba(254, 240, 138, 0.45); display: inline-flex; align-items: center; gap: 4px; box-shadow: 0 2px 6px rgba(0,0,0,0.15);">
                    <i class="fa-solid fa-star"></i> ISSB Masterclass
                  </span>
                @elseif($exam->isPrelim())
                  <span style="background: rgba(255,255,255,0.18); color: #ffffff; padding: 4px 10px; border-radius: 999px; font-size: 10px; font-weight: 800; border: 1px solid rgba(255,255,255,0.3); display: inline-flex; align-items: center; gap: 4px;">
                    <i class="fa-solid fa-shield"></i> Prelim
                  </span>
                @elseif($exam->isPolice())
                  <span style="background: rgba(255,255,255,0.18); color: #ffffff; padding: 4px 10px; border-radius: 999px; font-size: 10px; font-weight: 800; border: 1px solid rgba(255,255,255,0.3); display: inline-flex; align-items: center; gap: 4px;">
                    <i class="fa-solid fa-handcuffs"></i> {{ $exam->trackLabel() }}
                  </span>
                @endif
              </div>

              {{-- Top Right: Condition (LIVE / SCHEDULED / ENDED) --}}
              <span id="cadet-badge-{{ $exam->id }}">
                @if($isEnded)
                  <span style="background: rgba(239,68,68,0.35); color: #ffffff; border: 1.5px solid rgba(255,255,255,0.45); padding: 4px 11px; border-radius: 999px; font-size: 10.5px; font-weight: 800; display: inline-flex; align-items: center; gap: 5px; box-shadow: 0 2px 6px rgba(0,0,0,0.18);">
                    <i class="fa-solid fa-circle-xmark"></i> ENDED
                  </span>
                @elseif($isFuture)
                  <span style="background: rgba(245,158,11,0.35); color: #ffffff; border: 1.5px solid rgba(255,255,255,0.45); padding: 4px 11px; border-radius: 999px; font-size: 10.5px; font-weight: 800; display: inline-flex; align-items: center; gap: 5px; box-shadow: 0 2px 6px rgba(0,0,0,0.18);">
                    <i class="fa-regular fa-clock"></i> SCHEDULED
                  </span>
                @else
                  <span style="background: rgba(16,185,129,0.3); color: #ffffff; border: 1.5px solid rgba(255,255,255,0.45); padding: 4px 11px; border-radius: 999px; font-size: 10.5px; font-weight: 800; display: inline-flex; align-items: center; gap: 5px; box-shadow: 0 2px 6px rgba(0,0,0,0.18);">
                    <span style="width: 7px; height: 7px; border-radius: 50%; background: #34d399; box-shadow: 0 0 8px #34d399; display: inline-block;"></span> LIVE
                  </span>
                @endif
              </span>
            </div>

            {{-- Title of the Exam --}}
            <h3 style="font-size: 18px; font-weight: 800; margin: 12px 0 8px 0; color: #ffffff; line-height: 1.35; text-shadow: 0 2px 5px rgba(0,0,0,0.35); font-family: 'Poppins', sans-serif;">
              {{ $exam->title }}
            </h3>

            {{-- Small Description of 80 to 100 letters --}}
            <p style="font-size: 12.5px; color: rgba(255,255,255,0.92); line-height: 1.5; margin: 0 0 10px 0; text-shadow: 0 1px 2px rgba(0,0,0,0.2);">
              {{ \Illuminate\Support\Str::limit($exam->description ?? 'Official military assessment module conducted under timed evaluation conditions.', 95, '...') }}
            </p>

            {{-- Topic Name (When LIVE, clicking exam removes topic and shows ending time) --}}
            <div id="topic-container-{{ $exam->id }}" class="exam-topic-toggle-area" style="margin-bottom: 14px; {{ $isLive ? 'cursor: pointer;' : '' }}" @if($isLive) title="Click to toggle ending time" @endif>
              <div class="topic-view-active" style="display: flex; align-items: center; gap: 6px; font-size: 11.5px;">
                <span style="color: rgba(255,255,255,0.85); font-weight: 800; text-transform: uppercase; font-size: 10px; letter-spacing: 0.6px; text-shadow: 0 1px 2px rgba(0,0,0,0.2);">Topic:</span>
                <span style="background: rgba(255,255,255,0.22); color: #ffffff; font-weight: 700; font-size: 11px; padding: 3px 10px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.35); backdrop-filter: blur(4px); box-shadow: 0 2px 4px rgba(0,0,0,0.12); display: inline-flex; align-items: center; gap: 5px;">
                  <i class="fa-solid fa-layer-group" style="color: #fef08a;"></i> {{ $exam->category ?? 'General Aptitude' }}
                  @if($isLive)
                    <i class="fa-solid fa-arrows-rotate" style="font-size: 8.5px; opacity: 0.7; margin-left: 2px;" title="Click to view end time"></i>
                  @endif
                </span>
              </div>

              @if($isLive)
                <div class="endtime-view-toggled" style="display: none; align-items: center; gap: 6px; font-size: 11.5px;">
                  <span style="color: rgba(255,255,255,0.85); font-weight: 800; text-transform: uppercase; font-size: 10px; letter-spacing: 0.6px; text-shadow: 0 1px 2px rgba(0,0,0,0.2);">Ending Time:</span>
                  <span style="background: rgba(239, 68, 68, 0.3); color: #ffffff; font-weight: 700; font-size: 11px; padding: 3px 10px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.45); backdrop-filter: blur(4px); box-shadow: 0 2px 4px rgba(0,0,0,0.12); display: inline-flex; align-items: center; gap: 5px;">
                    <i class="fa-regular fa-clock" style="color: #fef08a;"></i> {{ $exam->schedule_end ? $exam->schedule_end->format('h:i A, M d') : 'Open until closed' }}
                    <i class="fa-solid fa-arrows-rotate" style="font-size: 8.5px; opacity: 0.7; margin-left: 2px;" title="Click to view topic"></i>
                  </span>
                </div>
              @endif
            </div>

            {{-- Exam Duration & Total Mark (2-Column Grid) --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; background: rgba(0, 0, 0, 0.18); border: 1.5px solid rgba(255, 255, 255, 0.22); border-radius: 14px; padding: 12px 14px; margin-bottom: 16px; text-align: center; box-shadow: inset 0 2px 4px rgba(0,0,0,0.15), 0 2px 6px rgba(0,0,0,0.08);">
              <div>
                <div style="font-size: 10px; color: rgba(255,255,255,0.8); text-transform: uppercase; font-weight: 800; letter-spacing: 0.6px; margin-bottom: 2px;">Duration</div>
                <strong style="font-size: 15px; font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 800; color: #ffffff;">{{ $exam->duration_minutes }} min</strong>
              </div>
              <div style="border-left: 1px solid rgba(255,255,255,0.22);">
                <div style="font-size: 10px; color: rgba(255,255,255,0.8); text-transform: uppercase; font-weight: 800; letter-spacing: 0.6px; margin-bottom: 2px;">Total Mark</div>
                <strong style="font-size: 15px; font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 800; color: #fef08a;">{{ (int)$exam->total_marks }}</strong>
              </div>
            </div>
          </div>

          {{-- Bottom area:
               - If SCHEDULED: shows time of starting
               - If ENDED: shows exam starting and ending time
               - If LIVE: Start Assessment Test button
          --}}
          <div id="cadet-btn-container-{{ $exam->id }}" style="position: relative; z-index: 2; margin-top: auto; padding-top: 4px; display: flex; justify-content: center; width: 100%;">
            @if($isEnded)
              <div class="btn-3d-white" style="cursor: default; opacity: 0.95; color: {{ $btnColor }}; font-size: 11.5px; padding: 10px 14px; flex-direction: column; gap: 3px; line-height: 1.35;">
                <div style="display: flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 800;">
                  <i class="fa-solid fa-calendar-xmark" style="color: #ef4444;"></i> Exam Concluded
                </div>
                <div style="font-size: 11px; font-weight: 700; opacity: 0.92;">
                  Started: {{ $exam->schedule_start ? $exam->schedule_start->format('h:i A') : ($exam->created_at ? $exam->created_at->format('h:i A') : 'N/A') }} &bull; Ended: {{ $exam->schedule_end ? $exam->schedule_end->format('h:i A, M d') : 'Closed' }}
                </div>
              </div>
            @elseif($isFuture)
              <div class="btn-3d-white" style="cursor: default; opacity: 0.95; color: {{ $btnColor }}; font-size: 12.5px; padding: 12px 16px;">
                <i class="fa-regular fa-clock"></i> Starts at {{ $exam->schedule_start ? $exam->schedule_start->format('h:i A, M d') : 'Scheduled Time' }}
              </div>
            @else
              @php
                $myAttempt = isset($userAttempts) ? ($userAttempts->get($exam->id)?->first()) : null;
                $canAccess = $exam->canCandidateAccess($student, auth()->user());
              @endphp
              @if(!$canAccess)
                <div class="btn-3d-white" style="cursor: not-allowed; opacity: 0.9; color: #dc2626; font-size: 11.5px; padding: 10px 14px; flex-direction: column; gap: 3px; line-height: 1.35; text-align: center;">
                  <div style="font-weight: 800; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-lock"></i> Enrollment Required
                  </div>
                  <div style="font-size: 10px; font-weight: 600; opacity: 0.88;">
                    @if($exam->isPrelim())
                      Exclusive to {{ $exam->branchLabel() }} Cadets
                    @elseif($exam->isPolice())
                      Requires {{ $exam->trackLabel() }} Course
                    @else
                      Course Enrollment Needed
                    @endif
                  </div>
                </div>
              @elseif($myAttempt)
                <div style="width: 100%; display: flex; flex-direction: column; gap: 6px; align-items: center;">
                    @if($myAttempt->isRankPublished())
                      <span><i class="fa-solid fa-trophy" style="color: #fbbf24;"></i> Rank #{{ $myAttempt->rank }} of {{ $myAttempt->total_candidates }}</span>
                    @else
                      <span><i class="fa-regular fa-clock" style="color: #93c5fd;"></i> Rank Pending</span>
                    @endif
                    <span>&bull;</span>
                  @if($myAttempt->isRankPublished())
                    <a href="{{ route('cadet.exams.result', $myAttempt->id) }}" class="btn-3d-white" style="color: {{ $btnColor }}; padding: 10px 14px; font-size: 12.5px;" onclick="event.stopPropagation();">
                      <i class="fa-solid fa-square-poll-vertical"></i> Scorecard ({{ $myAttempt->score }}/{{ (int)$myAttempt->total_marks }})
                    </a>
                  @else
                    <div class="btn-3d-white" style="cursor: default; opacity: 0.92; color: {{ $btnColor }}; padding: 10px 14px; font-size: 11.5px; display: inline-flex; align-items: center; gap: 6px;">
                      <i class="fa-solid fa-lock"></i> Scorecard Unlocks After Schedule
                    </div>
                  @endif
                  <a href="{{ $examStartUrl }}" style="font-size: 11.5px; color: #ffffff; text-decoration: underline; opacity: 0.92; display: inline-flex; align-items: center; gap: 4px; text-shadow: 0 1px 2px rgba(0,0,0,0.3);" onclick="event.stopPropagation();">
                    <i class="fa-solid fa-rotate-right"></i> Retake Test
                  </a>
                </div>
              @else
                <a href="{{ $examStartUrl }}" class="btn-3d-white" style="color: {{ $btnColor }};" onclick="event.stopPropagation();">
                  <i class="fa-solid fa-play"></i> Start Assessment Test
                </a>
              @endif
            @endif
          </div>
        </div>
      @empty
        <div style="grid-column: 1 / -1; text-align: center; padding: 48px; color: var(--text-muted);" class="tactical-card">
          No assessment exams currently configured for this sector.
        </div>
      @endforelse
    </div>
  </div>
  @else
    @php
      $branchMeta = [
        'army' => [
          'name' => 'Bangladesh Army',
          'icon' => 'fa-shield-halved',
          'color' => '#10b981',
          'dark_color' => '#047857',
          'gradient' => 'linear-gradient(135deg, #10b981 0%, #059669 100%)',
          'badge_bg' => '#ecfdf5',
          'badge_border' => '#a7f3d0',
          'badge_color' => '#047857',
        ],
        'navy' => [
          'name' => 'Bangladesh Navy',
          'icon' => 'fa-anchor',
          'color' => '#1e3a8a',
          'dark_color' => '#172554',
          'gradient' => 'linear-gradient(135deg, #2563eb 0%, #1e3a8a 100%)',
          'badge_bg' => '#eff6ff',
          'badge_border' => '#bfdbfe',
          'badge_color' => '#1e3a8a',
        ],
        'air_force' => [
          'name' => 'Bangladesh Air Force',
          'icon' => 'fa-jet-fighter',
          'color' => '#0284c7',
          'dark_color' => '#0369a1',
          'gradient' => 'linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%)',
          'badge_bg' => '#f0f9ff',
          'badge_border' => '#bae6fd',
          'badge_color' => '#0369a1',
        ],
        'police' => [
          'name' => 'Bangladesh Police',
          'icon' => 'fa-user-shield',
          'color' => '#ea580c',
          'dark_color' => '#c2410c',
          'gradient' => 'linear-gradient(135deg, #f97316 0%, #ea580c 100%)',
          'badge_bg' => '#fff7ed',
          'badge_border' => '#fed7aa',
          'badge_color' => '#c2410c',
        ],
      ];
      $meta = $branchMeta[$selectedBranch] ?? [
        'name' => ucfirst(str_replace('_', ' ', $selectedBranch)),
        'icon' => 'fa-layer-group',
        'color' => '#2563eb',
        'dark_color' => '#1d4ed8',
        'gradient' => 'linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%)',
        'badge_bg' => '#eff6ff',
        'badge_border' => '#bfdbfe',
        'badge_color' => '#1d4ed8',
      ];
    @endphp

    <div>
      <!-- Sector Info Notice Banner -->
      <div class="tactical-card" style="padding: 24px; border-left: 5px solid {{ $meta['color'] }}; background: #ffffff; margin-bottom: 24px; box-shadow: 0 4px 16px rgba(15, 23, 42, 0.06); border-radius: 16px;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
          <div style="display: flex; align-items: center; gap: 16px;">
            <div style="width: 54px; height: 54px; border-radius: 14px; background: {{ $meta['gradient'] }}; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 24px; box-shadow: 0 6px 16px {{ $meta['color'] }}44;">
              <i class="fa-solid {{ $meta['icon'] }}"></i>
            </div>
            <div>
              <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                <span style="font-size: 11px; font-weight: 800; text-transform: uppercase; color: {{ $meta['dark_color'] }}; letter-spacing: 0.8px;">
                  Branch Sector: {{ $meta['name'] }}
                </span>
                <span style="font-size: 10.5px; font-weight: 700; background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; padding: 1px 8px; border-radius: 999px;">
                  <i class="fa-solid fa-lock" style="font-size: 9px;"></i> Not Enrolled
                </span>
              </div>
              <h2 style="font-size: 20px; font-weight: 800; color: #0f172a; margin: 0 0 6px 0; letter-spacing: -0.02em;">
                Ongoing Courses in {{ $meta['name'] }}
              </h2>
              <p style="margin: 0; font-size: 13.5px; color: #64748b; line-height: 1.45; max-width: 800px;">
                You are not currently enrolled in the <strong>{{ $meta['name'] }}</strong> sector. To unlock computerized mock tests, preliminary exams, and specialized screening modules for this wing, enroll in one of our ongoing academy preparatory programs below.
              </p>
            </div>
          </div>
          <div>
            <a href="{{ route('cadet.exams.index') }}" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px; border-radius: 10px; background: #f1f5f9; color: #334155; font-size: 13px; font-weight: 700; text-decoration: none; border: 1px solid #cbd5e1; transition: all 0.2s ease;">
              <i class="fa-solid fa-arrow-left"></i> Back to My Exams
            </a>
          </div>
        </div>
      </div>

      <!-- Ongoing Courses Header -->
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
        <h3 style="font-size: 16px; font-weight: 800; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 8px;">
          <i class="fa-solid fa-graduation-cap" style="color: {{ $meta['color'] }};"></i> Active Preparatory Programs
        </h3>
        <span style="font-size: 12px; font-weight: 700; color: #64748b;">
          Showing {{ $ongoingCourses->count() }} Ongoing {{ Str::plural('Course', $ongoingCourses->count()) }}
        </span>
      </div>

      <!-- Ongoing Courses Cards Grid -->
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px;">
        @forelse($ongoingCourses as $course)
          <div style="background: #ffffff; border-radius: 20px; border: 1.5px solid #e2e8f0; border-top: 4.5px solid {{ $meta['color'] }}; box-shadow: 0 10px 28px -6px rgba(15, 23, 42, 0.08); padding: 24px; display: flex; flex-direction: column; justify-content: space-between; position: relative; overflow: hidden; transition: transform 0.2s ease, box-shadow 0.2s ease;">
            
            <div>
              {{-- Top Row: Category badge & Admission Status --}}
              <div style="display: flex; justify-content: space-between; align-items: center; gap: 8px; margin-bottom: 12px;">
                <span style="font-size: 11px; font-weight: 800; color: {{ $meta['badge_color'] }}; background: {{ $meta['badge_bg'] }}; border: 1px solid {{ $meta['badge_border'] }}; padding: 3px 10px; border-radius: 999px; display: inline-flex; align-items: center; gap: 5px;">
                  <i class="fa-solid {{ $meta['icon'] }}"></i> {{ $meta['name'] }}
                </span>
                <span style="font-size: 10.5px; font-weight: 800; color: #047857; background: #ecfdf5; border: 1px solid #a7f3d0; padding: 3px 10px; border-radius: 999px; display: inline-flex; align-items: center; gap: 5px;">
                  <span style="width: 6px; height: 6px; border-radius: 50%; background: #10b981; display: inline-block;"></span> Admission Open
                </span>
              </div>

              {{-- Course Title --}}
              <h3 style="font-size: 18px; font-weight: 800; color: #0f172a; margin: 0 0 10px 0; line-height: 1.35; font-family: 'Poppins', sans-serif;">
                {{ $course->title }}
              </h3>

              {{-- Description --}}
              <p style="font-size: 13px; color: #64748b; line-height: 1.5; margin: 0 0 16px 0;">
                {{ Str::limit($course->description, 130) }}
              </p>

              {{-- Key Metrics (Duration, Eligibility, Fee) --}}
              <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px 14px; margin-bottom: 16px;">
                <div>
                  <div style="font-size: 10px; font-weight: 800; text-transform: uppercase; color: #94a3b8; letter-spacing: 0.5px;">Duration</div>
                  <div style="font-size: 13.5px; font-weight: 800; color: #0f172a; margin-top: 2px;">
                    <i class="fa-regular fa-clock" style="color: {{ $meta['color'] }}; font-size: 12px;"></i> {{ $course->duration ?? '3 Months' }}
                  </div>
                </div>
                <div>
                  <div style="font-size: 10px; font-weight: 800; text-transform: uppercase; color: #94a3b8; letter-spacing: 0.5px;">Course Fee</div>
                  <div style="font-size: 14.5px; font-weight: 800; color: #047857; margin-top: 2px;">
                    ৳ {{ number_format($course->fee, 0) }} <span style="font-size: 10px; font-weight: 600; color: #64748b;">BDT</span>
                  </div>
                </div>
                @if($course->eligibility)
                  <div style="grid-column: span 2; border-top: 1px solid #e2e8f0; padding-top: 8px; margin-top: 2px;">
                    <div style="font-size: 10px; font-weight: 800; text-transform: uppercase; color: #94a3b8; letter-spacing: 0.5px;">Eligibility</div>
                    <div style="font-size: 12px; font-weight: 600; color: #334155; margin-top: 2px;">
                      {{ Str::limit($course->eligibility, 85) }}
                    </div>
                  </div>
                @endif
              </div>

              {{-- Syllabus / Features Highlights --}}
              @if(!empty($course->features) && is_array($course->features))
                <div style="margin-bottom: 20px;">
                  <div style="font-size: 10.5px; font-weight: 800; text-transform: uppercase; color: #64748b; letter-spacing: 0.5px; margin-bottom: 8px;">
                    What You'll Master:
                  </div>
                  <div style="display: flex; flex-direction: column; gap: 6px;">
                    @foreach(array_slice($course->features, 0, 3) as $feat)
                      <div style="font-size: 12px; font-weight: 600; color: #334155; display: flex; align-items: flex-start; gap: 8px; line-height: 1.35;">
                        <i class="fa-solid fa-check" style="color: {{ $meta['color'] }}; margin-top: 2px; font-size: 11px;"></i>
                        <span>{{ $feat }}</span>
                      </div>
                    @endforeach
                  </div>
                </div>
              @endif

              @if($course->schedule_info)
                <div style="font-size: 11.5px; color: #64748b; margin-bottom: 16px; display: flex; align-items: center; gap: 6px;">
                  <i class="fa-regular fa-calendar-check" style="color: {{ $meta['color'] }};"></i>
                  <span><strong>Schedule:</strong> {{ $course->schedule_info }}</span>
                </div>
              @endif
            </div>

            {{-- Action Buttons: Enroll / Inquire & View Details --}}
            <div style="display: flex; gap: 10px; margin-top: auto; padding-top: 8px;">
              <a href="{{ route('courses.detail', $course->slug) }}" style="flex: 1; text-align: center; background: {{ $meta['gradient'] }}; color: #ffffff; padding: 11px 14px; border-radius: 12px; font-size: 13px; font-weight: 800; text-decoration: none; box-shadow: 0 4px 14px {{ $meta['color'] }}40; display: inline-flex; align-items: center; justify-content: center; gap: 6px; transition: all 0.2s ease;">
                <i class="fa-solid fa-graduation-cap"></i> View Course
              </a>
              <a href="{{ route('contact') }}" style="flex: 1; text-align: center; background: #ffffff; color: #0f172a; border: 1.5px solid #cbd5e1; padding: 11px 14px; border-radius: 12px; font-size: 13px; font-weight: 800; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; gap: 6px; transition: all 0.2s ease;">
                <i class="fa-solid fa-headset"></i> Enroll / Inquire
              </a>
            </div>

          </div>
        @empty
          <div style="grid-column: 1 / -1; text-align: center; padding: 48px; color: var(--text-muted);" class="tactical-card">
            <i class="fa-solid fa-info-circle" style="font-size: 28px; color: #94a3b8; margin-bottom: 12px; display: block;"></i>
            New courses for {{ $meta['name'] }} will be announced shortly. Please contact the admission office for upcoming batch details.
          </div>
        @endforelse
      </div>
    </div>
  @endif

<!-- Cadet Interaction & Countdown Script -->
<script>
document.addEventListener('DOMContentLoaded', function () {
  // LIVE exam click handler: if someone clicks on the exam, toggle between topic and ending time
  document.querySelectorAll('.cadet-exam-3d-card.is-live-card').forEach(function (card) {
    card.addEventListener('click', function (e) {
      if (e.target.closest('a') || e.target.closest('button')) {
        return; // Don't intercept start button clicks
      }
      const toggleArea = card.querySelector('.exam-topic-toggle-area');
      if (!toggleArea) return;
      const topicView = toggleArea.querySelector('.topic-view-active');
      const endtimeView = toggleArea.querySelector('.endtime-view-toggled');
      if (topicView && endtimeView) {
        if (topicView.style.display === 'none') {
          topicView.style.display = 'flex';
          endtimeView.style.display = 'none';
        } else {
          topicView.style.display = 'none';
          endtimeView.style.display = 'flex';
        }
      }
    });
  });

  const boxes = document.querySelectorAll('.cadet-countdown-box');
  boxes.forEach(function (box) {
    let secondsLeft = parseInt(box.getAttribute('data-seconds'), 10);
    const examId = box.getAttribute('data-exam-id');
    const examUrl = box.getAttribute('data-url');
    const timerDisplay = box.querySelector('.cadet-timer-digits');

    function tick() {
      if (secondsLeft <= 0) {
        if (timerDisplay) {
          timerDisplay.innerHTML = '<span style="color: #059669;"><i class="fa-solid fa-check"></i> UNLOCKED</span>';
        }
        const badge = document.getElementById('cadet-badge-' + examId);
        if (badge) {
          badge.innerHTML = '<span style="background: rgba(16,185,129,0.3); color: #ffffff; border: 1.5px solid rgba(255,255,255,0.45); padding: 4px 11px; border-radius: 999px; font-size: 10.5px; font-weight: 800; display: inline-flex; align-items: center; gap: 5px; box-shadow: 0 2px 6px rgba(0,0,0,0.18);"><span style="width: 7px; height: 7px; border-radius: 50%; background: #34d399; box-shadow: 0 0 8px #34d399; display: inline-block;"></span> LIVE</span>';
        }
        const btnContainer = document.getElementById('cadet-btn-container-' + examId);
        if (btnContainer) {
          btnContainer.innerHTML = '<a href="' + examUrl + '" class="btn-3d-white" style="color: #047857;" onclick="event.stopPropagation();"><i class="fa-solid fa-play"></i> Start Assessment Test</a>';
        }
        const card = document.querySelector('.cadet-exam-3d-card[data-exam-id="' + examId + '"]');
        if (card) {
          card.classList.add('is-live-card');
        }
        return;
      }

      const h = Math.floor(secondsLeft / 3600);
      const m = Math.floor((secondsLeft % 3600) / 60);
      const s = Math.floor(secondsLeft % 60);
      const pad = (n) => (n < 10 ? '0' + n : n);
      if (timerDisplay) {
        timerDisplay.textContent = pad(h) + 'h : ' + pad(m) + 'm : ' + pad(s) + 's';
      }
      secondsLeft--;
      setTimeout(tick, 1000);
    }
    tick();
  });
});
</script>
@endsection
