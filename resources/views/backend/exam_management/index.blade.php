@extends('layouts.portal')

@section('title', 'Exam Management')
@section('page_title', 'Exam Management')
@section('page_subtitle', '')

@section('topbar_actions')
@endsection

@section('content')
<div style="width: 100%; margin: 0 auto; position: relative;">

  {{-- Floating Tactical Toast Notification Container --}}
  <div id="tacticalToast" style="display: none; position: fixed; top: 24px; right: 24px; z-index: 99999; max-width: 420px; background: rgba(15, 23, 42, 0.96); backdrop-filter: blur(8px); border-radius: 12px; box-shadow: 0 20px 40px rgba(0,0,0,0.5), 0 0 20px rgba(16,185,129,0.25); border: 1px solid rgba(16,185,129,0.4); padding: 14px 18px; color: #ffffff; animation: toastSlideIn 0.3s cubic-bezier(0.16, 1, 0.3, 1);">
    <div style="display: flex; align-items: flex-start; gap: 12px;">
      <span id="tacticalToastIcon" style="width: 28px; height: 28px; border-radius: 8px; background: rgba(16,185,129,0.15); color: #34d399; display: grid; place-items: center; font-size: 14px; flex-shrink: 0; margin-top: 1px;">
        <i class="fa-solid fa-circle-check"></i>
      </span>
      <div style="flex: 1;">
        <div id="tacticalToastTitle" style="font-size: 12.5px; font-weight: 800; letter-spacing: 0.3px; color: #ffffff; text-transform: uppercase;">System Synchronized</div>
        <div id="tacticalToastMsg" style="font-size: 12px; color: #cbd5e1; margin-top: 2px; line-height: 1.4;">Changes saved.</div>
      </div>
      <button type="button" onclick="hideTacticalToast()" style="background: none; border: none; color: #64748b; font-size: 16px; cursor: pointer; padding: 0 4px; line-height: 1;">&times;</button>
    </div>
  </div>

  {{-- TOP HEADER BAR --}}
  <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px; margin-bottom: 22px;">
    <div style="display: flex; align-items: center; gap: 12px;">
      @if(!$isLanding)
        <a href="{{ route('admin.exam_management.index') }}" class="btn-tactical" style="background: rgba(255,255,255,0.06); color: #cbd5e1; border: 1px solid rgba(255,255,255,0.12); padding: 8px 14px; font-size: 12px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 7px; border-radius: 8px; transition: all 0.2s ease;">
          <i class="fa-solid fa-arrow-left"></i> Back to Exam Management
        </a>
      @endif
      <h2 style="font-size: 20px; font-weight: 800; color: #ffffff; margin: 0; font-family: 'Poppins', sans-serif;">Exam Management</h2>
    </div>

    {{-- Type Switcher Tabs (Free Exams, Cadet Exams, All Exams) --}}
    <div style="display: flex; gap: 6px; flex-wrap: wrap; background: rgba(0,0,0,0.3); padding: 4px; border-radius: 9px; border: 1px solid rgba(255,255,255,0.06);">
      <a href="{{ route('admin.exam_management.index', array_filter(['type' => 'free', 'branch' => $selectedBranch, 'cadre' => $selectedCadre, 'track' => $selectedTrack])) }}" class="subnav-pill {{ $type === 'free' ? 'active' : '' }}">
        <i class="fa-solid fa-unlock-keyhole"></i> Free Exams ({{ $stats['free'] }})
      </a>
      <a href="{{ route('admin.exam_management.index', array_filter(['type' => 'paid', 'branch' => $selectedBranch, 'cadre' => $selectedCadre, 'track' => $selectedTrack])) }}" class="subnav-pill {{ $type === 'paid' ? 'active' : '' }}">
        <i class="fa-solid fa-user-graduate"></i> Cadet Exams ({{ $stats['cadet'] ?? $stats['paid'] }})
      </a>
      <a href="{{ route('admin.exam_management.index', array_filter(['type' => 'all', 'branch' => $selectedBranch, 'cadre' => $selectedCadre, 'track' => $selectedTrack])) }}" class="subnav-pill {{ $type === 'all' ? 'active' : '' }}">
        <i class="fa-solid fa-list-check"></i> All Exams ({{ $stats['total'] }})
      </a>
    </div>
  </div>

  {{-- SUCCESS / ERROR NOTIFICATIONS --}}
  @if(session('success'))
    <div id="successBanner" style="background: rgba(16,185,129,0.12); border: 1px solid rgba(16,185,129,0.35); border-radius: 10px; padding: 12px 16px; margin-bottom: 16px; color: #34d399; font-size: 13px; display: flex; align-items: center; justify-content: space-between; animation: slideDown 0.3s ease;">
      <div style="display: flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-circle-check" style="font-size: 15px;"></i>
        <span>{{ session('success') }}</span>
      </div>
      <button type="button" onclick="this.parentElement.remove()" style="background: none; border: none; color: #34d399; cursor: pointer; font-size: 16px; padding: 0 4px;">&times;</button>
    </div>
  @endif

  @if(session('error'))
    <div style="background: rgba(239,68,68,0.12); border: 1px solid rgba(239,68,68,0.35); border-radius: 10px; padding: 12px 16px; margin-bottom: 16px; color: #fca5a5; font-size: 13px; display: flex; align-items: center; justify-content: space-between;">
      <div style="display: flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-triangle-exclamation" style="font-size: 15px;"></i>
        <span>{{ session('error') }}</span>
      </div>
      <button type="button" onclick="this.parentElement.remove()" style="background: none; border: none; color: #fca5a5; cursor: pointer; font-size: 16px; padding: 0 4px;">&times;</button>
    </div>
  @endif

  @if(isset($errors) && $errors->any())
    <div style="background: rgba(239,68,68,0.12); border: 1px solid rgba(239,68,68,0.35); border-radius: 10px; padding: 12px 16px; margin-bottom: 16px; color: #fca5a5; font-size: 13px;">
      <div style="display: flex; align-items: center; gap: 8px; font-weight: 700; margin-bottom: 4px;">
        <i class="fa-solid fa-triangle-exclamation"></i>
        <span>Please correct the following errors:</span>
      </div>
      <ul style="margin: 0; padding-left: 20px; font-size: 12px;">
        @foreach($errors->all() as $err)
          <li>{{ $err }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  @if($isLanding)
    {{-- ================================================================ --}}
    {{-- LANDING VIEW: 2 PROFESSIONAL COMMAND BOXES (FREE & CADET EXAMS)  --}}
    {{-- ================================================================ --}}
    <div class="portal-landing-hub">
      <span class="section-caption">EXAMINATION ACCESS CONTROL</span>
      <div class="command-boxes-grid exam-2-boxes-grid">

        {{-- 1. FREE EXAMS --}}
        <a href="{{ route('admin.exam_management.index', ['type' => 'free']) }}" class="dark-action-card simple-box exam-box-free">
          <div class="dark-action-icon-wrap box-icon-wrap" style="color: #10b981;">
            <i class="fa-solid fa-unlock-keyhole"></i>
          </div>
          <h3 class="box-title">Free Exams</h3>
          <p class="box-count" style="font-size: 14px; font-weight: 700; color: #34d399; margin-bottom: 0;">
            {{ $stats['free'] }} {{ $stats['free'] == 1 ? 'Exam' : 'Exams' }}
          </p>
        </a>

        {{-- 2. CADET EXAMS --}}
        <a href="{{ route('admin.exam_management.index', ['type' => 'paid']) }}" class="dark-action-card simple-box exam-box-cadet">
          <div class="dark-action-icon-wrap box-icon-wrap" style="color: #60a5fa;">
            <i class="fa-solid fa-user-graduate"></i>
          </div>
          <h3 class="box-title">Cadet Exams</h3>
          <p class="box-count" style="font-size: 14px; font-weight: 700; color: #60a5fa; margin-bottom: 0;">
            {{ $stats['cadet'] ?? $stats['paid'] }} {{ ($stats['cadet'] ?? $stats['paid']) == 1 ? 'Exam' : 'Exams' }}
          </p>
        </a>

      </div>

      {{-- Total Exams Banner --}}
      <div style="margin-top: 24px; background: #181c26; border: 1px solid rgba(255,255,255,0.06); border-radius: 14px; padding: 16px 24px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
        <div style="display: flex; align-items: center; gap: 12px;">
          <span style="width: 38px; height: 38px; border-radius: 10px; background: rgba(255,87,87,0.12); color: #ff5757; display: grid; place-items: center; font-size: 18px;">
            <i class="fa-solid fa-layer-group"></i>
          </span>
          <div>
            <strong style="font-size: 15px; font-weight: 800; color: #ffffff; display: block;">Total Exams: {{ $stats['total'] }}</strong>
          </div>
        </div>

        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
          <button type="button" onclick="openModal('selectExamModal')" class="btn-tactical" style="background: rgba(255,87,87,0.12); color: #ff5757; border: 1px solid rgba(255,87,87,0.3); padding: 8px 16px; font-size: 12px; font-weight: 700; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-pen-to-square"></i> Edit Exam from Edit Page
          </button>
          <a href="{{ route('admin.exam_management.index', ['type' => 'all']) }}" class="btn-tactical" style="background: rgba(255,255,255,0.06); color: #cbd5e1; border: 1px solid rgba(255,255,255,0.12); padding: 8px 16px; font-size: 12px; font-weight: 600; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
            View All Exams
          </a>
          <a href="{{ route('admin.exam_management.create') }}" class="btn-primary" style="padding: 8px 16px; font-size: 12px; font-weight: 600; border-radius: 8px; display: inline-flex; align-items: center; gap: 6px; text-decoration: none;">
            <i class="fa-solid fa-plus"></i> Create a New Exam
          </a>
        </div>
      </div>
    </div>

  @else
    {{-- ================================================================ --}}
    {{-- SUBPAGE VIEW: CASCADING DROPDOWN FILTER SUITE + SEARCH + CARDS    --}}
    {{-- ================================================================ --}}

    @php
      $isMilitaryBranch = in_array($selectedBranch, ['navy', 'army', 'air_force']);
      $isPoliceBranch = ($selectedBranch === 'police');
    @endphp

    {{-- Filter & Action Suite with Cascading Dropdowns --}}
    <div class="content-panel classical-card" style="background: #181c26; border: 1px solid rgba(255, 255, 255, 0.06); border-radius: 14px; padding: 16px 20px; margin-bottom: 24px; position: relative; z-index: 40;">
      <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px;">
        
        {{-- Left Side: Cascading Dropdowns --}}
        <form id="examFilterForm" method="GET" action="{{ route('admin.exam_management.index') }}" style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap; margin: 0;">
          <input type="hidden" name="type" value="{{ $type }}">
          @if(!empty($search))
            <input type="hidden" name="search" value="{{ $search }}">
          @endif

          {{-- Dropdown 1: Branch Select --}}
          <div style="position: relative;">
            <select name="branch" id="examBranchSelect" class="ida-track-select" onchange="onExamBranchChange(this.value)">
              <option value="" {{ empty($selectedBranch) ? 'selected' : '' }}>All Branches</option>
              <option value="navy" {{ $selectedBranch === 'navy' ? 'selected' : '' }}>Navy ({{ $stats['navy'] }})</option>
              <option value="police" {{ $selectedBranch === 'police' ? 'selected' : '' }}>Police ({{ $stats['police'] }})</option>
              <option value="army" {{ $selectedBranch === 'army' ? 'selected' : '' }}>Army ({{ $stats['army'] }})</option>
              <option value="air_force" {{ $selectedBranch === 'air_force' ? 'selected' : '' }}>Air Force ({{ $stats['air_force'] }})</option>
            </select>
          </div>

          @if($isMilitaryBranch)
            {{-- Dropdown 2: Cadre Select (Officer vs Sailor/Soldier/Airman) --}}
            @php
              $soldierLabel = ($selectedBranch === 'navy') ? 'Sailor' : (($selectedBranch === 'air_force') ? 'Airman' : 'Soldier');
              $wingName = ucfirst(str_replace('_', ' ', $selectedBranch));
            @endphp
            <div style="position: relative;">
              <select name="cadre" id="examCadreSelect" class="ida-track-select" onchange="onExamCadreChange(this.value)">
                <option value="" {{ empty($selectedCadre) ? 'selected' : '' }}>All {{ $wingName }} Modules</option>
                <option value="officer" {{ $selectedCadre === 'officer' ? 'selected' : '' }}>Officer ({{ $stats['officer'] ?? 0 }})</option>
                <option value="soldier" {{ $selectedCadre === 'soldier' ? 'selected' : '' }}>{{ $soldierLabel }} ({{ $stats['soldier'] ?? 0 }})</option>
              </select>
            </div>

            {{-- Dropdown 3: Officer Two-Exam Stage (Preliminary vs ISSB) --}}
            <div id="examOfficerStageWrapper" style="display: {{ $selectedCadre === 'officer' ? 'inline-block' : 'none' }}; position: relative;">
              <select name="track" id="examOfficerTrackSelect" class="ida-track-select" onchange="document.getElementById('examFilterForm').submit()" {{ $selectedCadre === 'officer' ? '' : 'disabled' }}>
                <option value="" {{ empty($selectedTrack) || $selectedTrack === 'soldier' ? 'selected' : '' }}>All Officer Exams</option>
                <option value="prelim" {{ $selectedTrack === 'prelim' ? 'selected' : '' }}>Preliminary (Officer 1) ({{ $stats['prelim'] }})</option>
                <option value="issb" {{ $selectedTrack === 'issb' ? 'selected' : '' }}>ISSB Masterclass (Officer 2) ({{ $stats['issb'] }})</option>
              </select>
            </div>

            {{-- Dropdown 3 for Soldier/Sailor: Soldier recruitment vs ISSB --}}
            <div id="examSoldierStageWrapper" style="display: {{ $selectedCadre === 'soldier' ? 'inline-block' : 'none' }}; position: relative;">
              <select name="track" id="examSoldierTrackSelect" class="ida-track-select" onchange="document.getElementById('examFilterForm').submit()" {{ $selectedCadre === 'soldier' ? '' : 'disabled' }}>
                <option value="soldier" {{ empty($selectedTrack) || $selectedTrack === 'soldier' ? 'selected' : '' }}>{{ $soldierLabel }} Track ({{ $stats['soldier'] }})</option>
                <option value="issb" {{ $selectedTrack === 'issb' ? 'selected' : '' }}>ISSB Masterclass ({{ $stats['issb'] }})</option>
              </select>
            </div>

          @elseif($isPoliceBranch)
            {{-- Dropdown 2 for Police: 3 Police Rank Options (NO ISSB) --}}
            <div style="position: relative;">
              <select name="track" id="examPoliceTrackSelect" class="ida-track-select" onchange="document.getElementById('examFilterForm').submit()">
                <option value="" {{ empty($selectedTrack) ? 'selected' : '' }}>All Police Exams</option>
                <option value="constable" {{ $selectedTrack === 'constable' ? 'selected' : '' }}>Constable ({{ $stats['constable'] }})</option>
                <option value="si" {{ $selectedTrack === 'si' ? 'selected' : '' }}>Sub-Inspector SI ({{ $stats['si'] }})</option>
                <option value="asi" {{ $selectedTrack === 'asi' ? 'selected' : '' }}>Assistant SI ASI ({{ $stats['asi'] }})</option>
              </select>
            </div>
          @endif

          @if(!empty($selectedBranch) || !empty($selectedCadre) || !empty($selectedTrack))
            <a href="{{ route('admin.exam_management.index', array_filter(['type' => $type, 'search' => $search])) }}" style="font-size: 11.5px; color: #f87171; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; padding: 7px 11px; border-radius: 8px; background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.25); font-weight: 600;" title="Reset filters">
              <i class="fa-solid fa-rotate-left"></i> Reset
            </a>
          @endif
        </form>

        {{-- Right Side: Search Form & Create Button --}}
        <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
          <form method="GET" action="{{ route('admin.exam_management.index') }}" style="display: flex; align-items: center; gap: 6px; margin: 0;">
            <input type="hidden" name="type" value="{{ $type }}">
            @if($selectedBranch)
              <input type="hidden" name="branch" value="{{ $selectedBranch }}">
            @endif
            @if($selectedCadre)
              <input type="hidden" name="cadre" value="{{ $selectedCadre }}">
            @endif
            @if($selectedTrack)
              <input type="hidden" name="track" value="{{ $selectedTrack }}">
            @endif
            <div style="position: relative; display: flex; align-items: center;">
              <input type="text" name="search" value="{{ $search }}" placeholder="Search exam title..." style="background: #11141d; border: 1px solid rgba(255,255,255,0.12); border-radius: 9px; padding: 9px 14px; font-size: 12.5px; color: #ffffff; width: 210px; outline: none; transition: border-color 0.2s ease;">
            </div>
            <button type="submit" class="btn-secondary" style="height: 40px; padding: 0 14px; font-size: 13px; border-radius: 9px; display: inline-flex; align-items: center; justify-content: center; cursor: pointer;" title="Search">
              <i class="fa-solid fa-magnifying-glass"></i>
            </button>
            @if(!empty($search))
              <a href="{{ route('admin.exam_management.index', array_filter(['type' => $type, 'branch' => $selectedBranch, 'cadre' => $selectedCadre, 'track' => $selectedTrack])) }}" class="btn-secondary" style="height: 40px; padding: 0 12px; font-size: 13px; border-radius: 9px; color: #ef4444; display: inline-flex; align-items: center; justify-content: center; text-decoration: none;" title="Clear Search">
                <i class="fa-solid fa-xmark"></i>
              </a>
            @endif
          </form>

          <a href="{{ route('admin.exam_management.create', ['type' => $type]) }}" class="btn-primary" style="height: 40px; padding: 0 18px; font-size: 12.5px; font-weight: 700; border-radius: 9px; display: inline-flex; align-items: center; gap: 7px; text-decoration: none; box-shadow: 0 4px 12px rgba(255,87,87,0.25);">
            <i class="fa-solid fa-plus"></i> Create Exam
          </a>
        </div>

      </div>
    </div>

    {{-- Exam Cards Grid (Matching Online Exam Page Layout + Rich Admin Controls) --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px; position: relative; z-index: 10;">
      @forelse($exams as $exam)
        @php
          $isFuture = $exam->isScheduledFuture();
          $isPaid = (bool) $exam->is_paid_for_external;
        @endphp

        <div id="exam-card-{{ $exam->id }}" class="content-panel classical-card" style="background: #181c26; border: 1px solid rgba(255, 255, 255, 0.06); border-radius: 14px; padding: 22px; display: flex; flex-direction: column; justify-content: space-between; border-top: 4px solid {{ $exam->branchColor() }}; position: relative; box-shadow: 0 4px 16px rgba(0,0,0,0.25);">
          <div>
            {{-- Header Badges --}}
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; gap: 6px; flex-wrap: wrap;">
              <div style="display: flex; gap: 6px; align-items: center; flex-wrap: wrap;">
                {{-- Branch Badge --}}
                <span class="badge" style="background: {{ $exam->branchColor() }}; color: #ffffff; font-size: 10.5px;">
                  <i class="fa-solid {{ $exam->branchIcon() }}"></i> {{ $exam->branchLabel() }}
                </span>

                {{-- Free vs Cadet Status Badge --}}
                @if($exam->access_type === 'free')
                  <span class="badge badge-emerald" style="font-size: 10.5px; font-weight: 700;">
                    <i class="fa-solid fa-unlock-keyhole"></i> FREE EXAM
                  </span>
                @elseif($exam->access_type === 'both')
                  <span class="badge" style="background: rgba(99, 102, 241, 0.15); color: #818cf8; border: 1px solid rgba(99, 102, 241, 0.3); font-size: 10.5px; font-weight: 700;">
                    <i class="fa-solid fa-globe"></i> CADET & FREE
                  </span>
                @else
                  <span class="badge" style="background: rgba(59, 130, 246, 0.15); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.3); font-size: 10.5px; font-weight: 700;">
                    <i class="fa-solid fa-user-graduate"></i> CADET EXAM
                  </span>
                @endif
              </div>

              {{-- Target Portal / Schedule Badge --}}
              <div>
                @if($isFuture)
                  <span class="badge badge-gold" style="font-size: 10px;"><i class="fa-regular fa-clock"></i> SCHEDULED</span>
                @elseif($exam->status === 'open')
                  <span class="badge badge-emerald" style="font-size: 10px;"><span style="width: 6px; height: 6px; border-radius: 50%; background: #10b981; display: inline-block;"></span> LIVE NOW</span>
                @else
                  <span class="badge badge-navy" style="font-size: 10px;">{{ strtoupper($exam->status) }}</span>
                @endif
              </div>
            </div>

            {{-- Exam Title & Category --}}
            <h3 style="font-size: 16px; font-weight: 800; color: #ffffff; margin: 0 0 6px 0; font-family: 'Poppins', sans-serif; line-height: 1.35;">
              {{ $exam->title }}
            </h3>
            <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 10px; flex-wrap: wrap;">
              <span class="badge badge-navy" style="font-size: 10px;">{{ $exam->category }}</span>
              @if($exam->isIssb())
                <span style="background: rgba(234, 179, 8, 0.15); color: #facc15; border: 1px solid rgba(234, 179, 8, 0.35); font-size: 9.5px; font-weight: 700; padding: 2px 7px; border-radius: 5px; display: inline-flex; align-items: center; gap: 4px;">
                  <i class="fa-solid fa-star"></i> ISSB Masterclass (Officer Exam 2)
                </span>
              @elseif($exam->isPrelim())
                <span style="background: rgba(59, 130, 246, 0.15); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.35); font-size: 9.5px; font-weight: 700; padding: 2px 7px; border-radius: 5px; display: inline-flex; align-items: center; gap: 4px;">
                  <i class="fa-solid fa-shield"></i> {{ $exam->branchLabel() }} Preliminary (Officer Exam 1)
                </span>
              @elseif($exam->isPolice())
                <span style="background: rgba(168, 85, 247, 0.15); color: #c084fc; border: 1px solid rgba(168, 85, 247, 0.35); font-size: 9.5px; font-weight: 700; padding: 2px 7px; border-radius: 5px; display: inline-flex; align-items: center; gap: 4px;">
                  <i class="fa-solid fa-shield-halved"></i> {{ $exam->trackLabel() }}
                </span>
              @endif
              <span style="font-size: 11px; color: #64748b;">• {{ strtoupper(str_replace('_', ' ', $exam->exam_type)) }}</span>
            </div>

            {{-- Description --}}
            @if($exam->description)
              <p style="font-size: 12px; color: #8c96a8; line-height: 1.5; margin: 0 0 14px 0; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                {{ $exam->description }}
              </p>
            @endif

            {{-- Exam Specs Grid --}}
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; background: #11141d; border: 1px solid rgba(255,255,255,0.05); border-radius: 10px; padding: 10px 12px; margin-bottom: 14px; text-align: center;">
              <div>
                <small style="display: block; font-size: 9.5px; color: #64748b; text-transform: uppercase;">Duration</small>
                <strong style="font-size: 12.5px; color: #ffffff;">{{ $exam->duration_minutes }}m</strong>
              </div>
              <div>
                <small style="display: block; font-size: 9.5px; color: #64748b; text-transform: uppercase;">Marks</small>
                <strong style="font-size: 12.5px; color: #34d399;">{{ (int)$exam->total_marks }}</strong>
              </div>
              <div>
                <small style="display: block; font-size: 9.5px; color: #64748b; text-transform: uppercase;">Questions</small>
                <strong style="font-size: 12.5px; color: #38bdf8;">{{ $exam->questions_count }} Qs</strong>
              </div>
            </div>

            {{-- Portal Routing Notice --}}
            <div style="padding: 7px 10px; border-radius: 8px; background: rgba(255,255,255,0.02); border: 1px dashed rgba(255,255,255,0.08); font-size: 11px; color: #94a3b8; margin-bottom: 16px; line-height: 1.4;">
              @if($isPaid)
                <div style="display: flex; align-items: center; gap: 5px; margin-bottom: 3px;">
                  <i class="fa-solid fa-user-graduate" style="color: #60a5fa;"></i> Target: <strong style="color: #60a5fa;">Cadet Portal Only</strong>
                </div>
                <div style="font-size: 10.5px; color: #cbd5e1;">
                  @if($exam->isIssb())
                    <span style="color: #facc15;"><i class="fa-solid fa-unlock-keyhole"></i> Officer Exam 2 (Tri-Services):</span> Universal assessment across Navy, Army, and Air Force.
                  @elseif($exam->isPrelim())
                    <span style="color: #60a5fa;"><i class="fa-solid fa-lock"></i> Officer Exam 1 (Branch Exclusive):</span> Requires enrollment in {{ $exam->branchLabel() }} Officer track.
                  @elseif($exam->isPolice())
                    <span style="color: #c084fc;"><i class="fa-solid fa-shield-halved"></i> Police Track (No ISSB):</span> Restricted to {{ $exam->trackLabel() }} candidates.
                  @else
                    <span style="color: #94a3b8;"><i class="fa-solid fa-circle-info"></i> Program Track:</span> {{ $exam->trackLabel() }}.
                  @endif
                </div>
              @else
                <i class="fa-solid fa-globe" style="color: #34d399;"></i> Target: <strong style="color: #34d399;">Frontend Online Tests</strong> (Free Access to all candidates)
              @endif
            </div>
          </div>

          {{-- Admin Action Toolbar --}}
          <div style="border-top: 1px solid rgba(255,255,255,0.06); padding-top: 12px; display: flex; justify-content: space-between; align-items: center; gap: 8px; flex-wrap: wrap;">
            
            <div style="display: flex; align-items: center; gap: 6px;">
              <a href="{{ route('admin.exams.questions', $exam->id) }}" class="btn-tactical" style="background: rgba(255,255,255,0.06); color: #94a3b8; border: 1px solid rgba(255,255,255,0.1); padding: 6px 11px; font-size: 11.5px; text-decoration: none; border-radius: 6px; display: inline-flex; align-items: center; gap: 5px;">
                <i class="fa-solid fa-list-ol"></i> Manage Qs ({{ $exam->questions_count }})
              </a>

              {{-- Public Visibility Toggle (No page reload) --}}
              <button type="button" 
                id="toggle-public-btn-{{ $exam->id }}"
                class="btn-tactical" 
                onclick="togglePublicAjax({{ $exam->id }}, '{{ route('admin.exams.toggle_public', $exam->id) }}', this)"
                style="padding: 6px 10px; font-size: 11.5px; border-radius: 6px; cursor: pointer; {{ $exam->is_public_for_external ? 'background: rgba(56, 189, 248, 0.1); color: #38bdf8; border: 1px solid rgba(56, 189, 248, 0.25);' : 'background: rgba(239, 68, 68, 0.1); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.25);' }}" 
                title="{{ $exam->is_public_for_external ? 'Visible on Frontend (Click to hide)' : 'Hidden from Frontend (Click to publish)' }}">
                <i class="fa-solid {{ $exam->is_public_for_external ? 'fa-eye' : 'fa-eye-slash' }}"></i>
              </button>
            </div>

            <div style="display: flex; align-items: center; gap: 6px;">
              {{-- Dedicated Edit Page Button --}}
              <a href="{{ route('admin.exam_management.edit', ['id' => $exam->id, 'return_to' => url()->full()]) }}" class="btn-tactical" style="background: rgba(255, 87, 87, 0.12); color: #ff5757; border: 1px solid rgba(255, 87, 87, 0.3); padding: 6px 12px; font-size: 11.5px; font-weight: 700; text-decoration: none; border-radius: 6px; display: inline-flex; align-items: center; gap: 5px;" title="Edit Exam from Edit Page">
                <i class="fa-solid fa-pen-to-square"></i> Edit
              </a>

              {{-- Asynchronous Delete (No page reload) --}}
              <button type="button" 
                onclick="deleteExamAjax({{ $exam->id }}, '{{ addslashes($exam->title) }}', '{{ route('admin.exams.destroy', $exam->id) }}', this)"
                style="background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2); padding: 6px 9px; font-size: 11.5px; border-radius: 6px; cursor: pointer;" 
                title="Delete Exam">
                <i class="fa-solid fa-trash-can"></i>
              </button>
            </div>
          </div>
        </div>
      @empty
        <div style="grid-column: 1 / -1; background: #181c26; border: 1px dashed rgba(255,255,255,0.12); border-radius: 14px; padding: 40px 20px; text-align: center; color: #64748b;">
          <i class="fa-solid fa-inbox" style="font-size: 32px; margin-bottom: 12px; display: block; color: #475569;"></i>
          <h4 style="color: #cbd5e1; font-size: 15px; margin-bottom: 6px;">No Assessment Modules Found</h4>
          <p style="font-size: 12px; margin-bottom: 16px;">There are no {{ $type === 'free' ? 'free' : ($type === 'paid' ? 'cadet' : '') }} exams matching this criteria.</p>
          <a href="{{ route('admin.exam_management.create', ['type' => $type]) }}" class="btn-primary" style="padding: 7px 16px; font-size: 12px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-plus"></i> Create {{ $type === 'paid' ? 'Cadet' : ucfirst($type ?? 'Assessment') }} Exam
          </a>
        </div>
      @endforelse
    </div>

  @endif
</div>

{{-- ================================================================ --}}
{{-- MODAL: QUICK SELECT EXAM TO OPEN DEDICATED EDIT PAGE             --}}
{{-- ================================================================ --}}
<div id="selectExamModal" class="ida-modal-overlay" style="display: none;">
  <div class="ida-modal-box" style="max-width: 560px;">
    <div class="ida-modal-header">
      <h3 class="ida-modal-title">
        <i class="fa-solid fa-pen-to-square" style="color: #ff5757;"></i> Edit an Exam
      </h3>
      <button type="button" onclick="closeModal('selectExamModal')" class="ida-modal-close">&times;</button>
    </div>
    <div style="padding: 22px;">
      <p style="font-size: 12.5px; color: #94a3b8; margin-top: 0; margin-bottom: 16px; line-height: 1.5;">
        Select an assessment module below to open its dedicated edit page where you can modify identity parameters, scoring rules, time limits, condition status, and questions.
      </p>

      <div style="margin-bottom: 18px;">
        <label class="ida-label">Select Exam to Edit *</label>
        <select id="quickEditExamSelect" class="form-control" style="width: 100%; height: 42px; background: #0f1219; border: 1px solid rgba(255,255,255,0.12); color: #ffffff; border-radius: 8px; padding: 0 12px; font-size: 13px; color-scheme: dark !important;">
          <option value="">-- Choose an Exam to Edit --</option>
          @if(isset($allExams))
            @foreach($allExams as $item)
              @php
                $brLabel = ucfirst(str_replace('_', ' ', $item->branch ?? 'General'));
                $accLabel = $item->is_paid_for_external ? 'Cadet Exam' : 'Free Exam';
              @endphp
              <option value="{{ $item->id }}">[{{ $brLabel }} • {{ $accLabel }}] {{ $item->title }} ({{ $item->category }})</option>
            @endforeach
          @endif
        </select>
      </div>

      <div style="display: flex; justify-content: flex-end; gap: 10px;">
        <button type="button" onclick="closeModal('selectExamModal')" class="btn-tactical" style="background: rgba(255,255,255,0.06); color: #cbd5e1; border: 1px solid rgba(255,255,255,0.12); padding: 8px 16px; font-size: 12px; font-weight: 600; border-radius: 8px; cursor: pointer;">
          Cancel
        </button>
        <button type="button" onclick="goToExamEditPage()" class="btn-primary" style="padding: 8px 18px; font-size: 12px; font-weight: 700; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
          <i class="fa-solid fa-arrow-up-right-from-square"></i> Edit Exam from Edit Page
        </button>
      </div>
    </div>
  </div>
</div>

<style>
  /* MATCH STUDENT MANAGEMENT UI COMMAND BOXES */
  .portal-landing-hub {
    padding: 24px 0 40px 0;
    max-width: 1000px;
    margin: 0 auto;
  }
  .portal-landing-hub .section-caption {
    font-size: 11.5px;
    font-weight: 800;
    letter-spacing: 1.2px;
    color: #64748b;
    text-transform: uppercase;
    margin-bottom: 16px;
    display: block;
    text-align: left;
  }
  .exam-2-boxes-grid {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 24px;
  }
  .exam-2-boxes-grid .dark-action-card,
  .exam-2-boxes-grid .simple-box {
    width: calc(50% - 16px);
    min-width: 320px;
    box-sizing: border-box;
  }
  @media (max-width: 720px) {
    .exam-2-boxes-grid .dark-action-card,
    .exam-2-boxes-grid .simple-box {
      width: 100%;
    }
  }

  .dark-action-card,
  .simple-box {
    background: #181c26;
    border: 1px solid rgba(255, 255, 255, 0.05);
    border-radius: 22px;
    padding: 38px 28px 32px;
    text-align: center;
    text-decoration: none;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
  }
  .dark-action-card:hover,
  .simple-box:hover {
    transform: translateY(-5px);
    border-color: rgba(255, 87, 87, 0.35);
    box-shadow: 0 16px 35px -8px rgba(0, 0, 0, 0.45), 0 0 24px rgba(255, 87, 87, 0.12);
  }

  .dark-action-icon-wrap,
  .box-icon-wrap {
    width: 64px;
    height: 64px;
    border-radius: 20px;
    background: #11141c;
    box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.5), 0 2px 6px rgba(0, 0, 0, 0.25);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 16px;
    color: #ff5757;
    font-size: 24px;
    transition: all 0.3s ease;
  }
  .dark-action-card:hover .dark-action-icon-wrap,
  .simple-box:hover .box-icon-wrap {
    transform: scale(1.08);
    box-shadow: 0 0 20px rgba(255, 87, 87, 0.4);
  }

  .dark-action-card h3,
  .box-title {
    font-size: 20px;
    font-weight: 800;
    color: #ffffff;
    margin: 0 0 6px 0;
    letter-spacing: -0.2px;
    font-family: 'Poppins', sans-serif;
  }

  /* SUBNAV PILLS */
  .subnav-pill {
    padding: 6px 14px;
    font-size: 11.5px;
    font-weight: 600;
    color: #8c96a8;
    text-decoration: none;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.15s ease;
  }
  .subnav-pill:hover {
    color: #ffffff;
    background: rgba(255,255,255,0.06);
  }
  .subnav-pill.active {
    background: #ff5757 !important;
    color: #ffffff !important;
    font-weight: 700 !important;
  }

  /* MODAL */
  .ida-modal-overlay {
    position: fixed; inset: 0; background: rgba(0,0,0,0.75); backdrop-filter: blur(4px);
    z-index: 9999; overflow-y: auto; padding: 30px 16px; display: flex; align-items: center; justify-content: center;
  }
  .ida-modal-box {
    width: 100%; margin: auto; background: #131722; border: 1px solid rgba(255,255,255,0.1);
    border-radius: 14px; box-shadow: 0 20px 50px rgba(0,0,0,0.5);
    animation: modalIn 0.2s ease;
  }
  .ida-modal-header {
    display: flex; justify-content: space-between; align-items: center;
    padding: 16px 18px; border-bottom: 1px solid rgba(255,255,255,0.06);
  }
  .ida-modal-title {
    font-size: 14px; font-weight: 800; color: #fff; margin: 0;
    display: flex; align-items: center; gap: 8px;
  }
  .ida-modal-close {
    background: none; border: none; color: #475569; font-size: 18px; cursor: pointer;
    width: 28px; height: 28px; display: flex; align-items: center; justify-content: center;
    border-radius: 6px; transition: all 0.15s;
  }
  .ida-modal-close:hover { background: rgba(255,255,255,0.06); color: #94a3b8; }
  .ida-label {
    display: block; font-size: 10.5px; font-weight: 700; color: #64748b;
    text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;
  }
  @keyframes modalIn {
    from { opacity: 0; transform: translateY(-8px); }
    to { opacity: 1; transform: translateY(0); }
  }
  @keyframes slideDown {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
  }
  @keyframes toastSlideIn {
    from { opacity: 0; transform: translateY(-16px) scale(0.96); }
    to { opacity: 1; transform: translateY(0) scale(1); }
  }

  /* CUSTOM TACTICAL TRACK SELECT DROPDOWN */
  .ida-track-select {
    color-scheme: dark !important;
    height: 40px !important;
    line-height: normal !important;
    padding: 0 34px 0 14px !important;
    font-size: 12.5px !important;
    font-weight: 700 !important;
    font-family: inherit !important;
    background-color: #181d29 !important;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23cbd5e1' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E") !important;
    background-repeat: no-repeat !important;
    background-position: right 12px center !important;
    border: 1px solid rgba(255, 255, 255, 0.14) !important;
    border-radius: 9px !important;
    color: #ffffff !important;
    min-width: 170px !important;
    max-width: 280px !important;
    width: auto !important;
    -webkit-appearance: none !important;
    -moz-appearance: none !important;
    appearance: none !important;
    cursor: pointer !important;
    box-sizing: border-box !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25) !important;
    transition: all 0.2s ease !important;
  }
  .ida-track-select:hover {
    border-color: rgba(255, 255, 255, 0.3) !important;
    background-color: #212737 !important;
  }
  .ida-track-select:focus {
    border-color: #ff5757 !important;
    box-shadow: 0 0 0 3px rgba(255, 87, 87, 0.2) !important;
    outline: none !important;
  }
  .ida-track-select option {
    background-color: #181d29 !important;
    color: #ffffff !important;
    font-size: 13px !important;
    font-weight: 600 !important;
  }
</style>

<script>
  function onExamBranchChange(branch) {
    const form = document.getElementById('examFilterForm');
    if (!form) return;
    const cadreInput = form.querySelector('[name="cadre"]');
    if (cadreInput) cadreInput.value = '';
    form.querySelectorAll('[name="track"]').forEach(el => el.disabled = true);
    form.submit();
  }

  function onExamCadreChange(cadre) {
    const form = document.getElementById('examFilterForm');
    if (!form) return;
    const officerSelect = document.getElementById('examOfficerTrackSelect');
    const officerWrapper = document.getElementById('examOfficerStageWrapper');
    const soldierSelect = document.getElementById('examSoldierTrackSelect');
    const soldierWrapper = document.getElementById('examSoldierStageWrapper');

    if (cadre === 'soldier') {
      if (officerWrapper) officerWrapper.style.display = 'none';
      if (officerSelect) officerSelect.disabled = true;
      if (soldierWrapper) soldierWrapper.style.display = 'inline-block';
      if (soldierSelect) {
        soldierSelect.disabled = false;
        soldierSelect.value = 'soldier';
      }
      form.submit();
    } else if (cadre === 'officer') {
      if (soldierWrapper) soldierWrapper.style.display = 'none';
      if (soldierSelect) soldierSelect.disabled = true;
      if (officerWrapper) officerWrapper.style.display = 'inline-block';
      if (officerSelect) {
        officerSelect.disabled = false;
        officerSelect.value = '';
      }
      form.submit();
    } else {
      if (officerWrapper) officerWrapper.style.display = 'none';
      if (officerSelect) officerSelect.disabled = true;
      if (soldierWrapper) soldierWrapper.style.display = 'none';
      if (soldierSelect) soldierSelect.disabled = true;
      form.submit();
    }
  }

  let toastTimer = null;
  function showTacticalToast(message, type = 'success', title = 'System Synchronized') {
    const toast = document.getElementById('tacticalToast');
    const msgEl = document.getElementById('tacticalToastMsg');
    const titleEl = document.getElementById('tacticalToastTitle');
    const iconEl = document.getElementById('tacticalToastIcon');
    if (!toast || !msgEl) return;

    if (toastTimer) clearTimeout(toastTimer);

    msgEl.textContent = message;
    if (titleEl) titleEl.textContent = title;

    if (type === 'success') {
      toast.style.borderColor = 'rgba(16,185,129,0.4)';
      toast.style.boxShadow = '0 20px 40px rgba(0,0,0,0.5), 0 0 20px rgba(16,185,129,0.25)';
      if (iconEl) {
        iconEl.style.background = 'rgba(16,185,129,0.15)';
        iconEl.style.color = '#34d399';
        iconEl.innerHTML = '<i class="fa-solid fa-circle-check"></i>';
      }
    } else {
      toast.style.borderColor = 'rgba(239,68,68,0.4)';
      toast.style.boxShadow = '0 20px 40px rgba(0,0,0,0.5), 0 0 20px rgba(239,68,68,0.25)';
      if (iconEl) {
        iconEl.style.background = 'rgba(239,68,68,0.15)';
        iconEl.style.color = '#f87171';
        iconEl.innerHTML = '<i class="fa-solid fa-circle-exclamation"></i>';
      }
    }

    toast.style.display = 'block';
    toastTimer = setTimeout(() => hideTacticalToast(), 4000);
  }

  function hideTacticalToast() {
    const toast = document.getElementById('tacticalToast');
    if (toast) toast.style.display = 'none';
  }

  function togglePublicAjax(examId, url, btn) {
    const originalHtml = btn.innerHTML;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
    btn.disabled = true;

    fetch(url, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      }
    })
    .then(res => {
      if (!res.ok) throw new Error('HTTP ' + res.status);
      return res.json();
    })
    .then(data => {
      btn.disabled = false;
      if (data.success) {
        const isPublic = data.is_public;
        if (isPublic) {
          btn.style.background = 'rgba(56, 189, 248, 0.1)';
          btn.style.color = '#38bdf8';
          btn.style.border = '1px solid rgba(56, 189, 248, 0.25)';
          btn.title = 'Visible on Frontend (Click to hide)';
          btn.innerHTML = '<i class="fa-solid fa-eye"></i>';
        } else {
          btn.style.background = 'rgba(239, 68, 68, 0.1)';
          btn.style.color = '#f87171';
          btn.style.border = '1px solid rgba(239, 68, 68, 0.25)';
          btn.title = 'Hidden from Frontend (Click to publish)';
          btn.innerHTML = '<i class="fa-solid fa-eye-slash"></i>';
        }
        showTacticalToast(data.message || 'Visibility updated successfully.', 'success', 'Visibility Updated');
      } else {
        btn.innerHTML = originalHtml;
        showTacticalToast(data.message || 'Failed to toggle visibility.', 'error', 'Operation Failed');
      }
    })
    .catch(err => {
      btn.disabled = false;
      btn.innerHTML = originalHtml;
      showTacticalToast('An error occurred while updating visibility.', 'error', 'Network Error');
    });
  }

  function deleteExamAjax(examId, examTitle, deleteUrl, btn) {
    if (!confirm(`Are you sure you want to permanently delete the exam "${examTitle}"? This cannot be undone.`)) {
      return;
    }

    const card = document.getElementById('exam-card-' + examId);
    const originalHtml = btn.innerHTML;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
    btn.disabled = true;

    if (card) {
      card.style.opacity = '0.35';
      card.style.pointerEvents = 'none';
    }

    fetch(deleteUrl, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ _method: 'DELETE' })
    })
    .then(res => {
      if (!res.ok) throw new Error('HTTP ' + res.status);
      return res.json();
    })
    .then(data => {
      if (data.success) {
        if (card) {
          card.style.transition = 'all 0.35s cubic-bezier(0.16, 1, 0.3, 1)';
          card.style.transform = 'scale(0.9)';
          card.style.opacity = '0';
          setTimeout(() => card.remove(), 350);
        }
        showTacticalToast(data.message || 'Exam deleted successfully.', 'success', 'Exam Deleted');
      } else {
        btn.innerHTML = originalHtml;
        btn.disabled = false;
        if (card) {
          card.style.opacity = '1';
          card.style.pointerEvents = 'auto';
        }
        showTacticalToast(data.message || 'Failed to delete exam.', 'error', 'Delete Failed');
      }
    })
    .catch(err => {
      btn.innerHTML = originalHtml;
      btn.disabled = false;
      if (card) {
        card.style.opacity = '1';
        card.style.pointerEvents = 'auto';
      }
      showTacticalToast('An error occurred while deleting the exam.', 'error', 'Error');
    });
  }

  function openModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
      modal.style.display = 'flex';
      const select = modal.querySelector('select');
      if (select) setTimeout(function() { select.focus(); }, 100);
    }
  }

  function closeModal(id) {
    const modal = document.getElementById(id);
    if (modal) modal.style.display = 'none';
  }

  function goToExamEditPage() {
    const select = document.getElementById('quickEditExamSelect');
    const examId = select ? select.value : '';
    if (!examId) {
      alert('Please select an assessment module to edit.');
      return;
    }
    window.location.href = "{{ route('admin.exam_management.index') }}/" + examId + "/edit";
  }

  document.querySelectorAll('.ida-modal-overlay').forEach(function(overlay) {
    overlay.addEventListener('click', function(e) {
      if (e.target === overlay) overlay.style.display = 'none';
    });
  });

  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      document.querySelectorAll('.ida-modal-overlay').forEach(function(m) {
        m.style.display = 'none';
      });
    }
  });

  document.addEventListener('DOMContentLoaded', function() {
    if (typeof window.initTacticalSelect === 'function') {
      document.querySelectorAll('.ida-track-select').forEach(function(sel) {
        window.initTacticalSelect(sel);
      });
    }
  });
</script>
@endsection
