@extends('layouts.portal')

@section('title', 'Ledger')
@section('page_title', 'Academy Financial Ledger')
@section('page_subtitle', 'Track verified inflows, operating expenses, and current cash balance')

@section('topbar_actions')
  <div style="display: flex; gap: 8px;">
    <button type="button" class="btn-tactical btn-tactical-outline" onclick="openExpenseModal()">
      <i class="fa-solid fa-arrow-up-right-from-square" style="color: #ef4444;"></i> Record Expense
    </button>
    <button type="button" class="btn-tactical btn-tactical-primary" onclick="openInflowModal()">
      <i class="fa-solid fa-arrow-down-left-and-up-right-to-center" style="color: #10b981;"></i> Record Inflow
    </button>
  </div>
@endsection

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

  <!-- Primary Financial Metrics Strip -->
  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 16px;">
    <!-- Cash Balance (Highlighted Hero) -->
    <div class="stat-card" style="background: linear-gradient(135deg, rgba(6, 78, 59, 0.4), rgba(15, 23, 42, 0.8)); border: 1px solid rgba(16, 185, 129, 0.4); grid-column: span 1;">
      <div class="stat-icon" style="background: rgba(16, 185, 129, 0.2); color: #10b981;">
        <i class="fa-solid fa-vault"></i>
      </div>
      <div>
        <div style="display: flex; align-items: center; gap: 6px;">
          <span class="stat-label" style="color: #6ee7b7;">Current Cash Balance</span>
          <span title="Strictly calculated from opening balance + total verified inflows - total recorded outflows" style="color: #6ee7b7; cursor: help; font-size: 11px;"><i class="fa-solid fa-lock"></i> DERIVED</span>
        </div>
        <div class="stat-value" style="color: #10b981; font-size: 28px;">
          ৳{{ number_format($currentBalance, 2) }}
        </div>
        <div style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">
          Lifetime: +৳{{ number_format($totalInflow, 0) }} / -৳{{ number_format($totalOutflow, 0) }}
        </div>
      </div>
    </div>

    <!-- Monthly Net Flow -->
    <div class="stat-card">
      <div class="stat-icon" style="background: rgba(59, 130, 246, 0.15); color: #60a5fa;">
        <i class="fa-solid fa-calendar-day"></i>
      </div>
      <div>
        <div class="stat-label">This Month Cash Flow</div>
        <div class="stat-value" style="font-size: 24px; color: {{ ($monthlyIncome - $monthlyExpense) >= 0 ? '#10b981' : '#ef4444' }};">
          {{ ($monthlyIncome - $monthlyExpense) >= 0 ? '+' : '' }}৳{{ number_format($monthlyIncome - $monthlyExpense, 2) }}
        </div>
        <div style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">
          Inflow: ৳{{ number_format($monthlyIncome, 0) }} | Outflow: ৳{{ number_format($monthlyExpense, 0) }}
        </div>
      </div>
    </div>

    <!-- Today Inflow / Outflow -->
    <div class="stat-card">
      <div class="stat-icon" style="background: rgba(217, 119, 6, 0.15); color: #d97706;">
        <i class="fa-solid fa-clock-rotate-left"></i>
      </div>
      <div>
        <div class="stat-label">Today's Activity</div>
        <div class="stat-value" style="font-size: 22px;">
          ৳{{ number_format($todayIncome, 2) }}
        </div>
        <div style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">
          Expense today: ৳{{ number_format($todayExpense, 2) }}
        </div>
      </div>
    </div>

    <!-- Outstanding Receivables -->
    <div class="stat-card">
      <div class="stat-icon" style="background: rgba(239, 68, 68, 0.15); color: #ef4444;">
        <i class="fa-solid fa-hand-holding-dollar"></i>
      </div>
      <div>
        <div class="stat-label">Cadet Dues Outstanding</div>
        <div class="stat-value" style="font-size: 22px; color: #ef4444;">
          ৳{{ number_format($outstandingFees, 2) }}
        </div>
        <div style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">
          Pending verification: ৳{{ number_format($pendingPayments, 2) }}
        </div>
      </div>
    </div>
  </div>

  <!-- Filter & Ledger Search -->
  <div class="tactical-card" style="padding: 16px;">
    <form action="{{ route('admin.finance.index') }}" method="GET" style="display: flex; gap: 14px; flex-wrap: wrap; align-items: flex-end;">
      <div style="width: 160px;">
        <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 6px;">Flow Type</label>
        <select name="type" class="form-tactical" style="height: 40px;">
          <option value="">All Flow Types</option>
          <option value="inflow" {{ request('type') === 'inflow' ? 'selected' : '' }}>Inflow (Income)</option>
          <option value="outflow" {{ request('type') === 'outflow' ? 'selected' : '' }}>Outflow (Expense)</option>
        </select>
      </div>

      <div style="width: 220px;">
        <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 6px;">Category</label>
        <select name="category_id" class="form-tactical" style="height: 40px;">
          <option value="">All Categories</option>
          @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
              {{ $cat->name }} ({{ strtoupper($cat->type) }})
            </option>
          @endforeach
        </select>
      </div>

      <div style="width: 170px;">
        <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 6px;">From Date</label>
        <input type="date" name="from_date" value="{{ request('from_date') }}" class="form-tactical" style="height: 40px;">
      </div>

      <div style="width: 170px;">
        <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 6px;">To Date</label>
        <input type="date" name="to_date" value="{{ request('to_date') }}" class="form-tactical" style="height: 40px;">
      </div>

      <button type="submit" class="btn-tactical btn-tactical-outline" style="height: 40px;">
        <i class="fa-solid fa-filter"></i> Filter
      </button>

      @if(request()->hasAny(['type', 'category_id', 'from_date', 'to_date']))
        <a href="{{ route('admin.finance.index') }}" class="btn-tactical" style="height: 40px; background: rgba(255,255,255,0.06); color: var(--text-muted);">
          <i class="fa-solid fa-xmark"></i> Clear
        </a>
      @endif
    </form>
  </div>

  <!-- Transactions Table -->
  <div class="tactical-card" style="padding: 0; overflow: hidden;">
    <div style="padding: 16px 20px; border-bottom: 1px solid var(--border-soft); display: flex; justify-content: space-between; align-items: center;">
      <h3 style="font-size: 15px; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-book-journal-whills" style="color: var(--accent-gold);"></i> Double-Entry Cash Ledger
      </h3>
      <span style="font-size: 12px; color: var(--text-muted);">Showing {{ $transactions->firstItem() ?? 0 }} - {{ $transactions->lastItem() ?? 0 }} of {{ $transactions->total() }}</span>
    </div>

    <div style="overflow-x: auto;">
      <table class="tactical-table">
        <thead>
          <tr>
            <th>Txn Number</th>
            <th>Date</th>
            <th>Flow Type</th>
            <th>Category</th>
            <th>Payee / Source</th>
            <th>Payment Method & Ref</th>
            <th>Amount (৳)</th>
            <th>Recorded By</th>
          </tr>
        </thead>
        <tbody>
          @forelse($transactions as $t)
            <tr>
              <td>
                <span style="font-family: 'Plus Jakarta Sans', monospace; font-weight: 700; color: #60a5fa;">{{ $t->transaction_number }}</span>
              </td>
              <td>
                <div style="font-size: 12px;">{{ \Carbon\Carbon::parse($t->transaction_date)->format('d M Y') }}</div>
              </td>
              <td>
                @if($t->type === 'inflow')
                  <span class="badge badge-emerald"><i class="fa-solid fa-arrow-down"></i> INFLOW</span>
                @else
                  <span class="badge badge-danger"><i class="fa-solid fa-arrow-up"></i> OUTFLOW</span>
                @endif
              </td>
              <td>
                <div style="font-weight: 600; font-size: 13px;">{{ $t->category->name ?? 'General' }}</div>
                <div style="font-size: 11px; color: var(--text-muted);">{{ Str::limit($t->description, 35) }}</div>
              </td>
              <td>
                <strong style="font-size: 12.5px;">{{ $t->source_payee }}</strong>
              </td>
              <td>
                <span class="badge badge-navy" style="text-transform: uppercase; font-size: 10px;">{{ $t->payment_method }}</span>
                @if($t->reference_no)
                  <span style="font-family: 'Plus Jakarta Sans', monospace; font-size: 11px; color: #f59e0b; margin-left: 4px;">{{ $t->reference_no }}</span>
                @endif
              </td>
              <td>
                <span style="font-family: 'Plus Jakarta Sans', monospace; font-weight: 800; font-size: 14px; color: {{ $t->type === 'inflow' ? '#10b981' : '#ef4444' }};">
                  {{ $t->type === 'inflow' ? '+' : '-' }}৳{{ number_format($t->amount, 2) }}
                </span>
              </td>
              <td>
                <div style="font-size: 11px; color: var(--text-muted);">{{ $t->recorder->name ?? 'System' }}</div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" style="text-align: center; padding: 48px; color: var(--text-muted);">
                <i class="fa-solid fa-vault" style="font-size: 32px; margin-bottom: 12px; display: block; opacity: 0.4;"></i>
                No financial transactions logged yet.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($transactions->hasPages())
      <div style="padding: 16px 20px; border-top: 1px solid var(--border-soft);">
        {{ $transactions->links() }}
      </div>
    @endif
  </div>

