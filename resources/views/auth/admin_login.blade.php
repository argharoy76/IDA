<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin & Command Portal Access | Imperial Defence Academy</title>

  <!-- Google Fonts: Roboto -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,300;0,400;0,500;0,700;0,900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    body {
      background-color: #0b0e17;
      color: #cbd5e1;
      font-family: 'Roboto', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      position: relative;
      overflow-x: hidden;
    }

    /* Tactical Matte Grid Background with Radial Lighting */
    body::before {
      content: '';
      position: absolute;
      inset: 0;
      background-image: 
        radial-gradient(circle at 50% 0%, rgba(255, 87, 87, 0.14) 0%, transparent 60%),
        radial-gradient(circle at 85% 90%, rgba(16, 185, 129, 0.05) 0%, transparent 50%),
        linear-gradient(rgba(255, 255, 255, 0.018) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255, 255, 255, 0.018) 1px, transparent 1px);
      background-size: 100% 100%, 100% 100%, 42px 42px, 42px 42px;
      pointer-events: none;
      z-index: 0;
    }

    /* Top Command Header Bar */
    .top-command-bar {
      position: relative;
      z-index: 2;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 16px 28px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.06);
      background: rgba(11, 14, 23, 0.75);
      backdrop-filter: blur(12px);
    }
    .brand-section {
      display: flex;
      align-items: center;
      gap: 12px;
      text-decoration: none;
    }
    .brand-icon {
      width: 38px;
      height: 38px;
      border-radius: 9px;
      background: rgba(255, 87, 87, 0.15);
      border: 1px solid rgba(255, 87, 87, 0.35);
      display: flex;
      align-items: center;
      justify-content: center;
      color: #ff5757;
      font-size: 18px;
    }
    .brand-text h1 {
      font-size: 14px;
      font-weight: 800;
      color: #ffffff;
      letter-spacing: 0.8px;
      text-transform: uppercase;
      line-height: 1.2;
    }
    .brand-text span {
      font-size: 11px;
      font-weight: 600;
      color: #ff5757;
      letter-spacing: 1px;
      text-transform: uppercase;
    }
    .top-links {
      display: flex;
      align-items: center;
      gap: 14px;
    }
    .top-link {
      font-size: 12px;
      font-weight: 500;
      color: #94a3b8;
      text-decoration: none;
      display: flex;
      align-items: center;
      gap: 6px;
      padding: 6px 12px;
      border-radius: 6px;
      border: 1px solid rgba(255, 255, 255, 0.08);
      background: rgba(255, 255, 255, 0.02);
      transition: all 0.2s;
    }
    .top-link:hover {
      color: #ffffff;
      background: rgba(255, 255, 255, 0.06);
      border-color: rgba(255, 255, 255, 0.15);
    }

    /* Main Content Container */
    .main-container {
      position: relative;
      z-index: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 40px 20px;
      flex: 1;
    }

    /* Tactical Login Portal Box */
    .portal-box {
      width: 100%;
      max-width: 460px;
      background: #121622;
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 18px;
      padding: 36px 32px;
      box-shadow: 0 25px 60px rgba(0, 0, 0, 0.7), 0 0 35px rgba(255, 87, 87, 0.08);
      position: relative;
    }

    /* Top glowing accent line */
    .portal-box::before {
      content: '';
      position: absolute;
      top: 0;
      left: 15%;
      right: 15%;
      height: 2px;
      background: linear-gradient(90deg, transparent, #ff5757, transparent);
    }

    .portal-header {
      text-align: center;
      margin-bottom: 24px;
    }
    .badge-secure {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-size: 10.5px;
      font-weight: 700;
      color: #ff8585;
      text-transform: uppercase;
      letter-spacing: 1px;
      padding: 4px 12px;
      background: rgba(255, 87, 87, 0.1);
      border: 1px solid rgba(255, 87, 87, 0.25);
      border-radius: 20px;
      margin-bottom: 12px;
    }
    .portal-title {
      font-size: 20px;
      font-weight: 800;
      color: #ffffff;
      margin-bottom: 6px;
      letter-spacing: 0.5px;
    }
    .portal-subtitle {
      font-size: 12.5px;
      color: #8c96a8;
      line-height: 1.5;
    }

    /* Form Fields */
    .form-group {
      margin-bottom: 18px;
    }
    .form-label {
      display: block;
      font-size: 11px;
      font-weight: 700;
      color: #94a3b8;
      text-transform: uppercase;
      letter-spacing: 0.8px;
      margin-bottom: 6px;
    }
    .input-wrapper {
      position: relative;
    }
    .input-icon {
      position: absolute;
      left: 14px;
      top: 50%;
      transform: translateY(-50%);
      color: #64748b;
      font-size: 13px;
    }
    .input-control {
      width: 100%;
      background: #090c14;
      border: 1px solid rgba(255, 255, 255, 0.12);
      border-radius: 10px;
      padding: 12px 14px 12px 42px;
      color: #ffffff;
      font-size: 13.5px;
      outline: none;
      transition: all 0.2s;
    }
    .input-control:focus {
      border-color: #ff5757;
      box-shadow: 0 0 0 3px rgba(255, 87, 87, 0.15);
      background: #0d101b;
    }
    .password-toggle {
      position: absolute;
      right: 14px;
      top: 50%;
      transform: translateY(-50%);
      color: #64748b;
      cursor: pointer;
      font-size: 13px;
      background: none;
      border: none;
    }
    .password-toggle:hover {
      color: #cbd5e1;
    }

    /* Submit Button */
    .btn-submit {
      width: 100%;
      background: linear-gradient(135deg, #ff5757 0%, #dc2626 100%);
      color: #ffffff;
      font-size: 13.5px;
      font-weight: 700;
      letter-spacing: 0.6px;
      padding: 13px;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      box-shadow: 0 4px 14px rgba(255, 87, 87, 0.35);
      transition: all 0.2s;
      margin-top: 8px;
    }
    .btn-submit:hover {
      transform: translateY(-1px);
      box-shadow: 0 6px 20px rgba(255, 87, 87, 0.45);
    }



    /* Alert Banner */
    .alert-danger {
      background: rgba(239, 68, 68, 0.12);
      border: 1px solid rgba(239, 68, 68, 0.35);
      border-radius: 10px;
      padding: 12px 14px;
      margin-bottom: 18px;
      color: #f87171;
      font-size: 12.5px;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    /* Footer */
    .command-footer {
      position: relative;
      z-index: 2;
      text-align: center;
      padding: 16px 20px;
      font-size: 11px;
      color: #475569;
      border-top: 1px solid rgba(255, 255, 255, 0.04);
    }
  </style>
</head>
<body>

  <!-- Top Command Bar -->
  <header class="top-command-bar">
    <a href="{{ route('home') }}" class="brand-section">
      <div class="brand-icon">
        <i class="fa-solid fa-shield-halved"></i>
      </div>
      <div class="brand-text">
        <h1>Imperial Defence Academy</h1>
        <span>Command & Admin Portal</span>
      </div>
    </a>

    <div class="top-links">
      <a href="{{ route('home') }}" class="top-link">
        <i class="fa-solid fa-globe"></i>
        <span>Public Website</span>
      </a>
      <a href="{{ route('login') }}" class="top-link">
        <i class="fa-solid fa-user-graduate"></i>
        <span>Student Login</span>
      </a>
    </div>
  </header>

  <!-- Main Login Deck -->
  <main class="main-container">
    <div class="portal-box">

      <div class="portal-header">
        <h1 class="portal-brand-title" style="font-size: 21px; font-weight: 900; color: #ffffff; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 6px; line-height: 1.2;">Imperial Defence Academy</h1>
        <div class="portal-section-tag" style="display: inline-block; font-size: 11.5px; font-weight: 800; color: #ff5757; text-transform: uppercase; letter-spacing: 1.2px; padding: 3px 10px; background: rgba(255, 87, 87, 0.1); border: 1px solid rgba(255, 87, 87, 0.25); border-radius: 6px; margin-bottom: 6px;">Admin Section</div>
        <p class="portal-subtitle" style="font-size: 13px; color: #94a3b8; margin: 0;">Sign in to access the admin panel</p>
      </div>

      <!-- Errors -->
      @if($errors->any())
        <div class="alert-danger">
          <i class="fa-solid fa-triangle-exclamation"></i>
          <span>{{ $errors->first() }}</span>
        </div>
      @endif

      @if(session('error'))
        <div class="alert-danger">
          <i class="fa-solid fa-triangle-exclamation"></i>
          <span>{{ session('error') }}</span>
        </div>
      @endif

      <form method="POST" action="{{ route('admin.login.post') }}">
        @csrf

        <!-- Identity -->
        <div class="form-group">
          <label class="form-label">Identity</label>
          <div class="input-wrapper">
            <i class="fa-solid fa-id-badge input-icon"></i>
            <input type="text" name="login_id" required autofocus
                   value="{{ old('login_id', old('email')) }}"
                   class="input-control"
                   placeholder="Enter your user identity, username or email">
          </div>
        </div>

        <!-- Password -->
        <div class="form-group">
          <label class="form-label">Password</label>
          <div class="input-wrapper">
            <i class="fa-solid fa-key input-icon"></i>
            <input type="password" name="password" id="adminPasswordInput" required
                   class="input-control"
                   placeholder="Enter your password">
            <button type="button" class="password-toggle" onclick="toggleAdminPassword()">
              <i class="fa-regular fa-eye" id="toggleAdminEye"></i>
            </button>
          </div>
        </div>

        <!-- Remember Me -->
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 18px;">
          <label style="display: flex; align-items: center; gap: 8px; font-size: 12px; color: #94a3b8; cursor: pointer;">
            <input type="checkbox" name="remember" style="accent-color: #ff5757; width: 14px; height: 14px;">
            <span>Maintain active session</span>
          </label>
        </div>

        <!-- Sign In / Start Session Button -->
        <button type="submit" class="btn-submit">
          <i class="fa-solid fa-arrow-right-to-bracket"></i>
          <span>Sign In / Start Session</span>
        </button>

        <!-- Create an Admin Account Option -->
        <div style="margin-top: 20px; padding-top: 18px; border-top: 1px solid rgba(255, 255, 255, 0.08); text-align: center;">
          <a href="{{ route('admin.register') }}" class="btn-create-admin" style="display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; padding: 12px; background: rgba(255, 255, 255, 0.04); border: 1px solid rgba(255, 255, 255, 0.12); border-radius: 10px; color: #cbd5e1; font-size: 13px; font-weight: 700; text-decoration: none; transition: all 0.2s;">
            <i class="fa-solid fa-user-plus" style="color: #ff5757;"></i>
            <span>Create an Admin Account</span>
          </a>
        </div>

        <!-- Candidate / Student redirect hint -->
        <div style="text-align: center; margin-top: 16px; font-size: 12px; color: #64748b;">
          Are you a student or candidate? 
          <a href="{{ route('login') }}" style="color: #38bdf8; text-decoration: none; font-weight: 600;">Go to Student Login</a>
        </div>

      </form>
    </div>
  </main>

  <!-- Command Footer -->
  <footer class="command-footer">
    Imperial Defence Academy &bull; Central Command & Control System &bull; Authorized Personnel Only
  </footer>

  <script>
    function toggleAdminPassword() {
      const input = document.getElementById('adminPasswordInput');
      const icon = document.getElementById('toggleAdminEye');
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
</body>
</html>