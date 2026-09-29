@extends('layouts.portal')

@section('title', 'Cadet Management')
@section('page_title', 'Cadet Management')

@section('content')
<div style="width: 100%; margin: 0 auto;">

  @php
    $activeWing = $selectedWing ?? request('wing');
    $isLanding = empty($activeWing);
    $isWingSegment = in_array($activeWing, ['Navy', 'Police', 'Army', 'Air Force']);
    $isAllMode = ($activeWing === 'all');
  @endphp

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
    {{-- LANDING VIEW: ONLY THE FIVE PROFESSIONAL COMMAND BOXES           --}}
    {{-- ================================================================ --}}
    <div class="portal-landing-hub">
      <span class="section-caption">QUICK ACTIONS</span>
      <div class="command-boxes-grid">

        {{-- 1. NAVY --}}
        <a href="{{ route('admin.student_accounts.index', ['wing' => 'Navy']) }}" class="dark-action-card simple-box">
          <div class="dark-action-icon-wrap box-icon-wrap">
            <i class="fa-solid fa-anchor"></i>
          </div>
          <h3 class="box-title">Navy</h3>
          <p class="box-count">{{ $stats['navy'] }} {{ $stats['navy'] == 1 ? 'Cadet' : 'Cadets' }}</p>
        </a>

        {{-- 2. POLICE --}}
        <a href="{{ route('admin.student_accounts.index', ['wing' => 'Police']) }}" class="dark-action-card simple-box">
          <div class="dark-action-icon-wrap box-icon-wrap">
            <i class="fa-solid fa-shield-halved"></i>
          </div>
          <h3 class="box-title">Police</h3>
          <p class="box-count">{{ $stats['police'] }} {{ $stats['police'] == 1 ? 'Cadet' : 'Cadets' }}</p>
        </a>

        {{-- 3. ARMY --}}
        <a href="{{ route('admin.student_accounts.index', ['wing' => 'Army']) }}" class="dark-action-card simple-box">
          <div class="dark-action-icon-wrap box-icon-wrap">
            <i class="fa-solid fa-person-military-rifle"></i>
          </div>
          <h3 class="box-title">Army</h3>
          <p class="box-count">{{ $stats['army'] }} {{ $stats['army'] == 1 ? 'Cadet' : 'Cadets' }}</p>
        </a>

        {{-- 4. AIR FORCE --}}
        <a href="{{ route('admin.student_accounts.index', ['wing' => 'Air Force']) }}" class="dark-action-card simple-box">
          <div class="dark-action-icon-wrap box-icon-wrap">
            <i class="fa-solid fa-jet-fighter"></i>
          </div>
          <h3 class="box-title">Air Force</h3>
          <p class="box-count">{{ $stats['airforce'] }} {{ $stats['airforce'] == 1 ? 'Cadet' : 'Cadets' }}</p>
        </a>

        {{-- 5. ALL CADETS --}}
        <a href="{{ route('admin.student_accounts.index', ['wing' => 'all']) }}" class="dark-action-card simple-box card-all-students box-all-students">
          <div class="dark-action-icon-wrap box-icon-wrap">
            <i class="fa-solid fa-users"></i>
          </div>
          <h3 class="box-title">All Cadets</h3>
          <p class="box-count">{{ $stats['total'] }} {{ $stats['total'] == 1 ? 'Cadet' : 'Cadets' }}</p>
        </a>

      </div>
    </div>

  @else
    {{-- ================================================================ --}}
    {{-- SUBPAGE VIEW: TOP NAV + SEGMENT CONTROL / FILTER SUITE + TABLE   --}}
    {{-- ================================================================ --}}
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 18px;">
      <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
        <a href="{{ route('admin.student_accounts.index') }}" class="btn-tactical" style="background: rgba(255,255,255,0.06); color: #cbd5e1; border: 1px solid rgba(255,255,255,0.12); padding: 8px 14px; font-size: 12px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 7px; border-radius: 8px;">
          <i class="fa-solid fa-arrow-left"></i> Back to Cadet Management
        </a>
        <form method="POST" action="{{ route('admin.system.sync_database_tracks') }}" style="display: inline;" onsubmit="return confirm('Safely synchronize database tracks and missing columns on live server? No existing data will be modified.');">
          @csrf
          <button type="submit" class="btn-tactical" style="background: rgba(16,185,129,0.12); color: #34d399; border: 1px solid rgba(16,185,129,0.3); padding: 8px 14px; font-size: 12px; font-weight: 700; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;" title="Safely add missing schema columns and backfill tracks on Hostinger production without data loss">
            <i class="fa-solid fa-database"></i> Sync DB Tracks
          </button>
        </form>
      </div>

      {{-- Quick Wing Switcher Tabs --}}
      <div style="display: flex; gap: 5px; flex-wrap: wrap; background: rgba(0,0,0,0.3); padding: 4px; border-radius: 9px; border: 1px solid rgba(255,255,255,0.06);">
        <a href="{{ route('admin.student_accounts.index', ['wing' => 'Navy']) }}" class="subnav-pill {{ $activeWing === 'Navy' ? 'active' : '' }}">
          <i class="fa-solid fa-anchor"></i> Navy ({{ $stats['navy'] }})
        </a>
        <a href="{{ route('admin.student_accounts.index', ['wing' => 'Police']) }}" class="subnav-pill {{ $activeWing === 'Police' ? 'active' : '' }}">
          <i class="fa-solid fa-shield-halved"></i> Police ({{ $stats['police'] }})
        </a>
        <a href="{{ route('admin.student_accounts.index', ['wing' => 'Army']) }}" class="subnav-pill {{ $activeWing === 'Army' ? 'active' : '' }}">
          <i class="fa-solid fa-person-military-rifle"></i> Army ({{ $stats['army'] }})
        </a>
        <a href="{{ route('admin.student_accounts.index', ['wing' => 'Air Force']) }}" class="subnav-pill {{ $activeWing === 'Air Force' ? 'active' : '' }}">
          <i class="fa-solid fa-jet-fighter"></i> Air Force ({{ $stats['airforce'] }})
        </a>
        <a href="{{ route('admin.student_accounts.index', ['wing' => 'all']) }}" class="subnav-pill {{ $activeWing === 'all' ? 'active' : '' }}">
          <i class="fa-solid fa-users"></i> All Cadets ({{ $stats['total'] }})
        </a>
      </div>
    </div>

  {{-- ================================================================ --}}
  {{-- VIEW A: SEGMENT CONTROL PANEL (When Navy, Police, Army, Airforce) --}}
  {{-- ================================================================ --}}
  @if($isWingSegment)
    @php
      $wingConfig = [
        'Navy' => [
          'title' => 'Navy Cadets',
          'icon' => 'fa-anchor',
          'count' => $stats['navy'],
        ],
        'Police' => [
          'title' => 'Police Cadets',
          'icon' => 'fa-shield-halved',
          'count' => $stats['police'],
        ],
        'Army' => [
          'title' => 'Army Cadets',
          'icon' => 'fa-person-military-rifle',
          'count' => $stats['army'],
        ],
        'Air Force' => [
          'title' => 'Air Force Cadets',
          'icon' => 'fa-jet-fighter',
          'count' => $stats['airforce'],
        ],
      ][$activeWing];

      $wingRegisterLabels = [
        'Navy' => 'Register New Navigator',
        'Police' => 'Register New Police Officer',
        'Army' => 'Register New Army Cadet',
        'Air Force' => 'Register New Air Cadet',
      ];
      $registerBtnLabel = $wingRegisterLabels[$activeWing] ?? "Register New {$activeWing} Cadet";
    @endphp

    <div style="background: #131722; border: 1px solid rgba(255,255,255,0.08); border-radius: 12px; padding: 14px 18px; margin-bottom: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.25);">
      {{-- TOP ROW: Wing Identity & Top-Corner Register Button --}}
      <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        
        {{-- Wing Identity & Cadet Counter --}}
        <div style="display: flex; align-items: center; gap: 12px;">
          <div style="width: 42px; height: 42px; border-radius: 10px; background: #1c202d; border: 1px solid rgba(255,255,255,0.08); display: flex; align-items: center; justify-content: center; flex-shrink: 0; color: #ff5757; font-size: 18px;">
            <i class="fa-solid {{ $wingConfig['icon'] }}"></i>
          </div>
          <div>
            <div style="display: flex; align-items: center; gap: 8px;">
              <h3 style="font-size: 15.5px; font-weight: 700; color: #fff; margin: 0;">{{ $wingConfig['title'] }}</h3>
              <span style="font-size: 10.5px; font-weight: 600; color: #cbd5e1; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); border-radius: 999px; padding: 2px 9px;">
                {{ $wingConfig['count'] }} {{ $wingConfig['count'] === 1 ? 'Cadet' : 'Cadets' }}
              </span>
            </div>
          </div>
        </div>

        {{-- Top Corner: Register Button (Specialized Title, No All Cadets button) --}}
        <div>
          <button type="button" onclick="openModal('offlineModal', '{{ $activeWing }}')" class="btn-tactical" style="background: #ff5757; color: #fff; border: none; height: 36px; padding: 0 16px; font-size: 12px; font-weight: 700; white-space: nowrap; border-radius: 8px; display: inline-flex; align-items: center; gap: 7px; box-shadow: 0 4px 14px rgba(255,87,87,0.3);">
            <i class="fa-solid fa-user-plus"></i> {{ $registerBtnLabel }}
          </button>
        </div>
      </div>

      {{-- UNDER THAT: Program Tracks Dropdown Menu --}}
      <div style="margin-top: 14px; padding-top: 12px; border-top: 1px solid rgba(255,255,255,0.06);">
        <form method="GET" action="{{ route('admin.student_accounts.index') }}" style="display: flex; align-items: center; margin: 0;">
          <input type="hidden" name="wing" value="{{ $activeWing }}">
          @if(request('search'))
            <input type="hidden" name="search" value="{{ request('search') }}">
          @endif

          <select name="track" onchange="this.form.submit()" class="ida-track-select">
            @if($activeWing === 'Navy')
              <option value="" {{ empty($selectedTrack) || $selectedTrack === 'all' ? 'selected' : '' }}>All Navy Courses ({{ $wingTrackStats['Navy']['all'] ?? $wingConfig['count'] }})</option>
              <option value="preliminary" {{ $selectedTrack === 'preliminary' ? 'selected' : '' }}>Preliminary Courses ({{ $wingTrackStats['Navy']['preliminary'] ?? 0 }})</option>
              <option value="issb" {{ $selectedTrack === 'issb' ? 'selected' : '' }}>ISSB ({{ $wingTrackStats['Navy']['issb'] ?? 0 }})</option>
            @elseif($activeWing === 'Army')
              <option value="" {{ empty($selectedTrack) || $selectedTrack === 'all' ? 'selected' : '' }}>All Army Courses ({{ $wingTrackStats['Army']['all'] ?? $wingConfig['count'] }})</option>
              <option value="preliminary" {{ $selectedTrack === 'preliminary' ? 'selected' : '' }}>Preliminary Courses ({{ $wingTrackStats['Army']['preliminary'] ?? 0 }})</option>
              <option value="issb" {{ $selectedTrack === 'issb' ? 'selected' : '' }}>ISSB ({{ $wingTrackStats['Army']['issb'] ?? 0 }})</option>
            @elseif($activeWing === 'Air Force')
              <option value="" {{ empty($selectedTrack) || $selectedTrack === 'all' ? 'selected' : '' }}>All Air Force Courses ({{ $wingTrackStats['Air Force']['all'] ?? $wingConfig['count'] }})</option>
              <option value="preliminary" {{ $selectedTrack === 'preliminary' ? 'selected' : '' }}>Preliminary Courses ({{ $wingTrackStats['Air Force']['preliminary'] ?? 0 }})</option>
              <option value="issb" {{ $selectedTrack === 'issb' ? 'selected' : '' }}>ISSB ({{ $wingTrackStats['Air Force']['issb'] ?? 0 }})</option>
            @elseif($activeWing === 'Police')
              <option value="" {{ empty($selectedTrack) || $selectedTrack === 'all' ? 'selected' : '' }}>All Police Courses ({{ $wingTrackStats['Police']['all'] ?? $wingConfig['count'] }})</option>
              <option value="constable" {{ $selectedTrack === 'constable' ? 'selected' : '' }}>Constable Courses ({{ $wingTrackStats['Police']['constable'] ?? 0 }})</option>
              <option value="si" {{ $selectedTrack === 'si' ? 'selected' : '' }}>Sub-Inspector (SI) ({{ $wingTrackStats['Police']['si'] ?? 0 }})</option>
              <option value="asi" {{ $selectedTrack === 'asi' ? 'selected' : '' }}>Assistant Sub-Inspector (ASI) ({{ $wingTrackStats['Police']['asi'] ?? 0 }})</option>
            @endif
          </select>
        </form>
      </div>
    </div>

    {{-- ================================================================ --}}
    {{-- STANDALONE SEARCH SECTION (LEFT-ALIGNED BETWEEN CADETS & DIRECTORY) --}}
    {{-- ================================================================ --}}
    <div style="background: #131722; border: 1px solid rgba(255,255,255,0.08); border-radius: 12px; padding: 12px 16px; margin-bottom: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.2);">
      <form method="GET" action="{{ route('admin.student_accounts.index') }}" style="display: flex; align-items: center; justify-content: flex-start; gap: 8px; margin: 0; flex-wrap: wrap;">
        <input type="hidden" name="wing" value="{{ $activeWing }}">
        @if(request('track'))
          <input type="hidden" name="track" value="{{ request('track') }}">
        @endif

        {{-- Left-Aligned Search Input with Icon --}}
        <div style="position: relative; width: 320px; max-width: 100%;">
          <input type="text" name="search" value="{{ request('search') }}"
                 placeholder="Search..."
                 class="form-control"
                 style="font-size: 12.5px; height: 38px; border-radius: 8px; padding-left: 36px !important; padding-right: 12px !important; width: 100%; box-sizing: border-box; background: #1a1f2c; border: 1px solid rgba(255,255,255,0.12);">
          <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #8c96a8; font-size: 12px; pointer-events: none;"></i>
        </div>

        <button type="submit" class="btn-tactical" style="background: rgba(255,255,255,0.06); color: #cbd5e1; border: 1px solid rgba(255,255,255,0.12); height: 38px; padding: 0 16px; font-size: 12px; font-weight: 600; border-radius: 8px; display: inline-flex; align-items: center; gap: 6px; cursor: pointer;">
          <i class="fa-solid fa-magnifying-glass" style="color: #ff5757;"></i> Search
        </button>

        @if(request('search'))
          <a href="{{ route('admin.student_accounts.index', array_filter(['wing' => $activeWing, 'track' => request('track')])) }}" class="btn-tactical" style="background: rgba(239,68,68,0.1); color: #f87171; border: 1px solid rgba(239,68,68,0.25); height: 38px; padding: 0 12px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 5px; border-radius: 8px; text-decoration: none;" title="Clear search">
            <i class="fa-solid fa-xmark"></i> Clear
          </a>
        @endif
      </form>
    </div>

  {{-- ================================================================ --}}
  {{-- VIEW B: ALL STUDENTS FILTER SUITE (When wing=all or default)     --}}
  {{-- ================================================================ --}}
  @else
    <div style="background: #131722; border: 1px solid rgba(255,255,255,0.08); border-radius: 12px; padding: 14px 18px; margin-bottom: 16px;">
      <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; margin-bottom: 12px;">
        <div style="display: flex; align-items: center; gap: 8px;">
          <div style="width: 32px; height: 32px; border-radius: 8px; background: #1c202d; border: 1px solid rgba(255,255,255,0.08); display: flex; align-items: center; justify-content: center; color: #ff5757; font-size: 13px;">
            <i class="fa-solid fa-filter"></i>
          </div>
          <div>
            <h4 style="font-size: 13.5px; font-weight: 700; color: #fff; margin: 0;">Filter Students</h4>
          </div>
        </div>

        {{-- REGISTER OFFLINE STUDENT BUTTON --}}
        <button type="button" onclick="openModal('offlineModal')" class="btn-tactical" style="background: #ff5757; color: #fff; border: none; height: 35px; padding: 0 14px; font-size: 12px; font-weight: 600; white-space: nowrap; border-radius: 8px;">
          <i class="fa-solid fa-user-plus" style="margin-right: 6px;"></i> Register Offline Student
        </button>
      </div>

      <form method="GET" action="{{ route('admin.student_accounts.index') }}" style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
        <input type="hidden" name="wing" value="all">

        <div style="min-width: 200px; flex: 1; max-width: 320px;">
          <input type="text" name="search" value="{{ request('search') }}"
                 placeholder="Search..."
                 class="form-control"
                 style="font-size: 11.5px; height: 35px;">
        </div>

        {{-- Wing Filter --}}
        <select name="wing_filter" class="form-control" style="width: auto; font-size: 11.5px; height: 35px;" onchange="this.form.submit()">
          <option value="all">All Wings</option>
          <option value="Navy" {{ request('wing_filter') === 'Navy' ? 'selected' : '' }}>Navy ({{ $stats['navy'] }})</option>
          <option value="Police" {{ request('wing_filter') === 'Police' ? 'selected' : '' }}>Police ({{ $stats['police'] }})</option>
          <option value="Army" {{ request('wing_filter') === 'Army' ? 'selected' : '' }}>Army ({{ $stats['army'] }})</option>
          <option value="Air Force" {{ (request('wing_filter') === 'Air Force' || request('wing_filter') === 'Airforce') ? 'selected' : '' }}>Air Force ({{ $stats['airforce'] }})</option>
        </select>

        {{-- Student Type Filter --}}
        <select name="type" class="form-control" style="width: auto; font-size: 11.5px; height: 35px;" onchange="this.form.submit()">
          <option value="">All Types</option>
          <option value="offline" {{ request('type') === 'offline' ? 'selected' : '' }}>Offline (Staff)</option>
          <option value="online" {{ request('type') === 'online' ? 'selected' : '' }}>Online (Web)</option>
        </select>

        {{-- Course Status Filter --}}
        <select name="course_status" class="form-control" style="width: auto; font-size: 11.5px; height: 35px;" onchange="this.form.submit()">
          <option value="">All Courses</option>
          <option value="has_courses" {{ in_array(request('course_status'), ['has_courses', 'has_course']) ? 'selected' : '' }}>Enrolled in Courses</option>
          <option value="no_courses" {{ in_array(request('course_status'), ['no_courses', 'no_course']) ? 'selected' : '' }}>No Courses (Free)</option>
        </select>

        <button type="submit" class="btn-tactical" style="background: #ff5757; color: #fff; border: none; height: 35px; padding: 0 14px; font-size: 11.5px; font-weight: 600; border-radius: 8px;">
          <i class="fa-solid fa-filter"></i> Apply
        </button>

        @if(request()->anyFilled(['search', 'wing_filter', 'type', 'course_status']))
          <a href="{{ route('admin.student_accounts.index', ['wing' => 'all']) }}" class="btn-tactical" style="background: rgba(239,68,68,0.1); color: #f87171; border: 1px solid rgba(239,68,68,0.25); height: 35px; padding: 0 10px; font-size: 11.5px; display: inline-flex; align-items: center; gap: 4px; border-radius: 8px;" title="Reset filters">
            <i class="fa-solid fa-xmark"></i> Reset
          </a>
        @endif
      </form>
    </div>
  @endif

  {{-- ================================================================ --}}
  {{-- STUDENT DIRECTORY TABLE CONTAINER (NO HORIZONTAL SLIDER)         --}}
  {{-- ================================================================ --}}
  <div style="background: #131722; border: 1px solid rgba(255,255,255,0.08); border-radius: 12px; width: 100%; overflow: hidden;">
    <div style="padding: 12px 16px; border-bottom: 1px solid rgba(255,255,255,0.06); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
      <h3 style="font-size: 13.5px; font-weight: 700; color: #fff; margin: 0; display: flex; align-items: center; gap: 8px;">
        @if($isWingSegment)
          <i class="fa-solid {{ $wingConfig['icon'] }}" style="color: #ff5757;"></i> {{ $activeWing }} Student Directory
        @else
          <i class="fa-solid fa-users" style="color: #ff5757;"></i> All Students Directory
        @endif
      </h3>
      <span style="font-size: 11px; color: #8c96a8;">
        @if($students->total() > 0)
          Showing {{ $students->firstItem() }}–{{ $students->lastItem() }} of {{ $students->total() }} students
        @else
          0 students
        @endif
      </span>
    </div>

    @if($students->isEmpty())
      {{-- EMPTY STATE --}}
      <div style="padding: 50px 20px; text-align: center;">
        <div style="width: 56px; height: 56px; border-radius: 50%; background: rgba(255,255,255,0.03); border: 1px dashed rgba(255,255,255,0.12); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 14px;">
          <i class="fa-solid fa-user-graduate" style="font-size: 24px; color: #475569;"></i>
        </div>
        @if($isWingSegment)
          <h4 style="font-size: 14px; font-weight: 700; color: #cbd5e1; margin: 0 0 14px;">No {{ $activeWing }} students found</h4>
          <button type="button" onclick="openModal('offlineModal', '{{ $activeWing }}')" class="btn-tactical" style="background: #ff5757; color: #fff; border: none; padding: 8px 18px; font-size: 12px; font-weight: 600; border-radius: 8px;">
            <i class="fa-solid fa-user-plus"></i> Register {{ $activeWing }} Student
          </button>
        @else
          <h4 style="font-size: 14px; font-weight: 700; color: #cbd5e1; margin: 0 0 14px;">No students found</h4>
          <button type="button" onclick="openModal('offlineModal')" class="btn-tactical" style="background: #ff5757; color: #fff; border: none; padding: 8px 18px; font-size: 12px; font-weight: 600; border-radius: 8px;">
            <i class="fa-solid fa-user-plus"></i> Register Student
          </button>
        @endif
      </div>
    @else
      <div style="width: 100%; overflow: hidden;">
        <table style="width: 100%; table-layout: fixed; border-collapse: collapse; font-size: 11.5px;">
          <colgroup>
            <col style="width: 18%;">
            <col style="width: 32%;">
            <col style="width: 25%;">
            <col style="width: 25%;">
          </colgroup>
          <thead>
            <tr style="border-bottom: 1px solid rgba(255,255,255,0.06); text-align: left; color: #64748b; font-size: 10px; text-transform: uppercase; letter-spacing: 0.6px;">
              <th style="padding: 10px 10px;">ID</th>
              <th style="padding: 10px 10px;">Name</th>
              <th style="padding: 10px 10px;">Contact</th>
              <th style="padding: 10px 10px; text-align: right;">Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($students as $idx => $st)
              @php
                $loginId = $st->student_id_code ?: ($st->user->account_id ?? 'UNSET');
                $isOffline = in_array($st->student_type, ['offline', 'academic']);
                $assignedCourses = $st->courses;
                if ($assignedCourses->isEmpty() && $st->currentCourse) {
                  $assignedCourses = collect([$st->currentCourse]);
                }
              @endphp
              <tr style="border-bottom: 1px solid rgba(255,255,255,0.03); transition: background 0.15s;" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background='transparent'">
                
                {{-- COLUMN 1: ID FIRST --}}
                <td style="padding: 10px 10px; overflow: hidden; vertical-align: middle;">
                  <code style="background: rgba(255,87,87,0.1); color: #ff8585; border: 1px solid rgba(255,87,87,0.25); font-size: 11px; font-weight: 700; padding: 2px 5px; border-radius: 4px; font-family: monospace; display: inline-block; max-width: 100%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $loginId }}">{{ $loginId }}</code>
                  <div style="font-size: 9px; margin-top: 3px; color: #8c96a8;">
                    @if($isOffline)
                      <span><i class="fa-solid fa-building" style="font-size: 8px;"></i> Offline</span>
                    @else
                      <span><i class="fa-solid fa-globe" style="font-size: 8px;"></i> Online</span>
                    @endif
                  </div>
                </td>

                {{-- COLUMN 2: NAME SECOND (WITH TARGET WING BADGE) --}}
                <td style="padding: 10px 10px; overflow: hidden; vertical-align: middle;">
                  <div style="font-size: 12px; font-weight: 700; color: #fff; line-height: 1.3; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $st->user->name }}">
                    {{ $st->user->name }}
                  </div>
                  <div style="display: flex; align-items: center; gap: 4px; margin-top: 3px; flex-wrap: wrap;">
                    @php
                      $w = trim($st->target_wing ?? '');
                      $wIcon = 'fa-shield';
                      if (stripos($w, 'Navy') !== false) $wIcon = 'fa-anchor';
                      elseif (stripos($w, 'Police') !== false) $wIcon = 'fa-shield-halved';
                      elseif (stripos($w, 'Army') !== false) $wIcon = 'fa-person-military-rifle';
                      elseif (stripos($w, 'Air') !== false || stripos($w, 'BAFA') !== false) $wIcon = 'fa-jet-fighter';
                    @endphp
                    @if(!empty($w))
                      <span style="font-size: 8.5px; font-weight: 600; color: #cbd5e1; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); border-radius: 3px; padding: 1px 5px; display: inline-flex; align-items: center; gap: 3px;">
                        <i class="fa-solid {{ $wIcon }}" style="font-size: 7px; color: #8c96a8;"></i> {{ $w }}
                      </span>
                    @endif
                    <span style="font-size: 9px; color: #64748b;">
                      Joined {{ $st->created_at ? $st->created_at->format('d M Y') : 'N/A' }}
                    </span>
                  </div>
                </td>

                {{-- COLUMN 3: CONTACT --}}
                <td style="padding: 10px 10px; overflow: hidden; vertical-align: middle;">
                  <div style="font-size: 11px; color: #cbd5e1; display: flex; align-items: center; gap: 4px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                    <i class="fa-solid fa-phone" style="color: #8c96a8; font-size: 9px; flex-shrink: 0;"></i>
                    <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $st->user->phone ?: '—' }}</span>
                  </div>
                  <div style="font-size: 10px; color: #64748b; margin-top: 2px; display: flex; align-items: center; gap: 4px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $st->user->email }}">
                    <i class="fa-regular fa-envelope" style="font-size: 9px; flex-shrink: 0;"></i>
                    <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $st->user->email }}</span>
                  </div>
                </td>

                {{-- COLUMN 4: ACTIONS (VIEW DETAILS, EDIT DETAILS, DELETE) --}}
                <td style="padding: 10px 10px; text-align: right; vertical-align: middle;">
                  <div style="display: flex; justify-content: flex-end; align-items: center; gap: 4px; flex-wrap: wrap;">
                    
                    {{-- 1. VIEW DETAILS --}}
                    <a href="{{ route('admin.student_accounts.show', $st->id) }}"
                       class="btn-tactical"
                       style="background: rgba(255,255,255,0.06); color: #cbd5e1; border: 1px solid rgba(255,255,255,0.12); padding: 4px 7px; font-size: 10.5px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; border-radius: 5px; white-space: nowrap;"
                       title="View Full Student Details">
                       <i class="fa-solid fa-eye" style="font-size: 9.5px;"></i>
                       <span>View Details</span>
                    </a>

                    {{-- 2. EDIT DETAILS --}}
                    <a href="{{ route('admin.student_accounts.edit', $st->id) }}"
                       class="btn-tactical"
                       style="background: rgba(255,87,87,0.1); color: #ff8585; border: 1px solid rgba(255,87,87,0.25); padding: 4px 7px; font-size: 10.5px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; border-radius: 5px; white-space: nowrap;"
                       title="Edit Details, Assign ID & Courses">
                      <i class="fa-solid fa-user-pen" style="font-size: 9.5px;"></i>
                      <span>Edit Details</span>
                    </a>

                    {{-- 3. DELETE (WITH SECURITY WARNING CONFIRMATION) --}}
                    <button type="button"
                            onclick='confirmDelete({{ $st->id }}, "{{ addslashes($st->user->name) }}", "{{ $loginId }}")'
                            class="btn-tactical"
                            style="background: rgba(239,68,68,0.08); color: #f87171; border: 1px solid rgba(239,68,68,0.25); padding: 4px 7px; font-size: 10.5px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; border-radius: 5px; cursor: pointer; white-space: nowrap;"
                            title="Permanently Delete Student Account">
                      <i class="fa-solid fa-trash-can" style="font-size: 9.5px;"></i>
                      <span>Delete</span>
                    </button>

                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      @if($students->hasPages())
        <div style="padding: 10px 16px; border-top: 1px solid rgba(255,255,255,0.06); overflow: hidden;">
          {{ $students->links() }}
        </div>
      @endif
    @endif
  </div>

  @endif

