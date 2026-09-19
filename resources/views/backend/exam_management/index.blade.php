@extends('layouts.portal')

@section('title', 'Exam Management')
@section('page_title', 'Exam Management')
@section('page_subtitle', 'Comprehensive access control, exam creation, condition statuses, and question palette management')

@section('topbar_actions')
  <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
    <button type="button" onclick="openModal('selectExamModal')" class="btn-tactical" style="background: rgba(255,87,87,0.12); color: #ff5757; border: 1px solid rgba(255,87,87,0.3); padding: 8px 16px; font-size: 13px; font-weight: 700; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; gap: 7px;">
      <i class="fa-solid fa-pen-to-square"></i> Edit an Exam
    </button>
    <a href="{{ route('admin.exam_management.create') }}" class="btn-primary" style="padding: 8px 18px; font-size: 13px; font-weight: 700; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 7px;">
      <i class="fa-solid fa-plus"></i> Create New Exam
    </a>
  </div>
@endsection

@section('content')
<div style="width: 100%; margin: 0 auto;">

  {{-- TOP HEADER BAR --}}
  <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 20px;">
    <div>
      <h2 style="font-size: 20px; font-weight: 800; color: #ffffff; margin: 0 0 4px 0; font-family: 'Poppins', sans-serif;">Exam Management</h2>
      <p style="font-size: 12.5px; color: #94a3b8; margin: 0;">Comprehensive access control, exam creation, condition statuses, and question palette management</p>
    </div>
    <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
      <button type="button" onclick="openModal('selectExamModal')" class="btn-tactical" style="background: rgba(255,87,87,0.12); color: #ff5757; border: 1px solid rgba(255,87,87,0.3); padding: 8px 16px; font-size: 13px; font-weight: 700; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; gap: 7px;">
        <i class="fa-solid fa-pen-to-square"></i> Edit an Exam
      </button>
      <a href="{{ route('admin.exam_management.create') }}" class="btn-primary" style="padding: 8px 18px; font-size: 13px; font-weight: 700; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 7px;">
        <i class="fa-solid fa-plus"></i> Create New Exam
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
    {{-- SUBPAGE VIEW: TOP NAV + SEGMENT CONTROL / FILTER SUITE + CARDS   --}}
    {{-- ================================================================ --}}
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 18px;">
      <a href="{{ route('admin.exam_management.index') }}" class="btn-tactical" style="background: rgba(255,255,255,0.06); color: #cbd5e1; border: 1px solid rgba(255,255,255,0.12); padding: 8px 14px; font-size: 12px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 7px; border-radius: 8px;">
        <i class="fa-solid fa-arrow-left"></i> Back to Exam Management
      </a>

      {{-- Quick Type Switcher Tabs --}}
      <div style="display: flex; gap: 5px; flex-wrap: wrap; background: rgba(0,0,0,0.3); padding: 4px; border-radius: 9px; border: 1px solid rgba(255,255,255,0.06);">
        <a href="{{ route('admin.exam_management.index', ['type' => 'free', 'branch' => $selectedBranch]) }}" class="subnav-pill {{ $type === 'free' ? 'active' : '' }}">
          <i class="fa-solid fa-unlock-keyhole"></i> Free Exams ({{ $stats['free'] }})
        </a>
        <a href="{{ route('admin.exam_management.index', ['type' => 'paid', 'branch' => $selectedBranch]) }}" class="subnav-pill {{ $type === 'paid' ? 'active' : '' }}">
          <i class="fa-solid fa-user-graduate"></i> Cadet Exams ({{ $stats['cadet'] ?? $stats['paid'] }})
        </a>
        <a href="{{ route('admin.exam_management.index', ['type' => 'all', 'branch' => $selectedBranch]) }}" class="subnav-pill {{ $type === 'all' ? 'active' : '' }}">
          <i class="fa-solid fa-list-check"></i> All Exams ({{ $stats['total'] }})
        </a>
      </div>
    </div>

    {{-- Filter & Action Suite (Same as Online Exam Page) --}}
    <div class="content-panel classical-card" style="background: #181c26; border: 1px solid rgba(255, 255, 255, 0.06); border-radius: 14px; padding: 18px 20px; margin-bottom: 24px;">
      <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px;">
        
        <!-- Branch Switcher Navigation -->
        <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
          <span style="font-size: 11px; font-weight: 800; text-transform: uppercase; color: #64748b; margin-right: 4px;">Branch:</span>
          
          <a href="{{ route('admin.exam_management.index', ['type' => $type]) }}" class="badge {{ empty($selectedBranch) ? 'badge-emerald' : 'badge-navy' }}" style="text-decoration: none; padding: 6px 12px; font-size: 11.5px;">
            All Branches
          </a>
          <a href="{{ route('admin.exam_management.index', ['type' => $type, 'branch' => 'army', 'search' => $search]) }}" class="badge {{ $selectedBranch === 'army' ? 'badge-emerald' : 'badge-navy' }}" style="text-decoration: none; padding: 6px 12px; font-size: 11.5px;">
            <i class="fa-solid fa-shield-halved"></i> Army ({{ $stats['army'] }})
          </a>
          <a href="{{ route('admin.exam_management.index', ['type' => $type, 'branch' => 'navy', 'search' => $search]) }}" class="badge {{ $selectedBranch === 'navy' ? 'badge-emerald' : 'badge-navy' }}" style="text-decoration: none; padding: 6px 12px; font-size: 11.5px;">
            <i class="fa-solid fa-anchor"></i> Navy ({{ $stats['navy'] }})
          </a>
          <a href="{{ route('admin.exam_management.index', ['type' => $type, 'branch' => 'air_force', 'search' => $search]) }}" class="badge {{ $selectedBranch === 'air_force' ? 'badge-emerald' : 'badge-navy' }}" style="text-decoration: none; padding: 6px 12px; font-size: 11.5px;">
            <i class="fa-solid fa-jet-fighter"></i> Air Force ({{ $stats['air_force'] }})
          </a>
          <a href="{{ route('admin.exam_management.index', ['type' => $type, 'branch' => 'police', 'search' => $search]) }}" class="badge {{ $selectedBranch === 'police' ? 'badge-emerald' : 'badge-navy' }}" style="text-decoration: none; padding: 6px 12px; font-size: 11.5px;">
            <i class="fa-solid fa-user-shield"></i> Police ({{ $stats['police'] }})
          </a>
        </div>

        <!-- Search Form & Create Button -->
        <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
          <form method="GET" action="{{ route('admin.exam_management.index') }}" style="display: flex; gap: 6px;">
            <input type="hidden" name="type" value="{{ $type }}">
            @if($selectedBranch)
              <input type="hidden" name="branch" value="{{ $selectedBranch }}">
            @endif
            <input type="text" name="search" value="{{ $search }}" placeholder="Search exam title..." style="background: #11141d; border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; padding: 7px 12px; font-size: 12px; color: #ffffff; width: 190px; outline: none;">
            <button type="submit" class="btn-secondary" style="padding: 7px 12px; font-size: 12px; border-radius: 8px;">
              <i class="fa-solid fa-magnifying-glass"></i>
            </button>
            @if(!empty($search))
              <a href="{{ route('admin.exam_management.index', ['type' => $type, 'branch' => $selectedBranch]) }}" class="btn-secondary" style="padding: 7px 10px; font-size: 12px; border-radius: 8px; color: #ef4444;">
                <i class="fa-solid fa-xmark"></i>
              </a>
            @endif
          </form>

          <a href="{{ route('admin.exam_management.create', ['type' => $type]) }}" class="btn-primary" style="padding: 7px 14px; font-size: 12px; border-radius: 8px; display: inline-flex; align-items: center; gap: 6px; text-decoration: none;">
            <i class="fa-solid fa-plus"></i> Create Exam
          </a>
        </div>

      </div>
    </div>

    {{-- Exam Cards Grid (Matching Online Exam Page Layout + Rich Admin Controls) --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px;">
      @forelse($exams as $exam)
        @php
          $isFuture = $exam->isScheduledFuture();
          $isPaid = (bool) $exam->is_paid_for_external;
        @endphp

        <div class="content-panel classical-card" style="background: #181c26; border: 1px solid rgba(255, 255, 255, 0.06); border-radius: 14px; padding: 22px; display: flex; flex-direction: column; justify-content: space-between; border-top: 4px solid {{ $exam->branchColor() }}; position: relative; box-shadow: 0 4px 16px rgba(0,0,0,0.25);">
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
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 10px;">
              <span class="badge badge-navy" style="font-size: 10px;">{{ $exam->category }}</span>
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
            <div style="padding: 6px 10px; border-radius: 8px; background: rgba(255,255,255,0.02); border: 1px dashed rgba(255,255,255,0.08); font-size: 11px; color: #94a3b8; margin-bottom: 16px;">
              @if($isPaid)
                <i class="fa-solid fa-user-graduate" style="color: #60a5fa;"></i> Target: <strong style="color: #60a5fa;">Cadet Portal Only</strong>
              @else
                <i class="fa-solid fa-globe" style="color: #34d399;"></i> Target: <strong style="color: #34d399;">Frontend Online Tests</strong> (Free Access)
              @endif
            </div>
          </div>

          {{-- Admin Action Toolbar --}}
          <div style="border-top: 1px solid rgba(255,255,255,0.06); padding-top: 12px; display: flex; justify-content: space-between; align-items: center; gap: 8px; flex-wrap: wrap;">
            
            <div style="display: flex; align-items: center; gap: 6px;">
              <a href="{{ route('admin.exams.questions', $exam->id) }}" class="btn-tactical" style="background: rgba(255,255,255,0.06); color: #94a3b8; border: 1px solid rgba(255,255,255,0.1); padding: 6px 11px; font-size: 11.5px; text-decoration: none; border-radius: 6px; display: inline-flex; align-items: center; gap: 5px;">
                <i class="fa-solid fa-list-ol"></i> Manage Qs ({{ $exam->questions_count }})
              </a>

              {{-- Public Visibility Toggle --}}
              <form action="{{ route('admin.exams.toggle_public', $exam->id) }}" method="POST" style="display: inline-block;">
                @csrf
                <button type="submit" class="btn-tactical" style="padding: 6px 10px; font-size: 11.5px; border-radius: 6px; cursor: pointer; {{ $exam->is_public_for_external ? 'background: rgba(56, 189, 248, 0.1); color: #38bdf8; border: 1px solid rgba(56, 189, 248, 0.25);' : 'background: rgba(239, 68, 68, 0.1); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.25);' }}" title="Toggle Frontend Visibility">
                  <i class="fa-solid {{ $exam->is_public_for_external ? 'fa-eye' : 'fa-eye-slash' }}"></i>
                </button>
              </form>
            </div>

            <div style="display: flex; align-items: center; gap: 6px;">
              {{-- Full Page Edit Button (NO POP-UP) --}}
              <a href="{{ route('admin.exam_management.edit', ['id' => $exam->id, 'return_to' => url()->full()]) }}" class="btn-tactical" style="background: rgba(255, 87, 87, 0.12); color: #ff5757; border: 1px solid rgba(255, 87, 87, 0.3); padding: 6px 12px; font-size: 11.5px; font-weight: 700; text-decoration: none; border-radius: 6px; display: inline-flex; align-items: center; gap: 5px;" title="Edit Exam from Edit Page">
                <i class="fa-solid fa-pen-to-square"></i> Edit
              </a>

              <form action="{{ route('admin.exams.destroy', $exam->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this exam module?');" style="display: inline-block;">
                @csrf
                @method('DELETE')
                <button type="submit" style="background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2); padding: 6px 9px; font-size: 11.5px; border-radius: 6px; cursor: pointer;" title="Delete Exam">
                  <i class="fa-solid fa-trash-can"></i>
                </button>
              </form>
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
        <i class="fa-solid fa-pen-to-square" style="color: #ff5757;"></i> Edit Exam from Edit Page
      </h3>
      <button type="button" onclick="closeModal('selectExamModal')" class="ida-modal-close">&times;</button>
    </div>
    <div style="padding: 22px;">
      <p style="font-size: 12.5px; color: #94a3b8; margin-top: 0; margin-bottom: 16px; line-height: 1.5;">
        Select an assessment module below to open its dedicated edit page where you can modify identity parameters, scoring rules, time limits, condition status, and questions.
      </p>

      <div style="margin-bottom: 18px;">
        <label class="ida-label">Select Exam to Edit *</label>
        <select id="quickEditExamSelect" class="form-control" style="width: 100%; height: 42px; background: #0f1219; border: 1px solid rgba(255,255,255,0.12); color: #ffffff; border-radius: 8px; padding: 0 12px; font-size: 13px;">
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
          <i class="fa-solid fa-arrow-up-right-from-square"></i> Open Edit Page
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
</style>

<script>
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
    window.location.href = "{{ url('/admin/exam-management') }}/" + examId + "/edit";
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
</script>
@endsection
