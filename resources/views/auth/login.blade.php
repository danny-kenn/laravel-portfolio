<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login – Portfolio Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Roboto+Mono:wght@400;700&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body {
  font-family: 'Inter', sans-serif;
  min-height: 100vh;
  background: #0d1117;
  display: flex; align-items: center; justify-content: center;
  position: relative; overflow: hidden;
}

/* Animated background */
.bg-grid {
  position: fixed; inset: 0; z-index: 0;
  background-image:
    linear-gradient(rgba(44,122,123,0.07) 1px, transparent 1px),
    linear-gradient(90deg, rgba(44,122,123,0.07) 1px, transparent 1px);
  background-size: 40px 40px;
}
.bg-glow {
  position: fixed;
  width: 600px; height: 600px;
  background: radial-gradient(circle, rgba(44,122,123,0.15) 0%, transparent 70%);
  top: -200px; left: -200px;
  pointer-events: none;
}
.bg-glow2 {
  position: fixed;
  width: 400px; height: 400px;
  background: radial-gradient(circle, rgba(212,160,23,0.08) 0%, transparent 70%);
  bottom: -100px; right: -100px;
  pointer-events: none;
}

.login-wrap { position: relative; z-index: 1; width: 100%; max-width: 420px; padding: 1rem; }

.login-card {
  background: rgba(22,27,34,0.95);
  backdrop-filter: blur(20px);
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: 20px;
  padding: 2.5rem 2rem;
  box-shadow: 0 24px 64px rgba(0,0,0,0.5);
}

.logo-icon {
  width: 60px; height: 60px;
  background: linear-gradient(135deg, #2c7a7b, #d4a017);
  border-radius: 16px;
  display: flex; align-items: center; justify-content: center;
  margin: 0 auto 1.25rem;
  font-size: 1.6rem; color: #fff;
}

.login-title { color: #d4a017; font-family: 'Roboto Mono', monospace; font-weight: 700; font-size: 1.4rem; text-align: center; }
.login-sub { color: #8b949e; font-size: 0.85rem; text-align: center; margin-top: 4px; margin-bottom: 1.75rem; }

.form-label { color: #8b949e; font-size: 0.8rem; font-weight: 500; display: block; margin-bottom: 0.3rem; }
.form-control {
  background: rgba(255,255,255,0.05);
  border: 1px solid rgba(255,255,255,0.1);
  color: #e6edf3; border-radius: 10px;
  padding: 0.65rem 0.9rem;
  font-size: 0.9rem; width: 100%;
  transition: border-color 0.2s, box-shadow 0.2s;
}
.form-control:focus {
  background: rgba(255,255,255,0.08);
  border-color: #2c7a7b; color: #e6edf3;
  box-shadow: 0 0 0 3px rgba(44,122,123,0.25);
  outline: none;
}
.form-control::placeholder { color: #484f58; }
.input-group-btn {
  position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
  background: none; border: none; color: #8b949e; cursor: pointer;
  padding: 4px; font-size: 0.85rem;
}

.btn-login {
  width: 100%; background: linear-gradient(90deg, #2c7a7b, #d4a017);
  border: none; border-radius: 10px; color: #fff;
  font-weight: 700; font-size: 0.95rem; padding: 0.75rem;
  cursor: pointer; transition: opacity 0.2s, transform 0.2s;
  margin-top: 0.5rem;
}
.btn-login:hover { opacity: 0.9; transform: translateY(-1px); }
.btn-login:active { transform: translateY(0); }

.divider { display: flex; align-items: center; gap: 0.75rem; margin: 1.25rem 0; }
.divider::before, .divider::after { content:''; flex:1; height:1px; background: rgba(255,255,255,0.08); }
.divider span { color: #484f58; font-size: 0.78rem; }

.back-link { text-align: center; margin-top: 1.25rem; }
.back-link a { color: #8b949e; font-size: 0.82rem; text-decoration: none; }
.back-link a:hover { color: #2c7a7b; }

.alert { border-radius: 10px; font-size: 0.85rem; padding: 0.65rem 0.9rem; margin-bottom: 1rem; }
.alert-danger { background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.25); color: #fca5a5; }
.alert-success { background: rgba(34,197,94,0.1); border: 1px solid rgba(34,197,94,0.25); color: #86efac; }
</style>
</head>
<body>
<div class="bg-grid"></div>
<div class="bg-glow"></div>
<div class="bg-glow2"></div>

<div class="login-wrap">
  <div class="login-card">
    <div class="logo-icon"><i class="fas fa-code"></i></div>
    <div class="login-title">Portfolio Builder</div>
    <div class="login-sub">Sign in to your admin dashboard</div>

    @if(session('success'))
    <div class="alert alert-success"><i class="fas fa-check-circle me-2"></i>{{ session('success') }}</div>
    @endif
    @if($errors->any())
    <div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
      @csrf
      <div class="mb-3">
        <label class="form-label">Email Address</label>
        <input type="email" name="email" class="form-control" placeholder="your@email.com" value="{{ old('email') }}" required autofocus>
      </div>
      <div class="mb-4" style="position:relative">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" placeholder="••••••••" id="pwdField" required style="padding-right:2.5rem">
        <button type="button" class="input-group-btn" onclick="togglePwd()"><i class="fas fa-eye" id="eyeIcon"></i></button>
      </div>
      <button type="submit" class="btn-login"><i class="fas fa-sign-in-alt me-2"></i>Sign In</button>
    </form>

    <div class="back-link">
      <a href="{{ url('/') }}"><i class="fas fa-arrow-left me-1"></i>Back to Portfolio</a>
    </div>
  </div>
</div>

<script>
function togglePwd() {
  const f = document.getElementById('pwdField');
  const i = document.getElementById('eyeIcon');
  f.type = f.type === 'password' ? 'text' : 'password';
  i.className = f.type === 'password' ? 'fas fa-eye' : 'fas fa-eye-slash';
}
</script>
</body>
</html>