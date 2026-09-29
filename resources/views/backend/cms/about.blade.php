@extends('layouts.portal')

@section('title', 'About Us - Web Management')
@section('page_title', 'About Page Editor')
@section('page_subtitle', 'Manage mission statement, strategic vision, campus history, hero section, and team members')

@section('content')
<div style="display: flex; flex-direction: column; gap: 20px;">

  @include('backend.cms.partials.nav')

  @if(session('success'))
    <div class="alert alert-success" style="background: rgba(16, 185, 129, 0.15); border: 1px solid var(--brand-mint); color: #065f46; padding: 12px 16px; border-radius: var(--radius-sm); font-size: 13px; font-weight: 600;">
      <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
    </div>
  @endif

  @if($errors->any())
    <div class="alert alert-danger" style="background: rgba(239, 68, 68, 0.15); border: 1px solid #ef4444; color: #b91c1c; padding: 12px 16px; border-radius: var(--radius-sm); font-size: 13px; font-weight: 600;">
        <ul style="margin: 0; padding-left: 20px;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
  @endif

  <!-- SECTION A: About Page Content -->
  <form action="{{ route('admin.cms.settings') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <!-- Hero Section -->
    <div class="tactical-card" style="margin-bottom: 24px;">
      <div style="border-bottom: 1px solid var(--border-soft); padding-bottom: 14px; margin-bottom: 20px;">
        <h3 style="font-size: 17px; font-weight: 800; color: var(--brand-deep); margin: 0 0 6px 0;">
          <i class="fa-solid fa-image" style="color: var(--brand-emerald);"></i> About Hero Section
        </h3>
        <p style="font-size: 12.5px; color: var(--text-muted); margin: 0;">
          Update the hero background and text displayed at the top of the About page.
        </p>
      </div>

      <div style="display: flex; flex-direction: column; gap: 16px;">
        <div>
          <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Hero Background Image</label>
          <div style="display: flex; gap: 16px; align-items: flex-start; flex-wrap: wrap;">
            @if(cms('about_hero_image'))
              <div style="display: flex; flex-direction: column; gap: 6px;">
                <img src="{{ asset(cms('about_hero_image')) }}" alt="Hero Background" style="max-height: 80px; border-radius: 6px; border: 1px solid var(--border-soft); box-shadow: 0 2px 6px rgba(0,0,0,0.1);">
                <label style="font-size: 11.5px; color: #ef4444; display: inline-flex; align-items: center; gap: 5px; cursor: pointer;">
                  <input type="checkbox" name="remove_about_hero_image" value="1">
                  <span>Remove background</span>
                </label>
              </div>
            @endif
            <div style="flex: 1; min-width: 240px;">
              <input type="file" name="about_hero_image" class="form-tactical" accept="image/jpeg,image/png,image/webp">
              <span style="font-size: 11px; color: var(--text-muted); display: block; margin-top: 4px;">Recommended: High-resolution landscape photo (1920x800px or larger).</span>
            </div>
          </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
          <div>
            <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Hero Title</label>
            <input type="text" name="about_hero_title" value="{{ cms('about_hero_title', 'Who We Are') }}" class="form-tactical">
          </div>
          <div>
            <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Hero Subtitle</label>
            <textarea name="about_hero_subtitle" rows="2" class="form-tactical">{{ cms('about_hero_subtitle', 'Forging the Future Leaders of the Armed Forces') }}</textarea>
          </div>
        </div>

        <div>
          <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Hero Overlay Text (Optional)</label>
          <textarea name="about_hero_overlay_text" rows="2" class="form-tactical">{{ cms('about_hero_overlay_text', '') }}</textarea>
        </div>
      </div>
    </div>

    <!-- About Content -->
    <div class="tactical-card" style="margin-bottom: 24px;">
      <div style="border-bottom: 1px solid var(--border-soft); padding-bottom: 14px; margin-bottom: 20px;">
        <h3 style="font-size: 17px; font-weight: 800; color: var(--brand-deep); margin: 0 0 6px 0;">
          <i class="fa-solid fa-landmark" style="color: var(--brand-emerald);"></i> About Us Header & Institutional Creed
        </h3>
        <p style="font-size: 12.5px; color: var(--text-muted); margin: 0;">
          Update the institutional statements, history, and core objectives displayed on <code>/about</code>.
        </p>
      </div>

      <div style="display: flex; flex-direction: column; gap: 16px;">
        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 16px;">
          <div>
            <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Eyebrow Badge</label>
            <input type="text" name="about_badge" value="{{ cms('about_badge', cms('about_page_badge', '★ ESTABLISHED IN KHULNA ★')) }}" class="form-tactical">
          </div>
          <div>
            <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Page Heading</label>
            <input type="text" name="about_title" value="{{ cms('about_title', cms('about_page_title', 'About Imperial Defence Academy')) }}" class="form-tactical">
          </div>
        </div>

        <div>
          <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Page Subtitle</label>
          <textarea name="about_subtitle" rows="2" class="form-tactical">{{ cms('about_subtitle', cms('about_page_subtitle', 'A specialized digital and ground training academy dedicated to training, mentoring, and commissioning the future officer corps of the Bangladesh Armed Forces.')) }}</textarea>
        </div>

        <!-- Mission & Vision -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
          <div style="background: var(--surface-subtle); border: 1px solid var(--border-soft); border-radius: var(--radius-sm); padding: 16px;">
            <label style="display: block; font-size: 12px; font-weight: 800; text-transform: uppercase; color: var(--brand-emerald); margin-bottom: 6px;">Mission Statement Title</label>
            <input type="text" name="mission_title" value="{{ cms('mission_title', cms('about_mission_title', 'Our Mission')) }}" class="form-tactical" style="margin-bottom: 10px;">
            <label style="display: block; font-size: 11px; font-weight: 700; margin-bottom: 4px;">Mission Description</label>
            <textarea name="about_mission" rows="4" class="form-tactical">{{ cms('about_mission', 'To provide rigorous, honest, and scientifically structured preparatory training that instills moral courage, rapid tactical decision-making, physical agility, and authentic Officer-Like Qualities (OLQ) in every candidate, preparing them to successfully conquer the ISSB board.') }}</textarea>
          </div>

          <div style="background: var(--surface-subtle); border: 1px solid var(--border-soft); border-radius: var(--radius-sm); padding: 16px;">
            <label style="display: block; font-size: 12px; font-weight: 800; text-transform: uppercase; color: var(--accent-gold); margin-bottom: 6px;">Strategic Vision Title</label>
            <input type="text" name="vision_title" value="{{ cms('vision_title', cms('about_vision_title', 'Our Vision')) }}" class="form-tactical" style="margin-bottom: 10px;">
            <label style="display: block; font-size: 11px; font-weight: 700; margin-bottom: 4px;">Vision Description</label>
            <textarea name="about_vision" rows="4" class="form-tactical">{{ cms('about_vision', 'To establish Imperial Defence Academy as the most technologically advanced and trusted military preparatory institution in Bangladesh, continuously bridging digital simulation with real-world physical conditioning.') }}</textarea>
          </div>
        </div>
      </div>
    </div>

    <!-- 3 Campus Facilities -->
    <div class="tactical-card" style="margin-bottom: 24px;">
      <div style="border-bottom: 1px solid var(--border-soft); padding-bottom: 14px; margin-bottom: 20px;">
        <h3 style="font-size: 17px; font-weight: 800; color: var(--brand-deep); margin: 0 0 6px 0;">
          <i class="fa-solid fa-building-columns" style="color: var(--accent-gold);"></i> 3 Campus Facilities & Training Grounds
        </h3>
        <p style="font-size: 12.5px; color: var(--text-muted); margin: 0;">
          Update the titles and descriptions of the 3 facility feature cards displayed on the About page.
        </p>
      </div>

      <div style="display: flex; flex-direction: column; gap: 16px;">
        <!-- Facility 1 -->
        <div style="background: var(--surface-subtle); border: 1px solid var(--border-soft); border-radius: var(--radius-sm); padding: 16px;">
          <label style="display: block; font-size: 12px; font-weight: 800; color: var(--brand-emerald); text-transform: uppercase; margin-bottom: 4px;">Facility 1: Ground Tasks</label>
          <input type="text" name="facility1_title" value="{{ cms('facility1_title', 'Regulation Obstacle Field') }}" class="form-tactical" style="margin-bottom: 8px;">
          <textarea name="facility1_desc" rows="2" class="form-tactical">{{ cms('facility1_desc', 'Full-scale Progressive Group Task (PGT), Half Group Task (HGT), rope climbing, beam balance, and command task bridging structures.') }}</textarea>
        </div>

        <!-- Facility 2 -->
        <div style="background: var(--surface-subtle); border: 1px solid var(--border-soft); border-radius: var(--radius-sm); padding: 16px;">
          <label style="display: block; font-size: 12px; font-weight: 800; color: var(--brand-emerald); text-transform: uppercase; margin-bottom: 4px;">Facility 2: Psychological Lab</label>
          <input type="text" name="facility2_title" value="{{ cms('facility2_title', 'Computerized IQ Laboratory') }}" class="form-tactical" style="margin-bottom: 8px;">
          <textarea name="facility2_desc" rows="2" class="form-tactical">{{ cms('facility2_desc', 'High-speed networked terminals providing simulated computerized preliminary intelligence screenings under timed constraints.') }}</textarea>
        </div>

        <!-- Facility 3 -->
        <div style="background: var(--surface-subtle); border: 1px solid var(--border-soft); border-radius: var(--radius-sm); padding: 16px;">
          <label style="display: block; font-size: 12px; font-weight: 800; color: var(--brand-emerald); text-transform: uppercase; margin-bottom: 4px;">Facility 3: Interview Suite</label>
          <input type="text" name="facility3_title" value="{{ cms('facility3_title', 'Lecturette & Viva Chambers') }}" class="form-tactical" style="margin-bottom: 8px;">
          <textarea name="facility3_desc" rows="2" class="form-tactical">{{ cms('facility3_desc', 'Acoustically isolated presentation suites for group discussions, impromptu speeches, and video-recorded mock interview panels.') }}</textarea>
        </div>
      </div>
    </div>

    <!-- Sticky Save Bar -->
    <div style="display: flex; justify-content: flex-end; gap: 12px; margin-bottom: 40px;">
      <a href="{{ route('about') }}" target="_blank" class="btn-tactical btn-tactical-outline">
        <i class="fa-solid fa-eye"></i> View Live About Page
      </a>
      <button type="submit" class="btn-tactical btn-tactical-primary" style="padding: 10px 24px; font-size: 13.5px;">
        <i class="fa-solid fa-floppy-disk"></i> Save About Page Settings
      </button>
    </div>
  </form>

  <!-- SECTION B: Team Members Management -->
  <div class="tactical-card">
    <div style="border-bottom: 1px solid var(--border-soft); padding-bottom: 14px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
      <div>
        <h3 style="font-size: 17px; font-weight: 800; color: var(--brand-deep); margin: 0 0 6px 0;">
          <i class="fa-solid fa-users" style="color: var(--accent-gold);"></i> Team & Directing Staff
        </h3>
        <p style="font-size: 12.5px; color: var(--text-muted); margin: 0;">
          Manage the profiles displayed in the team section. Grouped by category.
        </p>
      </div>
      <button type="button" class="btn-tactical btn-tactical-primary" onclick="openAddModal()">
        <i class="fa-solid fa-user-plus"></i> Add New Member
      </button>
    </div>

    @if($teamMembers->isEmpty())
      <div style="padding: 40px; text-align: center; color: var(--text-muted); background: var(--surface-subtle); border-radius: var(--radius-sm);">
        <i class="fa-solid fa-users-slash" style="font-size: 32px; color: var(--border-soft); margin-bottom: 12px;"></i>
        <p style="margin: 0;">No team members added yet.</p>
      </div>
    @else
      @php
        $groupedMembers = $teamMembers->groupBy('display_category');
        $categories = [
            1 => 'Featured/Large (Commandant/Director)',
            2 => 'Standard (Senior Staff)',
            3 => 'Compact (Instructors)',
            4 => 'Mini (Support Staff)'
        ];
      @endphp

      @foreach($categories as $catId => $catName)
        @if(isset($groupedMembers[$catId]) && $groupedMembers[$catId]->count() > 0)
          <div style="margin-bottom: 30px;">
            <h4 style="font-size: 14px; font-weight: 800; color: var(--brand-emerald); text-transform: uppercase; margin-bottom: 12px; border-bottom: 2px solid var(--border-soft); padding-bottom: 6px;">{{ $catName }}</h4>
            
            <div class="table-responsive">
              <table class="table" style="width: 100%; border-collapse: collapse; font-size: 13px;">
                <thead>
                  <tr style="background: var(--surface-subtle); border-bottom: 2px solid var(--border-soft);">
                    <th style="padding: 10px; text-align: left; width: 60px;">Photo</th>
                    <th style="padding: 10px; text-align: left;">Name & Designation</th>
                    <th style="padding: 10px; text-align: left;">Bio Snippet</th>
                    <th style="padding: 10px; text-align: right; width: 140px;">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($groupedMembers[$catId] as $member)
                    <tr style="border-bottom: 1px solid var(--border-soft);">
                      <td style="padding: 10px;">
                        <div style="width: 40px; height: 40px; border-radius: 50%; background: #ddd; overflow: hidden;">
                          @if($member->photo)
                            <img src="{{ asset($member->photo) }}" alt="" style="width: 100%; height: 100%; object-fit: cover;">
                          @else
                            <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: var(--brand-deep); color: white; font-weight: bold; font-size: 16px;">
                              {{ substr($member->name, 0, 1) }}
                            </div>
                          @endif
                        </div>
                      </td>
                      <td style="padding: 10px;">
                        <strong style="color: var(--text-main); display: block;">{{ $member->name }}</strong>
                        <span style="color: var(--text-muted); font-size: 11.5px;">{{ $member->designation ?? 'N/A' }}</span>
                      </td>
                      <td style="padding: 10px; color: var(--text-muted);">
                        {{ Str::limit($member->bio, 50) }}
                      </td>
                      <td style="padding: 10px; text-align: right;">
                        <button type="button" class="btn-tactical btn-tactical-outline" style="padding: 5px 10px; font-size: 11px;" onclick="openEditModal({{ $member }})">
                          <i class="fa-solid fa-pen"></i>
                        </button>
                        <form action="{{ route('admin.cms.team.delete', $member->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this team member?');">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn-tactical btn-tactical-outline" style="padding: 5px 10px; font-size: 11px; color: #ef4444; border-color: #fca5a5;">
                            <i class="fa-solid fa-trash"></i>
                          </button>
                        </form>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        @endif
      @endforeach
    @endif
  </div>

