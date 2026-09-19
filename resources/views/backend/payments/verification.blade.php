@extends('layouts.portal')

@section('title', 'Payment Approvals')
@section('page_title', 'Payment Approvals')
@section('page_subtitle', 'Review submitted bKash, Nagad, Bank, and Cash receipts')

@section('topbar_actions')
  <div style="display: flex; gap: 8px;">
    <a href="{{ route('admin.payments.verification', ['status' => 'pending']) }}" class="btn-tactical {{ $status === 'pending' ? 'btn-tactical-primary' : 'btn-tactical-outline' }}">
      <i class="fa-solid fa-clock"></i> Pending ({{ $pendingCount }})
    </a>
    <a href="{{ route('admin.payments.verification', ['status' => 'approved']) }}" class="btn-tactical {{ $status === 'approved' ? 'btn-tactical-primary' : 'btn-tactical-outline' }}">
      <i class="fa-solid fa-circle-check"></i> Approved ({{ $approvedCount }})
    </a>
    <a href="{{ route('admin.payments.verification', ['status' => 'all']) }}" class="btn-tactical {{ $status === 'all' ? 'btn-tactical-primary' : 'btn-tactical-outline' }}">
      All Records
    </a>
  </div>
@endsection

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

  <!-- Alert Info Box -->
  <div style="background: #ecfdf5; border: 1px solid #a7f3d0; border-left: 4px solid var(--brand-emerald); border-radius: 8px; padding: 14px 18px; display: flex; align-items: center; justify-content: space-between;">
    <div style="display: flex; align-items: center; gap: 14px;">
      <i class="fa-solid fa-shield-check" style="color: var(--brand-emerald); font-size: 22px;"></i>
      <div>
        <strong style="font-size: 13.5px; color: #065f46; font-weight: 800;">Automated Ledger Sync</strong>
        <p style="font-size: 12.5px; color: #047857; margin: 2px 0 0 0; line-height: 1.5;">
          Approving a payment automatically creates an inflow entry in the financial ledger and updates the student invoice balance.
        </p>
      </div>
    </div>
  </div>

  <!-- Payments Table -->
  <div class="tactical-card" style="padding: 0; overflow: hidden;">
    <div style="padding: 16px 20px; border-bottom: 1px solid var(--border-soft); display: flex; justify-content: space-between; align-items: center;">
      <h3 style="font-size: 15px; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-money-bill-transfer" style="color: var(--accent-gold);"></i> Submitted Payment Submissions
      </h3>
      <span style="font-size: 12px; color: var(--text-muted);">Total: {{ $payments->total() }}</span>
    </div>

    <div style="overflow-x: auto;">
      <table class="tactical-table">
        <thead>
          <tr>
            <th>Payment ID</th>
            <th>Cadet / Candidate</th>
            <th>Invoice / Reason</th>
            <th>Method</th>
            <th>Trx ID / Ref</th>
            <th>Amount</th>
            <th>Date</th>
            <th>Status</th>
            <th>Verification</th>
          </tr>
        </thead>
        <tbody>
          @forelse($payments as $p)
            <tr>
              <td>
                <span style="font-family: 'Plus Jakarta Sans', monospace; font-weight: 700; color: #60a5fa;">{{ $p->payment_number }}</span>
              </td>
              <td>
                @if($p->student)
                  <a href="{{ route('admin.students.show', $p->student->id) }}" style="font-weight: 700; color: var(--text-main); text-decoration: none;">
                    {{ $p->student->user->name ?? 'Cadet' }}
                  </a>
                  <div style="font-size: 11px; color: var(--accent-gold); font-family: 'Plus Jakarta Sans', monospace;">{{ $p->student->student_id_code }}</div>
                @elseif($p->user)
                  <strong>{{ $p->user->name }}</strong>
                  <div style="font-size: 11px; color: var(--text-muted);">External Candidate</div>
                @else
                  <span style="color: var(--text-muted);">Direct Submission</span>
                @endif
              </td>
              <td>
                @if($p->invoice)
                  <div style="font-size: 12px; font-weight: 600;">{{ $p->invoice->title }}</div>
                  <span style="font-size: 11px; font-family: 'Plus Jakarta Sans', monospace; color: var(--text-muted);">{{ $p->invoice->invoice_number }}</span>
                @else
                  <div style="font-size: 12px; color: var(--text-muted);">Direct Assessment Fee</div>
                @endif
              </td>
              <td>
                <span class="badge badge-navy" style="text-transform: uppercase; font-size: 11px;">
                  <i class="fa-solid fa-credit-card"></i> {{ $p->payment_method }}
                </span>
              </td>
              <td>
                <div style="font-family: 'Plus Jakarta Sans', monospace; font-weight: 600; color: #f59e0b;">
                  {{ $p->transaction_reference ?: 'N/A' }}
                </div>
                @if($p->bank_name || $p->account_last4)
                  <div style="font-size: 10px; color: var(--text-muted);">{{ $p->bank_name }} ({{ $p->account_last4 }})</div>
                @endif
              </td>
              <td>
                <strong style="font-family: 'Plus Jakarta Sans', monospace; font-size: 14px; color: #10b981;">
                  ৳{{ number_format($p->amount, 2) }}
                </strong>
              </td>
              <td>
                <div style="font-size: 12px;">{{ $p->payment_date ? \Carbon\Carbon::parse($p->payment_date)->format('d M Y') : $p->created_at->format('d M Y') }}</div>
              </td>
              <td>
                @if($p->verification_status === 'approved')
                  <span class="badge badge-emerald"><i class="fa-solid fa-check"></i> APPROVED</span>
                  <div style="font-size: 10px; color: var(--text-muted); margin-top: 2px;">By: {{ $p->verifier->name ?? 'Finance' }}</div>
                @elseif($p->verification_status === 'rejected')
                  <span class="badge badge-danger"><i class="fa-solid fa-xmark"></i> REJECTED</span>
                  @if($p->rejection_reason)
                    <div style="font-size: 10px; color: #ef4444; max-width: 140px; margin-top: 2px;">{{ $p->rejection_reason }}</div>
                  @endif
                @else
                  <span class="badge badge-gold"><i class="fa-solid fa-hourglass-half"></i> PENDING</span>
                @endif
              </td>
              <td>
                @if($p->verification_status === 'pending')
                  <div style="display: flex; gap: 6px;">
                    <!-- Approve Form -->
                    <form action="{{ route('admin.payments.approve', $p->id) }}" method="POST" onsubmit="return confirm('Confirm verification of ৳{{ number_format($p->amount, 2) }}? This will instantly credit the academy cash balance.');">
                      @csrf
                      <button type="submit" class="btn-tactical btn-tactical-primary" style="padding: 4px 10px; font-size: 11px;">
                        <i class="fa-solid fa-check"></i> Approve
                      </button>
                    </form>

                    <!-- Reject Button triggers modal -->
                    <button type="button" class="btn-tactical btn-tactical-outline" style="padding: 4px 8px; font-size: 11px; border-color: #ef4444; color: #ef4444;" onclick="openRejectModal('{{ $p->id }}', '{{ $p->payment_number }}', '{{ $p->amount }}')">
                      <i class="fa-solid fa-xmark"></i> Reject
                    </button>
                  </div>
                @else
                  <span style="font-size: 11px; color: var(--text-muted);"><i class="fa-solid fa-lock"></i> Processed</span>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="9" style="text-align: center; padding: 48px; color: var(--text-muted);">
                <i class="fa-solid fa-file-circle-check" style="font-size: 32px; margin-bottom: 12px; display: block; opacity: 0.4;"></i>
                No payment verification records found in this queue.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($payments->hasPages())
      <div style="padding: 16px 20px; border-top: 1px solid var(--border-soft);">
        {{ $payments->links() }}
      </div>
    @endif
  </div>

