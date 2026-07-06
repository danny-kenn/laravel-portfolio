<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Portfolio Builder</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: #0f1923;
            color: #e2e8f0;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #1a2535;
            border-radius: 12px;
            padding: 40px;
            border: 1px solid rgba(255,255,255,0.06);
        }
        .header {
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.06);
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #d4a017;
            font-size: 28px;
            margin: 0;
        }
        .content {
            line-height: 1.8;
        }
        .content h2 {
            color: #d4a017;
            font-size: 22px;
        }
        .details {
            background: #0f1923;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            border: 1px solid rgba(255,255,255,0.06);
        }
        .details p {
            margin: 8px 0;
        }
        .details strong {
            color: #d4a017;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(45deg, #2c7a7b, #d4a017);
            color: #fff;
            padding: 14px 35px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            margin-top: 20px;
        }
        .btn:hover {
            opacity: 0.9;
        }
        .credentials-box {
            background: rgba(44,122,123,0.15);
            border-left: 4px solid #2c7a7b;
            padding: 15px 20px;
            border-radius: 4px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid rgba(255,255,255,0.06);
            font-size: 14px;
            color: #64748b;
        }
        .password-box {
            background: rgba(212,160,23,0.1);
            border: 1px dashed #d4a017;
            padding: 10px 15px;
            border-radius: 6px;
            font-family: monospace;
            font-size: 18px;
            color: #d4a017;
            text-align: center;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚀 Portfolio Builder</h1>
        </div>

        <div class="content">
            <h2>Welcome, {{ $user->full_name }}!</h2>

            <p>Your account has been successfully created on the Portfolio Builder platform.</p>

            <div class="details">
                <p><strong>Name:</strong> {{ $user->full_name }}</p>
                <p><strong>Username:</strong> {{ $user->username }}</p>
                <p><strong>Email:</strong> {{ $user->email }}</p>
                <p><strong>Role:</strong> {{ $user->getRoleDisplayName() }}</p>
            </div>

            @if($password)
            <div class="credentials-box">
                <p><strong>🔑 Login Credentials</strong></p>
                <p><strong>Username/Email:</strong> {{ $user->email }}</p>
                <p><strong>Password:</strong></p>
                <div class="password-box">{{ $password }}</div>
                <p style="font-size: 14px; color: #94a3b8; margin-top: 10px;">
                    ⚠️ Please change your password after your first login.
                </p>
            </div>
            @endif

            <div style="text-align: center;">
                <a href="{{ config('app.url') }}/login" class="btn">🔐 Login to Dashboard</a>
            </div>

            <p style="margin-top: 20px; font-size: 14px; color: #94a3b8;">
                If you have any issues logging in, please contact your system administrator.
            </p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} Portfolio Builder. All rights reserved.</p>
            <p style="font-size: 12px;">This is an automated message. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>