</div>

<!-- ADD MODAL -->
<div id="addModal" style="display: none; position: fixed; inset: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.8); z-index: 10000; align-items: center; justify-content: center; backdrop-filter: blur(6px); padding: 20px;">
  <div style="background: #11151f; width: 100%; max-width: 580px; border-radius: 16px; border: 1px solid rgba(255, 255, 255, 0.12); box-shadow: 0 25px 60px rgba(0, 0, 0, 0.65), 0 0 0 1px rgba(255, 255, 255, 0.05); max-height: 90vh; display: flex; flex-direction: column; overflow: hidden;">
    <div style="padding: 18px 24px; border-bottom: 1px solid rgba(255, 255, 255, 0.08); display: flex; justify-content: space-between; align-items: center; background: #161c28;">
      <h3 style="margin: 0; font-size: 15px; font-weight: 800; color: #ffffff; display: flex; align-items: center; gap: 10px;">
        <i class="fa-solid fa-user-plus" style="color: #85c9cc;"></i> Add New Team Member
      </h3>
      <button type="button" onclick="closeAddModal()" style="background: rgba(255, 255, 255, 0.06); border: 1px solid rgba(255, 255, 255, 0.1); color: #94a3b8; cursor: pointer; font-size: 15px; width: 30px; height: 30px; border-radius: 8px; display: flex; align-items: center; justify-content: center; transition: all 0.2s;">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>
    <form action="{{ route('admin.cms.team.store') }}" method="POST" enctype="multipart/form-data" style="padding: 24px; overflow-y: auto; display: flex; flex-direction: column; gap: 18px;">
      @csrf
      
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; color: #cbd5e1; text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 6px;">
            Name <span style="color: #f87171;">*</span>
          </label>
          <input type="text" name="name" required class="form-tactical" placeholder="e.g. Brigadier General M. Rahman" style="background: #171d29 !important; border: 1px solid rgba(255, 255, 255, 0.12) !important; color: #ffffff !important; border-radius: 8px; padding: 10px 14px; font-size: 13.5px;">
        </div>
        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; color: #cbd5e1; text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 6px;">
            Designation
          </label>
          <input type="text" name="designation" class="form-tactical" placeholder="e.g. Chief Executive Officer" style="background: #171d29 !important; border: 1px solid rgba(255, 255, 255, 0.12) !important; color: #ffffff !important; border-radius: 8px; padding: 10px 14px; font-size: 13.5px;">
        </div>
      </div>

      <div>
        <label style="display: block; font-size: 11px; font-weight: 700; color: #cbd5e1; text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 6px;">
          Category <span style="color: #f87171;">*</span>
        </label>
        <select name="display_category" required class="form-tactical" style="background: #171d29 !important; border: 1px solid rgba(255, 255, 255, 0.12) !important; color: #ffffff !important; border-radius: 8px; padding: 10px 14px; font-size: 13.5px;">
          <option value="1" style="background: #171d29; color: #ffffff;">1 - Featured / Large (CEO / Founder Spotlight)</option>
          <option value="2" style="background: #171d29; color: #ffffff;">2 - Standard (Directors & Core Leadership)</option>
          <option value="3" style="background: #171d29; color: #ffffff;">3 - Compact (Faculty Squad & Instructors)</option>
          <option value="4" style="background: #171d29; color: #ffffff;">4 - Mini (Support Cadre & Administrative)</option>
        </select>
      </div>

      <div>
        <label style="display: block; font-size: 11px; font-weight: 700; color: #cbd5e1; text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 6px;">
          Short Bio / Paragraph
        </label>
        <textarea name="bio" rows="4" class="form-tactical" placeholder="Enter biography or leadership statement..." style="background: #171d29 !important; border: 1px solid rgba(255, 255, 255, 0.12) !important; color: #ffffff !important; border-radius: 8px; padding: 10px 14px; font-size: 13.5px; resize: vertical;"></textarea>
      </div>

      <div>
        <label style="display: block; font-size: 11px; font-weight: 700; color: #cbd5e1; text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 6px;">
          Photo
        </label>
        <input type="file" name="photo" class="form-tactical" accept="image/jpeg,image/png,image/webp" style="background: #171d29 !important; border: 1px solid rgba(255, 255, 255, 0.12) !important; color: #94a3b8 !important; border-radius: 8px; padding: 8px 12px; font-size: 12.5px;">
      </div>

      <div style="margin-top: 10px; padding-top: 18px; border-top: 1px solid rgba(255, 255, 255, 0.08); display: flex; justify-content: flex-end; gap: 12px;">
        <button type="button" onclick="closeAddModal()" class="btn-tactical" style="background: rgba(255, 255, 255, 0.06); color: #cbd5e1; border: 1px solid rgba(255, 255, 255, 0.12); padding: 9px 20px; font-size: 13px; font-weight: 600; border-radius: 8px; cursor: pointer;">
          Cancel
        </button>
        <button type="submit" class="btn-tactical" style="background: #85c9cc; color: #082d2f; border: none; padding: 9px 22px; font-size: 13px; font-weight: 800; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 14px rgba(133, 201, 204, 0.35);">
          <i class="fa-solid fa-check"></i> Save Member
        </button>
      </div>
    </form>
  </div>
