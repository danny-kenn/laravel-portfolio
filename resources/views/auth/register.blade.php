<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Portfolio Builder</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #0f1923, #1a2535);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }
        .register-card {
            background: rgba(255,255,255,0.05);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 16px;
            padding: 2.5rem;
            width: 100%;
            max-width: 440px;
            color: #fff;
        }
        .register-card h2 { color: #d4a017; font-weight: 700; }
        .form-control {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.2);
            color: #fff;
            border-radius: 8px;
        }
        .form-control:focus {
            background: rgba(255,255,255,0.12);
            color: #fff;
            border-color: #2c7a7b;
            box-shadow: 0 0 0 3px rgba(44,122,123,0.3);
        }
        .btn-register {
            background: linear-gradient(45deg, #2c7a7b, #d4a017);
            border: none;
            border-radius: 8px;
            font-weight: 600;
            padding: 0.7rem;
            color: #fff;
            width: 100%;
        }
        .btn-register:hover { opacity: 0.9; color: #fff; }
    </style>
</head>
<body>
    <div class="register-card shadow-lg">
        <div class="text-center mb-4">
            <i class="fas fa-user-plus fa-3x" style="color:#d4a017;"></i>
            <h2 class="mt-2">Create Account</h2>
            <p class="text-muted small">Join Portfolio Builder</p>
        </div>

        @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="mb-2">
                <label class="form-label small text-muted">Full Name</label>
                <input type="text" name="full_name" class="form-control" placeholder="John Doe" required>
            </div>
            <div class="mb-2">
                <label class="form-label small text-muted">Username</label>
                <input type="text" name="username" class="form-control" placeholder="johndoe" required>
            </div>
            <div class="mb-2">
                <label class="form-label small text-muted">Email</label>
                <input type="email" name="email" class="form-control" placeholder="john@example.com" required>
            </div>
            <div class="mb-2">
                <label class="form-label small text-muted">Password</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
            <div class="mb-3">
                <label class="form-label small text-muted">Confirm Password</label>
                <input type="password" name="password_confirmation" class="form-control" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn-register"><i class="fas fa-user-plus me-2"></i>Register</button>
        </form>
        <div class="text-center mt-3">
            <a href="{{ route('login') }}" class="text-muted small">Already have an account? Login</a>
        </div>
    </div>
</body>
</html>