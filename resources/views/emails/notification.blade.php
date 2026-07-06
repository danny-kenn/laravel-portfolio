<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $notification->title }}</title>
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
            font-size: 24px;
            margin: 0;
        }
        .content {
            line-height: 1.8;
        }
        .content h2 {
            color: #d4a017;
            font-size: 20px;
            margin-top: 0;
        }
        .notification-box {
            background: #0f1923;
            border-left: 4px solid #2c7a7b;
            padding: 15px 20px;
            border-radius: 4px;
            margin: 20px 0;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(45deg, #2c7a7b, #d4a017);
            color: #fff;
            padding: 12px 30px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            margin-top: 20px;
        }
        .btn:hover { opacity: 0.9; }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid rgba(255,255,255,0.06);
            font-size: 14px;
            color: #64748b;
        }
        .badge {
            display: inline-block;
            padding: 3px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-info { background: rgba(59,130,246,0.2); color: #60a5fa; }
        .badge-success { background: rgba(34,197,94,0.2); color: #34d399; }
        .badge-warning { background: rgba(234,179,8,0.2); color: #fbbf24; }
        .badge-danger { background: rgba(239,68,68,0.2); color: #f87171; }
        .badge-create { background: rgba(34,197,94,0.2); color: #34d399; }
        .badge-update { background: rgba(59,130,246,0.2); color: #60a5fa; }
        .badge-delete { background: rgba(239,68,68,0.2); color: #f87171; }
        .badge-login { background: rgba(16,185,129,0.2); color: #34d399; }
        .badge-logout { background: rgba(107,114,128,0.2); color: #9ca3af; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔔 Portfolio Builder</h1>
        </div>

        <div class="content">
            <h2>{{ $notification->title }}</h2>
            
            <p>Hello <strong>{{ $user->full_name }}</strong>,</p>
            
            <div class="notification-box">
                <p style="margin:0;font-size:16px;">
                    <span class="badge badge-{{ $notification->action ?? 'info' }}">
                        {{ ucfirst($notification->action ?? 'info') }}
                    </span>
                    <span class="badge badge-info">
                        {{ ucfirst($notification->module ?? 'system') }}
                    </span>
                </p>
                <p style="margin-top:15px;font-size:15px;line-height:1.6;">
                    {{ $notification->message }}
                </p>
                <p style="font-size:12px;color:#64748b;margin-top:10px;">
                    {{ $notification->created_at->format('F j, Y g:i A') }}
                </p>
            </div>

            <div style="text-align:center;">
                <a href="{{ config('app.url') }}/admin/dashboard" class="btn">🔐 Go to Dashboard</a>
            </div>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} Portfolio Builder. All rights reserved.</p>
            <p style="font-size:12px;">You are receiving this because you have notifications enabled.</p>
        </div>
    </div>
</body>
</html>