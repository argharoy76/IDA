@extends('layouts.public')

@section('title', 'Join Us — Create Student Account | Imperial Defence Academy')

@section('content')
<section style="min-height: calc(100vh - 200px); background: radial-gradient(circle at 50% 0%, rgba(5, 150, 105, 0.08) 0%, #f8fafc 70%); display: flex; align-items: center; justify-content: center; padding: 60px 20px; position: relative;">
  <style>
    .register-form-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px;
    }
    .register-card {
      background: #ffffff;
      border: 1px solid #e2e8f0;
      border-radius: 22px;
      padding: 42px 38px;
      box-shadow: 0 20px 45px -15px rgba(15, 23, 42, 0.08), 0 1px 3px rgba(15, 23, 42, 0.04);
      font-family: 'Poppins', sans-serif;
    }
    .reg-input {
      width: 100%;
      background: #ffffff;
      border: 1.5px solid #cbd5e1;
      border-radius: 10px;
      padding: 12px 14px 12px 40px;
      color: #0f172a;
      font-size: 13.5px;
      font-family: 'Poppins', sans-serif;
      outline: none;
      transition: all 0.2s ease;
    }
    .reg-input:focus {
      border-color: #059669 !important;
      box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.14) !important;
    }
    @media (max-width: 640px) {
      .register-form-grid {
        grid-template-columns: 1fr !important;
      }
      .register-form-grid > div {
        grid-column: span 1 !important;
      }
      .register-card {
        padding: 28px 20px !important;
        border-radius: 18px !important;
      }
    }
  </style>
  
  <div style="position: relative; width: 100%; max-width: 680px;">
    
    <!-- Central Registration Card -->
    <div class="register-card">
      
      <!-- Academy Header -->
      <div style="text-align: center; margin-bottom: 24px;">
        <div style="width: 60px; height: 60px; border-radius: 16px; background: #ecfdf5; border: 1.5px solid #a7f3d0; display: grid; place-items: center; font-size: 26px; color: #059669; margin: 0 auto 14px;">
          <i class="fa-solid fa-user-plus"></i>
        </div>
        <span style="display: inline-block; font-size: 11px; font-weight: 700; color: #047857; letter-spacing: 0.8px; text-transform: uppercase; background: #ecfdf5; border: 1px solid #a7f3d0; padding: 3px 12px; border-radius: 20px; margin-bottom: 8px;">
          <i class="fa-solid fa-gift" style="margin-right: 4px;"></i> Free Registration &bull; No Upfront Payment
        </span>
        <h1 style="font-size: 24px; font-weight: 800; color: #0f172a; margin: 0 0 6px 0;">
          Join Us — Create Student Account
        </h1>
        <p style="font-size: 13px; color: #64748b; margin: 0;">
          Register your profile today. Courses and exams will be assigned to your account upon selection.
        </p>
      </div>

      <!-- Info Banner: No Course Assigned Initially -->
      <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 12px 14px; margin-bottom: 22px; font-size: 12.5px; color: #166534; display: flex; align-items: center; gap: 10px;">
        <i class="fa-solid fa-circle-info" style="font-size: 16px; color: #059669; flex-shrink: 0;"></i>
        <span>Free account creation. You can register now, and our administration will assign your enrolled course(s) whenever you choose.</span>
      </div>

      <!-- Validation Error Display -->
      @if($errors->any())
        <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 10px; padding: 14px 16px; margin-bottom: 20px; color: #991b1b; font-size: 13px;">
          <strong><i class="fa-solid fa-triangle-exclamation" style="margin-right: 6px; color: #ef4444;"></i> Please correct the following errors:</strong>
          <ul style="margin: 6px 0 0 18px; padding: 0;">
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form method="POST" action="{{ route('register.post') }}">
        @csrf
        <input type="hidden" name="student_type" value="{{ old('student_type', 'external') }}">

        <!-- 2-Column Responsive Grid for the 6 Required Form Fields -->
        <div class="register-form-grid">
          
          <!-- 1. Full Name -->
          <div style="grid-column: span 2;">
            <label style="display: block; font-size: 11.5px; font-weight: 700; text-transform: uppercase; color: #475569; margin-bottom: 6px; letter-spacing: 0.5px;">
              Full Name *
            </label>
            <div style="position: relative;">
              <i class="fa-regular fa-user" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 14px;"></i>
              <input type="text" name="name" required class="reg-input"
                     placeholder="Candidate's full name" value="{{ old('name') }}">
            </div>
          </div>

          <!-- 2. Age -->
          <div>
            <label style="display: block; font-size: 11.5px; font-weight: 700; text-transform: uppercase; color: #475569; margin-bottom: 6px; letter-spacing: 0.5px;">
              Age *
            </label>
            <div style="position: relative;">
              <i class="fa-regular fa-calendar-check" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 14px;"></i>
              <input type="number" name="age" required min="12" max="60" class="reg-input"
                     placeholder="e.g. 19" value="{{ old('age') }}">
            </div>
          </div>

          <!-- 3. Gender -->
          <div>
            <label style="display: block; font-size: 11.5px; font-weight: 700; text-transform: uppercase; color: #475569; margin-bottom: 6px; letter-spacing: 0.5px;">
              Gender *
            </label>
            <div style="position: relative;">
              <select name="gender" required class="reg-input" style="padding-left: 14px;">
                <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                <option value="other" {{ old('gender') === 'other' ? 'selected' : '' }}>Other</option>
              </select>
            </div>
          </div>

          <!-- 4. Mobile Number -->
          <div>
            <label style="display: block; font-size: 11.5px; font-weight: 700; text-transform: uppercase; color: #475569; margin-bottom: 6px; letter-spacing: 0.5px;">
              Mobile / WhatsApp Number *
            </label>
            <div style="position: relative;">
              <i class="fa-solid fa-phone" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 13px;"></i>
              <input type="text" name="phone" required class="reg-input"
                     placeholder="017XXXXXXXX" value="{{ old('phone') }}">
            </div>
          </div>

          <!-- 5. Email Address -->
          <div>
            <label style="display: block; font-size: 11.5px; font-weight: 700; text-transform: uppercase; color: #475569; margin-bottom: 6px; letter-spacing: 0.5px;">
              Email Address *
            </label>
            <div style="position: relative;">
              <i class="fa-regular fa-envelope" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 14px;"></i>
              <input type="email" name="email" required class="reg-input"
                     placeholder="candidate@example.com" value="{{ old('email') }}">
            </div>
          </div>

          <!-- 6. Address -->
          <div style="grid-column: span 2;">
            <label style="display: block; font-size: 11.5px; font-weight: 700; text-transform: uppercase; color: #475569; margin-bottom: 6px; letter-spacing: 0.5px;">
              Present / Permanent Address *
            </label>
            <div style="position: relative;">
              <i class="fa-solid fa-location-dot" style="position: absolute; left: 14px; top: 16px; color: #94a3b8; font-size: 14px;"></i>
              <textarea name="address" required rows="2" class="reg-input" style="padding-top: 12px; resize: vertical;"
                        placeholder="House / Village, Post Office, Police Station, District">{{ old('address') }}</textarea>
            </div>
          </div>

        </div>

        <!-- Optional Academic Details -->
        <details style="margin-top: 14px; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 10px; padding: 10px 14px;">
          <summary style="cursor: pointer; font-size: 12px; font-weight: 600; color: #475569; user-select: none;">
            + Additional Info (Target Wing, College, HSC Year) — Optional
          </summary>
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 12px;">
            <div>
              <label style="font-size: 11px; color: #64748b; display: block; margin-bottom: 3px;">Target Wing</label>
              <select name="target_wing" class="reg-input" style="padding: 8px 10px; font-size: 12px;">
                <option value="Army">Army</option>
                <option value="Navy">Navy</option>
                <option value="Airforce">Air Force</option>
                <option value="Police">Police</option>
              </select>
            </div>
            <div>
              <label style="font-size: 11px; color: #64748b; display: block; margin-bottom: 3px;">College / Institution</label>
              <input type="text" name="institution" value="{{ old('institution') }}" placeholder="e.g. Dhaka College" class="reg-input" style="padding: 8px 10px; font-size: 12px;">
            </div>
            <div>
              <label style="font-size: 11px; color: #64748b; display: block; margin-bottom: 3px;">HSC Year</label>
              <input type="text" name="hsc_year" value="{{ old('hsc_year') }}" placeholder="2025" class="reg-input" style="padding: 8px 10px; font-size: 12px;">
            </div>
            <div>
              <label style="font-size: 11px; color: #64748b; display: block; margin-bottom: 3px;">District</label>
              <input type="text" name="district" value="{{ old('district') }}" placeholder="Dhaka" class="reg-input" style="padding: 8px 10px; font-size: 12px;">
            </div>
          </div>
        </details>

        <!-- Password & Confirm Password -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-top: 18px;">
          <div>
            <label style="display: block; font-size: 11.5px; font-weight: 700; text-transform: uppercase; color: #475569; margin-bottom: 6px; letter-spacing: 0.5px;">
              Password *
            </label>
            <div style="position: relative;">
              <i class="fa-solid fa-lock" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 13px;"></i>
              <input type="password" name="password" id="regPassword" required class="reg-input" style="padding-right: 38px;"
                     placeholder="Min. 6 characters">
              <button type="button" onclick="togglePasswordVisibility('regPassword', 'eyeIcon1')" 
                      style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #94a3b8; cursor: pointer; font-size: 14px; padding: 4px;">
                <i class="fa-regular fa-eye" id="eyeIcon1"></i>
              </button>
            </div>
          </div>

          <div>
            <label style="display: block; font-size: 11.5px; font-weight: 700; text-transform: uppercase; color: #475569; margin-bottom: 6px; letter-spacing: 0.5px;">
              Confirm Password *
            </label>
            <div style="position: relative;">
              <i class="fa-solid fa-lock" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 13px;"></i>
              <input type="password" name="password_confirmation" id="regPasswordConfirm" required class="reg-input" style="padding-right: 38px;"
                     placeholder="Re-enter password">
              <button type="button" onclick="togglePasswordVisibility('regPasswordConfirm', 'eyeIcon2')" 
                      style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #94a3b8; cursor: pointer; font-size: 14px; padding: 4px;">
                <i class="fa-regular fa-eye" id="eyeIcon2"></i>
              </button>
            </div>
          </div>
        </div>

        <!-- Submit Button -->
        <button type="submit" 
                style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px; background: #059669; color: #ffffff; font-weight: 700; font-size: 14.5px; padding: 14px; border-radius: 10px; border: 1px solid #047857; box-shadow: 0 4px 15px rgba(5, 150, 105, 0.25); cursor: pointer; margin-top: 24px; transition: all 0.2s ease;">
          <i class="fa-solid fa-user-plus"></i> Join Us — Create Free Account
        </button>

        <p style="text-align: center; font-size: 13px; color: #64748b; margin: 20px 0 0 0;">
          Already have a cadet account? 
          <a href="{{ route('login') }}" style="color: #059669; font-weight: 700; text-decoration: none; margin-left: 4px;">
            Sign In here &rarr;
          </a>
        </p>
      </form>
    </div>
  </div>
</section>

<script>
  function togglePasswordVisibility(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    if (input.type === 'password') {
      input.type = 'text';
      icon.className = 'fa-regular fa-eye-slash';
    } else {
      input.type = 'password';
      icon.className = 'fa-regular fa-eye';
    }
  }
</script>
@endsection