@extends('layouts.portal')

@section('title', 'Fees & Payments')
@section('page_title', 'Cadet Fee Invoices & Payment Verification')
@section('page_subtitle', 'Review tuition invoices, submit online fee payments via bKash/Nagad/Bank, and track verification status')

@section('topbar_actions')
  @if(false)
  <button type="button" class="btn-tactical btn-tactical-primary" onclick="openPaymentModal()">
    <i class="fa-solid fa-credit-card"></i> Submit Fee Payment
  </button>
  @endif
@endsection

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

  <!-- Fee Module Notice -->
  <div class="tactical-card" style="text-align: center; padding: 48px 24px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 18px;">
    <div style="width: 64px; height: 64px; border-radius: 50%; background: #fdf2f8; color: #db2777; display: flex; align-items: center; justify-content: center; font-size: 28px; margin: 0 auto 16px auto;">
      <i class="fa-solid fa-credit-card"></i>
    </div>
    <h3 style="font-size: 18px; font-weight: 800; color: #0f172a; margin: 0 0 8px 0;">Payment Section Currently Hidden</h3>
    <p style="font-size: 13.5px; color: #64748b; margin: 0 auto 20px auto; max-width: 480px; line-height: 1.5;">
      Online fee payment and tuition invoice reconciliation is currently hidden. Please proceed to the Exam portal.
    </p>
    <a href="{{ route('cadet.exams.index') }}" class="btn-tactical btn-tactical-primary" style="display: inline-flex; align-items: center; gap: 8px; text-decoration: none; padding: 10px 20px; border-radius: 10px;">
      <i class="fa-solid fa-clipboard-list"></i> Go to Exams
    </a>
  </div>

</div>

