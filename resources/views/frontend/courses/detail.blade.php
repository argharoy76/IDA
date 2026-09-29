@extends('layouts.public')

@section('title', $course->title . ' | Imperial Defence Academy')

@section('content')
<section style="position: relative; background: linear-gradient(135deg, #f0f9fa 0%, #e6f4f5 50%, #f8fafc 100%); border-bottom: 1px solid rgba(133, 201, 204, 0.35); padding: 75px 24px 65px; overflow: hidden;">
  <div style="position: absolute; inset: 0; background: radial-gradient(circle at 50% 20%, rgba(133, 201, 204, 0.25) 0%, transparent 70%); pointer-events: none;"></div>
  <div style="position: relative; z-index: 2; max-width: 1280px; margin: 0 auto;">
    <div data-aos="fade-down" style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px;">
      <a href="{{ route('courses') }}" style="color: #64748b; font-size: 13px; text-decoration: none; font-weight: 600;">Courses</a>
      <span style="color: #cbd5e1;">/</span>
      <span style="color: #082d2f; font-size: 11.5px; font-weight: 800; background: rgba(133, 201, 204, 0.22); padding: 4px 14px; border-radius: 9999px; border: 1px solid #85c9cc; letter-spacing: 0.8px; text-transform: uppercase; box-shadow: 0 2px 8px rgba(133, 201, 204, 0.25);">{{ $course->category }}</span>
    </div>
    <h1 data-aos="zoom-in" data-aos-delay="150" style="font-size: clamp(30px, 4.5vw, 42px); font-weight: 900; color: #082d2f; margin-bottom: 14px; font-family: 'Roboto', sans-serif; letter-spacing: -0.02em;">{{ $course->title }}</h1>
    <p data-aos="fade-up" data-aos-delay="250" style="color: #475569; font-size: 16px; max-width: 800px; line-height: 1.75; font-weight: 400;">{{ $course->description }}</p>
  </div>
</section>

<section style="max-width: 1280px; margin: 50px auto; padding: 0 24px;">
  <style>
    .course-detail-layout {
      display: grid;
      grid-template-columns: 1.8fr 1.2fr;
      gap: 40px;
    }
    @media (max-width: 991px) {
      .course-detail-layout {
        grid-template-columns: 1fr !important;
        gap: 30px !important;
      }
    }
  </style>
  <div class="course-detail-layout">
    <div data-aos="fade-right" data-aos-delay="200">
      <!-- Syllabus Breakdown -->
      <div class="content-panel classical-card">
        <h2 style="font-size: 20px; font-weight: 800; margin-bottom: 16px; color: #082d2f;">Detailed Course Syllabus</h2>
        @if(!empty($course->syllabus))
          <div style="display: flex; flex-direction: column; gap: 12px;">
            @foreach($course->syllabus as $idx => $item)
              <div style="background: var(--surface-subtle); padding: 14px 18px; border-radius: var(--radius-sm); border-left: 3px solid #85c9cc;">
                <strong style="display: block; font-size: 14px; color: #082d2f;">Module {{ $idx + 1 }}</strong>
                <span style="font-size: 13.5px; color: var(--text-main);">{{ $item }}</span>
              </div>
            @endforeach
          </div>
        @else
          <p style="color: var(--text-muted);">Standard military selection board preparatory curriculum.</p>
        @endif
      </div>

      <!-- Features & Outcomes -->
      <div class="content-panel classical-card" style="margin-top: 24px;">
        <h2 style="font-size: 20px; font-weight: 800; margin-bottom: 16px; color: #082d2f;">Key Training Highlights</h2>
        @if(!empty($course->features))
          <div class="grid-cols-2-responsive" style="gap: 14px;">
            @foreach($course->features as $feat)
              <div style="display: flex; align-items: flex-start; gap: 10px;">
                <i class="fa-solid fa-shield-halved" style="color: #082d2f; margin-top: 4px;"></i>
                <span style="font-size: 13.5px; font-weight: 600;">{{ $feat }}</span>
              </div>
            @endforeach
          </div>
        @endif
      </div>
    </div>

    <!-- Sidebar Card: Enrollment & Active Batches -->
    <div data-aos="fade-left" data-aos-delay="300">
      <div class="content-panel classical-card" style="position: sticky; top: 90px;">
        <div style="border-bottom: 1px solid var(--border-soft); padding-bottom: 16px; margin-bottom: 20px;">
          <small style="display: block; font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">COURSE TUITION FEE</small>
          <div style="display: flex; align-items: baseline; gap: 6px; margin-top: 4px;">
            <strong style="font-size: 32px; font-weight: 800; color: var(--brand-deep); font-family: 'Roboto', sans-serif;">৳{{ number_format($course->fee, 0) }}</strong>
            <small style="color: var(--text-muted);">Total Course</small>
          </div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 14px; margin-bottom: 24px;">
          <div style="display: flex; justify-content: space-between; font-size: 13.5px;">
            <span style="color: var(--text-muted);"><i class="fa-regular fa-clock" style="width: 20px;"></i> Duration</span>
            <strong>{{ $course->duration }}</strong>
          </div>
          <div style="display: flex; justify-content: space-between; font-size: 13.5px;">
            <span style="color: var(--text-muted);"><i class="fa-solid fa-flag" style="width: 20px;"></i> Wing</span>
            <strong>{{ $course->category }}</strong>
          </div>
          <div style="display: flex; justify-content: space-between; font-size: 13.5px;">
            <span style="color: var(--text-muted);"><i class="fa-solid fa-user-check" style="width: 20px;"></i> Admission</span>
            <span class="badge badge-emerald">{{ ucfirst($course->admission_status) }}</span>
          </div>
          <div style="display: flex; justify-content: space-between; font-size: 13.5px;">
            <span style="color: var(--text-muted);"><i class="fa-solid fa-calendar-days" style="width: 20px;"></i> Schedule</span>
            <strong style="text-align: right; max-width: 180px;">{{ $course->schedule_info ?? 'Morning & Evening' }}</strong>
          </div>
        </div>

        <!-- Active Batches -->
        <h4 style="font-size: 14px; font-weight: 800; margin-bottom: 12px;">Active Squad Batches</h4>
        @forelse($course->activeBatches as $b)
          <div style="background: var(--surface-subtle); padding: 10px 14px; border-radius: var(--radius-sm); margin-bottom: 10px; font-size: 12.5px;">
            <strong>{{ $b->batch_name }}</strong> ({{ $b->batch_code }})
            <div style="color: var(--text-muted); font-size: 11px; margin-top: 2px;">
              Primary Instructor: {{ $b->primaryInstructor->user->name ?? 'Senior Military Assessor' }}
            </div>
          </div>
        @empty
          <p style="font-size: 12px; color: var(--text-muted); margin-bottom: 14px;">Next batch commencing soon.</p>
        @endforelse

        <a href="{{ route('register', ['type' => 'academic', 'course_id' => $course->id]) }}" class="btn-primary" style="width: 100%; justify-content: center; padding: 14px; font-size: 14px; margin-top: 14px;">
          <i class="fa-solid fa-file-signature"></i> Apply for Direct Admission
        </a>
      </div>
    </div>
  </div>
</section>
@endsection