</div>

{{-- ================================================================ --}}
{{-- MODAL: REGISTER OFFLINE CADET (New Registration Only)             --}}
{{-- ================================================================ --}}
<div id="offlineModal" class="ida-modal-overlay" style="display: none;">
  <div class="ida-modal-box" style="max-width: 580px;">
    <div class="ida-modal-header">
      <h3 class="ida-modal-title"><i class="fa-solid fa-user-plus" style="color: #ff5757;"></i> Register Offline Cadet</h3>
      <button type="button" onclick="closeModal('offlineModal')" class="ida-modal-close">&times;</button>
    </div>
    <form method="POST" action="{{ route('admin.student_accounts.store_offline') }}">
      @csrf
      <div style="padding: 20px; max-height: 75vh; overflow-y: auto;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
          <div style="grid-column: span 2;">
            <label class="ida-label">Full Name *</label>
            <input type="text" name="name" required class="form-control" placeholder="Cadet full name" value="{{ old('name') }}">
          </div>

          <div>
            <label class="ida-label">Custom Login ID</label>
            <input type="text" name="custom_id" class="form-control" style="font-family: monospace; font-weight: 700; color: #ff8585;" placeholder="Auto-generated if empty" value="{{ old('custom_id') }}">
          </div>

          <div>
            <label class="ida-label">Target Defence Wing *</label>
            <select name="target_wing" id="modalTargetWing" required class="form-control" onchange="syncProgramTracks()">
              <option value="Army" {{ (old('target_wing') === 'Army' || !old('target_wing')) ? 'selected' : '' }}>Bangladesh Army</option>
              <option value="Navy" {{ old('target_wing') === 'Navy' ? 'selected' : '' }}>Bangladesh Navy</option>
              <option value="Air Force" {{ old('target_wing') === 'Air Force' ? 'selected' : '' }}>Bangladesh Air Force</option>
              <option value="Police" {{ old('target_wing') === 'Police' ? 'selected' : '' }}>Bangladesh Police</option>
            </select>
          </div>

          <div style="grid-column: span 2;">
            <label class="ida-label">Course Program / Track *</label>
            <select name="target_program" id="modalTargetProgram" required class="form-control" onchange="filterCoursesByTrack()">
              <!-- Dynamically populated via JS according to Wing -->
            </select>
            <small style="color: #64748b; font-size: 11px; margin-top: 3px; display: block;">Select the preparatory stream according to Academy syllabus.</small>
          </div>

          <div>
            <label class="ida-label">Age *</label>
            <input type="number" name="age" required min="12" max="60" class="form-control" placeholder="e.g. 19" value="{{ old('age') }}">
          </div>

          <div>
            <label class="ida-label">Gender *</label>
            <select name="gender" required class="form-control">
              <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
              <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
              <option value="other" {{ old('gender') === 'other' ? 'selected' : '' }}>Other</option>
            </select>
          </div>

          <div>
            <label class="ida-label">Mobile Number *</label>
            <input type="text" name="phone" required class="form-control" placeholder="01XXXXXXXXX" value="{{ old('phone') }}">
          </div>

          <div>
            <label class="ida-label">Initial Course (Optional)</label>
            <select name="course_id" id="modalCourseSelect" class="form-control">
              <option value="">No Course (Assign Later)</option>
              @foreach($courses as $c)
                <option value="{{ $c->id }}" data-branch="{{ $c->branch_key }}" data-track="{{ $c->program_track }}">{{ $c->title }}</option>
              @endforeach
            </select>
          </div>

          <div style="grid-column: span 2;">
            <label class="ida-label">Email Address *</label>
            <input type="email" name="email" required class="form-control" placeholder="cadet@example.com" value="{{ old('email') }}">
          </div>

          <div style="grid-column: span 2;">
            <label class="ida-label">Address *</label>
            <textarea name="address" required rows="2" class="form-control" placeholder="House, Road, Area, District">{{ old('address') }}</textarea>
          </div>

          <div style="grid-column: span 2;">
            <label class="ida-label">Login Password</label>
            <input type="text" name="password" class="form-control" placeholder="Default: password123">
          </div>
        </div>
      </div>
      <div style="display: flex; justify-content: flex-end; gap: 8px; padding: 14px 20px; border-top: 1px solid rgba(255,255,255,0.06);">
        <button type="button" onclick="closeModal('offlineModal')" class="btn-tactical" style="background: rgba(255,255,255,0.06); color: #94a3b8; border: 1px solid rgba(255,255,255,0.1);">Cancel</button>
        <button type="submit" class="btn-tactical" style="background: #ff5757; color: #fff; border: none; font-weight: 700;">
          <i class="fa-solid fa-check"></i> Register Cadet
        </button>
      </div>
    </form>
  </div>
