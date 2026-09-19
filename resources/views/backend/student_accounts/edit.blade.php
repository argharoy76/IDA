@extends('layouts.portal')

@section('title', 'Edit Student: ' . $student->user->name)
@section('page_title', 'Update Student Details')

@section('content')
<div style="max-width: 1100px; margin: 0 auto;">

  <!-- Top Action Bar -->
  <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 24px;">
    <div style="display: flex; gap: 10px;">
      <a href="{{ route('admin.student_accounts.index') }}" class="btn-tactical" style="background: rgba(255,255,255,0.06); color: #94a3b8; border: 1px solid rgba(255,255,255,0.12); padding: 8px 16px; font-size: 13px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-arrow-left"></i> Back to Student Management
      </a>
      <a href="{{ route('admin.student_accounts.show', $student->id) }}" class="btn-tactical" style="background: rgba(255,255,255,0.06); color: #cbd5e1; border: 1px solid rgba(255,255,255,0.12); padding: 8px 16px; font-size: 13px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-eye"></i> View Details
      </a>
    </div>

    <span style="font-size: 12px; color: #64748b;">
      ID: #{{ $student->id }}
    </span>
  </div>

  @if(session('success'))
    <div style="background: rgba(16,185,129,0.12); border: 1px solid rgba(16,185,129,0.35); border-radius: 10px; padding: 14px 18px; margin-bottom: 24px; color: #34d399; font-size: 13.5px; display: flex; align-items: center; gap: 10px;">
      <i class="fa-solid fa-circle-check" style="font-size: 16px;"></i>
      <span>{{ session('success') }}</span>
    </div>
  @endif

  @if(session('error'))
    <div style="background: rgba(239,68,68,0.12); border: 1px solid rgba(239,68,68,0.35); border-radius: 10px; padding: 14px 18px; margin-bottom: 24px; color: #f87171; font-size: 13.5px; display: flex; align-items: center; gap: 10px;">
      <i class="fa-solid fa-triangle-exclamation" style="font-size: 16px;"></i>
      <span>{{ session('error') }}</span>
    </div>
  @endif

  @if($errors->any())
    <div style="background: rgba(239,68,68,0.12); border: 1px solid rgba(239,68,68,0.35); border-radius: 10px; padding: 16px 20px; margin-bottom: 24px; color: #fca5a5; font-size: 13px;">
      <strong style="display: block; margin-bottom: 6px;"><i class="fa-solid fa-triangle-exclamation"></i> Please resolve the following errors:</strong>
      <ul style="margin: 0; padding-left: 20px; line-height: 1.6;">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  @php
    $currentId = old('custom_id', $student->student_id_code ?: ($student->user->account_id ?? ''));
    $enrolledCourseIds = old('course_ids', $student->courses->pluck('id')->toArray());
    if (empty($enrolledCourseIds) && $student->current_course_id) {
      $enrolledCourseIds = [(int)$student->current_course_id];
    }
  @endphp

  <!-- Main Update Form -->
  <form method="POST" action="{{ route('admin.student_accounts.update', $student->id) }}" id="updateStudentForm">
    @csrf
    @method('PUT')

    <!-- Account Identity -->
    <div class="content-panel" style="margin-bottom: 24px;">
      <div class="panel-header" style="margin-bottom: 18px;">
        <h3 style="font-size: 15px; font-weight: 800; margin: 0; display: flex; align-items: center; gap: 8px;">
          <i class="fa-solid fa-id-card-clip" style="color: #ff5757;"></i>
          <span>Account Identity</span>
        </h3>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 18px;">
        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">
            Student Login ID *
          </label>
          <input type="text" name="custom_id" required value="{{ $currentId }}"
                 class="form-control"
                 style="font-family: monospace; font-size: 14px; font-weight: 700; color: #ff8585; text-transform: uppercase;"
                 placeholder="e.g. 250236">
        </div>

        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">
            Registration Type *
          </label>
          <select name="student_type" class="form-control" style="font-size: 13.5px;">
            <option value="offline" {{ old('student_type', $student->student_type) === 'offline' ? 'selected' : '' }}>Offline (Staff Enrolled)</option>
            <option value="online" {{ old('student_type', $student->student_type) === 'online' ? 'selected' : '' }}>Online (Self-Registered)</option>
            <option value="academic" {{ old('student_type', $student->student_type) === 'academic' ? 'selected' : '' }}>Academic Cadet</option>
            <option value="external" {{ old('student_type', $student->student_type) === 'external' ? 'selected' : '' }}>External Candidate</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Personal Information -->
    <div class="content-panel" style="margin-bottom: 24px;">
      <div class="panel-header" style="margin-bottom: 18px;">
        <h3 style="font-size: 15px; font-weight: 800; margin: 0; display: flex; align-items: center; gap: 8px;">
          <i class="fa-solid fa-user" style="color: #8c96a8;"></i>
          <span>Personal Information</span>
        </h3>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 6px;">Full Name *</label>
          <input type="text" name="name" required value="{{ old('name', $student->user->name) }}" class="form-control" placeholder="Candidate's legal name">
        </div>

        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 6px;">Email Address *</label>
          <input type="email" name="email" required value="{{ old('email', $student->user->email) }}" class="form-control" placeholder="student@example.com">
        </div>

        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 6px;">Mobile / WhatsApp Number *</label>
          <input type="text" name="phone" required value="{{ old('phone', $student->user->phone) }}" class="form-control" placeholder="01XXXXXXXXX">
        </div>

        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 6px;">Age *</label>
          <input type="number" name="age" min="10" max="100" value="{{ old('age', $student->age) }}" class="form-control" placeholder="e.g. 21">
        </div>

        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 6px;">Gender *</label>
          <select name="gender" class="form-control">
            <option value="male" {{ old('gender', $student->gender) === 'male' ? 'selected' : '' }}>Male</option>
            <option value="female" {{ old('gender', $student->gender) === 'female' ? 'selected' : '' }}>Female</option>
            <option value="other" {{ old('gender', $student->gender) === 'other' ? 'selected' : '' }}>Other</option>
          </select>
        </div>

        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 6px;">Target Wing</label>
          <select name="target_wing" class="form-control">
            <option value="Army" {{ old('target_wing', $student->target_wing) === 'Army' ? 'selected' : '' }}>Bangladesh Army</option>
            <option value="Navy" {{ old('target_wing', $student->target_wing) === 'Navy' ? 'selected' : '' }}>Bangladesh Navy</option>
            <option value="Air Force" {{ old('target_wing', $student->target_wing) === 'Air Force' ? 'selected' : '' }}>Bangladesh Air Force</option>
            <option value="Police" {{ old('target_wing', $student->target_wing) === 'Police' ? 'selected' : '' }}>Police Cadets</option>
            <option value="General" {{ old('target_wing', $student->target_wing) === 'General' ? 'selected' : '' }}>General ISSB</option>
          </select>
        </div>

        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 6px;">Educational Institution</label>
          <input type="text" name="institution" value="{{ old('institution', $student->institution) }}" class="form-control" placeholder="College / University">
        </div>

        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 6px;">HSC / Passing Year</label>
          <input type="text" name="hsc_year" value="{{ old('hsc_year', $student->hsc_year) }}" class="form-control" placeholder="e.g. 2025">
        </div>

        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 6px;">Home District</label>
          <input type="text" name="district" value="{{ old('district', $student->district) }}" class="form-control" placeholder="e.g. Khulna, Dhaka">
        </div>

        <div style="grid-column: span 2;">
          <label style="display: block; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 6px;">Full Address *</label>
          <textarea name="address" required rows="2" class="form-control" placeholder="House, Road, Area, Thana, District">{{ old('address', $student->address) }}</textarea>
        </div>
      </div>
    </div>

    <!-- Enrolled Courses -->
    <div class="content-panel" style="margin-bottom: 24px;">
      <div class="panel-header" style="margin-bottom: 14px;">
        <h3 style="font-size: 15px; font-weight: 800; margin: 0; display: flex; align-items: center; gap: 8px;">
          <i class="fa-solid fa-graduation-cap" style="color: #ff5757;"></i>
          <span>Enrolled Courses</span>
        </h3>
      </div>

      <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 14px; margin-top: 14px;">
        @foreach($courses as $course)
          @php
            $isEnrolled = in_array($course->id, $enrolledCourseIds);
          @endphp
          <label style="display: block; cursor: pointer; border: 1.5px solid {{ $isEnrolled ? '#ff5757' : 'rgba(255,255,255,0.08)' }}; background: {{ $isEnrolled ? 'rgba(255,87,87,0.08)' : 'rgba(255,255,255,0.02)' }}; border-radius: 12px; padding: 16px; transition: all 0.2s;"
                 id="course_card_{{ $course->id }}">
            <div style="display: flex; align-items: flex-start; gap: 12px;">
              <input type="checkbox" name="course_ids[]" value="{{ $course->id }}"
                     {{ $isEnrolled ? 'checked' : '' }}
                     style="accent-color: #ff5757; width: 18px; height: 18px; margin-top: 2px;"
                     onchange="toggleCourseCardStyle(this, 'course_card_{{ $course->id }}')">
              <div style="flex: 1;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 8px;">
                  <strong style="font-size: 13.5px; color: #ffffff; display: block; margin-bottom: 2px;">{{ $course->title }}</strong>
                </div>
                <div style="display: flex; gap: 10px; font-size: 11.5px; color: #94a3b8; margin-top: 6px;">
                  <span style="color: #ffffff; font-weight: 700;">৳{{ number_format($course->course_fee, 0) }}</span>
                  <span>&bull;</span>
                  <span>{{ $course->duration_weeks ?? 12 }} weeks</span>
                </div>
              </div>
            </div>
          </label>
        @endforeach
      </div>
    </div>

    <!-- Payment Details -->
    <div class="content-panel" id="payment_section" style="margin-bottom: 24px;">
      <div class="panel-header" style="margin-bottom: 16px;">
        <h3 style="font-size: 15px; font-weight: 800; margin: 0; display: flex; align-items: center; gap: 8px;">
          <i class="fa-solid fa-file-invoice-dollar" style="color: #ff5757;"></i>
          <span>Payment Details</span>
        </h3>
      </div>

      <!-- Financial Metric Strip -->
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px; margin-bottom: 20px;">
        <div style="background: #0f121a; border: 1px solid rgba(255,255,255,0.06); border-radius: 10px; padding: 12px 14px;">
          <div style="font-size: 10.5px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">Total Billed</div>
          <div style="font-size: 18px; font-weight: 800; color: #fff; margin-top: 2px;">৳{{ number_format($totalBilled, 2) }}</div>
        </div>
        <div style="background: #0f121a; border: 1px solid rgba(255,255,255,0.06); border-radius: 10px; padding: 12px 14px;">
          <div style="font-size: 10.5px; font-weight: 700; color: #8c96a8; text-transform: uppercase; letter-spacing: 0.5px;">Total Paid</div>
          <div style="font-size: 18px; font-weight: 800; color: #ffffff; margin-top: 2px;">৳{{ number_format($totalPaid, 2) }}</div>
        </div>
        <div style="background: #0f121a; border: 1px solid {{ $totalDue > 0 ? 'rgba(239,68,68,0.25)' : 'rgba(255,255,255,0.06)' }}; border-radius: 10px; padding: 12px 14px;">
          <div style="font-size: 10.5px; font-weight: 700; color: {{ $totalDue > 0 ? '#f87171' : '#64748b' }}; text-transform: uppercase; letter-spacing: 0.5px;">Outstanding Dues</div>
          <div style="font-size: 18px; font-weight: 800; color: {{ $totalDue > 0 ? '#f87171' : '#64748b' }}; margin-top: 2px;">৳{{ number_format($totalDue, 2) }}</div>
        </div>
      </div>

      <!-- Subsection A: Course Payment Clearance -->
      <div style="margin-bottom: 22px;">
        <label style="display: block; font-size: 11.5px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">
          Course Payment Clearance Status
        </label>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 10px;">
          @foreach($courses as $c)
            @php
              $pivotCourse = $student->courses->firstWhere('id', $c->id);
              $currentPayStatus = old('course_payment_status.' . $c->id, $pivotCourse ? ($pivotCourse->pivot->payment_status ?? 'paid') : 'paid');
            @endphp
            <div style="background: #0f121a; border: 1px solid rgba(255,255,255,0.06); border-radius: 8px; padding: 10px 14px; display: flex; justify-content: space-between; align-items: center; gap: 10px;">
              <div style="min-width: 0; flex: 1;">
                <div style="font-size: 12px; font-weight: 700; color: #ffffff; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $c->title }}</div>
                <div style="font-size: 10.5px; color: #cbd5e1; font-weight: 600;">Fee: ৳{{ number_format($c->course_fee, 0) }}</div>
              </div>
              <div style="width: 130px; flex-shrink: 0;">
                <select name="course_payment_status[{{ $c->id }}]" class="form-control" style="font-size: 11.5px; padding: 6px 8px; height: auto;">
                  <option value="paid" {{ $currentPayStatus === 'paid' ? 'selected' : '' }}>Paid in Full</option>
                  <option value="partial" {{ $currentPayStatus === 'partial' ? 'selected' : '' }}>Partial Paid</option>
                  <option value="unpaid" {{ $currentPayStatus === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                  <option value="exempt" {{ $currentPayStatus === 'exempt' ? 'selected' : '' }}>Exempt</option>
                </select>
              </div>
            </div>
          @endforeach
        </div>
      </div>

      <!-- Subsection B: Issued Invoices & Status Adjustments -->
      @if($invoices->isNotEmpty())
        <div style="margin-bottom: 22px;">
          <label style="display: block; font-size: 11.5px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">
            Issued Fee Invoices (Edit Dues & Status)
          </label>
          <div style="background: #0f121a; border: 1px solid rgba(255,255,255,0.06); border-radius: 8px; overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; font-size: 12px; min-width: 580px;">
              <thead>
                <tr style="background: rgba(255,255,255,0.02); border-bottom: 1px solid rgba(255,255,255,0.06); text-align: left; color: #64748b; font-size: 10px; text-transform: uppercase;">
                  <th style="padding: 8px 12px;">Invoice #</th>
                  <th style="padding: 8px 12px;">Category</th>
                  <th style="padding: 8px 12px;">Net Total</th>
                  <th style="padding: 8px 12px;">Paid Amount</th>
                  <th style="padding: 8px 12px;">Due Balance</th>
                  <th style="padding: 8px 12px;">Invoice Status</th>
                </tr>
              </thead>
              <tbody>
                @foreach($invoices as $inv)
                  <tr style="border-bottom: 1px solid rgba(255,255,255,0.03);">
                    <td style="padding: 8px 12px;">
                      <code style="font-family: monospace; color: #cbd5e1; font-size: 11px;">{{ $inv->invoice_number }}</code>
                    </td>
                    <td style="padding: 8px 12px; color: #fff;">
                      {{ $inv->title }}
                    </td>
                    <td style="padding: 8px 12px; font-family: monospace; color: #94a3b8;">
                      ৳{{ number_format($inv->net_amount, 2) }}
                    </td>
                    <td style="padding: 8px 12px;">
                      <input type="number" step="0.01" min="0" name="invoice_paid_amount[{{ $inv->id }}]" value="{{ old('invoice_paid_amount.' . $inv->id, $inv->paid_amount) }}"
                             class="form-control" style="font-size: 11.5px; padding: 4px 8px; height: auto; max-width: 100px; font-family: monospace;">
                    </td>
                    <td style="padding: 8px 12px;">
                      <input type="number" step="0.01" min="0" name="invoice_due_amount[{{ $inv->id }}]" value="{{ old('invoice_due_amount.' . $inv->id, $inv->due_amount) }}"
                             class="form-control" style="font-size: 11.5px; padding: 4px 8px; height: auto; max-width: 100px; font-family: monospace; color: {{ $inv->due_amount > 0 ? '#f87171' : '#cbd5e1' }};">
                    </td>
                    <td style="padding: 8px 12px;">
                      <select name="invoice_status[{{ $inv->id }}]" class="form-control" style="font-size: 11px; padding: 4px 8px; height: auto;">
                        <option value="paid" {{ old('invoice_status.' . $inv->id, $inv->status) === 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="partially_paid" {{ old('invoice_status.' . $inv->id, $inv->status) === 'partially_paid' ? 'selected' : '' }}>Partially Paid</option>
                        <option value="pending" {{ old('invoice_status.' . $inv->id, $inv->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="overdue" {{ old('invoice_status.' . $inv->id, $inv->status) === 'overdue' ? 'selected' : '' }}>Overdue</option>
                      </select>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      @endif

      <!-- Subsection C: Record New Payment Voucher (Optional) -->
      <div style="background: rgba(255,255,255,0.02); border: 1px dashed rgba(255,255,255,0.1); border-radius: 10px; padding: 16px;">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
          <strong style="font-size: 12.5px; color: #fff; display: flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-circle-plus" style="color: #ff5757;"></i> Record New Payment Voucher (Optional)
          </strong>
          <small style="color: #64748b;">Instant payment credit</small>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px;">
          <div>
            <label style="display: block; font-size: 10.5px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 4px;">Payment Amount (৳)</label>
            <input type="number" step="0.01" min="1" name="new_payment_amount" value="{{ old('new_payment_amount') }}"
                   class="form-control" placeholder="e.g. 5000" style="font-family: monospace;">
          </div>
          <div>
            <label style="display: block; font-size: 10.5px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 4px;">Payment Method</label>
            <select name="new_payment_method" class="form-control">
              <option value="Cash / Office Receipt">Cash / Office Receipt</option>
              <option value="bKash">bKash</option>
              <option value="Nagad">Nagad</option>
              <option value="Bank Deposit">Bank Deposit</option>
              <option value="Online Gateway">Online Gateway</option>
            </select>
          </div>
          <div>
            <label style="display: block; font-size: 10.5px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 4px;">Transaction ID / Receipt #</label>
            <input type="text" name="new_payment_trx" value="{{ old('new_payment_trx') }}"
                   class="form-control" placeholder="e.g. BKASH-TX123, REC-0098">
          </div>
          <div>
            <label style="display: block; font-size: 10.5px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 4px;">Verification Status</label>
            <select name="new_payment_status" class="form-control">
              <option value="approved">Approved / Verified</option>
              <option value="pending">Pending Review</option>
            </select>
          </div>
          @if($invoices->isNotEmpty())
            <div style="grid-column: span 2;">
              <label style="display: block; font-size: 10.5px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 4px;">Apply to Invoice (Optional)</label>
              <select name="new_payment_invoice_id" class="form-control">
                <option value="">-- Apply as General Student Payment --</option>
                @foreach($invoices as $inv)
                  <option value="{{ $inv->id }}">
                    Invoice {{ $inv->invoice_number }} — {{ $inv->title }} (Due: ৳{{ number_format($inv->due_amount, 2) }})
                  </option>
                @endforeach
              </select>
            </div>
          @endif
        </div>
      </div>

    </div>

    <!-- Reset Password -->
    <div class="content-panel" style="margin-bottom: 24px;">
      <div class="panel-header" style="margin-bottom: 14px;">
        <h3 style="font-size: 15px; font-weight: 800; margin: 0; display: flex; align-items: center; gap: 8px;">
          <i class="fa-solid fa-key" style="color: #8c96a8;"></i>
          <span>Reset Password (Optional)</span>
        </h3>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 6px;">New Password</label>
          <input type="password" name="password" minlength="6" class="form-control" placeholder="Enter new password (min 6 chars)">
        </div>
        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 6px;">Confirm New Password</label>
          <input type="password" name="password_confirmation" minlength="6" class="form-control" placeholder="Re-enter new password">
        </div>
      </div>
    </div>

    <!-- Submit Toolbar -->
    <div style="display: flex; justify-content: flex-end; align-items: center; gap: 12px; background: #131722; border: 1px solid rgba(255,255,255,0.08); border-radius: 14px; padding: 14px 20px; margin-bottom: 30px;">
      <a href="{{ route('admin.student_accounts.index') }}" class="btn-tactical" style="background: rgba(255,255,255,0.06); color: #94a3b8; border: 1px solid rgba(255,255,255,0.1); padding: 9px 18px; font-size: 13px; text-decoration: none; border-radius: 8px;">
        Cancel
      </a>
      <button type="submit" class="btn-tactical" style="background: #ff5757; color: #ffffff; padding: 9px 24px; font-size: 13px; font-weight: 700; border: none; border-radius: 8px; cursor: pointer;">
        <i class="fa-solid fa-check"></i> Save Changes
      </button>
    </div>

  </form>

  <!-- Danger Zone -->
  <div class="content-panel" style="border: 1px solid rgba(239, 68, 68, 0.3); background: rgba(239, 68, 68, 0.02); margin-bottom: 40px;">
    <div class="panel-header" style="border-bottom: 1px solid rgba(239, 68, 68, 0.15); margin-bottom: 16px;">
      <h3 style="font-size: 15px; font-weight: 800; color: #ef4444; margin: 0; display: flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-triangle-exclamation" style="color: #ef4444;"></i>
        <span>Delete Account</span>
      </h3>
    </div>

    <p style="font-size: 12px; color: #94a3b8; margin: 0 0 16px; line-height: 1.5;">
      Once deleted, this student account, enrollment records, and exam scores cannot be recovered.
    </p>

    <form method="POST" action="{{ route('admin.student_accounts.destroy', $student->id) }}" id="dangerDeleteForm">
      @csrf
      @method('DELETE')

      <div style="max-width: 520px; margin-bottom: 16px;">
        <label style="display: block; font-size: 11px; font-weight: 700; color: #f87171; text-transform: uppercase; margin-bottom: 6px;">
          Type <code style="color: #ffffff; background: rgba(239, 68, 68, 0.25); padding: 2px 6px; border-radius: 4px;">{{ $currentId }}</code> or <code style="color: #ffffff; background: rgba(239, 68, 68, 0.25); padding: 2px 6px; border-radius: 4px;">DELETE</code> to confirm:
        </label>
        <input type="text" name="confirm_delete" id="dangerConfirmInput" required autocomplete="off"
               class="form-control" style="font-family: monospace; font-size: 13.5px; font-weight: 700; color: #ff8585; border-color: rgba(239,68,68,0.35); text-transform: uppercase;"
               placeholder="Type confirmation here..."
               oninput="checkDangerDelete()">
      </div>

      <label style="display: flex; align-items: center; gap: 8px; font-size: 12px; color: #cbd5e1; cursor: pointer; margin-bottom: 18px;">
        <input type="checkbox" id="dangerAckCheckbox" style="accent-color: #ef4444; width: 16px; height: 16px;" onchange="checkDangerDelete()">
        <span>I understand this deletion is permanent.</span>
      </label>

      <button type="submit" id="dangerDeleteBtn" disabled class="btn-tactical"
              style="background: #ef4444; color: #ffffff; border: none; padding: 10px 22px; font-size: 13px; font-weight: 700; opacity: 0.35; cursor: not-allowed; transition: all 0.2s;">
        <i class="fa-solid fa-trash-can"></i> Delete Student Account
      </button>
    </form>
  </div>

</div>

<script>
  function toggleCourseCardStyle(checkbox, cardId) {
    var card = document.getElementById(cardId);
    if (card) {
      if (checkbox.checked) {
        card.style.borderColor = '#ff5757';
        card.style.background = 'rgba(255, 87, 87, 0.08)';
      } else {
        card.style.borderColor = 'rgba(255,255,255,0.08)';
        card.style.background = 'rgba(255,255,255,0.02)';
      }
    }
  }

  function checkDangerDelete() {
    var expected = "{{ strtoupper(trim($currentId)) }}";
    var typed = (document.getElementById('dangerConfirmInput').value || '').trim().toUpperCase();
    var ack = document.getElementById('dangerAckCheckbox').checked;
    var btn = document.getElementById('dangerDeleteBtn');

    var isMatch = (typed === expected && typed.length > 0) || typed === 'DELETE';

    if (isMatch && ack) {
      btn.disabled = false;
      btn.style.opacity = '1';
      btn.style.cursor = 'pointer';
      btn.style.background = 'linear-gradient(135deg, #ef4444, #b91c1c)';
      btn.style.boxShadow = '0 4px 14px rgba(239, 68, 68, 0.4)';
    } else {
      btn.disabled = true;
      btn.style.opacity = '0.35';
      btn.style.cursor = 'not-allowed';
      btn.style.boxShadow = 'none';
    }
  }
</script>
@endsection