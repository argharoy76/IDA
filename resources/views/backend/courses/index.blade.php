@extends('layouts.portal')

@section('title', 'Course Management')
@section('page_title', 'Course Management')
@section('page_subtitle', 'Preparatory programs, officer tracks, durations, and tuition fees')

@section('topbar_actions')
  <button type="button" onclick="openAddCourseModal()" class="btn-primary" style="padding: 8px 18px; font-size: 13px; font-weight: 700; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; gap: 7px;">
    <i class="fa-solid fa-plus"></i> Add New Course
  </button>
@endsection

@section('content')
<div style="width: 100%; margin: 0 auto; position: relative;">

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

  {{-- TOP CONTROLS & WING SWITCHER --}}
  <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 20px;">
    {{-- Standardized Wing Lineup Switcher Tabs: Navy -> Police -> Army -> Air Force --}}
    <div style="display: flex; gap: 6px; flex-wrap: wrap; background: rgba(0,0,0,0.3); padding: 5px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.06);">
      <a href="{{ route('admin.courses.index') }}" class="subnav-pill {{ empty($selectedBranch) ? 'active' : '' }}">
        <i class="fa-solid fa-book-bookmark"></i> All Courses ({{ $stats['total'] }})
      </a>
      <a href="{{ route('admin.courses.index', ['branch' => 'navy', 'search' => $search]) }}" class="subnav-pill {{ $selectedBranch === 'navy' ? 'active' : '' }}">
        <i class="fa-solid fa-anchor"></i> Navy ({{ $stats['navy'] }})
      </a>
      <a href="{{ route('admin.courses.index', ['branch' => 'police', 'search' => $search]) }}" class="subnav-pill {{ $selectedBranch === 'police' ? 'active' : '' }}">
        <i class="fa-solid fa-shield-halved"></i> Police ({{ $stats['police'] }})
      </a>
      <a href="{{ route('admin.courses.index', ['branch' => 'army', 'search' => $search]) }}" class="subnav-pill {{ $selectedBranch === 'army' ? 'active' : '' }}">
        <i class="fa-solid fa-person-military-rifle"></i> Army ({{ $stats['army'] }})
      </a>
      <a href="{{ route('admin.courses.index', ['branch' => 'air_force', 'search' => $search]) }}" class="subnav-pill {{ $selectedBranch === 'air_force' ? 'active' : '' }}">
        <i class="fa-solid fa-jet-fighter"></i> Air Force ({{ $stats['air_force'] }})
      </a>
    </div>

    {{-- Quick Create Action Button --}}
    <button type="button" onclick="openAddCourseModal()" class="btn-primary" style="padding: 8px 18px; font-size: 13px; font-weight: 700; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; gap: 7px;">
      <i class="fa-solid fa-plus"></i> Add New Course
    </button>
  </div>

  {{-- FILTER & TRACK CONTROL PANEL --}}
  <div class="content-panel classical-card" style="background: #181c26; border: 1px solid rgba(255, 255, 255, 0.06); border-radius: 14px; padding: 18px 20px; margin-bottom: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px;">
      
      {{-- Left Side: Clean Search Form --}}
      <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
        <form method="GET" action="{{ route('admin.courses.index') }}" style="display: flex; gap: 6px;">
          @if($selectedBranch)
            <input type="hidden" name="branch" value="{{ $selectedBranch }}">
          @endif
          @if($selectedTrack)
            <input type="hidden" name="track" value="{{ $selectedTrack }}">
          @endif
          <div style="position: relative;">
            <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); font-size: 13px; color: #64748b; pointer-events: none;"></i>
            <input type="text" name="search" value="{{ $search }}" placeholder="Search course by title or code..." style="background: #11141d; border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; padding: 8px 14px 8px 34px; font-size: 12.5px; color: #ffffff; width: 240px; outline: none;">
          </div>
          <button type="submit" class="btn-secondary" style="padding: 8px 14px; font-size: 12px; font-weight: 700; border-radius: 8px;">
            Search
          </button>
          @if(!empty($search))
            <a href="{{ route('admin.courses.index', array_filter(['branch' => $selectedBranch, 'track' => $selectedTrack])) }}" class="btn-secondary" style="padding: 8px 12px; font-size: 12px; font-weight: 700; border-radius: 8px; color: #ef4444;" title="Clear Search">
              ✕ Clear
            </a>
          @endif
        </form>
      </div>

      {{-- Total Active Programs Counter --}}
      <div>
        <span style="font-size: 12px; font-weight: 700; color: #94a3b8;">
          Showing <strong style="color: #34d399;">{{ $courses->total() }}</strong> Courses
        </span>
      </div>
    </div>

    {{-- Dynamic Track Filter Strip (Enforces Officer 2-Exam Lineup vs Police Dedicated Lineup) --}}
    @php
      $isMilitaryBranch = in_array($selectedBranch, ['navy', 'army', 'air_force']);
      $isPoliceBranch = ($selectedBranch === 'police');
    @endphp
    <div style="margin-top: 14px; padding-top: 12px; border-top: 1px solid rgba(255,255,255,0.06); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
      <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
        <span style="font-size: 11px; font-weight: 800; text-transform: uppercase; color: #64748b; margin-right: 4px;">
          <i class="fa-solid fa-crosshairs" style="color: #ff5757;"></i> Track Stage:
        </span>

        <a href="{{ route('admin.courses.index', array_filter(['branch' => $selectedBranch, 'search' => $search])) }}" 
           class="btn-tactical" 
           style="height: 28px; padding: 0 10px; font-size: 11px; font-weight: 700; border-radius: 6px; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; {{ empty($selectedTrack) ? 'background: #ff5757; color: #fff; border: 1px solid #ff5757;' : 'background: rgba(255,255,255,0.05); color: #94a3b8; border: 1px solid rgba(255,255,255,0.1);' }}">
          All {{ $selectedBranch ? ucfirst(str_replace('_', ' ', $selectedBranch)) : '' }} Courses
        </a>

        @if($isMilitaryBranch)
          {{-- Military Wings: Strictly Officer 2-Exam Architecture (Preliminary & ISSB) --}}
          <a href="{{ route('admin.courses.index', array_filter(['branch' => $selectedBranch, 'track' => 'preliminary', 'search' => $search])) }}" 
             class="btn-tactical" 
             style="height: 28px; padding: 0 10px; font-size: 11px; font-weight: 700; border-radius: 6px; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; {{ $selectedTrack === 'preliminary' ? 'background: #3b82f6; color: #fff; border: 1px solid #3b82f6;' : 'background: rgba(59,130,246,0.1); color: #60a5fa; border: 1px solid rgba(59,130,246,0.25);' }}">
            <i class="fa-solid fa-shield"></i> Preliminary (Officer 1) ({{ $stats['prelim'] }})
          </a>
          <a href="{{ route('admin.courses.index', array_filter(['branch' => $selectedBranch, 'track' => 'issb', 'search' => $search])) }}" 
             class="btn-tactical" 
             style="height: 28px; padding: 0 10px; font-size: 11px; font-weight: 700; border-radius: 6px; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; {{ $selectedTrack === 'issb' ? 'background: #eab308; color: #000; border: 1px solid #eab308;' : 'background: rgba(234,179,8,0.1); color: #facc15; border: 1px solid rgba(234,179,8,0.25);' }}">
            <i class="fa-solid fa-star"></i> ISSB Masterclass (Officer 2) ({{ $stats['issb'] }})
          </a>
          @if(($stats['soldier'] ?? 0) > 0)
            <a href="{{ route('admin.courses.index', array_filter(['branch' => $selectedBranch, 'track' => 'soldier', 'search' => $search])) }}" 
               class="btn-tactical" 
               style="height: 28px; padding: 0 10px; font-size: 11px; font-weight: 700; border-radius: 6px; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; {{ $selectedTrack === 'soldier' ? 'background: #f97316; color: #fff; border: 1px solid #f97316;' : 'background: rgba(249,115,22,0.1); color: #fb923c; border: 1px solid rgba(249,115,22,0.25);' }}">
              <i class="fa-solid fa-person-military-rifle"></i> {{ $selectedBranch === 'navy' ? 'Sailor' : ($selectedBranch === 'air_force' ? 'Airman' : 'Soldier') }} ({{ $stats['soldier'] }})
            </a>
          @endif

        @elseif($isPoliceBranch)
          {{-- Police Wing: Dedicated Police Lineup (NO ISSB) --}}
          <a href="{{ route('admin.courses.index', array_filter(['branch' => $selectedBranch, 'track' => 'constable', 'search' => $search])) }}" 
             class="btn-tactical" 
             style="height: 28px; padding: 0 10px; font-size: 11px; font-weight: 700; border-radius: 6px; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; {{ $selectedTrack === 'constable' ? 'background: #a855f7; color: #fff; border: 1px solid #a855f7;' : 'background: rgba(168,85,247,0.1); color: #c084fc; border: 1px solid rgba(168,85,247,0.25);' }}">
            <i class="fa-solid fa-shield-halved"></i> Constable Courses ({{ $stats['constable'] }})
          </a>
          <a href="{{ route('admin.courses.index', array_filter(['branch' => $selectedBranch, 'track' => 'si', 'search' => $search])) }}" 
             class="btn-tactical" 
             style="height: 28px; padding: 0 10px; font-size: 11px; font-weight: 700; border-radius: 6px; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; {{ $selectedTrack === 'si' ? 'background: #a855f7; color: #fff; border: 1px solid #a855f7;' : 'background: rgba(168,85,247,0.1); color: #c084fc; border: 1px solid rgba(168,85,247,0.25);' }}">
            <i class="fa-solid fa-shield"></i> Sub-Inspector SI ({{ $stats['si'] }})
          </a>
          <a href="{{ route('admin.courses.index', array_filter(['branch' => $selectedBranch, 'track' => 'asi', 'search' => $search])) }}" 
             class="btn-tactical" 
             style="height: 28px; padding: 0 10px; font-size: 11px; font-weight: 700; border-radius: 6px; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; {{ $selectedTrack === 'asi' ? 'background: #a855f7; color: #fff; border: 1px solid #a855f7;' : 'background: rgba(168,85,247,0.1); color: #c084fc; border: 1px solid rgba(168,85,247,0.25);' }}">
            <i class="fa-solid fa-id-badge"></i> Assistant SI ASI ({{ $stats['asi'] }})
          </a>

        @else
          {{-- All Branches: Comprehensive Lineup --}}
          <a href="{{ route('admin.courses.index', array_filter(['branch' => $selectedBranch, 'track' => 'preliminary', 'search' => $search])) }}" 
             class="btn-tactical" 
             style="height: 28px; padding: 0 10px; font-size: 11px; font-weight: 700; border-radius: 6px; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; {{ $selectedTrack === 'preliminary' ? 'background: #3b82f6; color: #fff; border: 1px solid #3b82f6;' : 'background: rgba(59,130,246,0.1); color: #60a5fa; border: 1px solid rgba(59,130,246,0.25);' }}">
            <i class="fa-solid fa-shield"></i> Preliminary (Officer 1) ({{ $stats['prelim'] }})
          </a>
          <a href="{{ route('admin.courses.index', array_filter(['branch' => $selectedBranch, 'track' => 'issb', 'search' => $search])) }}" 
             class="btn-tactical" 
             style="height: 28px; padding: 0 10px; font-size: 11px; font-weight: 700; border-radius: 6px; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; {{ $selectedTrack === 'issb' ? 'background: #eab308; color: #000; border: 1px solid #eab308;' : 'background: rgba(234,179,8,0.1); color: #facc15; border: 1px solid rgba(234,179,8,0.25);' }}">
            <i class="fa-solid fa-star"></i> ISSB Masterclass (Officer 2) ({{ $stats['issb'] }})
          </a>
          <a href="{{ route('admin.courses.index', array_filter(['branch' => $selectedBranch, 'track' => 'constable', 'search' => $search])) }}" 
             class="btn-tactical" 
             style="height: 28px; padding: 0 10px; font-size: 11px; font-weight: 700; border-radius: 6px; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; {{ $selectedTrack === 'constable' ? 'background: #a855f7; color: #fff; border: 1px solid #a855f7;' : 'background: rgba(168,85,247,0.1); color: #c084fc; border: 1px solid rgba(168,85,247,0.25);' }}">
            <i class="fa-solid fa-shield-halved"></i> Constable ({{ $stats['constable'] }})
          </a>
          <a href="{{ route('admin.courses.index', array_filter(['branch' => $selectedBranch, 'track' => 'si', 'search' => $search])) }}" 
             class="btn-tactical" 
             style="height: 28px; padding: 0 10px; font-size: 11px; font-weight: 700; border-radius: 6px; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; {{ $selectedTrack === 'si' ? 'background: #a855f7; color: #fff; border: 1px solid #a855f7;' : 'background: rgba(168,85,247,0.1); color: #c084fc; border: 1px solid rgba(168,85,247,0.25);' }}">
            <i class="fa-solid fa-shield"></i> SI ({{ $stats['si'] }})
          </a>
          <a href="{{ route('admin.courses.index', array_filter(['branch' => $selectedBranch, 'track' => 'asi', 'search' => $search])) }}" 
             class="btn-tactical" 
             style="height: 28px; padding: 0 10px; font-size: 11px; font-weight: 700; border-radius: 6px; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; {{ $selectedTrack === 'asi' ? 'background: #a855f7; color: #fff; border: 1px solid #a855f7;' : 'background: rgba(168,85,247,0.1); color: #c084fc; border: 1px solid rgba(168,85,247,0.25);' }}">
            <i class="fa-solid fa-id-badge"></i> ASI ({{ $stats['asi'] }})
          </a>
        @endif
      </div>

      @if(!empty($selectedTrack))
        <a href="{{ route('admin.courses.index', array_filter(['branch' => $selectedBranch, 'search' => $search])) }}" style="font-size: 11px; color: #f87171; text-decoration: underline;">
          Clear Track Filter
        </a>
      @endif
    </div>
  </div>

  {{-- COURSES DATA TABLE --}}
  <div class="content-panel" style="background: #181c26; border: 1px solid rgba(255, 255, 255, 0.06); border-radius: 14px; padding: 20px; overflow: hidden; box-shadow: 0 4px 16px rgba(0,0,0,0.25);">
    <div class="table-responsive">
      <table class="tactical-table" style="width: 100%; border-collapse: separate; border-spacing: 0 8px;">
        <thead>
          <tr style="color: #64748b; font-size: 11px; text-transform: uppercase; letter-spacing: 0.8px;">
            <th style="padding: 10px 14px;">Course Title &amp; Code</th>
            <th style="padding: 10px 14px;">Branch Wing</th>
            <th style="padding: 10px 14px;">Officer / Program Track</th>
            <th style="padding: 10px 14px;">Duration</th>
            <th style="padding: 10px 14px;">Tuition Fee</th>
            <th style="padding: 10px 14px; text-align: center;">Batches</th>
            <th style="padding: 10px 14px; text-align: center;">Cadets</th>
            <th style="padding: 10px 14px; text-align: center;">Status</th>
            <th style="padding: 10px 14px; text-align: right;">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($courses as $c)
            <tr id="course-row-{{ $c->id }}" style="background: #11141d; border-radius: 10px; transition: background 0.15s ease;">
              <td style="padding: 14px;">
                <div style="display: flex; align-items: flex-start; gap: 8px;">
                  <div>
                    <strong style="font-size: 13.5px; color: #ffffff; display: block; font-family: 'Poppins', sans-serif;">{{ $c->title }}</strong>
                    <div style="display: flex; align-items: center; gap: 6px; margin-top: 3px; flex-wrap: wrap;">
                      @if($c->course_code)
                        <span style="background: rgba(255,255,255,0.06); color: #cbd5e1; border: 1px solid rgba(255,255,255,0.12); padding: 1px 6px; border-radius: 4px; font-size: 10px; font-weight: 700; letter-spacing: 0.3px;">
                          {{ $c->course_code }}
                        </span>
                      @endif
                      <small style="color: #64748b; font-size: 11px;">{{ Str::limit($c->description, 55) }}</small>
                    </div>
                  </div>
                </div>
              </td>

              {{-- Branch Badge --}}
              <td style="padding: 14px; vertical-align: middle;">
                <span class="badge" style="background: rgba(255,255,255,0.06); color: #ffffff; border: 1px solid rgba(255,255,255,0.12); font-size: 11px; font-weight: 700; padding: 4px 9px; display: inline-flex; align-items: center; gap: 5px;">
                  <i class="fa-solid {{ $c->branchIcon() }}" style="color: {{ $c->branchColor() }};"></i> {{ $c->branchLabel() }}
                </span>
              </td>

              {{-- Program Track Badge --}}
              <td style="padding: 14px; vertical-align: middle;">
                <span style="{{ $c->trackBadgeStyle() }} font-size: 10.5px; font-weight: 700; padding: 3px 8px; border-radius: 6px; display: inline-flex; align-items: center; gap: 5px;">
                  @if($c->isIssb())
                    <i class="fa-solid fa-star"></i>
                  @elseif($c->isPrelim())
                    <i class="fa-solid fa-shield"></i>
                  @elseif($c->isPolice())
                    <i class="fa-solid fa-shield-halved"></i>
                  @else
                    <i class="fa-solid fa-award"></i>
                  @endif
                  {{ $c->trackLabel() }}
                </span>
              </td>

              {{-- Duration --}}
              <td style="padding: 14px; vertical-align: middle; color: #cbd5e1; font-size: 12px; font-weight: 600;">
                <i class="fa-regular fa-clock" style="color: #64748b; margin-right: 3px;"></i> {{ $c->duration }}
              </td>

              {{-- Tuition Fee --}}
              <td style="padding: 14px; vertical-align: middle;">
                <strong style="color: #34d399; font-size: 13.5px; font-weight: 800;">৳{{ number_format($c->fee, 0) }}</strong>
              </td>

              {{-- Batches --}}
              <td style="padding: 14px; vertical-align: middle; text-align: center;">
                <span class="badge badge-blue" style="font-size: 10.5px;">{{ $c->batches_count }} Batches</span>
              </td>

              {{-- Cadets --}}
              <td style="padding: 14px; vertical-align: middle; text-align: center;">
                <span class="badge badge-emerald" style="font-size: 10.5px;">{{ $c->students_count }} Cadets</span>
              </td>

              {{-- Admission Status --}}
              <td style="padding: 14px; vertical-align: middle; text-align: center;">
                @if($c->admission_status === 'open')
                  <span class="badge badge-emerald" style="font-size: 10px; font-weight: 700;">Open</span>
                @elseif($c->admission_status === 'upcoming')
                  <span class="badge badge-amber" style="font-size: 10px; font-weight: 700;">Upcoming</span>
                @else
                  <span class="badge badge-navy" style="font-size: 10px; font-weight: 700;">Closed</span>
                @endif
              </td>

              {{-- Actions --}}
              <td style="padding: 14px; vertical-align: middle; text-align: right;">
                <div style="display: inline-flex; align-items: center; gap: 6px;">
                  {{-- Preview --}}
                  <a href="{{ route('courses.detail', $c->slug) }}" target="_blank" class="btn-tactical" style="background: rgba(255,255,255,0.06); color: #cbd5e1; border: 1px solid rgba(255,255,255,0.12); padding: 5px 9px; font-size: 11px; border-radius: 6px; text-decoration: none;" title="Preview Course on Website">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                  </a>

                  {{-- Edit Modal Button --}}
                  <button type="button" 
                    onclick='openEditCourseModal(@json($c))' 
                    class="btn-tactical" 
                    style="background: rgba(255, 87, 87, 0.12); color: #ff5757; border: 1px solid rgba(255, 87, 87, 0.3); padding: 5px 10px; font-size: 11px; font-weight: 700; border-radius: 6px; cursor: pointer;" 
                    title="Edit Course Parameters">
                    <i class="fa-solid fa-pen-to-square"></i> Edit
                  </button>

                  {{-- Delete Button --}}
                  <form method="POST" action="{{ route('admin.courses.destroy', $c->id) }}" style="display: inline;" onsubmit="return confirm('Permanently delete course &quot;{{ addslashes($c->title) }}&quot;?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-tactical" style="background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2); padding: 5px 8px; font-size: 11px; border-radius: 6px; cursor: pointer;" title="Delete Course">
                      <i class="fa-solid fa-trash-can"></i>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="9" style="text-align: center; padding: 40px 20px; color: #64748b;">
                <i class="fa-solid fa-book-open" style="font-size: 32px; margin-bottom: 12px; display: block; color: #475569;"></i>
                <h4 style="color: #cbd5e1; font-size: 14px; margin-bottom: 6px;">No Courses Found</h4>
                <p style="font-size: 12px; margin-bottom: 14px;">No courses matching the selected branch and track criteria.</p>
                <button type="button" onclick="openAddCourseModal()" class="btn-primary" style="padding: 6px 14px; font-size: 11.5px; border-radius: 6px;">
                  <i class="fa-solid fa-plus"></i> Add New Course
                </button>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($courses->hasPages())
      <div style="margin-top: 18px; padding-top: 14px; border-top: 1px solid rgba(255,255,255,0.06); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
        <span style="font-size: 12px; color: #64748b;">
          Showing {{ $courses->firstItem() }} to {{ $courses->lastItem() }} of {{ $courses->total() }} courses
        </span>
        <div>
          {{ $courses->links() }}
        </div>
      </div>
    @endif
  </div>