</div>

<!-- Modal: Record Transaction (Inflow or Outflow) -->
<div id="txnModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); z-index: 1000; align-items: center; justify-content: center;">
  <div class="tactical-card" style="width: 100%; max-width: 560px; margin: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-soft); padding-bottom: 12px; margin-bottom: 20px;">
      <h3 style="font-size: 16px; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 8px;" id="modalTitle">
        Record Financial Entry
      </h3>
      <button onclick="closeTxnModal()" style="background: none; border: none; color: var(--text-muted); cursor: pointer; font-size: 18px;">&times;</button>
    </div>

    <form action="{{ route('admin.finance.store') }}" method="POST">
      @csrf
      <input type="hidden" name="type" id="txn_type" value="outflow">

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
        <div style="grid-column: span 2;">
          <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Category *</label>
          <select name="category_id" id="modal_category" class="form-tactical" required>
            <option value="">-- Choose Category --</option>
            @foreach($categories as $c)
              <option value="{{ $c->id }}" data-type="{{ $c->type }}">[{{ strtoupper($c->type) }}] {{ $c->name }}</option>
            @endforeach
          </select>
        </div>

        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Amount (৳) *</label>
          <input type="number" step="0.01" min="1" name="amount" class="form-tactical" placeholder="0.00" required>
        </div>

        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Transaction Date *</label>
          <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" class="form-tactical" required>
        </div>

        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;" id="payeeLabel">Source / Payee *</label>
          <input type="text" name="source_payee" class="form-tactical" placeholder="e.g. Khulna City Utility / Vendor" required>
        </div>

        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Payment Method *</label>
          <select name="payment_method" class="form-tactical" required>
            <option value="Cash">Cash In Hand</option>
            <option value="Bank">Bank Transfer / Cheque</option>
            <option value="bKash">bKash Merchant</option>
            <option value="Nagad">Nagad Merchant</option>
          </select>
        </div>

        <div style="grid-column: span 2;">
          <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Cheque / Trx / Voucher Ref #</label>
          <input type="text" name="reference_no" class="form-tactical" placeholder="e.g. CHQ-991823 or Voucher #102">
        </div>

        <div style="grid-column: span 2;">
          <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Description / Purpose *</label>
          <textarea name="description" rows="2" class="form-tactical" placeholder="Brief military accounting justification..." required></textarea>
        </div>
      </div>

      <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px; padding-top: 16px; border-top: 1px solid var(--border-soft);">
        <button type="button" onclick="closeTxnModal()" class="btn-tactical btn-tactical-outline">Cancel</button>
        <button type="submit" class="btn-tactical btn-tactical-primary" id="submitBtn">
          Confirm Entry
        </button>
      </div>
    </form>
  </div>
