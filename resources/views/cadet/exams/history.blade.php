@extends('layouts.portal')

@section('title', 'Exam History')
@section('cadet_badge', 'Exam History')
@section('page_title', 'Cadet Exam History & Scorecards')
@section('page_subtitle', 'Review your previous examination attempts, performance scorecards, and qualification records')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

  <!-- Branches Navigation & Filter Suite -->
  <div class="tactical-card" style="padding: 16px 20px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; box-shadow: 0 4px 16px rgba(15, 23, 42, 0.05); display: flex; flex-direction: column; gap: 14px;">
    
    <!-- Row 1: Branches Selector -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
      <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
        <span style="font-size: 11px; font-weight: 800; text-transform: uppercase; color: #64748b; margin-right: 4px; letter-spacing: 0.5px;">
          <i class="fa-solid fa-code-branch" style="color: #2563eb;"></i> Branches:
        </span>
        <a href="{{ route('cadet.exams.history', array_filter(['date' => request('date'), 'topic' => request('topic')])) }}" class="badge {{ !$selectedBranch || $selectedBranch === 'all' ? 'badge-emerald' : 'badge-navy' }}" style="text-decoration: none; padding: 7px 14px; font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; border-radius: 8px;">
          <i class="fa-solid fa-layer-group"></i> All Exam History
        </a>
        <a href="{{ route('cadet.exams.history', array_filter(['branch' => 'army', 'date' => request('date'), 'topic' => request('topic')])) }}" class="badge {{ $selectedBranch === 'army' ? 'badge-emerald' : 'badge-navy' }}" style="text-decoration: none; padding: 7px 14px; font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; border-radius: 8px;">
          <i class="fa-solid fa-shield-halved"></i> Army
          @if(in_array('army', $enrolledBranches))
            <span style="font-size: 9.5px; background: rgba(16,185,129,0.25); color: #047857; padding: 1px 7px; border-radius: 999px; font-weight: 800;">Enrolled</span>
          @else
            <span style="font-size: 9.5px; opacity: 0.85; background: rgba(0,0,0,0.06); padding: 1px 7px; border-radius: 999px;">Courses</span>
          @endif
        </a>
        <a href="{{ route('cadet.exams.history', array_filter(['branch' => 'navy', 'date' => request('date'), 'topic' => request('topic')])) }}" class="badge {{ $selectedBranch === 'navy' ? 'badge-emerald' : 'badge-navy' }}" style="text-decoration: none; padding: 7px 14px; font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; border-radius: 8px;">
          <i class="fa-solid fa-anchor"></i> Navy
          @if(in_array('navy', $enrolledBranches))
            <span style="font-size: 9.5px; background: rgba(16,185,129,0.25); color: #047857; padding: 1px 7px; border-radius: 999px; font-weight: 800;">Enrolled</span>
          @else
            <span style="font-size: 9.5px; opacity: 0.85; background: rgba(0,0,0,0.06); padding: 1px 7px; border-radius: 999px;">Courses</span>
          @endif
        </a>
        <a href="{{ route('cadet.exams.history', array_filter(['branch' => 'air_force', 'date' => request('date'), 'topic' => request('topic')])) }}" class="badge {{ $selectedBranch === 'air_force' ? 'badge-emerald' : 'badge-navy' }}" style="text-decoration: none; padding: 7px 14px; font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; border-radius: 8px;">
          <i class="fa-solid fa-jet-fighter"></i> Air Force
          @if(in_array('air_force', $enrolledBranches))
            <span style="font-size: 9.5px; background: rgba(16,185,129,0.25); color: #047857; padding: 1px 7px; border-radius: 999px; font-weight: 800;">Enrolled</span>
          @else
            <span style="font-size: 9.5px; opacity: 0.85; background: rgba(0,0,0,0.06); padding: 1px 7px; border-radius: 999px;">Courses</span>
          @endif
        </a>
        <a href="{{ route('cadet.exams.history', array_filter(['branch' => 'police', 'date' => request('date'), 'topic' => request('topic')])) }}" class="badge {{ $selectedBranch === 'police' ? 'badge-emerald' : 'badge-navy' }}" style="text-decoration: none; padding: 7px 14px; font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; border-radius: 8px;">
          <i class="fa-solid fa-user-shield"></i> Police
          @if(in_array('police', $enrolledBranches))
            <span style="font-size: 9.5px; background: rgba(16,185,129,0.25); color: #047857; padding: 1px 7px; border-radius: 999px; font-weight: 800;">Enrolled</span>
          @else
            <span style="font-size: 9.5px; opacity: 0.85; background: rgba(0,0,0,0.06); padding: 1px 7px; border-radius: 999px;">Courses</span>
          @endif
        </a>
      </div>
    </div>

    <!-- Row 2: Under Branches - Find Exam Option (Exam Name OR Find by Date) -->
    <div style="border-top: 1px solid #f1f5f9; padding-top: 14px;">
      <form method="GET" action="{{ route('cadet.exams.history') }}" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
        @if($selectedBranch)
          <input type="hidden" name="branch" value="{{ $selectedBranch }}">
        @endif

        <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap; flex: 1;">
          
          <!-- Section Sign Label -->
          <span style="font-size: 11px; font-weight: 800; text-transform: uppercase; color: #475569; letter-spacing: 0.6px; display: inline-flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-magnifying-glass" style="color: #2563eb;"></i> Find Exam:
          </span>

          <!-- Exam Name Input (Clean, no icon inside) -->
          <div style="min-width: 220px; max-width: 320px; flex: 1;">
            <input type="text" name="topic" value="{{ request('topic') }}" placeholder="Search by exam name..." class="form-control" style="width: 100%; height: 38px; padding: 8px 14px; font-size: 12.5px; border-radius: 9px; border: 1.5px solid #cbd5e1; background: #ffffff; color: #0f172a; box-sizing: border-box; transition: all 0.2s ease;">
          </div>

          <!-- OR Separator -->
          <span style="font-size: 11px; font-weight: 800; color: #64748b; background: #f1f5f9; border: 1px solid #e2e8f0; padding: 4px 10px; border-radius: 999px; text-transform: uppercase; letter-spacing: 0.8px;">OR</span>

          <!-- Find by Date (Whitish Clean Styling) -->
          <div style="display: flex; align-items: center; gap: 6px;">
            <label for="examTimingDate" style="font-size: 12px; font-weight: 700; color: #475569; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap;">
              <i class="fa-regular fa-calendar-days" style="color: #2563eb;"></i> Find by Date:
            </label>
            <input type="date" id="examTimingDate" name="date" value="{{ request('date') }}" class="form-control" style="height: 38px; padding: 6px 12px; font-size: 12.5px; border-radius: 9px; border: 1.5px solid #cbd5e1; background: #ffffff; color: #0f172a; outline: none; box-sizing: border-box;" onchange="this.form.submit()">
            @if(request('date'))
              <a href="{{ route('cadet.exams.history', array_filter(['branch' => $selectedBranch, 'topic' => request('topic')])) }}" style="display: inline-flex; align-items: center; justify-content: center; width: 24px; height: 24px; border-radius: 50%; background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; text-decoration: none; font-size: 11px;" title="Clear Date Filter">
                <i class="fa-solid fa-xmark"></i>
              </a>
            @endif
          </div>

          <!-- Find Exam Action Button -->
          <button type="submit" class="btn-find-exam" style="height: 38px; padding: 0 16px; border-radius: 9px; background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); color: #ffffff; font-size: 12.5px; font-weight: 700; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 3px 10px rgba(37, 99, 235, 0.25); transition: all 0.15s ease;">
            <i class="fa-solid fa-magnifying-glass"></i> Find Exam
          </button>

          <!-- Reset / Clear All Search Filters -->
          @if(request('date') || request('topic'))
            <a href="{{ route('cadet.exams.history', array_filter(['branch' => $selectedBranch])) }}" style="height: 38px; padding: 0 12px; border-radius: 9px; background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; font-size: 11.5px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; transition: all 0.15s ease;" title="Reset Find Filters">
              <i class="fa-solid fa-arrow-rotate-left"></i> Reset
            </a>
          @endif

        </div>

        @if(request('date') || request('topic'))
          <div style="font-size: 11px; font-weight: 700; color: #0284c7; background: #f0f9ff; border: 1px solid #bae6fd; padding: 5px 12px; border-radius: 999px; display: inline-flex; align-items: center; gap: 6px;">
            <span style="width: 6px; height: 6px; border-radius: 50%; background: #0284c7; display: inline-block;"></span>
            <span>
              Filtered:
              @if(request('topic')) Exam: "<strong>{{ request('topic') }}</strong>" @endif
              @if(request('topic') && request('date')) &bull; @endif
              @if(request('date')) Date: <strong>{{ Carbon\Carbon::parse(request('date'))->format('M d, Y') }}</strong> @endif
            </span>
          </div>
        @endif

      </form>
    </div>
  </div>

  @if($isEnrolledInSelectedBranch)

    <!-- Past Test Attempts Table (Fitted & No Horizontal Scrolling) -->
    <div class="tactical-card exam-history-card" style="padding: 0; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; box-shadow: 0 4px 16px rgba(15, 23, 42, 0.05); overflow: hidden; width: 100%; box-sizing: border-box;">
      <div style="padding: 14px 20px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px; background: #fafbfc;">
        <h3 style="font-size: 14px; font-weight: 800; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 8px; font-family: 'Poppins', sans-serif;">
          <i class="fa-solid fa-square-poll-vertical" style="color: #2563eb;"></i>
          @if($selectedBranch && $selectedBranch !== 'all')
            {{ ucfirst(str_replace('_', ' ', $selectedBranch)) }} Assessment History & Exam Details
          @else
            All Assessment History & Exam Details
          @endif
        </h3>
        <span style="font-size: 12px; font-weight: 700; color: #64748b;">
          Showing {{ $attempts->count() }} {{ Str::plural('record', $attempts->count()) }}
          @if(request('topic') && request('date'))
            (Topic: "{{ request('topic') }}" &bull; Date: {{ Carbon\Carbon::parse(request('date'))->format('M d, Y') }})
          @elseif(request('topic'))
            (Topic: "{{ request('topic') }}")
          @elseif(request('date'))
            (Date: {{ Carbon\Carbon::parse(request('date'))->format('M d, Y') }})
          @endif
        </span>
      </div>

      <table class="history-table">
        <thead>
          <tr>
            <th style="width: 55px; text-align: center;">Attempt #</th>
            <th style="text-align: left; min-width: 170px;">Assessment Module</th>
            <th style="text-align: left; width: 140px;">Exam Timing & Date</th>
            <th style="text-align: center; width: 95px;">Mark</th>
            <th style="text-align: center; width: 110px;">Candidate Rank</th>
            <th style="text-align: center; width: 95px;">Result</th>
            <th style="text-align: right; width: 95px;">Details</th>
          </tr>
        </thead>
        <tbody>
          @forelse($attempts as $attempt)
            <tr>
              <td style="text-align: center;">
                <strong style="color: #2563eb; font-weight: 800; font-size: 12px;">#{{ $attempt->attempt_number }}</strong>
              </td>
              <td>
                <div style="color: #0f172a; font-size: 13px; font-weight: 700; line-height: 1.35;">{{ $attempt->exam->title ?? 'N/A' }}</div>
                <div style="display: flex; gap: 5px; align-items: center; margin-top: 4px; flex-wrap: wrap;">
                  @if(optional($attempt->exam)->branch)
                    <span style="font-size: 10px; font-weight: 700; color: #334155; background: #f1f5f9; padding: 2px 7px; border-radius: 4px; display: inline-flex; align-items: center; gap: 4px;">
                      <i class="fa-solid {{ $attempt->exam->branchIcon() }}"></i> {{ $attempt->exam->branchLabel() }}
                    </span>
                  @endif
                  <span style="font-size: 9.5px; font-weight: 700; color: #1e40af; background: #dbeafe; padding: 2px 6px; border-radius: 4px;">
                    {{ strtoupper(str_replace('_', ' ', $attempt->exam->exam_type ?? 'mcq')) }}
                  </span>
                </div>
              </td>
              <td>
                <div style="display: flex; flex-direction: column; gap: 2px; font-size: 11.5px;">
                  <span style="color: #0f172a; font-weight: 700;">
                    <i class="fa-regular fa-calendar" style="color: #2563eb; font-size: 10.5px; width: 13px;"></i>
                    {{ $attempt->started_at ? $attempt->started_at->format('M d, Y') : ($attempt->created_at ? $attempt->created_at->format('M d, Y') : 'N/A') }}
                  </span>
                  <span style="color: #64748b; font-size: 10.5px;">
                    <i class="fa-regular fa-clock" style="font-size: 10px; width: 13px;"></i>
                    {{ $attempt->started_at ? $attempt->started_at->format('h:i A') : ($attempt->created_at ? $attempt->created_at->format('h:i A') : '—') }} &bull; Spent: {{ $attempt->time_spent_seconds > 0 ? gmdate('i:s', $attempt->time_spent_seconds) : ($attempt->exam ? $attempt->exam->duration_minutes . 'm' : '—') }}
                  </span>
                </div>
              </td>
              <td style="text-align: center;">
                <strong style="font-size: 13.5px; font-family: 'Plus Jakarta Sans', monospace; font-weight: 800; color: {{ $attempt->result_status === 'passed' ? '#059669' : ($attempt->result_status === 'failed' ? '#dc2626' : '#b45309') }};">
                  {{ (fmod($attempt->score, 1) == 0 ? (int)$attempt->score : number_format($attempt->score, 2)) }}/{{ (int)$attempt->total_marks }}
                </strong>
                <div style="font-size: 10px; color: #64748b; font-weight: 600;">({{ round($attempt->percentage) }}%)</div>
              </td>
              <td style="text-align: center;">
                @if(!$attempt->isRankPublished())
                  <span style="background: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1; font-size: 10px; font-weight: 800; padding: 3px 8px; border-radius: 999px; display: inline-flex; align-items: center; gap: 4px;" title="Rank will be calculated and published after the exam schedule concludes">
                    <i class="fa-solid fa-lock" style="font-size: 9px; color: #94a3b8;"></i> Not Published
                  </span>
                  <div style="font-size: 9.5px; color: #94a3b8; margin-top: 2px; font-weight: 600;">
                    @if(optional($attempt->exam)->schedule_end)
                      After {{ $attempt->exam->schedule_end->format('h:i A') }}
                    @else
                      After exam ends
                    @endif
                  </div>
                @else
                  <span style="background: #fef3c7; color: #b45309; border: 1px solid #fde68a; font-size: 10.5px; font-weight: 800; padding: 2px 8px; border-radius: 999px; display: inline-flex; align-items: center; gap: 4px;">
                    <i class="fa-solid fa-trophy" style="font-size: 9px;"></i> Rank #{{ $attempt->rank }}
                  </span>
                  <div style="font-size: 9.5px; color: #64748b; margin-top: 2px; font-weight: 500;">
                    of {{ $attempt->total_candidates }} {{ $attempt->total_candidates === 1 ? 'candidate' : 'candidates' }}
                  </div>
                @endif
              </td>
              <td style="text-align: center;">
                @if($attempt->result_status === 'passed')
                  <span style="background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; padding: 3px 8px; border-radius: 999px; font-size: 10px; font-weight: 800; display: inline-flex; align-items: center; gap: 4px;">
                    <i class="fa-solid fa-circle-check"></i> PASSED
                  </span>
                @elseif($attempt->result_status === 'failed')
                  <span style="background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; padding: 3px 8px; border-radius: 999px; font-size: 10px; font-weight: 800; display: inline-flex; align-items: center; gap: 4px;">
                    <i class="fa-solid fa-circle-xmark"></i> NOT QUALIFIED
                  </span>
                @else
                  <span style="background: #fffbeb; color: #b45309; border: 1px solid #fde68a; padding: 3px 8px; border-radius: 999px; font-size: 10px; font-weight: 800; display: inline-flex; align-items: center; gap: 4px;">
                    <i class="fa-solid fa-spinner fa-spin"></i> EVALUATION
                  </span>
                @endif
              </td>
              <td style="text-align: right;">
                <a href="{{ route('cadet.exams.result', $attempt->id) }}" style="padding: 6px 12px; font-size: 11.5px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; background: #eff6ff; color: #1d4ed8; border: 1.5px solid #bfdbfe; border-radius: 7px; white-space: nowrap; transition: all 0.15s ease;" title="View Complete Exam Details, Question Paper & Answers">
                  <i class="fa-solid fa-file-lines"></i> Details
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" style="text-align: center; padding: 40px 20px; color: #64748b;">
                <i class="fa-solid fa-clock-rotate-left" style="font-size: 32px; color: #94a3b8; opacity: 0.6; margin-bottom: 10px; display: block;"></i>
                <div style="font-size: 14px; font-weight: 700; margin-bottom: 4px; color: #1e293b;">No Exam History Found</div>
                <p style="font-size: 12px; margin-bottom: 14px; max-width: 480px; margin-left: auto; margin-right: auto; line-height: 1.5; color: #64748b;">
                  @if(request('topic') && request('date'))
                    No examination records found matching topic "{{ request('topic') }}" on {{ Carbon\Carbon::parse(request('date'))->format('M d, Y') }}.
                  @elseif(request('topic'))
                    No examination records found matching topic "{{ request('topic') }}".
                  @elseif(request('date'))
                    No examination records found on {{ Carbon\Carbon::parse(request('date'))->format('M d, Y') }}.
                  @elseif($selectedBranch && $selectedBranch !== 'all')
                    You have not taken any mock assessments for the {{ ucfirst(str_replace('_', ' ', $selectedBranch)) }} wing yet.
                  @else
                    You have not completed any mock assessments yet. Take an assessment to test your aptitude and track your ranking.
                  @endif
                </p>
                <div style="display: flex; justify-content: center; gap: 8px;">
                  @if(request('date') || request('topic'))
                    <a href="{{ route('cadet.exams.history', array_filter(['branch' => $selectedBranch])) }}" class="btn-tactical" style="padding: 7px 16px; font-size: 12px; text-decoration: none; border-radius: 8px;">
                      <i class="fa-solid fa-arrow-rotate-left"></i> Reset Search Filters
                    </a>
                  @else
                    <a href="{{ route('cadet.exams.index', array_filter(['branch' => $selectedBranch])) }}" class="btn-primary" style="padding: 7px 16px; font-size: 12px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; border-radius: 8px;">
                      <i class="fa-solid fa-play"></i> Browse Available Exams
                    </a>
                  @endif
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>

      @if($attempts->hasPages())
        <div style="padding: 12px 18px; border-top: 1px solid #e2e8f0; background: #ffffff;">
          {{ $attempts->links() }}
        </div>
      @endif
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
                You are not currently enrolled in the <strong>{{ $meta['name'] }}</strong> sector. To access mock tests, take wing assessments, and view your exam history for this sector, enroll in one of our ongoing academy preparatory programs below.
              </p>
            </div>
          </div>
          <div>
            <a href="{{ route('cadet.exams.history') }}" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px; border-radius: 10px; background: #f1f5f9; color: #334155; font-size: 13px; font-weight: 700; text-decoration: none; border: 1px solid #cbd5e1; transition: all 0.2s ease;">
              <i class="fa-solid fa-arrow-left"></i> All Exam History
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

              {{-- Duration & Pricing Pills --}}
              <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 18px;">
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 6px 12px; border-radius: 8px; font-size: 12px; color: #475569; display: flex; align-items: center; gap: 6px;">
                  <i class="fa-regular fa-clock" style="color: {{ $meta['color'] }};"></i>
                  <span>{{ $course->duration_weeks ? $course->duration_weeks . ' Weeks' : 'Comprehensive' }}</span>
                </div>
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 6px 12px; border-radius: 8px; font-size: 12px; color: #475569; display: flex; align-items: center; gap: 6px;">
                  <i class="fa-solid fa-bangladeshi-taka-sign" style="color: {{ $meta['color'] }};"></i>
                  <strong style="color: #0f172a;">৳{{ number_format($course->total_fee ?? 0) }}</strong>
                </div>
              </div>

              {{-- Highlights / Features list --}}
              @if(!empty($course->features))
                @php
                  $features = is_array($course->features) ? $course->features : json_decode($course->features, true) ?? [];
                @endphp
                @if(!empty($features))
                  <div style="border-top: 1px dashed #e2e8f0; padding-top: 14px; margin-bottom: 18px;">
                    <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; color: #94a3b8; letter-spacing: 0.5px; margin-bottom: 8px;">Key Inclusions</div>
                    <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 6px;">
                      @foreach(array_slice($features, 0, 3) as $feat)
                        <li style="font-size: 12.5px; color: #334155; display: flex; align-items: flex-start; gap: 8px;">
                          <i class="fa-solid fa-check" style="color: {{ $meta['color'] }}; font-size: 11px; margin-top: 3px;"></i>
                          <span>{{ is_string($feat) ? $feat : ($feat['text'] ?? '') }}</span>
                        </li>
                      @endforeach
                    </ul>
                  </div>
                @endif
              @endif
            </div>

            {{-- Action Buttons --}}
            <div style="margin-top: 16px; border-top: 1px solid #f1f5f9; padding-top: 14px; display: flex; gap: 10px;">
              <a href="{{ route('courses.detail', $course->slug) }}" style="flex: 1; text-align: center; padding: 11px 16px; border-radius: 12px; background: {{ $meta['gradient'] }}; color: #ffffff; font-size: 13px; font-weight: 800; text-decoration: none; box-shadow: 0 4px 12px {{ $meta['color'] }}44; transition: transform 0.15s ease;">
                <i class="fa-solid fa-graduation-cap"></i> View Course
              </a>
              <a href="{{ route('contact') }}" style="padding: 11px 16px; border-radius: 12px; background: #f8fafc; color: #475569; border: 1px solid #e2e8f0; font-size: 13px; font-weight: 700; text-decoration: none;">
                Enroll / Inquire
              </a>
            </div>

          </div>
        @empty
          <div style="grid-column: 1 / -1; background: #ffffff; border-radius: 16px; padding: 40px; text-align: center; border: 1.5px dashed #cbd5e1;">
            <i class="fa-solid fa-graduation-cap" style="font-size: 36px; color: #94a3b8; margin-bottom: 12px; display: block;"></i>
            <h4 style="font-size: 16px; font-weight: 800; color: #1e293b; margin: 0 0 6px 0;">No Ongoing Courses Listed Yet</h4>
            <p style="font-size: 13px; color: #64748b; margin: 0 0 16px 0;">New batches for {{ $meta['name'] }} wing will be scheduled soon. Contact administration for early registration.</p>
            <a href="{{ route('contact') }}" class="btn-primary" style="padding: 8px 20px; font-size: 13px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
              <i class="fa-solid fa-envelope"></i> Contact Academy Office
            </a>
          </div>
        @endforelse
      </div>
    </div>
  @endif

</div>

<style>
  .exam-history-card {
    overflow-x: hidden !important;
  }
  .history-table {
    width: 100% !important;
    border-collapse: collapse !important;
    table-layout: auto !important;
  }
  .history-table th {
    background: #f8fafc !important;
    color: #475569 !important;
    font-size: 10.5px !important;
    font-weight: 800 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.5px !important;
    padding: 11px 8px !important;
    border-bottom: 1.5px solid #e2e8f0 !important;
    white-space: nowrap !important;
  }
  .history-table td {
    background: #ffffff !important;
    color: #1e293b !important;
    font-size: 12px !important;
    padding: 11px 8px !important;
    border-bottom: 1px solid #f1f5f9 !important;
    vertical-align: middle !important;
  }
  .history-table tr:hover td {
    background: #f8fafc !important;
  }
  @media (max-width: 900px) {
    .exam-history-card {
      overflow-x: auto !important;
      -webkit-overflow-scrolling: touch;
    }
  }

  .btn-find-exam:hover {
    transform: translateY(-1px) !important;
    box-shadow: 0 5px 14px rgba(37, 99, 235, 0.35) !important;
  }
</style>
@endsection

