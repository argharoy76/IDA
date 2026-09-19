@extends('layouts.portal')

@section('title', 'Generate Invoice')
@section('page_title', 'Issue Cadet Fee Invoice')
@section('page_subtitle', 'Generate an official tuition or examination invoice for an active cadet')

@section('topbar_actions')
  <a href="{{ route('admin.fees.index') }}" class="btn-tactical btn-tactical-outline">
    <i class="fa-solid fa-arrow-left"></i> Back to Invoices
  </a>
@endsection

@section('content')
<div style="max-width: 860px; margin: 0 auto;">
  <div class="tactical-card">
    <div style="border-bottom: 1px solid var(--border-soft); padding-bottom: 16px; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between;">
      <div>
        <h3 style="font-size: 16px; font-weight: 700; margin: 0; color: var(--accent-gold);">
          <i class="fa-solid fa-file-invoice-dollar"></i> Invoice Issuance Form
        </h3>
        <p style="font-size: 13px; color: var(--text-muted); margin: 4px 0 0 0;">Automatic net calculation and ledger integration upon verification</p>
      </div>
    </div>

    <form action="{{ route('admin.fees.store') }}" method="POST">
      @csrf

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <!-- Cadet Selector -->
        <div>
          <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Select Cadet *</label>
          <select name="student_id" class="form-tactical" required>
            <option value="">-- Choose Active Cadet --</option>
            @foreach($students as $s)
              <option value="{{ $s->id }}" {{ old('student_id') == $s->id ? 'selected' : '' }}>
                {{ $s->student_id_code }} - {{ $s->user->name ?? 'Cadet' }} (Roll: {{ $s->roll_number }})
              </option>
            @endforeach
          </select>
          @error('student_id') <span style="color: #ef4444; font-size: 11px;">{{ $message }}</span> @enderror
        </div>

        <!-- Fee Category -->
        <div>
          <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Fee Category *</label>
          <select name="fee_type_id" id="fee_type_id" class="form-tactical" required>
            <option value="">-- Select Category --</option>
            @foreach($feeTypes as $ft)
              <option value="{{ $ft->id }}" data-amount="{{ $ft->default_amount }}" {{ old('fee_type_id') == $ft->id ? 'selected' : '' }}>
                {{ $ft->name }} (Default: ৳{{ number_format($ft->default_amount, 2) }})
              </option>
            @endforeach
          </select>
          @error('fee_type_id') <span style="color: #ef4444; font-size: 11px;">{{ $message }}</span> @enderror
        </div>

        <!-- Invoice Title -->
        <div style="grid-column: span 2;">
          <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Invoice Title / Description *</label>
          <input type="text" name="title" value="{{ old('title', 'Academic & ISSB Training Term Fee') }}" placeholder="e.g. 94 BMA Long Course - Term 1 Tuition" class="form-tactical" required>
          @error('title') <span style="color: #ef4444; font-size: 11px;">{{ $message }}</span> @enderror
        </div>

        <!-- Gross Amount -->
        <div>
          <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Gross Amount (৳) *</label>
          <input type="number" step="0.01" min="0" name="gross_amount" id="gross_amount" value="{{ old('gross_amount', 25000) }}" class="form-tactical" required>
          @error('gross_amount') <span style="color: #ef4444; font-size: 11px;">{{ $message }}</span> @enderror
        </div>

        <!-- Due Date -->
        <div>
          <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Payment Due Date *</label>
          <input type="date" name="due_date" value="{{ old('due_date', now()->addDays(14)->format('Y-m-d')) }}" class="form-tactical" required>
          @error('due_date') <span style="color: #ef4444; font-size: 11px;">{{ $message }}</span> @enderror
        </div>

        <!-- Discount Amount -->
        <div>
          <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Scholarship / Discount (৳)</label>
          <input type="number" step="0.01" min="0" name="discount_amount" id="discount_amount" value="{{ old('discount_amount', 0) }}" class="form-tactical">
          @error('discount_amount') <span style="color: #ef4444; font-size: 11px;">{{ $message }}</span> @enderror
        </div>

        <!-- Waiver Amount -->
        <div>
          <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Special Waiver (৳)</label>
          <input type="number" step="0.01" min="0" name="waiver_amount" id="waiver_amount" value="{{ old('waiver_amount', 0) }}" class="form-tactical">
          @error('waiver_amount') <span style="color: #ef4444; font-size: 11px;">{{ $message }}</span> @enderror
        </div>

        <!-- Net Calculation Preview Box -->
        <div style="grid-column: span 2; background: rgba(16, 185, 129, 0.08); border: 1px solid rgba(16, 185, 129, 0.3); border-radius: 8px; padding: 16px; display: flex; justify-content: space-between; align-items: center;">
          <div>
            <div style="font-size: 12px; text-transform: uppercase; font-weight: 700; color: #10b981;">Net Payable Amount</div>
            <div style="font-size: 11px; color: var(--text-muted);">Calculated as: Gross - Discount - Waiver</div>
          </div>
          <div style="font-size: 28px; font-weight: 800; font-family: 'Plus Jakarta Sans', monospace; color: #10b981;" id="net_preview">
            ৳25,000.00
          </div>
        </div>

        <!-- Internal Notes -->
        <div style="grid-column: span 2;">
          <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Internal Notes (Optional)</label>
          <textarea name="notes" rows="3" class="form-tactical" placeholder="e.g. Armed Forces ward discount applied with authorization">{{ old('notes') }}</textarea>
        </div>
      </div>

      <div style="margin-top: 24px; padding-top: 16px; border-top: 1px solid var(--border-soft); display: flex; justify-content: flex-end; gap: 12px;">
        <a href="{{ route('admin.fees.index') }}" class="btn-tactical btn-tactical-outline">Cancel</a>
        <button type="submit" class="btn-tactical btn-tactical-primary">
          <i class="fa-solid fa-check"></i> Generate & Issue Invoice
        </button>
      </div>
    </form>
  </div>
</div>

<script>
  const feeTypeSelect = document.getElementById('fee_type_id');
  const grossInput = document.getElementById('gross_amount');
  const discountInput = document.getElementById('discount_amount');
  const waiverInput = document.getElementById('waiver_amount');
  const netPreview = document.getElementById('net_preview');

  feeTypeSelect.addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    const defaultAmount = selected.getAttribute('data-amount');
    if (defaultAmount && parseFloat(defaultAmount) > 0) {
      grossInput.value = parseFloat(defaultAmount).toFixed(2);
      calcNet();
    }
  });

  function calcNet() {
    const gross = parseFloat(grossInput.value) || 0;
    const discount = parseFloat(discountInput.value) || 0;
    const waiver = parseFloat(waiverInput.value) || 0;
    const net = Math.max(0, gross - discount - waiver);
    netPreview.innerText = '৳' + net.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  [grossInput, discountInput, waiverInput].forEach(inp => inp.addEventListener('input', calcNet));
  calcNet();
</script>
@endsection