</div>

{{-- ================================================================ --}}
{{-- MODAL: DELETE CADET ACCOUNT (CRITICAL WARNING & SECURITY GUARD)   --}}
{{-- ================================================================ --}}
<div id="deleteModal" class="ida-modal-overlay" style="display: none;">
  <div class="ida-modal-box" style="max-width: 500px; border-color: rgba(239,68,68,0.3);">
    <div class="ida-modal-header" style="background: rgba(239,68,68,0.06); border-bottom-color: rgba(239,68,68,0.15);">
      <h3 class="ida-modal-title" style="color: #f87171;">
        <i class="fa-solid fa-triangle-exclamation" style="color: #ef4444;"></i>
        Security Warning: Delete Cadet Account
      </h3>
      <button type="button" onclick="closeModal('deleteModal')" class="ida-modal-close">&times;</button>
    </div>

    <form method="POST" id="deleteStudentForm" action="">
      @csrf
      @method('DELETE')

      <div style="padding: 18px 20px;">
        {{-- High-Impact Security Warning Banner --}}
        <div style="background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.25); border-radius: 8px; padding: 12px 14px; margin-bottom: 14px;">
          <div style="display: flex; align-items: flex-start; gap: 10px;">
            <i class="fa-solid fa-skull-crossbones" style="color: #ef4444; font-size: 16px; margin-top: 2px;"></i>
            <div>
              <div style="font-weight: 800; color: #fca5a5; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">
                Critical Warning: Permanent & Irreversible Action
              </div>
              <div style="font-size: 11.5px; color: #fca5a5; margin-top: 3px; line-height: 1.5;">
                You are about to delete the account for <strong id="delStudentName" style="color: #fff; text-decoration: underline;"></strong> (ID: <code id="delStudentIdDisplay" style="color: #ff8585; font-weight: bold; background: rgba(0,0,0,0.3); padding: 1px 5px; border-radius: 3px;"></code>).
              </div>
            </div>
          </div>
        </div>

        <div style="font-size: 11.5px; color: #cbd5e1; margin-bottom: 14px; line-height: 1.5;">
          <div style="font-weight: 700; color: #e2e8f0; margin-bottom: 4px;">This deletion will permanently destroy:</div>
          <ul style="margin: 0; padding-left: 18px; color: #94a3b8; font-size: 11px;">
            <li>Login credentials and cadet authentication access</li>
            <li>Demographics, address, and profile information</li>
            <li>All enrolled course access permissions</li>
            <li>Exam attempts, scores, and academic history</li>
          </ul>
        </div>

        {{-- Type to Confirm Field --}}
        <div style="margin-bottom: 14px;">
          <label class="ida-label" style="color: #fca5a5; margin-bottom: 4px;">
            To confirm, type cadet ID (<span id="delRequiredCode" style="color: #ff8585; font-family: monospace; font-weight: 800;"></span>) or <span style="color: #fff; font-weight: 800;">DELETE</span> below:
          </label>
          <input type="text"
                 id="delConfirmInput"
                 name="confirm_delete"
                 class="form-control"
                 autocomplete="off"
                 oninput="validateDeleteConfirmation()"
                 placeholder="Type ID or DELETE to confirm"
                 style="font-family: monospace; font-size: 12.5px; font-weight: 700; letter-spacing: 0.5px; border-color: rgba(239,68,68,0.3); background: rgba(15,23,42,0.6);">
          <div id="delMatchHint" style="font-size: 10.5px; margin-top: 4px; color: #64748b;">
            Type exact ID or DELETE to unlock the delete button.
          </div>
        </div>

        {{-- Acknowledgment Checkbox --}}
        <div style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06); border-radius: 8px; padding: 8px 12px; display: flex; align-items: center; gap: 8px;">
          <input type="checkbox" id="delConfirmCheckbox" onchange="validateDeleteConfirmation()" style="width: 15px; height: 15px; cursor: pointer; accent-color: #ef4444;">
          <label for="delConfirmCheckbox" style="font-size: 11px; color: #cbd5e1; cursor: pointer; user-select: none; margin: 0;">
            I confirm that I understand this deletion cannot be undone.
          </label>
        </div>
      </div>

      <div style="display: flex; justify-content: flex-end; gap: 8px; padding: 12px 18px; border-top: 1px solid rgba(255,255,255,0.06); background: rgba(0,0,0,0.15);">
        <button type="button" onclick="closeModal('deleteModal')" class="btn-tactical" style="background: rgba(255,255,255,0.06); color: #94a3b8; border: 1px solid rgba(255,255,255,0.1); padding: 7px 14px; font-size: 11.5px;">
          Cancel
        </button>
        <button type="submit" id="deleteSubmitBtn" disabled class="btn-tactical" style="background: #ef4444; color: #fff; border: none; font-weight: 700; opacity: 0.4; cursor: not-allowed; transition: all 0.2s; box-shadow: 0 2px 8px rgba(239,68,68,0.3); padding: 7px 14px; font-size: 11.5px;">
          <i class="fa-solid fa-trash-can"></i> Permanently Delete Account
        </button>
      </div>
    </form>
  </div>
