@extends('layouts.public')

@section('title', 'Online Assessment Tests | Imperial Defence Academy')

@section('content')
<section style="background: linear-gradient(135deg, #022c22 0%, #064e3b 100%); color: #ffffff; padding: 48px 20px 40px 20px; border-bottom: 3px solid var(--accent-gold);">
  <div style="max-width: 1200px; margin: 0 auto; text-align: center;">
    <span class="badge" style="background: rgba(255,255,255,0.15); color: #fff; font-size: 11px; padding: 4px 12px; border-radius: 6px; margin-bottom: 12px; display: inline-block;">
      CANDIDATE ASSESSMENT MODULES
    </span>
    <h1 style="font-size: 30px; font-weight: 800; margin: 0 0 8px 0; font-family: 'Poppins', sans-serif;">
      Military Online Assessment Catalog
    </h1>
    <p style="font-size: 14.5px; color: #cbd5e1; max-width: 680px; margin: 0 auto;">
      Computerized screening test modules designed to simulate Bangladesh Armed Forces ISSB standards.
    </p>
  </div>
</section>

<div style="max-width: 1200px; margin: 36px auto 60px auto; padding: 0 20px;">
  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px;">
    @forelse($exams as $test)
      <div class="content-panel classical-card" style="display: flex; flex-direction: column; justify-content: space-between; border-top: 4px solid {{ $test->branchColor() ?? '#059669' }}; padding: 24px; border-radius: 14px; background: #fff; box-shadow: 0 4px 20px rgba(0,0,0,0.05);">
        <div>
          <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
            <span class="badge" style="background: {{ $test->branchColor() ?? '#059669' }}; color: #fff; font-size: 11px;">
              <i class="fa-solid {{ $test->branchIcon() ?? 'fa-award' }}"></i> {{ strtoupper($test->branch ?? 'TEST') }}
            </span>
            <span class="badge badge-navy" style="font-size: 10.5px;">{{ strtoupper(str_replace('_', ' ', $test->exam_type)) }}</span>
          </div>

          <h3 style="font-size: 18px; font-weight: 800; margin: 0 0 8px 0; color: var(--brand-deep); font-family: 'Poppins', sans-serif;">
            {{ $test->title }}
          </h3>
          <p style="font-size: 13px; color: var(--text-muted); line-height: 1.6; margin: 0 0 16px 0;">
            {{ $test->description ?? 'Simulates official ISSB computerized intelligence screening.' }}
          </p>

          <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; background: #f8fafc; padding: 12px; border-radius: 8px; margin-bottom: 16px; border: 1px solid #e2e8f0;">
            <div style="text-align: center;">
              <div style="font-size: 10px; color: #64748b; text-transform: uppercase;">Duration</div>
              <strong style="font-size: 13px; color: #0284c7;">{{ $test->duration_minutes }} min</strong>
            </div>
            <div style="text-align: center; border-left: 1px solid #e2e8f0; border-right: 1px solid #e2e8f0;">
              <div style="font-size: 10px; color: #64748b; text-transform: uppercase;">Total Marks</div>
              <strong style="font-size: 13px; color: #d97706;">{{ $test->total_marks }}</strong>
            </div>
            <div style="text-align: center;">
              <div style="font-size: 10px; color: #64748b; text-transform: uppercase;">Pass Mark</div>
              <strong style="font-size: 13px; color: #059669;">{{ $test->pass_marks }}</strong>
            </div>
          </div>
        </div>

        <a href="{{ route('external.tests.start', $test->id) }}" class="btn-primary" style="justify-content: center; text-align: center; text-decoration: none; padding: 10px 18px;">
          <i class="fa-solid fa-play"></i> Start Assessment Test
        </a>
      </div>
    @empty
      <div style="grid-column: span 3; text-align: center; padding: 48px; color: var(--text-muted);" class="content-panel">
        No assessment tests open at the moment.
      </div>
    @endforelse
  </div>
</div>
@endsection