</div>

{{-- ================================================================ --}}
{{-- MODAL 1: ADD NEW COURSE                                          --}}
{{-- ================================================================ --}}
<div id="addCourseModal" class="ida-modal-overlay" style="display: none;">
  <div class="ida-modal-box" style="max-width: 680px;">
    <div class="ida-modal-header">
      <h3 class="ida-modal-title">
        <i class="fa-solid fa-plus" style="color: #ff5757;"></i> Add New Preparatory Course
      </h3>
      <button type="button" onclick="closeModal('addCourseModal')" class="ida-modal-close">&times;</button>
    </div>
    <form action="{{ route('admin.courses.store') }}" method="POST" style="padding: 22px;">
      @csrf

      <div style="display: grid; grid-template-columns: 3fr 1fr; gap: 14px; margin-bottom: 16px;">
        <div>
          <label class="ida-label">Course Title *</label>
          <input type="text" name="title" required class="form-tactical" style="width: 100%; padding: 10px 14px; font-size: 13.5px;" placeholder="e.g. Bangladesh Navy Preliminary Officer Cadet Prep">
        </div>
        <div>
          <label class="ida-label">Course Code</label>
          <input type="text" name="course_code" class="form-tactical" style="width: 100%; padding: 10px 14px; font-size: 13.5px;" placeholder="e.g. NV-101">
        </div>
      </div>

      {{-- Standardized Lineup: Branch and Dynamic Program Track --}}
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px;">
        <div>
          <label class="ida-label">Branch Wing *</label>
          <select name="branch" id="addCourseBranchSelect" class="form-tactical" required style="width: 100%; padding: 10px 14px; font-size: 13px;" onchange="updateAddCourseTracks()">
            <option value="navy" {{ $selectedBranch === 'navy' ? 'selected' : '' }}>Navy (BNA)</option>
            <option value="police" {{ $selectedBranch === 'police' ? 'selected' : '' }}>Police Service</option>
            <option value="army" {{ $selectedBranch === 'army' ? 'selected' : '' }}>Army (BMA)</option>
            <option value="air_force" {{ $selectedBranch === 'air_force' ? 'selected' : '' }}>Air Force (BAFA)</option>
          </select>
        </div>

        <div>
          <label class="ida-label">Officer / Program Track *</label>
          <select name="target_track" id="addCourseTrackSelect" class="form-tactical" required style="width: 100%; padding: 10px 14px; font-size: 13px;">
            <!-- Dynamically populated via JS -->
          </select>
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 14px; margin-bottom: 16px;">
        <div>
          <label class="ida-label">Duration *</label>
          <input type="text" name="duration" required class="form-tactical" style="width: 100%; padding: 10px 14px; font-size: 13px;" placeholder="e.g. 3 Months">
        </div>
        <div>
          <label class="ida-label">Tuition Fee (BDT) *</label>
          <input type="number" name="fee" required class="form-tactical" style="width: 100%; padding: 10px 14px; font-size: 13px;" placeholder="15000">
        </div>
        <div>
          <label class="ida-label">Admission Status *</label>
          <select name="admission_status" class="form-tactical" style="width: 100%; padding: 10px 14px; font-size: 13px;">
            <option value="open">Open</option>
            <option value="upcoming">Upcoming</option>
            <option value="closed">Closed</option>
          </select>
        </div>
      </div>

      <div style="margin-bottom: 16px;">
        <label class="ida-label">Eligibility Criteria</label>
        <input type="text" name="eligibility" class="form-tactical" style="width: 100%; padding: 10px 14px; font-size: 13px;" placeholder="e.g. HSC Science with Physics & Math; Age 17-21">
      </div>

      <div style="margin-bottom: 16px;">
        <label class="ida-label">Schedule Information</label>
        <input type="text" name="schedule_info" class="form-tactical" style="width: 100%; padding: 10px 14px; font-size: 13px;" placeholder="e.g. Morning & Evening Batches (Sun, Tue, Thu)">
      </div>

      <div style="margin-bottom: 16px;">
        <label class="ida-label">Description &amp; Syllabus Overview</label>
        <textarea name="description" rows="3" class="form-tactical" style="width: 100%; padding: 10px 14px; font-size: 13px;" placeholder="Course curriculum summary and preliminary examination guidelines..."></textarea>
      </div>

      <div style="margin-bottom: 20px;">
        <label style="display: flex; align-items: center; gap: 8px; font-size: 12.5px; font-weight: 600; color: #ffffff; cursor: pointer;">
          <input type="checkbox" name="is_featured" value="1" checked>
          <span>Feature on Academy Homepage</span>
        </label>
      </div>

      <div style="display: flex; justify-content: flex-end; gap: 10px;">
        <button type="button" onclick="closeModal('addCourseModal')" class="btn-tactical" style="background: rgba(255,255,255,0.06); color: #cbd5e1; border: 1px solid rgba(255,255,255,0.12); padding: 9px 18px; font-size: 12.5px; font-weight: 600; border-radius: 8px; cursor: pointer;">
          Cancel
        </button>
        <button type="submit" class="btn-primary" style="background: #ff5757; color: #ffffff; border: none; padding: 9px 24px; font-size: 13px; font-weight: 700; border-radius: 8px; cursor: pointer;">
          Create Course
        </button>
      </div>
    </form>
  </div>
