@extends('layouts.portal')

@section('title', 'Student Details: ' . $student->user->name)
@section('page_title', 'Student Details')

@section('content')
<div style="max-width: 1300px; margin: 0 auto;">

  <!-- Top Action Bar -->
  <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 24px;">
    <a href="{{ route('admin.student_accounts.index') }}" class="btn-tactical" style="background: rgba(255,255,255,0.06); color: #94a3b8; border: 1px solid rgba(255,255,255,0.12); padding: 8px 16px; font-size: 13px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
      <i class="fa-solid fa-arrow-left"></i> Back to Student Management
    </a>

    <div style="display: flex; gap: 10px;">
      <a href="{{ route('admin.student_accounts.edit', $student->id) }}" class="btn-tactical" style="background: #ff5757; color: #ffffff; padding: 8px 18px; font-size: 13px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; border: none; border-radius: 8px;">
        <i class="fa-solid fa-user-pen"></i> Edit Student Details
      </a>
    </div>
  </div>

  @if(session('success'))
    <div style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; padding: 14px 18px; margin-bottom: 24px; color: #cbd5e1; font-size: 13.5px; display: flex; align-items: center; gap: 10px;">
      <i class="fa-solid fa-circle-check" style="font-size: 16px; color: #ff5757;"></i>
      <span>{{ session('success') }}</span>
    </div>
  @endif

  @if(session('error'))
    <div style="background: rgba(239,68,68,0.12); border: 1px solid rgba(239,68,68,0.35); border-radius: 10px; padding: 14px 18px; margin-bottom: 24px; color: #f87171; font-size: 13.5px; display: flex; align-items: center; gap: 10px;">
      <i class="fa-solid fa-triangle-exclamation" style="font-size: 16px;"></i>
      <span>{{ session('error') }}</span>
    </div>
  @endif

  <!-- Top 2-Column Grid: Identity & Course Privileges -->
  <div style="display: grid; grid-template-columns: 1fr 1.25fr; gap: 24px; align-items: start; margin-bottom: 24px;">
    
    <!-- LEFT COLUMN: Identity & Demographic Details -->
    <div style="display: flex; flex-direction: column; gap: 24px;">
      
      <!-- Identity & Login Credentials Card -->
      <div class="content-panel" style="margin-bottom: 0;">
        <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 20px; padding-bottom: 18px; border-bottom: 1px solid rgba(255,255,255,0.06);">
          <div style="width: 58px; height: 58px; border-radius: 14px; background: #1c202d; color: #ff5757; display: grid; place-items: center; font-size: 24px; font-weight: 800; border: 1px solid rgba(255,87,87,0.3); flex-shrink: 0;">
            {{ strtoupper(substr($student->user->name, 0, 1)) }}
          </div>
          <div>
            <h2 style="font-size: 20px; font-weight: 800; color: #ffffff; margin: 0 0 4px 0;">{{ $student->user->name }}</h2>
            <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
              @php
                $isOffline = in_array($student->student_type, ['offline', 'academic']);
              @endphp
              <span style="font-size: 10.5px; font-weight: 600; color: #cbd5e1; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); border-radius: 6px; padding: 3px 8px;">
                <i class="fa-solid {{ $isOffline ? 'fa-building-columns' : 'fa-globe' }}" style="color: #8c96a8;"></i>
                {{ $isOffline ? 'Offline Enrolled (Staff)' : 'Online Self-Registered' }}
              </span>
              <span style="font-size: 10.5px; font-weight: 600; color: #ff8585; background: rgba(255,87,87,0.1); border: 1px solid rgba(255,87,87,0.25); border-radius: 6px; padding: 3px 8px;">
                <i class="fa-solid fa-circle-check"></i> Active Account
              </span>
            </div>
          </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
          <div>
            <small style="display: block; font-size: 10.5px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Assigned Login ID</small>
            @php
              $loginId = $student->student_id_code ?: ($student->user->account_id ?? 'UNSET');
            @endphp
            <div style="display: inline-flex; align-items: center; gap: 6px; background: rgba(255,87,87,0.12); border: 1px solid rgba(255,87,87,0.3); padding: 5px 10px; border-radius: 6px;">
              <span style="font-family: monospace; font-size: 14px; font-weight: 800; color: #ff8585;">{{ $loginId }}</span>
            </div>
          </div>

          <div>
            <small style="display: block; font-size: 10.5px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Registration Date</small>
            <strong style="font-size: 13px; color: #cbd5e1;">{{ $student->created_at ? $student->created_at->format('d M Y, h:i A') : 'N/A' }}</strong>
          </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr; gap: 12px; border-top: 1px solid rgba(255,255,255,0.06); padding-top: 16px;">
          <div>
            <small style="display: block; font-size: 10.5px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 2px;">Email Address</small>
            <a href="mailto:{{ $student->user->email }}" style="color: #cbd5e1; text-decoration: none; font-size: 13px;">
              <i class="fa-regular fa-envelope" style="margin-right: 4px; color: #8c96a8;"></i> {{ $student->user->email }}
            </a>
          </div>
          <div>
            <small style="display: block; font-size: 10.5px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 2px;">Phone / Mobile</small>
            <a href="tel:{{ $student->user->phone }}" style="color: #cbd5e1; text-decoration: none; font-size: 13px;">
              <i class="fa-solid fa-phone" style="margin-right: 4px; color: #8c96a8;"></i> {{ $student->user->phone ?: 'Not provided' }}
            </a>
          </div>
        </div>
      </div>

      <!-- Demographic & Academic Card -->
      <div class="content-panel" style="margin-bottom: 0;">
        <div class="panel-header" style="margin-bottom: 16px;">
          <h3 style="font-size: 14px; font-weight: 800; margin: 0; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-address-card" style="color: #8c96a8;"></i> Demographic & Academic Info
          </h3>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
          <div>
            <small style="display: block; font-size: 10.5px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 2px;">Age</small>
            <strong style="color: #ffffff; font-size: 13px;">{{ $student->age ? $student->age . ' years' : 'N/A' }}</strong>
          </div>
          <div>
            <small style="display: block; font-size: 10.5px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 2px;">Gender</small>
            <strong style="color: #ffffff; font-size: 13px; text-transform: capitalize;">{{ $student->gender ?: 'N/A' }}</strong>
          </div>
          <div>
            <small style="display: block; font-size: 10.5px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 2px;">Target Wing</small>
            <span style="font-size: 11px; font-weight: 600; color: #cbd5e1; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); border-radius: 6px; padding: 2px 8px; display: inline-flex; align-items: center;">
              <i class="fa-solid fa-jet-fighter" style="margin-right: 4px; color: #8c96a8;"></i> {{ $student->target_wing ?: 'General Military' }}
            </span>
          </div>
          <div>
            <small style="display: block; font-size: 10.5px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 2px;">HSC / Passing Year</small>
            <strong style="color: #ffffff; font-size: 13px;">{{ $student->hsc_year ?: 'N/A' }}</strong>
          </div>
          <div style="grid-column: span 2;">
            <small style="display: block; font-size: 10.5px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 2px;">Educational Institution</small>
            <strong style="color: #ffffff; font-size: 13px;">{{ $student->institution ?: 'Not specified' }}</strong>
          </div>
          <div style="grid-column: span 2;">
            <small style="display: block; font-size: 10.5px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 2px;">Home District</small>
            <strong style="color: #ffffff; font-size: 13px;">{{ $student->district ?: 'Not specified' }}</strong>
          </div>
          <div style="grid-column: span 2;">
            <small style="display: block; font-size: 10.5px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 2px;">Full Postal Address</small>
            <span style="color: #cbd5e1; font-size: 13px; line-height: 1.5; display: block;">{{ $student->address ?: 'Not provided' }}</span>
          </div>
        </div>
      </div>

    </div>

    <!-- RIGHT COLUMN: Enrolled Courses & Permissions -->
    <div style="display: flex; flex-direction: column; gap: 24px;">
      
      <!-- Course Privileges Card -->
      <div class="content-panel" style="margin-bottom: 0;">
        <div class="panel-header" style="margin-bottom: 18px; display: flex; justify-content: space-between; align-items: center;">
          <h3 style="font-size: 15px; font-weight: 800; margin: 0; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-graduation-cap" style="color: #ff5757;"></i>
            <span>Enrolled Courses & Permissions</span>
          </h3>
          @php
            $activeCourses = $student->courses;
            if ($activeCourses->isEmpty() && $student->currentCourse) {
              $activeCourses = collect([$student->currentCourse]);
            }
          @endphp
          <span style="font-size: 11px; font-weight: 600; color: #ff8585; background: rgba(255,87,87,0.1); border: 1px solid rgba(255,87,87,0.25); border-radius: 6px; padding: 3px 8px;">
            {{ $activeCourses->count() }} Course(s) Active
          </span>
        </div>

        @if($activeCourses->isNotEmpty())
          <div style="display: flex; flex-direction: column; gap: 12px;">
            @foreach($activeCourses as $course)
              <div style="background: rgba(255,255,255,0.025); border: 1px solid rgba(255,255,255,0.08); border-radius: 12px; padding: 16px;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; margin-bottom: 8px;">
                  <div>
                    <h4 style="font-size: 14px; font-weight: 700; color: #ffffff; margin: 0 0 4px 0;">{{ $course->title }}</h4>
                    <span style="font-size: 11px; color: #cbd5e1; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); padding: 2px 8px; border-radius: 4px; font-weight: 600;">
                      Code: {{ $course->course_code ?? 'IDA-' . $course->id }}
                    </span>
                  </div>
                  <span style="font-size: 10.5px; font-weight: 600; color: #cbd5e1; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); border-radius: 6px; padding: 3px 8px; display: inline-flex; align-items: center; gap: 4px;">
                    <i class="fa-solid fa-lock-open" style="color: #ff5757;"></i> Full Access Granted
                  </span>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(110px, 1fr)); gap: 10px; font-size: 12px; color: #94a3b8; margin-top: 12px; border-top: 1px solid rgba(255,255,255,0.04); padding-top: 10px;">
                  <div>
                    <span style="color: #64748b; display: block; font-size: 10px; text-transform: uppercase;">Course Fee</span>
                    <strong style="color: #ffffff; font-size: 13px;">৳{{ number_format($course->course_fee, 0) }}</strong>
                  </div>
                  <div>
                    <span style="color: #64748b; display: block; font-size: 10px; text-transform: uppercase;">Duration</span>
                    <strong style="color: #cbd5e1; font-size: 13px;">{{ $course->duration_weeks ?? 'N/A' }} weeks</strong>
                  </div>
                  <div>
                    <span style="color: #64748b; display: block; font-size: 10px; text-transform: uppercase;">Course Payment</span>
                    @php
                      $cPayStatus = $course->pivot->payment_status ?? 'paid';
                    @endphp
                    @if($cPayStatus === 'paid')
                      <span style="color: #cbd5e1; font-weight: 600;"><i class="fa-solid fa-circle-check" style="color: #ff5757;"></i> Paid</span>
                    @else
                      <span style="color: #8c96a8; font-weight: 600;"><i class="fa-solid fa-clock"></i> {{ ucfirst($cPayStatus) }}</span>
                    @endif
                  </div>
                  <div>
                    <span style="color: #64748b; display: block; font-size: 10px; text-transform: uppercase;">Enrolled On</span>
                    <strong style="color: #cbd5e1; font-size: 12px;">
                      {{ $course->pivot && $course->pivot->enrolled_at ? \Carbon\Carbon::parse($course->pivot->enrolled_at)->format('d M Y') : 'Active' }}
                    </strong>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        @else
          <!-- Free Account Banner -->
          <div style="text-align: center; padding: 36px 20px; background: rgba(255,255,255,0.02); border: 1px dashed rgba(255,255,255,0.12); border-radius: 12px;">
            <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(255,255,255,0.06); color: #8c96a8; display: grid; place-items: center; font-size: 20px; margin: 0 auto 12px;">
              <i class="fa-solid fa-circle-exclamation"></i>
            </div>
            <h4 style="font-size: 14px; font-weight: 700; color: #ffffff; margin: 0 0 14px 0;">No Courses Assigned</h4>
            <a href="{{ route('admin.student_accounts.edit', $student->id) }}" class="btn-tactical" style="background: #ff5757; color: #ffffff; border: none; padding: 8px 16px; font-size: 12.5px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; border-radius: 8px;">
              <i class="fa-solid fa-graduation-cap"></i> Assign Courses
            </a>
          </div>
        @endif
      </div>

      <!-- Quick Action Navigation Card -->
      <div class="content-panel" style="margin-bottom: 0;">
        <div class="panel-header" style="margin-bottom: 14px;">
          <h3 style="font-size: 14px; font-weight: 800; margin: 0;">
            <i class="fa-solid fa-bolt" style="color: #8c96a8;"></i> Management Quick Actions
          </h3>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
          <a href="{{ route('admin.student_accounts.edit', $student->id) }}" class="btn-tactical" style="background: rgba(255,255,255,0.04); color: #cbd5e1; border: 1px solid rgba(255,255,255,0.08); padding: 10px; font-size: 12px; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 8px;">
            <i class="fa-solid fa-id-card-clip" style="color: #ff8585;"></i>
            <span>Change Login ID</span>
          </a>

          <a href="{{ route('admin.student_accounts.edit', $student->id) }}" class="btn-tactical" style="background: rgba(255,255,255,0.04); color: #cbd5e1; border: 1px solid rgba(255,255,255,0.08); padding: 10px; font-size: 12px; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 8px;">
            <i class="fa-solid fa-key" style="color: #8c96a8;"></i>
            <span>Reset Password</span>
          </a>
        </div>
      </div>

    </div>

  </div>


  <!-- ================================================================= -->
  <!-- FULL WIDTH SECTION: PAYMENT & BILLING DETAILS (AS REQUESTED)       -->
  <!-- ================================================================= -->
  <div class="content-panel" style="margin-bottom: 30px;">
    
    <!-- Section Title & Status Badge -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; padding-bottom: 18px; margin-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.06);">
      <h3 style="font-size: 16px; font-weight: 800; color: #fff; margin: 0; display: flex; align-items: center; gap: 10px;">
        <i class="fa-solid fa-file-invoice-dollar" style="color: #ff5757;"></i>
        <span>Fee Invoices &amp; Payment Records</span>
      </h3>
      <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
        @if($totalDue <= 0 && ($totalPaid > 0 || $totalBilled > 0))
          <span style="font-size: 12px; font-weight: 600; color: #cbd5e1; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); border-radius: 6px; padding: 5px 12px; display: inline-flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-circle-check" style="color: #ff5757;"></i> Account Fully Settled (No Dues)
          </span>
        @elseif($totalDue > 0)
          <span style="font-size: 12px; font-weight: 600; color: #f87171; background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.25); border-radius: 6px; padding: 5px 12px; display: inline-flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-triangle-exclamation"></i> Outstanding Due: ৳{{ number_format($totalDue, 2) }}
          </span>
        @else
          <span style="font-size: 12px; font-weight: 600; color: #8c96a8; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); border-radius: 6px; padding: 5px 12px; display: inline-flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-circle-info"></i> Free Candidate Account
          </span>
        @endif

        <a href="{{ route('admin.student_accounts.edit', $student->id) }}#payment_section"
           class="btn-tactical"
           style="background: rgba(255,87,87,0.1); color: #ff8585; border: 1px solid rgba(255,87,87,0.25); padding: 5px 12px; font-size: 12px; font-weight: 600; text-decoration: none; border-radius: 6px; display: inline-flex; align-items: center; gap: 6px;"
           title="Change / Update Payment Details in Edit Details page">
          <i class="fa-solid fa-pen-to-square"></i>
          <span>Change Payment Details</span>
        </a>
      </div>
    </div>

    <!-- Financial Stats Strip -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 14px; margin-bottom: 24px;">
      
      <div style="background: #0f121a; border: 1px solid rgba(255,255,255,0.06); border-radius: 12px; padding: 14px 18px;">
        <div style="font-size: 11px; font-weight: 700; color: #8c96a8; text-transform: uppercase; letter-spacing: 0.5px;">Total Verified Paid</div>
        <div style="font-size: 22px; font-weight: 800; color: #ffffff; margin-top: 4px;">৳{{ number_format($totalPaid, 2) }}</div>
        <small style="color: #64748b; font-size: 10.5px;">Approved tuition & enrollment fees</small>
      </div>

      <div style="background: #0f121a; border: 1px solid rgba(255,255,255,0.06); border-radius: 12px; padding: 14px 18px;">
        <div style="font-size: 11px; font-weight: 700; color: #8c96a8; text-transform: uppercase; letter-spacing: 0.5px;">Total Invoiced Amount</div>
        <div style="font-size: 22px; font-weight: 800; color: #ffffff; margin-top: 4px;">৳{{ number_format($totalBilled, 2) }}</div>
        <small style="color: #64748b; font-size: 10.5px;">{{ $invoices->count() }} invoice(s) generated</small>
      </div>

      <div style="background: #0f121a; border: 1px solid rgba(255,255,255,0.06); border-radius: 12px; padding: 14px 18px;">
        <div style="font-size: 11px; font-weight: 700; color: {{ $totalDue > 0 ? '#f87171' : '#8c96a8' }}; text-transform: uppercase; letter-spacing: 0.5px;">Current Due Balance</div>
        <div style="font-size: 22px; font-weight: 800; color: {{ $totalDue > 0 ? '#f87171' : '#ffffff' }}; margin-top: 4px;">৳{{ number_format($totalDue, 2) }}</div>
        <small style="color: #64748b; font-size: 10.5px;">{{ $totalDue <= 0 ? 'All fees settled' : 'Payment required' }}</small>
      </div>

      <div style="background: #0f121a; border: 1px solid rgba(255,255,255,0.06); border-radius: 12px; padding: 14px 18px;">
        <div style="font-size: 11px; font-weight: 700; color: #8c96a8; text-transform: uppercase; letter-spacing: 0.5px;">Submitted Transactions</div>
        <div style="font-size: 22px; font-weight: 800; color: #ffffff; margin-top: 4px;">{{ $payments->count() }}</div>
        <small style="color: #64748b; font-size: 10.5px;">Total payment vouchers recorded</small>
      </div>

    </div>

    <!-- 1. SUBMITTED PAYMENT TRANSACTIONS -->
    <div style="margin-bottom: 28px;">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
        <h4 style="font-size: 14px; font-weight: 700; color: #ffffff; margin: 0; display: flex; align-items: center; gap: 8px;">
          <i class="fa-solid fa-receipt" style="color: #8c96a8;"></i> Submitted Payment Transactions
        </h4>
        <span style="font-size: 11px; color: #64748b;">{{ $payments->count() }} payment record(s)</span>
      </div>

      @if($payments->isNotEmpty())
        <div style="background: #0f121a; border: 1px solid rgba(255,255,255,0.06); border-radius: 10px; overflow: hidden;">
          <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
            <thead>
              <tr style="border-bottom: 1px solid rgba(255,255,255,0.06); text-align: left; color: #64748b; font-size: 10.5px; text-transform: uppercase; letter-spacing: 0.6px; background: rgba(255,255,255,0.02);">
                <th style="padding: 10px 14px;">Voucher #</th>
                <th style="padding: 10px 14px;">Invoice Ref</th>
                <th style="padding: 10px 14px;">Payment Method</th>
                <th style="padding: 10px 14px;">Transaction ID</th>
                <th style="padding: 10px 14px;">Amount Paid</th>
                <th style="padding: 10px 14px;">Date</th>
                <th style="padding: 10px 14px; text-align: right;">Status</th>
              </tr>
            </thead>
            <tbody>
              @foreach($payments as $p)
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.03); transition: background 0.15s;" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background='transparent'">
                  
                  {{-- Payment Number --}}
                  <td style="padding: 12px 14px;">
                    <code style="background: rgba(255,255,255,0.06); color: #cbd5e1; border: 1px solid rgba(255,255,255,0.1); padding: 2px 7px; border-radius: 4px; font-family: monospace; font-size: 11.5px; font-weight: 700;">
                      {{ $p->payment_number }}
                    </code>
                  </td>

                  {{-- Invoice Reference --}}
                  <td style="padding: 12px 14px; color: #cbd5e1;">
                    @if($p->invoice)
                      <span style="font-family: monospace; color: #cbd5e1; font-size: 11.5px;">{{ $p->invoice->invoice_number }}</span>
                    @else
                      <span style="color: #64748b;">Direct Payment</span>
                    @endif
                  </td>

                  {{-- Payment Method --}}
                  <td style="padding: 12px 14px;">
                    <span style="background: rgba(255,255,255,0.06); color: #cbd5e1; border: 1px solid rgba(255,255,255,0.1); padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; text-transform: uppercase;">
                      {{ $p->payment_method }}
                    </span>
                  </td>

                  {{-- Trx ID --}}
                  <td style="padding: 12px 14px;">
                    <span style="font-family: monospace; font-size: 12px; color: #cbd5e1; font-weight: 600;">
                      {{ $p->transaction_reference ?: '—' }}
                    </span>
                  </td>

                  {{-- Amount Paid --}}
                  <td style="padding: 12px 14px;">
                    <strong style="color: #ffffff; font-size: 13px; font-family: monospace;">
                      ৳{{ number_format($p->amount, 2) }}
                    </strong>
                  </td>

                  {{-- Date --}}
                  <td style="padding: 12px 14px; color: #94a3b8; font-size: 11.5px;">
                    {{ $p->payment_date ? $p->payment_date->format('d M Y') : ($p->created_at ? $p->created_at->format('d M Y') : 'N/A') }}
                  </td>

                  {{-- Verification Status --}}
                  <td style="padding: 12px 14px; text-align: right;">
                    @php
                      $vStatus = strtolower($p->verification_status);
                    @endphp
                    @if(in_array($vStatus, ['approved', 'verified']))
                      <span style="font-size: 11px; font-weight: 600; color: #cbd5e1; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); border-radius: 4px; padding: 2px 7px; display: inline-flex; align-items: center; gap: 4px;">
                        <i class="fa-solid fa-circle-check" style="color: #ff5757;"></i> Approved
                      </span>
                    @elseif($vStatus === 'rejected')
                      <span style="font-size: 11px; font-weight: 600; color: #f87171; background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.25); border-radius: 4px; padding: 2px 7px; display: inline-flex; align-items: center; gap: 4px;">
                        <i class="fa-solid fa-circle-xmark"></i> Rejected
                      </span>
                    @else
                      <span style="font-size: 11px; font-weight: 600; color: #8c96a8; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); border-radius: 4px; padding: 2px 7px; display: inline-flex; align-items: center; gap: 4px;">
                        <i class="fa-solid fa-clock"></i> Pending Review
                      </span>
                    @endif
                  </td>

                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @else
        <div style="background: #0f121a; border: 1px dashed rgba(255,255,255,0.08); border-radius: 10px; padding: 24px; text-align: center; color: #64748b;">
          <i class="fa-solid fa-receipt" style="font-size: 20px; margin-bottom: 6px; display: block;"></i>
          <span style="font-size: 12.5px;">No payment transaction vouchers submitted for this student yet.</span>
        </div>
      @endif
    </div>

    <!-- 2. OFFICIAL FEE INVOICES -->
    <div>
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
        <h4 style="font-size: 14px; font-weight: 700; color: #ffffff; margin: 0; display: flex; align-items: center; gap: 8px;">
          <i class="fa-solid fa-file-invoice" style="color: #8c96a8;"></i> Issued Fee Invoices
        </h4>
        <span style="font-size: 11px; color: #64748b;">{{ $invoices->count() }} invoice record(s)</span>
      </div>

      @if($invoices->isNotEmpty())
        <div style="background: #0f121a; border: 1px solid rgba(255,255,255,0.06); border-radius: 10px; overflow: hidden;">
          <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
            <thead>
              <tr style="border-bottom: 1px solid rgba(255,255,255,0.06); text-align: left; color: #64748b; font-size: 10.5px; text-transform: uppercase; letter-spacing: 0.6px; background: rgba(255,255,255,0.02);">
                <th style="padding: 10px 14px;">Invoice #</th>
                <th style="padding: 10px 14px;">Fee Category / Title</th>
                <th style="padding: 10px 14px;">Gross</th>
                <th style="padding: 10px 14px;">Net Payable</th>
                <th style="padding: 10px 14px;">Amount Paid</th>
                <th style="padding: 10px 14px;">Due Amount</th>
                <th style="padding: 10px 14px;">Due Date</th>
                <th style="padding: 10px 14px; text-align: right;">Invoice Status</th>
              </tr>
            </thead>
            <tbody>
              @foreach($invoices as $inv)
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.03); transition: background 0.15s;" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background='transparent'">
                  
                  {{-- Invoice Number --}}
                  <td style="padding: 12px 14px;">
                    <code style="background: rgba(255,255,255,0.06); color: #cbd5e1; border: 1px solid rgba(255,255,255,0.1); padding: 2px 7px; border-radius: 4px; font-family: monospace; font-size: 11.5px; font-weight: 700;">
                      {{ $inv->invoice_number }}
                    </code>
                  </td>

                  {{-- Title --}}
                  <td style="padding: 12px 14px;">
                    <div style="font-weight: 700; color: #fff; font-size: 12.5px;">{{ $inv->title }}</div>
                    <div style="font-size: 10px; color: #64748b;">
                      {{ $inv->feeType ? $inv->feeType->title : 'Tuition & Assessment' }}
                    </div>
                  </td>

                  {{-- Gross --}}
                  <td style="padding: 12px 14px; color: #94a3b8; font-family: monospace;">
                    ৳{{ number_format($inv->gross_amount, 2) }}
                  </td>

                  {{-- Net Payable --}}
                  <td style="padding: 12px 14px; color: #fff; font-family: monospace; font-weight: 700;">
                    ৳{{ number_format($inv->net_amount, 2) }}
                  </td>

                  {{-- Paid Amount --}}
                  <td style="padding: 12px 14px; color: #fff; font-family: monospace; font-weight: 700;">
                    ৳{{ number_format($inv->paid_amount, 2) }}
                  </td>

                  {{-- Due Amount --}}
                  <td style="padding: 12px 14px; font-family: monospace; font-weight: 700; color: {{ $inv->due_amount > 0 ? '#f87171' : '#64748b' }};">
                    ৳{{ number_format($inv->due_amount, 2) }}
                  </td>

                  {{-- Due Date --}}
                  <td style="padding: 12px 14px; color: #94a3b8; font-size: 11.5px;">
                    {{ $inv->due_date ? $inv->due_date->format('d M Y') : 'N/A' }}
                  </td>

                  {{-- Status --}}
                  <td style="padding: 12px 14px; text-align: right;">
                    @php
                      $invStatus = strtolower($inv->status);
                    @endphp
                    @if($invStatus === 'paid')
                      <span style="font-size: 11px; font-weight: 600; color: #cbd5e1; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); border-radius: 4px; padding: 2px 7px; display: inline-flex; align-items: center; gap: 4px;">
                        <i class="fa-solid fa-check" style="color: #ff5757;"></i> Paid
                      </span>
                    @elseif($invStatus === 'partial')
                      <span style="font-size: 11px; font-weight: 600; color: #cbd5e1; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); border-radius: 4px; padding: 2px 7px;">
                        Partial
                      </span>
                    @else
                      <span style="font-size: 11px; font-weight: 600; color: #f87171; background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.25); border-radius: 4px; padding: 2px 7px;">
                        Unpaid
                      </span>
                    @endif
                  </td>

                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @else
        <div style="background: #0f121a; border: 1px dashed rgba(255,255,255,0.08); border-radius: 10px; padding: 24px; text-align: center; color: #64748b;">
          <i class="fa-solid fa-file-invoice" style="font-size: 20px; margin-bottom: 6px; display: block;"></i>
          <span style="font-size: 12.5px;">No tuition fee invoices issued for this student account.</span>
        </div>
      @endif
    </div>

  </div>

</div>
@endsection