@extends('layouts.public')

@section('title', 'Cadet Portal Login | Imperial Defence Academy')

@section('content')
<section style="min-height: calc(100vh - 200px); background: radial-gradient(circle at 50% 0%, rgba(5, 150, 105, 0.08) 0%, #f8fafc 70%); display: flex; align-items: center; justify-content: center; padding: 60px 20px; position: relative;">
  <style>
    .cadet-login-card {
      background: #ffffff;
      border: 1px solid #e2e8f0;
      border-radius: 22px;
      padding: 42px 38px;
      box-shadow: 0 20px 45px -15px rgba(15, 23, 42, 0.08), 0 1px 3px rgba(15, 23, 42, 0.04);
      font-family: 'Poppins', sans-serif;
    }
    .cadet-input {
      width: 100%;
      background: #ffffff;
      border: 1.5px solid #cbd5e1;
      border-radius: 10px;
      padding: 12px 14px 12px 42px;
      color: #0f172a;
      font-size: 14px;
      outline: none;
      transition: all 0.2s ease;
    }
    .cadet-input:focus {
      border-color: #059669 !important;
      box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.14) !important;
    }
    .cadet-submit-btn {
      width: 100%;
      background: #059669;
      color: #ffffff;
      font-size: 14px;
      font-weight: 700;
      padding: 13px 20px;
      border: 1px solid #047857;
      border-radius: 10px;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      box-shadow: 0 4px 14px rgba(5, 150, 105, 0.25);
      transition: all 0.2s ease;
    }
    .cadet-submit-btn:hover {
      background: #047857;
      transform: translateY(-1px);
      box-shadow: 0 6px 20px rgba(5, 150, 105, 0.35);
    }
    .cadet-join-btn {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      width: 100%;
      background: #f8fafc;
      color: #334155;
      font-size: 13.5px;
      font-weight: 600;
      padding: 12px;
      border: 1.5px solid #e2e8f0;
      border-radius: 10px;
      text-decoration: none;
      transition: all 0.2s ease;
    }
    .cadet-join-btn:hover {
      background: #f0fdf4;
      border-color: #a7f3d0;
      color: #047857;
    }
    @media (max-width: 480px) {
      .cadet-login-card {
        padding: 28px 20px !important;
        border-radius: 18px !important;
      }
    }
  </style>
  
  <div style="position: relative; width: 100%; max-width: 460px;">
    
    <!-- Central Light Login Card -->
    <div class="cadet-login-card">
      
      <!-- Academy Header -->
      <div style="text-align: center; margin-bottom: 26px;">
        <div style="width: 60px; height: 60px; border-radius: 16px; background: #ecfdf5; border: 1.5px solid #a7f3d0; display: grid; place-items: center; font-size: 26px; color: #059669; margin: 0 auto 14px;">
          <i class="fa-solid fa-graduation-cap"></i>
        </div>
        <span style="display: inline-block; font-size: 11px; font-weight: 700; color: #047857; letter-spacing: 0.8px; text-transform: uppercase; background: #ecfdf5; border: 1px solid #a7f3d0; padding: 3px 12px; border-radius: 20px; margin-bottom: 8px;">
          <i class="fa-solid fa-shield-halved" style="margin-right: 4px;"></i> Cadet Portal Access
        </span>
        <h1 style="font-size: 24px; font-weight: 800; color: #0f172a; margin: 0 0 6px 0;">
          Cadet Portal Login
        </h1>
        <p style="font-size: 13px; color: #64748b; margin: 0; line-height: 1.5;">
          Enter your candidate credentials to access your squadron flight deck, routine, and exam dossier.
        </p>
      </div>

      <!-- Quick Session Messages -->
      @if(session('success'))
        <div style="background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 10px; padding: 12px 14px; margin-bottom: 20px; color: #065f46; font-size: 13px; display: flex; align-items: center; gap: 8px;">
          <i class="fa-solid fa-circle-check" style="color: #059669;"></i>
          <span>{{ session('success') }}</span>
        </div>
      @endif

      @if($errors->any())
        <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 10px; padding: 12px 14px; margin-bottom: 20px; color: #991b1b; font-size: 13px;">
          <div style="display: flex; align-items: gap: 8px; font-weight: 700;">
            <i class="fa-solid fa-triangle-exclamation" style="color: #ef4444;"></i>
            <span>{{ $errors->first() }}</span>
          </div>
        </div>
      @endif

      <!-- Login Form -->
      <form method="POST" action="{{ route('login.post') }}">
        @csrf

        <!-- 1. Candidate / Account ID or Email -->
        <div style="margin-bottom: 18px;">
          <label style="display: block; font-size: 11.5px; font-weight: 700; text-transform: uppercase; color: #475569; margin-bottom: 6px; letter-spacing: 0.5px;">
            Cadet ID or Email Address *
          </label>
          <div style="position: relative;">
            <i class="fa-regular fa-id-badge" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 15px;"></i>
            <input type="text" name="login_id" required autofocus
                   value="{{ old('login_id', old('email')) }}"
                   class="cadet-input"
                   placeholder="e.g. 250236 or student@example.com">
          </div>
        </div>

        <!-- 2. Password -->
        <div style="margin-bottom: 18px;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
            <label style="display: block; font-size: 11.5px; font-weight: 700; text-transform: uppercase; color: #475569; letter-spacing: 0.5px; margin-bottom: 0;">
              Password *
            </label>
          </div>
          <div style="position: relative;">
            <i class="fa-solid fa-lock" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 14px;"></i>
            <input type="password" name="password" id="candidatePasswordInput" required
                   class="cadet-input"
                   style="padding-right: 42px;"
                   placeholder="Enter your account password">
            <button type="button" onclick="toggleCandidatePassword()" 
                    style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #94a3b8; cursor: pointer; font-size: 14px;">
              <i class="fa-regular fa-eye" id="toggleCandidateEye"></i>
            </button>
          </div>
        </div>

        <!-- Remember Me -->
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 22px;">
          <label style="display: flex; align-items: center; gap: 8px; font-size: 12.5px; color: #475569; cursor: pointer; user-select: none;">
            <input type="checkbox" name="remember" style="accent-color: #059669; width: 15px; height: 15px; cursor: pointer;">
            <span>Remember me</span>
          </label>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="cadet-submit-btn">
          <i class="fa-solid fa-arrow-right-to-bracket"></i>
          <span>Sign In to Cadet Portal</span>
        </button>

      </form>

      <!-- Divider -->
      <div style="position: relative; text-align: center; margin: 26px 0 20px;">
        <hr style="border: 0; border-top: 1px solid #e2e8f0;">
        <span style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: #ffffff; padding: 0 14px; font-size: 11px; font-weight: 600; color: #94a3b8; text-transform: uppercase;">
          New to IDA?
        </span>
      </div>

      <!-- Join Us Button -->
      <a href="{{ route('register') }}" class="cadet-join-btn">
        <i class="fa-solid fa-user-plus" style="color: #059669;"></i>
        <span>Join Us — Create Free Student Account</span>
      </a>

    </div>
  </div>

</section>

<script>
  function toggleCandidatePassword() {
    const input = document.getElementById('candidatePasswordInput');
    const icon = document.getElementById('toggleCandidateEye');
    if (input.type === 'password') {
      input.type = 'text';
      icon.classList.remove('fa-eye');
      icon.classList.add('fa-eye-slash');
    } else {
      input.type = 'password';
      icon.classList.remove('fa-eye-slash');
      icon.classList.add('fa-eye');
    }
  }
</script>
@endsection