</div>

<script>
  function openExpenseModal() {
    document.getElementById('txn_type').value = 'outflow';
    document.getElementById('modalTitle').innerHTML = '<i class="fa-solid fa-arrow-trend-down" style="color: #ef4444;"></i> Record Academy Expenditure / Outflow';
    document.getElementById('payeeLabel').innerText = 'Payee / Vendor Name *';
    document.getElementById('submitBtn').className = 'btn-tactical btn-tactical-outline';
    document.getElementById('submitBtn').style.borderColor = '#ef4444';
    document.getElementById('submitBtn').style.color = '#ef4444';
    filterCategories('outflow');
    document.getElementById('txnModal').style.display = 'flex';
  }

  function openInflowModal() {
    document.getElementById('txn_type').value = 'inflow';
    document.getElementById('modalTitle').innerHTML = '<i class="fa-solid fa-arrow-trend-up" style="color: #10b981;"></i> Record Direct Academy Revenue / Inflow';
    document.getElementById('payeeLabel').innerText = 'Source / Payer Name *';
    document.getElementById('submitBtn').className = 'btn-tactical btn-tactical-primary';
    document.getElementById('submitBtn').style.borderColor = '';
    document.getElementById('submitBtn').style.color = '';
    filterCategories('inflow');
    document.getElementById('txnModal').style.display = 'flex';
  }

  function filterCategories(type) {
    const select = document.getElementById('modal_category');
    const options = select.querySelectorAll('option');
    options.forEach(opt => {
      if (!opt.value) return;
      if (opt.getAttribute('data-type') === type) {
        opt.style.display = '';
      } else {
        opt.style.display = 'none';
      }
    });
    select.value = '';
  }

  function closeTxnModal() {
    document.getElementById('txnModal').style.display = 'none';
  }
</script>
@endsection
