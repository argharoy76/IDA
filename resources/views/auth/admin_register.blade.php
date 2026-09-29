<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create Admin Account | Imperial Defence Academy</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
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

    body::before {
      content: '';
      position: absolute;
      inset: 0;
      background-image: 
        radial-gradient(circle at 50% 0%, rgba(255, 87, 87, 0.14) 0%, transparent 60%),
        linear-gradient(rgba(255, 255, 255, 0.018) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255, 255, 255, 0.018) 1px, transparent 1px);
      background-size: 100% 100%, 42px 42px, 42px 42px;
      pointer-events: none;
      z-index: 0;
    }

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

    .main-container {
      position: relative;
      z-index: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 40px 20px;
      flex: 1;
    }

    .portal-box {
      width: 100%;
      max-width: 520px;
      background: #121622;
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 18px;
      padding: 36px 32px;
      box-shadow: 0 25px 60px rgba(0, 0, 0, 0.7), 0 0 35px rgba(255, 87, 87, 0.08);
      position: relative;
    }

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
    .portal-brand-title {
      font-size: 20px;
      font-weight: 900;
      color: #ffffff;
      text-transform: uppercase;
      letter-spacing: 1px;
      margin-bottom: 6px;
      line-height: 1.2;
    }
    .portal-section-tag {
      display: inline-block;
      font-size: 11px;
      font-weight: 800;
      color: #ff5757;
      text-transform: uppercase;
      letter-spacing: 1.2px;
      padding: 3px 10px;
      background: rgba(255, 87, 87, 0.1);
      border: 1px solid rgba(255, 87, 87, 0.25);
      border-radius: 6px;
      margin-bottom: 8px;
    }
    .portal-subtitle {
      font-size: 13px;
      color: #94a3b8;
      line-height: 1.4;
    }

    .form-group {
      margin-bottom: 16px;
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
      margin-top: 14px;
    }
    .btn-submit:hover {
      transform: translateY(-1px);
      box-shadow: 0 6px 20px rgba(255, 87, 87, 0.45);
    }

    .btn-back-login {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      color: #94a3b8;
      font-size: 12.5px;
      font-weight: 600;
      text-decoration: none;
      margin-top: 18px;
      transition: color 0.2s;
    }
    .btn-back-login:hover {
      color: #ffffff;
    }

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

  <header class="top-command-bar">
    <a href="{{ route('home') }}" class="brand-section">
      <div class="brand-icon">
        <i class="fa-solid fa-shield-halved"></i>
      </div>
      <div class="brand-text">
        <h1>Imperial Defence Academy</h1>
        <span>Admin Section</span>
      </div>
    </a>
    <a href="{{ route('admin.login') }}" style="color: #94a3b8; text-decoration: none; font-size: 12.5px; font-weight: 600; display: flex; align-items: center; gap: 6px;">
      <i class="fa-solid fa-arrow-left"></i> Back to Login
    </a>
  </header>

  <main class="main-container">
    <div class="portal-box">
      <div class="portal-header">
        <h1 class="portal-brand-title">Imperial Defence Academy</h1>
        <div class="portal-section-tag">Admin Section</div>
        <p class="portal-subtitle">Create an Admin Account</p>
      </div>

      @if(session('info'))
        <div style="background: rgba(59, 130, 246, 0.15); border: 1px solid rgba(59, 130, 246, 0.35); border-radius: 10px; padding: 12px 14px; margin-bottom: 18px; color: #60a5fa; font-size: 12.5px;">
          {{ session('info') }}
        </div>
      @endif

      <form method="POST" action="{{ route('admin.register.post') }}">
        @csrf

        <div class="form-group">
          <label class="form-label">Full Name</label>
          <div class="input-wrapper">
            <i class="fa-solid fa-user input-icon"></i>
            <input type="text" name="name" required class="input-control" placeholder="Enter officer or admin name">
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Identity</label>
          <div class="input-wrapper">
            <i class="fa-solid fa-id-badge input-icon"></i>
            <input type="text" name="account_id" required class="input-control" placeholder="e.g. ADM-008">
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Official Email Address</label>
          <div class="input-wrapper">
            <i class="fa-solid fa-envelope input-icon"></i>
            <input type="email" name="email" required class="input-control" placeholder="admin@ida.com">
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Phone Number</label>
          <div class="input-wrapper">
            <i class="fa-solid fa-phone input-icon"></i>
            <input type="text" name="phone" required class="input-control" placeholder="01711000000">
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Password</label>
          <div class="input-wrapper">
            <i class="fa-solid fa-key input-icon"></i>
            <input type="password" name="password" id="regPassword" required class="input-control" placeholder="Enter password (min 6 chars)">
            <button type="button" onclick="togglePass('regPassword', this)" style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #64748b; cursor: pointer;">
              <i class="fa-regular fa-eye"></i>
            </button>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Confirm Password</label>
          <div class="input-wrapper">
            <i class="fa-solid fa-key input-icon"></i>
            <input type="password" name="password_confirmation" id="regConfirmPassword" required class="input-control" placeholder="Re-enter password">
            <button type="button" onclick="togglePass('regConfirmPassword', this)" style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #64748b; cursor: pointer;">
              <i class="fa-regular fa-eye"></i>
            </button>
          </div>
        </div>

        <button type="submit" class="btn-submit">
          <i class="fa-solid fa-user-plus"></i>
          <span>Create Admin Account</span>
        </button>

        <div style="text-align: center;">
          <a href="{{ route('admin.login') }}" class="btn-back-login">
            <i class="fa-solid fa-arrow-left"></i> Already have credentials? Sign in
          </a>
        </div>
      </form>
    </div>
  </main>

  <footer class="command-footer">
    Imperial Defence Academy &bull; Central Command & Control System
  </footer>

  <script>
    function togglePass(id, btn) {
      const input = document.getElementById(id);
      const icon = btn.querySelector('i');
      if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
        icon.style.color = '#ff5757';
      } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
        icon.style.color = '#64748b';
      }
    }
  </script>
</body>
</html>
