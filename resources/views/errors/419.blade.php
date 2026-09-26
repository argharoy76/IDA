<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="refresh" content="0;url={{ url('/login') }}">
  <title>Session Expired - Redirecting to Login...</title>
  <script>
    window.location.replace("{{ url('/login') }}");
  </script>
</head>
<body style="background: #0f172a; color: #f8fafc; font-family: sans-serif; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0;">
  <div style="text-align: center;">
    <h2>Security session refreshed</h2>
    <p>Redirecting you to the login page...</p>
    <a href="{{ url('/login') }}" style="color: #10b981; font-weight: bold; text-decoration: underline;">Click here if not redirected automatically</a>
  </div>
</body>
</html>