{{-- Hidden fee and payment cards/tables - Code preserved intact for future activation --}}
@if(false)
<div style="display: flex; flex-direction: column; gap: 24px;">

  <!-- Fee Totals Strip -->
  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px;">
    <div class="stat-card">
      <div class="stat-icon" style="background: rgba(16, 185, 129, 0.15); color: #10b981;">
        <i class="fa-solid fa-circle-check"></i>
      </div>
      <div>
        <div class="stat-label">Total Verified Payments</div>
        <div class="stat-value" style="color: #10b981;">৳{{ number_format($totalPaid, 2) }}</div>
      </div>
    </div>

    <div class="stat-card" style="border: 1px solid {{ $totalDue > 0 ? 'rgba(239, 68, 68, 0.4)' : 'var(--border-soft)' }};">
      <div class="stat-icon" style="background: rgba(239, 68, 68, 0.15); color: #ef4444;">
        <i class="fa-solid fa-clock"></i>
      </div>
      <div>
        <div class="stat-label">Current Outstanding Balance</div>
        <div class="stat-value" style="color: {{ $totalDue > 0 ? '#ef4444' : '#10b981' }};">
          ৳{{ number_format($totalDue, 2) }}
        </div>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon" style="background: rgba(217, 119, 6, 0.15); color: #d97706;">
        <i class="fa-solid fa-file-invoice"></i>
      </div>
      <div>
        <div class="stat-label">Total Issued Invoices</div>
        <div class="stat-value">{{ $invoices->count() }}</div>
      </div>
    </div>
  </div>

  <!-- Academy Payment Instructions Box -->
  <div class="tactical-card" style="background: #fffbeb; border: 1px solid #fde68a; border-left: 4px solid #b45309; padding: 18px 22px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
      <div>
        <h4 style="font-size: 14.5px; font-weight: 800; color: #92400e; margin: 0 0 8px 0;">
          <i class="fa-solid fa-building-columns"></i> Official Imperial Defence Academy Payment Channels
        </h4>
        <div style="display: flex; gap: 24px; flex-wrap: wrap; font-size: 13px; color: #78350f;">
          <div><strong style="color: #92400e;">bKash Merchant:</strong> 01711-000000 (Payment / Counter 1)</div>
          <div><strong style="color: #92400e;">Nagad Merchant:</strong> 01811-000000 (Merchant Pay)</div>
          <div><strong style="color: #92400e;">Bank:</strong> Sonali Bank PLC, Khulna Cantt Branch (A/C: 01029384756)</div>
        </div>
      </div>
      <button type="button" class="btn-tactical btn-tactical-primary" style="font-size: 12.5px;" onclick="openPaymentModal()">
        <i class="fa-solid fa-paper-plane"></i> Submit Deposit Slip
      </button>
    </div>
  </div>

  <!-- Invoices Table -->
  <div class="tactical-card" style="padding: 0; overflow: hidden;">
    <div style="padding: 16px 20px; border-bottom: 1px solid var(--border-soft);">
      <h3 style="font-size: 15px; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-file-invoice-dollar" style="color: var(--accent-gold);"></i> Issued Fee Invoices
      </h3>
    </div>

    <div style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
      <table class="tactical-table">
        <thead>
          <tr>
            <th>Invoice #</th>
            <th>Title & Category</th>
            <th>Gross</th>
            <th>Discount / Waiver</th>
            <th>Net Amount</th>
            <th>Paid</th>
            <th>Due</th>
            <th>Due Date</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          @forelse($invoices as $inv)
            <tr>
              <td>
                <span style="font-family: 'Plus Jakarta Sans', monospace; font-weight: 700; color: #60a5fa;">{{ $inv->invoice_number }}</span>
              </td>
              <td>
                <strong>{{ $inv->title }}</strong>
                <div style="font-size: 11px; color: var(--text-muted);">{{ $inv->feeType->name ?? 'Course Fee' }}</div>
              </td>
              <td style="font-family: 'Plus Jakarta Sans', monospace;">৳{{ number_format($inv->gross_amount, 2) }}</td>
              <td style="font-family: 'Plus Jakarta Sans', monospace; color: #d97706;">-৳{{ number_format($inv->discount_amount + $inv->waiver_amount, 2) }}</td>
              <td style="font-family: 'Plus Jakarta Sans', monospace; font-weight: 700;">৳{{ number_format($inv->net_amount, 2) }}</td>
              <td style="font-family: 'Plus Jakarta Sans', monospace; color: #10b981; font-weight: 700;">৳{{ number_format($inv->paid_amount, 2) }}</td>
              <td style="font-family: 'Plus Jakarta Sans', monospace; color: {{ $inv->due_amount > 0 ? '#ef4444' : '#10b981' }}; font-weight: 700;">
                ৳{{ number_format($inv->due_amount, 2) }}
              </td>
              <td>
                <span style="font-size: 12px; color: {{ \Carbon\Carbon::parse($inv->due_date)->isPast() && $inv->due_amount > 0 ? '#ef4444' : 'inherit' }};">
                  {{ \Carbon\Carbon::parse($inv->due_date)->format('d M Y') }}
                </span>
              </td>
              <td>
                <span class="badge {{ $inv->status === 'paid' ? 'badge-emerald' : ($inv->status === 'partially_paid' ? 'badge-gold' : 'badge-danger') }}">
                  {{ strtoupper(str_replace('_', ' ', $inv->status)) }}
                </span>
              </td>
              <td>
                @if($inv->due_amount > 0)
                  <button type="button" class="btn-tactical btn-tactical-primary" style="padding: 4px 10px; font-size: 11px;" onclick="payInvoice('{{ $inv->id }}', '{{ $inv->invoice_number }}', '{{ $inv->due_amount }}')">
                    Pay Now
                  </button>
                @else
                  <span class="badge badge-emerald"><i class="fa-solid fa-check"></i> Settled</span>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="10" style="text-align: center; padding: 40px; color: var(--text-muted);">
                No fee invoices issued for your account.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <!-- Submitted Payments History -->
  <div class="tactical-card" style="padding: 0; overflow: hidden;">
    <div style="padding: 16px 20px; border-bottom: 1px solid var(--border-soft);">
      <h3 style="font-size: 15px; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-receipt" style="color: var(--accent-gold);"></i> Submitted Payment Records
      </h3>
    </div>

    <div style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
      <table class="tactical-table">
        <thead>
          <tr>
            <th>Payment #</th>
            <th>Invoice Ref</th>
            <th>Method</th>
            <th>Trx ID / Ref</th>
            <th>Amount Paid</th>
            <th>Date</th>
            <th>Verification Status</th>
          </tr>
        </thead>
        <tbody>
          @forelse($payments as $p)
            <tr>
              <td>
                <span style="font-family: 'Plus Jakarta Sans', monospace; font-weight: 700; color: #60a5fa;">{{ $p->payment_number }}</span>
              </td>
              <td>
                <span style="font-family: 'Plus Jakarta Sans', monospace;">{{ $p->invoice->invoice_number ?? 'Direct' }}</span>
              </td>
              <td>
                <span class="badge badge-navy" style="text-transform: uppercase;">{{ $p->payment_method }}</span>
              </td>
              <td>
                <strong style="font-family: 'Plus Jakarta Sans', monospace; color: #f59e0b;">{{ $p->transaction_reference }}</strong>
              </td>
              <td style="font-family: 'Plus Jakarta Sans', monospace; font-weight: 700; color: #10b981;">
                ৳{{ number_format($p->amount, 2) }}
              </td>
              <td>
                <span style="font-size: 12px;">{{ $p->created_at->format('d M Y') }}</span>
              </td>
              <td>
                @if($p->verification_status === 'approved')
                  <span class="badge badge-emerald"><i class="fa-solid fa-check"></i> VERIFIED</span>
                @elseif($p->verification_status === 'rejected')
                  <span class="badge badge-danger"><i class="fa-solid fa-xmark"></i> REJECTED</span>
                  @if($p->rejection_reason) <div style="font-size: 10px; color: #ef4444; margin-top: 2px;">{{ $p->rejection_reason }}</div> @endif
                @else
                  <span class="badge badge-gold"><i class="fa-solid fa-hourglass"></i> PENDING AUDIT</span>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" style="text-align: center; padding: 36px; color: var(--text-muted);">
                No payment submission records found.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>