</div>

<!-- EDIT MODAL -->
<div id="editModal" style="display: none; position: fixed; inset: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.8); z-index: 10000; align-items: center; justify-content: center; backdrop-filter: blur(6px); padding: 20px;">
  <div style="background: #11151f; width: 100%; max-width: 580px; border-radius: 16px; border: 1px solid rgba(255, 255, 255, 0.12); box-shadow: 0 25px 60px rgba(0, 0, 0, 0.65), 0 0 0 1px rgba(255, 255, 255, 0.05); max-height: 90vh; display: flex; flex-direction: column; overflow: hidden;">
    <div style="padding: 18px 24px; border-bottom: 1px solid rgba(255, 255, 255, 0.08); display: flex; justify-content: space-between; align-items: center; background: #161c28;">
      <h3 style="margin: 0; font-size: 15px; font-weight: 800; color: #ffffff; display: flex; align-items: center; gap: 10px;">
        <i class="fa-solid fa-user-pen" style="color: #85c9cc;"></i> Edit Team Member
      </h3>
      <button type="button" onclick="closeEditModal()" style="background: rgba(255, 255, 255, 0.06); border: 1px solid rgba(255, 255, 255, 0.1); color: #94a3b8; cursor: pointer; font-size: 15px; width: 30px; height: 30px; border-radius: 8px; display: flex; align-items: center; justify-content: center; transition: all 0.2s;">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>
    <form id="editForm" method="POST" enctype="multipart/form-data" style="padding: 24px; overflow-y: auto; display: flex; flex-direction: column; gap: 18px;">
      @csrf
      @method('PUT')
      
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; color: #cbd5e1; text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 6px;">
            Name <span style="color: #f87171;">*</span>
          </label>
          <input type="text" name="name" id="edit_name" required class="form-tactical" style="background: #171d29 !important; border: 1px solid rgba(255, 255, 255, 0.12) !important; color: #ffffff !important; border-radius: 8px; padding: 10px 14px; font-size: 13.5px;">
        </div>
        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; color: #cbd5e1; text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 6px;">
            Designation
          </label>
          <input type="text" name="designation" id="edit_designation" class="form-tactical" style="background: #171d29 !important; border: 1px solid rgba(255, 255, 255, 0.12) !important; color: #ffffff !important; border-radius: 8px; padding: 10px 14px; font-size: 13.5px;">
        </div>
      </div>

      <div>
        <label style="display: block; font-size: 11px; font-weight: 700; color: #cbd5e1; text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 6px;">
          Category <span style="color: #f87171;">*</span>
        </label>
        <select name="display_category" id="edit_display_category" required class="form-tactical" style="background: #171d29 !important; border: 1px solid rgba(255, 255, 255, 0.12) !important; color: #ffffff !important; border-radius: 8px; padding: 10px 14px; font-size: 13.5px;">
          <option value="1" style="background: #171d29; color: #ffffff;">1 - Featured / Large (CEO / Founder Spotlight)</option>
          <option value="2" style="background: #171d29; color: #ffffff;">2 - Standard (Directors & Core Leadership)</option>
          <option value="3" style="background: #171d29; color: #ffffff;">3 - Compact (Faculty Squad & Instructors)</option>
          <option value="4" style="background: #171d29; color: #ffffff;">4 - Mini (Support Cadre & Administrative)</option>
        </select>
      </div>

      <div>
        <label style="display: block; font-size: 11px; font-weight: 700; color: #cbd5e1; text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 6px;">
          Short Bio / Paragraph
        </label>
        <textarea name="bio" id="edit_bio" rows="4" class="form-tactical" style="background: #171d29 !important; border: 1px solid rgba(255, 255, 255, 0.12) !important; color: #ffffff !important; border-radius: 8px; padding: 10px 14px; font-size: 13.5px; resize: vertical;"></textarea>
      </div>

      <div>
        <label style="display: block; font-size: 11px; font-weight: 700; color: #cbd5e1; text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 6px;">
          Update Photo
        </label>
        <div id="edit_photo_preview_container" style="display: none; align-items: center; gap: 12px; margin-bottom: 10px; background: #171d29; padding: 8px 12px; border-radius: 8px; border: 1px solid rgba(255, 255, 255, 0.1);">
          <img id="edit_photo_preview" src="" alt="Current Photo" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border: 2px solid #85c9cc;">
          <label style="font-size: 12px; color: #f87171; display: inline-flex; align-items: center; gap: 6px; cursor: pointer; margin: 0;">
            <input type="checkbox" name="remove_photo" id="edit_remove_photo" value="1">
            <span>Remove current photo</span>
          </label>
        </div>
        <input type="file" name="photo" class="form-tactical" accept="image/jpeg,image/png,image/webp" style="background: #171d29 !important; border: 1px solid rgba(255, 255, 255, 0.12) !important; color: #94a3b8 !important; border-radius: 8px; padding: 8px 12px; font-size: 12.5px;">
      </div>

      <div>
        <label style="display: inline-flex; align-items: center; gap: 9px; font-size: 13px; font-weight: 600; color: #cbd5e1; cursor: pointer;">
          <input type="checkbox" name="is_active" id="edit_is_active" value="1" checked style="width: 16px; height: 16px; accent-color: #85c9cc;">
          <span>Active (Display on live About page)</span>
        </label>
      </div>

      <div style="margin-top: 10px; padding-top: 18px; border-top: 1px solid rgba(255, 255, 255, 0.08); display: flex; justify-content: flex-end; gap: 12px;">
        <button type="button" onclick="closeEditModal()" class="btn-tactical" style="background: rgba(255, 255, 255, 0.06); color: #cbd5e1; border: 1px solid rgba(255, 255, 255, 0.12); padding: 9px 20px; font-size: 13px; font-weight: 600; border-radius: 8px; cursor: pointer;">
          Cancel
        </button>
        <button type="submit" class="btn-tactical" style="background: #85c9cc; color: #082d2f; border: none; padding: 9px 22px; font-size: 13px; font-weight: 800; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 14px rgba(133, 201, 204, 0.35);">
          <i class="fa-solid fa-floppy-disk"></i> Save Changes
        </button>
      </div>
    </form>
  </div>
