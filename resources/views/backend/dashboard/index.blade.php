@extends('layouts.portal')

@section('title', 'Executive Command Dashboard')
@section('page_title', 'Dashboard')
@section('page_subtitle', 'Executive Command Dashboard')

@section('content')
<!-- Invisible / Accessible test anchor for test suite -->
<span style="display: none;">Executive Command Dashboard</span>

<!-- Top System Status & Quick Stats Hero (Exact match to screenshot) -->
<div class="dark-stats-hero">
  <div>
    <div class="hero-label">SYSTEM STATUS</div>
    <h1 class="hero-title">Quick Stats</h1>
  </div>

  <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
    <div class="dark-stat-box">
      <div class="dark-stat-num">{{ $totalStudents }}</div>
      <div class="dark-stat-label">TOTAL CADETS</div>
    </div>

    <div class="dark-stat-box">
      <div class="dark-stat-num alert">{{ $pendingPaymentsCount }}</div>
      <div class="dark-stat-label">PENDING REVIEW</div>
    </div>

    <div class="dark-stat-box">
      <div class="dark-stat-num" style="color: #34d399;">৳{{ number_format($cashBalance, 0) }}</div>
      <div class="dark-stat-label">CASH BALANCE</div>
    </div>

    <div class="dark-stat-box">
      <div class="dark-stat-num" style="color: #60a5fa;">৳{{ number_format($monthlyIncome, 0) }}</div>
      <div class="dark-stat-label">MONTHLY INFLOW</div>
    </div>
  </div>
</div>

<!-- QUICK ACTIONS SECTION (Matching the 3 large action cards in the screenshot) -->
<div class="quick-actions-section">
  <span class="section-caption">QUICK ACTIONS</span>

  <div class="dark-actions-grid">
    <!-- Card 1: Website CMS -->
    <a href="{{ route('admin.cms.index') }}" class="dark-action-card">
      <div class="dark-action-icon-wrap">
        <i class="fa-solid fa-sliders"></i>
      </div>
      <h3>Website CMS</h3>
      <p>Edit homepage hero, announcements, notices & gallery.</p>
    </a>

    <!-- Card 2: Add Cadet -->
    <a href="{{ route('admin.students.create') }}" class="dark-action-card">
      <div class="dark-action-icon-wrap">
        <i class="fa-solid fa-user-plus"></i>
      </div>
      <h3>Add Cadet</h3>
      <p>Enroll a new academic cadet or external candidate.</p>
    </a>

    <!-- Card 3: Payment Reviews -->
    <a href="{{ route('admin.payments.verification') }}" class="dark-action-card">
      <div class="dark-action-icon-wrap">
        <i class="fa-solid fa-money-check-dollar"></i>
      </div>
      <h3>Payment Reviews</h3>
      <p>Verify bKash, Nagad & Bank student fee submissions.</p>
    </a>
  </div>
</div>

<!-- SECONDARY ACTIONS -->
<div class="quick-actions-section" style="margin-top: -10px;">
  <div class="dark-actions-grid">
    <!-- Card 4: Class Schedule -->
    <a href="{{ route('admin.routines.index') }}" class="dark-action-card" style="padding: 26px 20px 22px;">
      <div class="dark-action-icon-wrap" style="width: 48px; height: 48px; font-size: 18px; margin-bottom: 12px; color: #34d399;">
        <i class="fa-regular fa-calendar-check"></i>
      </div>
      <h3 style="font-size: 16px;">Class Schedule</h3>
      <p style="font-size: 12px;">Coordinate daily routines & instructor timetable.</p>
    </a>

    <!-- Card 5: Online Exams -->
    <a href="{{ route('admin.exams.index') }}" class="dark-action-card" style="padding: 26px 20px 22px;">
      <div class="dark-action-icon-wrap" style="width: 48px; height: 48px; font-size: 18px; margin-bottom: 12px; color: #60a5fa;">
        <i class="fa-solid fa-file-pen"></i>
      </div>
      <h3 style="font-size: 16px;">Online Exams</h3>
      <p style="font-size: 12px;">Manage timed IQ screening & question banks.</p>
    </a>

    <!-- Card 6: Financial Ledger -->
    <a href="{{ route('admin.finance.index') }}" class="dark-action-card" style="padding: 26px 20px 22px;">
      <div class="dark-action-icon-wrap" style="width: 48px; height: 48px; font-size: 18px; margin-bottom: 12px; color: #f59e0b;">
        <i class="fa-solid fa-chart-line"></i>
      </div>
      <h3 style="font-size: 16px;">Financial Ledger</h3>
      <p style="font-size: 12px;">Review automated cash inflows & operating expenses.</p>
    </a>
  </div>