</div>

{{-- ================================================================ --}}
{{-- MODAL 2: EDIT EXISTING COURSE                                    --}}
{{-- ================================================================ --}}
<div id="editCourseModal" class="ida-modal-overlay" style="display: none;">
  <div class="ida-modal-box" style="max-width: 680px;">
    <div class="ida-modal-header">
      <h3 class="ida-modal-title">
        <i class="fa-solid fa-pen-to-square" style="color: #ff5757;"></i> Edit Course: <span id="editCourseTitleDisplay"></span>
      </h3>
      <button type="button" onclick="closeModal('editCourseModal')" class="ida-modal-close">&times;</button>
    </div>
    <form id="editCourseForm" method="POST" style="padding: 22px;">
      @csrf
      @method('PUT')

      <div style="display: grid; grid-template-columns: 3fr 1fr; gap: 14px; margin-bottom: 16px;">
        <div>
          <label class="ida-label">Course Title *</label>
          <input type="text" name="title" id="editCourseTitle" required class="form-tactical" style="width: 100%; padding: 10px 14px; font-size: 13.5px;">
        </div>
        <div>
          <label class="ida-label">Course Code</label>
          <input type="text" name="course_code" id="editCourseCode" class="form-tactical" style="width: 100%; padding: 10px 14px; font-size: 13.5px;">
        </div>
      </div>

      {{-- Standardized Lineup: Branch and Dynamic Program Track --}}
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px;">
        <div>
          <label class="ida-label">Branch Wing *</label>
          <select name="branch" id="editCourseBranchSelect" class="form-tactical" required style="width: 100%; padding: 10px 14px; font-size: 13px;" onchange="updateEditCourseTracks()">
            <option value="navy">Navy (BNA)</option>
            <option value="police">Police Service</option>
            <option value="army">Army (BMA)</option>
            <option value="air_force">Air Force (BAFA)</option>
          </select>
        </div>

        <div>
          <label class="ida-label">Officer / Program Track *</label>
          <select name="target_track" id="editCourseTrackSelect" class="form-tactical" required style="width: 100%; padding: 10px 14px; font-size: 13px;">
            <!-- Dynamically populated via JS -->
          </select>
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 14px; margin-bottom: 16px;">
        <div>
          <label class="ida-label">Duration *</label>
          <input type="text" name="duration" id="editCourseDuration" required class="form-tactical" style="width: 100%; padding: 10px 14px; font-size: 13px;">
        </div>
        <div>
          <label class="ida-label">Tuition Fee (BDT) *</label>
          <input type="number" name="fee" id="editCourseFee" required class="form-tactical" style="width: 100%; padding: 10px 14px; font-size: 13px;">
        </div>
        <div>
          <label class="ida-label">Admission Status *</label>
          <select name="admission_status" id="editCourseAdmissionStatus" class="form-tactical" style="width: 100%; padding: 10px 14px; font-size: 13px;">
            <option value="open">Open</option>
            <option value="upcoming">Upcoming</option>
            <option value="closed">Closed</option>
          </select>
        </div>
      </div>

      <div style="margin-bottom: 16px;">
        <label class="ida-label">Eligibility Criteria</label>
        <input type="text" name="eligibility" id="editCourseEligibility" class="form-tactical" style="width: 100%; padding: 10px 14px; font-size: 13px;">
      </div>

      <div style="margin-bottom: 16px;">
        <label class="ida-label">Schedule Information</label>
        <input type="text" name="schedule_info" id="editCourseSchedule" class="form-tactical" style="width: 100%; padding: 10px 14px; font-size: 13px;">
      </div>

      <div style="margin-bottom: 16px;">
        <label class="ida-label">Description &amp; Syllabus Overview</label>
        <textarea name="description" id="editCourseDescription" rows="3" class="form-tactical" style="width: 100%; padding: 10px 14px; font-size: 13px;"></textarea>
      </div>

      <div style="margin-bottom: 20px;">
        <label style="display: flex; align-items: center; gap: 8px; font-size: 12.5px; font-weight: 600; color: #ffffff; cursor: pointer;">
          <input type="checkbox" name="is_featured" id="editCourseFeatured" value="1">
          <span>Feature on Academy Homepage</span>
        </label>
      </div>

      <div style="display: flex; justify-content: flex-end; gap: 10px;">
        <button type="button" onclick="closeModal('editCourseModal')" class="btn-tactical" style="background: rgba(255,255,255,0.06); color: #cbd5e1; border: 1px solid rgba(255,255,255,0.12); padding: 9px 18px; font-size: 12.5px; font-weight: 600; border-radius: 8px; cursor: pointer;">
          Cancel
        </button>
        <button type="submit" class="btn-primary" style="background: #ff5757; color: #ffffff; border: none; padding: 9px 24px; font-size: 13px; font-weight: 700; border-radius: 8px; cursor: pointer;">
          Save Changes
        </button>
      </div>
    </form>
  </div>