</div>

<script>
const baseTeamUpdateUrl = "{{ url('admin/cms/about/team') }}";

function openAddModal() {
    document.getElementById('addModal').style.display = 'flex';
}
function closeAddModal() {
    document.getElementById('addModal').style.display = 'none';
}

function openEditModal(member) {
    document.getElementById('editForm').action = baseTeamUpdateUrl + '/' + member.id;
    document.getElementById('edit_name').value = member.name || '';
    document.getElementById('edit_designation').value = member.designation || '';
    document.getElementById('edit_display_category').value = member.display_category || 1;
    document.getElementById('edit_bio').value = member.bio || '';
    
    if (document.getElementById('edit_is_active')) {
        document.getElementById('edit_is_active').checked = member.is_active !== false && member.is_active !== 0;
    }
    
    const previewContainer = document.getElementById('edit_photo_preview_container');
    const previewImg = document.getElementById('edit_photo_preview');
    const removeCheck = document.getElementById('edit_remove_photo');
    if (removeCheck) {
        removeCheck.checked = false;
    }
    if (member.photo) {
        previewImg.src = member.photo.startsWith('http') ? member.photo : '{{ asset('') }}' + member.photo;
        previewContainer.style.display = 'flex';
    } else {
        previewContainer.style.display = 'none';
    }
    
    document.getElementById('editModal').style.display = 'flex';
}
function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}

// Close modals when clicking outside
window.onclick = function(event) {
    if (event.target == document.getElementById('addModal')) {
        closeAddModal();
    }
    if (event.target == document.getElementById('editModal')) {
        closeEditModal();
    }
}
</script>

@endsection