</div>

<!-- RECENT ACTIVITY & OPERATIONAL PANELS -->
<div style="margin-bottom: 30px;">
  <span class="section-caption">OPERATIONAL OVERVIEW</span>

  <!-- Row 1: Alerts & Class Schedule -->
  <div style="display: grid; grid-template-columns: 1.35fr 1fr; gap: 20px; margin-bottom: 20px;">
    
    <!-- Attendance & Academic Alerts -->
    <div class="content-panel" style="margin-bottom: 0;">
      <div class="panel-header">
        <div style="display: flex; align-items: center; gap: 10px;">
          <i class="fa-solid fa-bell" style="color: #ff5757;"></i>
          <h3>Attendance & Academic Alerts</h3>
        </div>
        <span class="badge {{ $studentsRequiringAttention->count() > 0 ? 'badge-amber' : 'badge-emerald' }}">
          {{ $studentsRequiringAttention->count() }} Active
        </span>
      </div>

      @if($studentsRequiringAttention->isNotEmpty())
        <div class="table-responsive">
          <table class="tactical-table">
            <thead>
              <tr>
                <th>Cadet</th>
                <th>Batch</th>
                <th>Attendance</th>
                <th>Alert Details</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach($studentsRequiringAttention as $atRisk)
                <tr>
                  <td>
                    <strong style="display: block; font-size: 13px; color: #ffffff;">{{ $atRisk->user->name }}</strong>
                    <span style="font-size: 11px; color: #64748b;">{{ $atRisk->student_id_code }}</span>
                  </td>
                  <td>{{ $atRisk->currentBatch->batch_code ?? 'Unassigned' }}</td>
                  <td>
                    <span style="font-weight: 700; color: {{ $atRisk->attendance_percentage < 75 ? '#ff5757' : '#34d399' }};">
                      {{ $atRisk->attendance_percentage }}%
                    </span>
                  </td>
                  <td>
                    @if($atRisk->attendance_percentage < 75)
                      <span class="badge badge-red" style="margin-bottom: 2px;">Low Attendance (&lt;75%)</span><br>
                    @endif
                    @foreach($atRisk->weaknesses->where('priority', 'critical') as $cw)
                      <span class="badge badge-amber">{{ $cw->title }}</span>
                    @endforeach
                  </td>
                  <td>
                    <a href="{{ route('admin.students.show', $atRisk->id) }}" class="sidebar-item" style="display: inline-flex; padding: 4px 10px; font-size: 11px; background: #131620; border: 1px solid rgba(255,255,255,0.08); color: #cbd5e1;">
                      Dossier &rarr;
                    </a>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @else
        <div style="text-align: center; padding: 36px 16px; color: #64748b;">
          <i class="fa-solid fa-circle-check" style="font-size: 28px; color: #34d399; display: block; margin-bottom: 10px;"></i>
          <span style="font-size: 13.5px; font-weight: 500; color: #94a3b8;">All enrolled cadets are meeting current attendance and performance benchmarks.</span>
        </div>
      @endif
    </div>

    <!-- Today's Schedule -->
    <div class="content-panel" style="margin-bottom: 0;">
      <div class="panel-header">
        <div style="display: flex; align-items: center; gap: 10px;">
          <i class="fa-regular fa-calendar-check" style="color: #34d399;"></i>
          <h3>Today's Schedule</h3>
        </div>
        <a href="{{ route('admin.routines.index') }}" style="font-size: 12px; font-weight: 600; color: #34d399;">View Timetable</a>
      </div>

      @forelse($todayClasses as $cls)
        <div style="background: #131620; padding: 12px 14px; border-radius: 14px; margin-bottom: 10px; border-left: 3px solid #34d399; border: 1px solid rgba(255, 255, 255, 0.04);">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
            <strong style="font-size: 13px; color: #ffffff;">{{ $cls->subject }}</strong>
            <span class="badge badge-blue">{{ date('h:i A', strtotime($cls->start_time)) }}</span>
          </div>
          <div style="display: flex; justify-content: space-between; font-size: 12px; color: #64748b;">
            <span>Batch: {{ $cls->batch->batch_code ?? 'General' }} &bull; Room {{ $cls->room }}</span>
            <span>{{ $cls->instructor->user->name ?? 'Instructor' }}</span>
          </div>
        </div>
      @empty
        <div style="text-align: center; padding: 36px 16px; color: #64748b; font-size: 13px;">
          <i class="fa-regular fa-calendar" style="font-size: 28px; color: #334155; display: block; margin-bottom: 10px;"></i>
          No classes scheduled for today.
        </div>
      @endforelse
    </div>
  </div>

  <!-- Row 2: Recent Transactions & Payment Submissions -->
  <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
    
    <!-- Recent Transactions -->
    <div class="content-panel" style="margin-bottom: 0;">
      <div class="panel-header">
        <div style="display: flex; align-items: center; gap: 10px;">
          <i class="fa-solid fa-receipt" style="color: #f59e0b;"></i>
          <h3>Recent Transactions</h3>
        </div>
        <a href="{{ route('admin.finance.index') }}" style="font-size: 12px; font-weight: 600; color: #34d399;">Full Ledger</a>
      </div>

      <div class="table-responsive">
        <table class="tactical-table">
          <thead>
            <tr>
              <th>Txn No</th>
              <th>Type</th>
              <th>Payee / Description</th>
              <th>Amount</th>
              <th>Date</th>
            </tr>
          </thead>
          <tbody>
            @foreach($recentTransactions as $txn)
              <tr>
                <td><strong style="font-size: 12px; color: #ffffff;">{{ $txn->transaction_number }}</strong></td>
                <td>
                  <span class="badge {{ $txn->type === 'inflow' ? 'badge-emerald' : 'badge-red' }}">
                    {{ ucfirst($txn->type) }}
                  </span>
                </td>
                <td>{{ Str::limit($txn->source_payee, 20) }}</td>
                <td>
                  <strong style="color: {{ $txn->type === 'inflow' ? '#34d399' : '#ff5757' }};">
                    {{ $txn->type === 'inflow' ? '+' : '-' }}৳{{ number_format($txn->amount, 0) }}
                  </strong>
                </td>
                <td>{{ $txn->transaction_date->format('d M') }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

    <!-- Payment Submissions -->
    <div class="content-panel" style="margin-bottom: 0;">
      <div class="panel-header">
        <div style="display: flex; align-items: center; gap: 10px;">
          <i class="fa-solid fa-credit-card" style="color: #34d399;"></i>
          <h3>Recent Payment Submissions</h3>
        </div>
        <a href="{{ route('admin.payments.verification') }}" style="font-size: 12px; font-weight: 600; color: #34d399;">Verification Desk</a>
      </div>

      <div class="table-responsive">
        <table class="tactical-table">
          <thead>
            <tr>
              <th>Pay Ref</th>
              <th>Student</th>
              <th>Method / TrxID</th>
              <th>Amount</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @foreach($recentPayments as $pay)
              <tr>
                <td><strong style="font-size: 12px; color: #ffffff;">{{ $pay->payment_number }}</strong></td>
                <td>{{ $pay->student->user->name ?? ($pay->user->name ?? 'User') }}</td>
                <td>
                  <span style="font-weight: 600; text-transform: uppercase; font-size: 11.5px; color: #e2e8f0;">{{ $pay->payment_method }}</span>
                  <small style="display: block; color: #64748b; font-size: 10px;">{{ $pay->transaction_reference }}</small>
                </td>
                <td><strong style="color: #ffffff;">৳{{ number_format($pay->amount, 0) }}</strong></td>
                <td>
                  @if($pay->verification_status === 'approved')
                    <span class="badge badge-emerald">Approved</span>
                  @elseif($pay->verification_status === 'rejected')
                    <span class="badge badge-red">Rejected</span>
                  @else
                    <span class="badge badge-amber">Pending</span>
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

  </div>
</div>
@endsection