</div>

<style>
  .subnav-pill {
    padding: 7px 15px;
    font-size: 12px;
    font-weight: 600;
    color: #8c96a8;
    text-decoration: none;
    border-radius: 8px;
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
    padding: 16px 22px; border-bottom: 1px solid rgba(255,255,255,0.06);
  }
  .ida-modal-title {
    font-size: 14.5px; font-weight: 800; color: #fff; margin: 0;
    display: flex; align-items: center; gap: 8px; font-family: 'Poppins', sans-serif;
  }
  .ida-modal-close {
    background: none; border: none; color: #475569; font-size: 18px; cursor: pointer;
    width: 28px; height: 28px; display: flex; align-items: center; justify-content: center;
    border-radius: 6px; transition: all 0.15s;
  }
  .ida-modal-close:hover { background: rgba(255,255,255,0.06); color: #94a3b8; }
  .ida-label {
    display: block; font-size: 11px; font-weight: 700; color: #94a3b8;
    text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 5px;
  }
  .form-tactical {
    background: #11141d;
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    color: #ffffff;
    box-sizing: border-box;
    outline: none;
    transition: border-color 0.2s, box-shadow 0.2s;
  }
  .form-tactical:focus {
    border-color: #ff5757;
    box-shadow: 0 0 0 3px rgba(255, 87, 87, 0.18);
  }
  @keyframes modalIn {
    from { opacity: 0; transform: translateY(-8px); }
    to { opacity: 1; transform: translateY(0); }
  }
</style>

<script>
  const courseTrackPresets = {
    navy: [
      { value: 'preliminary', label: 'Preliminary (Officer Exam 1)' },
      { value: 'issb', label: 'ISSB Special Masterclass (Officer Exam 2 - Universal Tri-Services)' },
      { value: 'soldier', label: 'Sailor (Navy Non-Commissioned)' }
    ],
    police: [
      { value: 'constable', label: 'Constable Recruitment Course' },
      { value: 'si', label: 'Sub-Inspector (SI) Comprehensive Track' },
      { value: 'asi', label: 'Assistant Sub-Inspector (ASI) Track' }
    ],
    army: [
      { value: 'preliminary', label: 'Preliminary (Officer Exam 1)' },
      { value: 'issb', label: 'ISSB Special Masterclass (Officer Exam 2 - Universal Tri-Services)' },
      { value: 'soldier', label: 'Soldier / Sainik (Army Non-Commissioned)' }
    ],
    air_force: [
      { value: 'preliminary', label: 'Preliminary (Officer Exam 1)' },
      { value: 'issb', label: 'ISSB Special Masterclass (Officer Exam 2 - Universal Tri-Services)' },
      { value: 'soldier', label: 'Airman (Air Force Non-Commissioned)' }
    ]
  };

  function updateAddCourseTracks(preselectedValue = 'preliminary') {
    const branchEl = document.getElementById('addCourseBranchSelect');
    const trackEl = document.getElementById('addCourseTrackSelect');
    if (!branchEl || !trackEl) return;

    const branch = branchEl.value;
    const tracks = courseTrackPresets[branch] || courseTrackPresets.navy;
    const fallback = (branch === 'police') ? 'si' : 'preliminary';
    const targetVal = preselectedValue || fallback;

    trackEl.innerHTML = tracks.map(t => `
      <option value="${t.value}" ${t.value === targetVal ? 'selected' : ''}>${t.label}</option>
    `).join('');
  }

  function updateEditCourseTracks(preselectedValue = null) {
    const branchEl = document.getElementById('editCourseBranchSelect');
    const trackEl = document.getElementById('editCourseTrackSelect');
    if (!branchEl || !trackEl) return;

    const branch = branchEl.value;
    const tracks = courseTrackPresets[branch] || courseTrackPresets.navy;
    const fallback = (branch === 'police') ? 'si' : 'preliminary';
    const targetVal = preselectedValue || trackEl.value || fallback;

    trackEl.innerHTML = tracks.map(t => `
      <option value="${t.value}" ${t.value === targetVal ? 'selected' : ''}>${t.label}</option>
    `).join('');
  }

  function openAddCourseModal() {
    updateAddCourseTracks();
    openModal('addCourseModal');
  }

  function openEditCourseModal(course) {
    const form = document.getElementById('editCourseForm');
    form.action = "{{ route('admin.courses.index') }}/" + course.id;

    document.getElementById('editCourseTitleDisplay').textContent = course.title;
    document.getElementById('editCourseTitle').value = course.title || '';
    document.getElementById('editCourseCode').value = course.course_code || '';
    document.getElementById('editCourseBranchSelect').value = course.branch || course.branch_key || 'navy';
    document.getElementById('editCourseDuration').value = course.duration || '';
    document.getElementById('editCourseFee').value = course.fee || 0;
    document.getElementById('editCourseAdmissionStatus').value = course.admission_status || 'open';
    document.getElementById('editCourseEligibility').value = course.eligibility || '';
    document.getElementById('editCourseSchedule').value = course.schedule_info || '';
    document.getElementById('editCourseDescription').value = course.description || '';
    document.getElementById('editCourseFeatured').checked = Boolean(course.is_featured);

    updateEditCourseTracks(course.target_track || course.program_track);

    openModal('editCourseModal');
  }

  function openModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
      modal.style.display = 'flex';
      const input = modal.querySelector('input[type="text"]');
      if (input) setTimeout(() => input.focus(), 100);
    }
  }

  function closeModal(id) {
    const modal = document.getElementById(id);
    if (modal) modal.style.display = 'none';
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
    updateAddCourseTracks();
  });
</script>
@endsection