</div>

<!-- Rejection Modal -->
<div id="rejectModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); z-index: 1000; align-items: center; justify-content: center;">
  <div class="tactical-card" style="width: 100%; max-width: 480px; margin: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-soft); padding-bottom: 12px; margin-bottom: 16px;">
      <h3 style="font-size: 15px; font-weight: 700; color: #ef4444; margin: 0;">
        <i class="fa-solid fa-circle-xmark"></i> Reject Payment Submission
      </h3>
      <button onclick="closeRejectModal()" style="background: none; border: none; color: var(--text-muted); cursor: pointer; font-size: 16px;">&times;</button>
    </div>

    <form id="rejectForm" method="POST" action="">
      @csrf
      <p style="font-size: 13.5px; color: var(--text-muted); margin-bottom: 14px;">
        Payment: <strong id="rejectPaymentNum" style="color: var(--text-main);"></strong> (৳<span id="rejectAmount"></span>)
      </p>

      <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Reason for Rejection *</label>
      <textarea name="rejection_reason" rows="3" class="form-tactical" placeholder="e.g. Transaction ID not found in bKash statement. Incorrect amount." required></textarea>

      <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
        <button type="button" onclick="closeRejectModal()" class="btn-tactical btn-tactical-outline">Cancel</button>
        <button type="submit" class="btn-tactical" style="background: #ef4444; color: #fff;">Confirm Rejection</button>
      </div>
    </form>
  </div>
</div>

<script>
  function openRejectModal(id, number, amount) {
    const modal = document.getElementById('rejectModal');
    const form = document.getElementById('rejectForm');
    form.action = "{{ route('admin.payments.reject', ['id' => ':id']) }}".replace(':id', id);
    document.getElementById('rejectPaymentNum').innerText = number;
    document.getElementById('rejectAmount').innerText = parseFloat(amount).toFixed(2);
    modal.style.display = 'flex';
  }

  function closeRejectModal() {
    document.getElementById('rejectModal').style.display = 'none';
  }
</script>
@endsection