</div>

<style>
  .ida-modal-overlay {
    position: fixed; inset: 0; background: rgba(0,0,0,0.75); backdrop-filter: blur(4px);
    z-index: 9999; overflow-y: auto; padding: 30px 16px;
  }
  .ida-modal-box {
    margin: auto; background: #131722; border: 1px solid rgba(255,255,255,0.1);
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
  .ida-track-select {
    height: 42px !important;
    line-height: normal !important;
    padding: 0 38px 0 14px !important;
    font-size: 13px !important;
    font-weight: 700 !important;
    font-family: inherit !important;
    background-color: #181d29 !important;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23cbd5e1' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E") !important;
    background-repeat: no-repeat !important;
    background-position: right 14px center !important;
    border: 1px solid rgba(255, 255, 255, 0.14) !important;
    border-radius: 9px !important;
    color: #ffffff !important;
    min-width: 240px !important;
    max-width: 360px !important;
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
    background-color: #131722 !important;
    color: #ffffff !important;
  }
  /* 5 ACTION CARDS - 4 IN A ROW LAYOUT */
  .portal-landing-hub {
    padding: 24px 0 40px 0;
    max-width: 1160px;
    margin: 0 auto;
  }
  .portal-landing-hub .section-caption {
    font-size: 11.5px;
    font-weight: 800;
    letter-spacing: 1.2px;
    color: #64748b;
    text-transform: uppercase;
    margin-bottom: 14px;
    display: block;
    text-align: left;
  }
  .command-boxes-grid {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 18px;
  }
  .command-boxes-grid .dark-action-card,
  .command-boxes-grid .simple-box {
    width: calc(25% - 14px);
    box-sizing: border-box;
  }
  @media (max-width: 1024px) {
    .command-boxes-grid .dark-action-card,
    .command-boxes-grid .simple-box {
      width: calc(50% - 10px);
    }
  }
  @media (max-width: 580px) {
    .command-boxes-grid .dark-action-card,
    .command-boxes-grid .simple-box {
      width: 100%;
    }
  }

  .dark-action-card,
  .simple-box {
    background: #181c26;
    border: 1px solid rgba(255, 255, 255, 0.05);
    border-radius: 22px;
    padding: 34px 24px 28px;
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
    width: 58px;
    height: 58px;
    border-radius: 18px;
    background: #11141c;
    box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.5), 0 2px 6px rgba(0, 0, 0, 0.25);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 16px;
    color: #ff5757;
    font-size: 20px;
    transition: all 0.3s ease;
  }
  .dark-action-card:hover .dark-action-icon-wrap,
  .simple-box:hover .box-icon-wrap {
    transform: scale(1.08);
    color: #ffffff;
    background: #ff5757;
    box-shadow: 0 0 20px rgba(255, 87, 87, 0.4);
  }

  .dark-action-card h3,
  .box-title {
    font-size: 18px;
    font-weight: 800;
    color: #ffffff;
    margin: 0 0 6px 0;
    letter-spacing: -0.2px;
    font-family: 'Poppins', sans-serif;
  }

  .dark-action-card p,
  .box-count {
    font-size: 12.5px;
    color: #717d96;
    margin: 0;
    line-height: 1.45;
  }

  /* SUBNAV PILLS */
  .subnav-pill {
    padding: 6px 12px;
    font-size: 11px;
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

  @keyframes modalIn {
    from { opacity: 0; transform: translateY(-8px); }
    to { opacity: 1; transform: translateY(0); }
  }
  @keyframes slideDown {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
  }
</style>

<script>
  // Auto-dismiss success banner after 5 seconds
  setTimeout(function() {
    var b = document.getElementById('successBanner');
    if (b) { b.style.transition = 'opacity 0.5s'; b.style.opacity = '0'; setTimeout(function(){ b.remove(); }, 500); }
  }, 5000);

  // Wing and Program Track Taxonomy (from Academy Structure)
  var wingProgramTaxonomy = {
    'Army': [
      { value: 'Soldier', text: 'Soldier / Sainik (Non-Commissioned)' },
      { value: 'Preliminary', text: 'Officer - Preliminary' },
      { value: 'ISSB', text: 'Officer - ISSB' }
    ],
    'Navy': [
      { value: 'Soldier', text: 'Sailor (Non-Commissioned)' },
      { value: 'Preliminary', text: 'Officer - Preliminary' },
      { value: 'ISSB', text: 'Officer - ISSB' }
    ],
    'Air Force': [
      { value: 'Soldier', text: 'Airman (Non-Commissioned)' },
      { value: 'Preliminary', text: 'Officer - Preliminary' },
      { value: 'ISSB', text: 'Officer - ISSB' }
    ],
    'Police': [
      { value: 'Constable', text: 'Con - Constable Recruitment' },
      { value: 'Sub-Inspector', text: 'SI - Sub-Inspector & Sergeant' },
      { value: 'Assistant Sub-Inspector', text: 'ASI - Assistant Sub-Inspector' }
    ]
  };

  function syncProgramTracks() {
    var wingSelect = document.getElementById('modalTargetWing');
    var programSelect = document.getElementById('modalTargetProgram');
    if (!wingSelect || !programSelect) return;
    var selectedWing = wingSelect.value || 'Army';
    var programs = wingProgramTaxonomy[selectedWing] || wingProgramTaxonomy['Army'];

    programSelect.innerHTML = '';
    programs.forEach(function(item) {
      var opt = document.createElement('option');
      opt.value = item.value;
      opt.textContent = item.text;
      programSelect.appendChild(opt);
    });

    filterCoursesByTrack();
  }

  function filterCoursesByTrack() {
    var wingSelect = document.getElementById('modalTargetWing');
    var programSelect = document.getElementById('modalTargetProgram');
    var courseSelect = document.getElementById('modalCourseSelect');
    if (!courseSelect) return;

    var wing = (wingSelect ? wingSelect.value : '').toLowerCase();
    var prog = (programSelect ? programSelect.value : '').toLowerCase();

    for (var i = 0; i < courseSelect.options.length; i++) {
      var opt = courseSelect.options[i];
      if (!opt.value) continue;
      var text = opt.textContent.toLowerCase();
      var bKey = (opt.getAttribute('data-branch') || '').toLowerCase();
      var tKey = (opt.getAttribute('data-track') || '').toLowerCase();

      var match = false;
      if (wing === 'air force' && (bKey === 'air_force' || text.includes('air') || text.includes('bafa'))) match = true;
      else if (wing === 'army' && (bKey === 'army' || text.includes('army') || text.includes('bma'))) match = true;
      else if (wing === 'navy' && (bKey === 'navy' || text.includes('navy') || text.includes('bna'))) match = true;
      else if (wing === 'police' && (bKey === 'police' || text.includes('police'))) match = true;

      if (match) {
        opt.style.display = '';
        if (prog.includes('issb') && (tKey === 'issb' || text.includes('issb'))) {
          opt.style.color = '#ff5757';
          opt.style.fontWeight = 'bold';
        } else if (prog.includes('prelim') && (tKey === 'preliminary' || text.includes('prelim') || text.includes('regular'))) {
          opt.style.color = '#34d399';
          opt.style.fontWeight = 'bold';
        } else {
          opt.style.color = '';
          opt.style.fontWeight = '';
        }
      } else {
        opt.style.display = 'none';
      }
    }
  }

  document.addEventListener('DOMContentLoaded', function() {
    syncProgramTracks();
  });

  function openModal(id, wing) {
    document.getElementById(id).style.display = 'block';
    if (id === 'offlineModal') {
      if (wing) {
        var wingSelect = document.getElementById('modalTargetWing');
        if (wingSelect) {
          for (var i = 0; i < wingSelect.options.length; i++) {
            if (wingSelect.options[i].value.toLowerCase() === wing.toLowerCase()) {
              wingSelect.selectedIndex = i;
              break;
            }
          }
        }
      }
      syncProgramTracks();
    }
  }
  function closeModal(id) { document.getElementById(id).style.display = 'none'; }

  // Close modal on backdrop click
  document.querySelectorAll('.ida-modal-overlay').forEach(function(overlay) {
    overlay.addEventListener('click', function(e) {
      if (e.target === overlay) overlay.style.display = 'none';
    });
  });

  // Close modal on Escape key
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      document.querySelectorAll('.ida-modal-overlay').forEach(function(m) { m.style.display = 'none'; });
    }
  });

  // Delete Confirmation Logic
  let activeDeleteIdCode = '';

  function confirmDelete(studentId, studentName, idCode) {
    activeDeleteIdCode = (idCode || '').trim();
    document.getElementById('delStudentName').textContent = studentName;
    document.getElementById('delStudentIdDisplay').textContent = activeDeleteIdCode;
    document.getElementById('delRequiredCode').textContent = activeDeleteIdCode;
    
    // Set form action dynamically
    document.getElementById('deleteStudentForm').action = "{{ route('admin.student_accounts.index') }}/" + studentId;
    
    // Reset inputs
    const input = document.getElementById('delConfirmInput');
    const checkbox = document.getElementById('delConfirmCheckbox');
    const btn = document.getElementById('deleteSubmitBtn');
    const hint = document.getElementById('delMatchHint');

    input.value = '';
    checkbox.checked = false;
    btn.disabled = true;
    btn.style.opacity = '0.4';
    btn.style.cursor = 'not-allowed';
    hint.innerHTML = 'Type exact ID or DELETE to unlock the delete button.';
    hint.style.color = '#64748b';

    openModal('deleteModal');
    setTimeout(function() { input.focus(); }, 150);
  }

  function validateDeleteConfirmation() {
    const input = document.getElementById('delConfirmInput');
    const checkbox = document.getElementById('delConfirmCheckbox');
    const btn = document.getElementById('deleteSubmitBtn');
    const hint = document.getElementById('delMatchHint');

    const val = input.value.trim().toUpperCase();
    const expected = activeDeleteIdCode.toUpperCase();
    const isMatched = (val === expected && expected.length > 0) || val === 'DELETE';
    const isChecked = checkbox.checked;

    if (isMatched) {
      hint.innerHTML = '<span style="color: #ff8585;"><i class="fa-solid fa-circle-check"></i> Confirmation phrase matched.</span>';
    } else {
      hint.innerHTML = 'Type <span style="color: #ff8585;">' + activeDeleteIdCode + '</span> or <span style="color: #fff;">DELETE</span> to confirm.';
      hint.style.color = '#64748b';
    }

    if (isMatched && isChecked) {
      btn.disabled = false;
      btn.style.opacity = '1';
      btn.style.cursor = 'pointer';
    } else {
      btn.disabled = true;
      btn.style.opacity = '0.4';
      btn.style.cursor = 'not-allowed';
    }
  }

  document.addEventListener('DOMContentLoaded', function() {
    if (typeof window.initTacticalSelect === 'function') {
      document.querySelectorAll('.ida-track-select').forEach(function(sel) {
        window.initTacticalSelect(sel);
      });
    }
  });
</script>
@endsection