<!-- Payment Submission Modal -->
<div id="paymentModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); z-index: 1000; align-items: center; justify-content: center;">
  <div class="tactical-card" style="width: 100%; max-width: 520px; margin: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-soft); padding-bottom: 12px; margin-bottom: 16px;">
      <h3 style="font-size: 15px; font-weight: 700; margin: 0; color: var(--accent-gold);">
        <i class="fa-solid fa-credit-card"></i> Submit Fee Payment Verification
      </h3>
      <button onclick="closePaymentModal()" style="background: none; border: none; color: var(--text-muted); cursor: pointer; font-size: 18px;">&times;</button>
    </div>

    <form action="{{ route('cadet.payments.submit') }}" method="POST" enctype="multipart/form-data">
      @csrf

      <div style="display: flex; flex-direction: column; gap: 14px;">
        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 4px;">Target Invoice *</label>
          <select name="invoice_id" id="modal_invoice_id" class="form-tactical" required>
            <option value="">-- Choose Invoice to Pay --</option>
            @foreach($invoices->where('due_amount', '>', 0) as $inv)
              <option value="{{ $inv->id }}" data-due="{{ $inv->due_amount }}">
                {{ $inv->invoice_number }} - {{ $inv->title }} (Due: ৳{{ number_format($inv->due_amount, 2) }})
              </option>
            @endforeach
          </select>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px;">
          <div>
            <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 4px;">Amount Paid (৳) *</label>
            <input type="number" step="0.01" min="10" name="amount" id="modal_amount" class="form-tactical" placeholder="0.00" required>
          </div>

          <div>
            <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 4px;">Payment Gateway *</label>
            <select name="payment_method" class="form-tactical" required>
              <option value="bkash">bKash (Merchant)</option>
              <option value="nagad">Nagad (Merchant)</option>
              <option value="rocket">Rocket</option>
              <option value="bank_transfer">Bank Transfer / Deposit</option>
              <option value="cash">Direct Cash to Office</option>
            </select>
          </div>
        </div>

        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 4px;">bKash TrxID / Bank Deposit Slip Ref *</label>
          <input type="text" name="transaction_reference" class="form-tactical" placeholder="e.g. 9K284JAL2M or Deposit Slip #1283" required>
        </div>

        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 4px;">Payment Screenshot / Receipt Slip (Optional)</label>
          <input type="file" name="payment_proof" class="form-tactical" accept="image/*,.pdf">
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 10px;">
          <button type="button" onclick="closePaymentModal()" class="btn-tactical btn-tactical-outline">Cancel</button>
          <button type="submit" class="btn-tactical btn-tactical-primary">
            <i class="fa-solid fa-check"></i> Submit for Verification
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
  function openPaymentModal() { document.getElementById('paymentModal').style.display = 'flex'; }
  function closePaymentModal() { document.getElementById('paymentModal').style.display = 'none'; }

  function payInvoice(id, number, due) {
    const select = document.getElementById('modal_invoice_id');
    select.value = id;
    document.getElementById('modal_amount').value = parseFloat(due).toFixed(2);
    openPaymentModal();
  }

  document.getElementById('modal_invoice_id').addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    const due = opt.getAttribute('data-due');
    if (due) {
      document.getElementById('modal_amount').value = parseFloat(due).toFixed(2);
    }
  });
</script>
@endif
@endsection
