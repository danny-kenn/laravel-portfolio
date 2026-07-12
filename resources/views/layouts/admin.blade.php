<!DOCTYPE html>
<html lang="en" data-theme="{{ session('theme', 'dark') }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Dashboard') – Portfolio Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Roboto+Mono:wght@400;600&display=swap" rel="stylesheet">
<style>
/* ══════════════════════════════════════════
   CSS VARIABLES — FULL THEME SYSTEM
══════════════════════════════════════════ */
:root { --sw: 260px; --teal: #2c7a7b; --gold: #d4a017; --radius: 12px; --transition: 0.25s ease; }

[data-theme="dark"] {
  --bg:       #0d1117;
  --bg2:      #161b22;
  --bg3:      #1c2333;
  --sidebar:  #0d1117;
  --surface:  #161b22;
  --border:   rgba(255,255,255,0.07);
  --border2:  rgba(255,255,255,0.12);
  --text:     #e6edf3;
  --text2:    #8b949e;
  --text3:    #484f58;
  --input-bg: #0d1117;
  --hover:    rgba(44,122,123,0.12);
  --card-shadow: 0 1px 3px rgba(0,0,0,0.4);
  --modal-overlay: rgba(0,0,0,0.7);
}

[data-theme="light"] {
  --bg:       #f6f8fa;
  --bg2:      #ffffff;
  --bg3:      #f0f2f5;
  --sidebar:  #111c27;
  --surface:  #ffffff;
  --border:   rgba(0,0,0,0.08);
  --border2:  #d0d7de;
  --text:     #1f2328;
  --text2:    #656d76;
  --text3:    #9198a1;
  --input-bg: #f6f8fa;
  --hover:    rgba(44,122,123,0.06);
  --card-shadow: 0 1px 3px rgba(0,0,0,0.1);
  --modal-overlay: rgba(0,0,0,0.5);
}

/* ── BASE ──────────────────────────────── */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html { font-size: 15px; }
body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); transition: background var(--transition), color var(--transition); }

/* ── SIDEBAR ───────────────────────────── */
.sidebar {
  position: fixed; top: 0; left: 0;
  width: var(--sw); height: 100vh;
  background: var(--sidebar);
  border-right: 1px solid var(--border);
  display: flex; flex-direction: column;
  z-index: 400;
  transition: transform var(--transition);
}

[data-theme="light"] .sidebar { border-right-color: rgba(0,0,0,0.12); }

.sidebar-brand {
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid var(--border);
  flex-shrink: 0;
}
.sidebar-brand .brand-title { color: var(--gold); font-family: 'Roboto Mono', monospace; font-weight: 700; font-size: 0.95rem; }
.sidebar-brand .brand-user { color: #64748b; font-size: 0.78rem; margin-top: 2px; }
.sidebar-brand .role-badge {
  display: inline-block; margin-top: 6px;
  background: rgba(212,160,23,0.15);
  color: var(--gold);
  border: 1px solid rgba(212,160,23,0.3);
  border-radius: 20px; padding: 2px 10px;
  font-size: 0.72rem; font-weight: 600;
}

.sidebar-scroll { flex: 1; overflow-y: auto; padding: 0.75rem 0; }
.sidebar-scroll::-webkit-scrollbar { width: 4px; }
.sidebar-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 4px; }

.nav-group-label {
  padding: 0.75rem 1.25rem 0.3rem;
  font-size: 0.65rem; text-transform: uppercase;
  letter-spacing: 1.2px; color: #475569;
  font-weight: 600;
}

.sidebar-link {
  display: flex; align-items: center; gap: 0.6rem;
  padding: 0.6rem 1.25rem;
  color: #8b949e; text-decoration: none;
  font-size: 0.875rem; font-weight: 500;
  border-left: 3px solid transparent;
  transition: all 0.18s;
}
.sidebar-link i { width: 17px; text-align: center; font-size: 0.85rem; }
.sidebar-link:hover { color: #c9d1d9; background: rgba(255,255,255,0.04); }
.sidebar-link.active { color: #fff; background: rgba(44,122,123,0.2); border-left-color: var(--teal); }
.sidebar-link .badge { margin-left: auto; }

.sidebar-footer {
  padding: 1rem 1.25rem;
  border-top: 1px solid var(--border);
  display: flex; flex-direction: column; gap: 0.5rem;
}

/* ── OVERLAY (mobile) ──────────────────── */
.sidebar-overlay {
  display: none; position: fixed; inset: 0;
  background: var(--modal-overlay); z-index: 390;
}
.sidebar-overlay.show { display: block; }

/* ── MAIN ──────────────────────────────── */
.main { margin-left: var(--sw); min-height: 100vh; display: flex; flex-direction: column; }

/* ── TOPBAR ────────────────────────────── */
.topbar {
  background: var(--surface);
  border-bottom: 1px solid var(--border);
  padding: 0.75rem 1.5rem;
  display: flex; align-items: center; justify-content: space-between;
  position: sticky; top: 0; z-index: 200;
  gap: 1rem;
}
.topbar-left { display: flex; align-items: center; gap: 0.75rem; }
.topbar-right { display: flex; align-items: center; gap: 0.75rem; }
.topbar-title { font-weight: 600; font-size: 0.95rem; color: var(--text); }
.hamburger { display: none; background: none; border: 1px solid var(--border2); border-radius: 8px; padding: 0.4rem 0.6rem; cursor: pointer; color: var(--text2); }

/* ── THEME BUTTON ─────────────────────── */
.theme-btn {
  background: none; border: 1px solid var(--border2);
  border-radius: 8px; padding: 0.4rem 0.75rem;
  cursor: pointer; color: var(--text2);
  font-size: 0.82rem; display: flex; align-items: center; gap: 0.4rem;
  transition: all 0.18s;
}
.theme-btn:hover { border-color: var(--teal); color: var(--teal); background: var(--hover); }

/* ── CONTENT ───────────────────────────── */
.page-content { padding: 1.5rem; flex: 1; }

/* ── STAT CARDS ─────────────────────────── */
.stat-card {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 1.25rem 1.4rem;
  box-shadow: var(--card-shadow);
  transition: transform 0.2s, box-shadow 0.2s;
  position: relative; overflow: hidden;
}
.stat-card::before {
  content: ''; position: absolute;
  top: 0; left: 0; right: 0; height: 3px;
  background: linear-gradient(90deg, var(--teal), var(--gold));
  opacity: 0; transition: opacity 0.2s;
}
.stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,0.15); }
.stat-card:hover::before { opacity: 1; }
.stat-num { font-size: 2.2rem; font-weight: 700; color: var(--gold); line-height: 1; }
.stat-lbl { color: var(--text2); font-size: 0.8rem; margin-top: 4px; font-weight: 500; }
.stat-icon { font-size: 2rem; opacity: 0.15; }

/* ── CONTENT CARDS ───────────────────────── */
.c-card {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  box-shadow: var(--card-shadow);
  overflow: hidden;
  margin-bottom: 1.5rem;
}
.c-card-header {
  padding: 1rem 1.25rem;
  border-bottom: 1px solid var(--border);
  display: flex; align-items: center; justify-content: space-between;
}
.c-card-header h6 { margin: 0; font-weight: 600; color: var(--text); font-size: 0.9rem; }
.c-card-body { padding: 1.25rem; }

/* ── TABLE ────────────────────────────────── */
.data-table { width: 100%; border-collapse: collapse; font-size: 0.875rem; }
.data-table th {
  padding: 0.65rem 1rem;
  text-align: left;
  font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.6px;
  font-weight: 600; color: var(--text3);
  background: var(--bg3); border-bottom: 1px solid var(--border);
}
.data-table td {
  padding: 0.75rem 1rem;
  border-bottom: 1px solid var(--border);
  color: var(--text); vertical-align: middle;
}
.data-table tbody tr:last-child td { border-bottom: none; }
.data-table tbody tr:hover td { background: var(--hover); }
.data-table .text-muted-col { color: var(--text2); }

/* ── FORMS ─────────────────────────────────── */
.form-label { color: var(--text2); font-size: 0.8rem; font-weight: 500; margin-bottom: 0.3rem; display: block; }
.form-control, .form-select {
  background: var(--input-bg);
  border: 1px solid var(--border2);
  color: var(--text);
  border-radius: 8px;
  padding: 0.55rem 0.85rem;
  font-size: 0.875rem;
  transition: border-color 0.18s, box-shadow 0.18s;
  width: 100%;
}
.form-control:focus, .form-select:focus {
  background: var(--input-bg);
  color: var(--text);
  border-color: var(--teal);
  box-shadow: 0 0 0 3px rgba(44,122,123,0.18);
  outline: none;
}
.form-control::placeholder { color: var(--text3); }
textarea.form-control { resize: vertical; min-height: 100px; }
.form-check-label { color: var(--text2); font-size: 0.875rem; }
.form-range { accent-color: var(--teal); }
.form-control[type="file"] { padding: 0.4rem 0.75rem; }

/* ── BUTTONS ────────────────────────────────── */
.btn { border-radius: 8px; font-size: 0.85rem; font-weight: 500; padding: 0.5rem 1rem; transition: all 0.18s; border: none; cursor: pointer; }
.btn-teal { background: var(--teal); color: #fff; }
.btn-teal:hover { background: #235f60; color: #fff; }
.btn-gold { background: var(--gold); color: #000; font-weight: 600; }
.btn-gold:hover { background: #b8890e; }
.btn-ghost { background: none; border: 1px solid var(--border2); color: var(--text2); }
.btn-ghost:hover { border-color: var(--teal); color: var(--teal); background: var(--hover); }
.btn-danger-soft { background: rgba(239,68,68,0.1); color: #ef4444; border: 1px solid rgba(239,68,68,0.2); }
.btn-danger-soft:hover { background: #ef4444; color: #fff; }
.btn-icon { width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center; border-radius: 7px; font-size: 0.8rem; }
.btn-sm { padding: 0.35rem 0.75rem; font-size: 0.8rem; }

/* ── BADGE ───────────────────────────────────── */
.badge { border-radius: 20px; font-size: 0.72rem; font-weight: 600; padding: 0.25em 0.75em; }
.badge-role-super { background: rgba(239,68,68,0.15); color: #ef4444; border: 1px solid rgba(239,68,68,0.3); }
.badge-role-admin { background: rgba(59,130,246,0.15); color: #3b82f6; border: 1px solid rgba(59,130,246,0.3); }
.badge-role-attache { background: rgba(16,185,129,0.15); color: #10b981; border: 1px solid rgba(16,185,129,0.3); }
.badge-status-pub { background: rgba(34,197,94,0.15); color: #22c55e; border: 1px solid rgba(34,197,94,0.3); }
.badge-status-draft { background: rgba(234,179,8,0.15); color: #eab308; border: 1px solid rgba(234,179,8,0.3); }
.badge-status-arch { background: rgba(107,114,128,0.15); color: #6b7280; border: 1px solid rgba(107,114,128,0.2); }

/* ── MODAL / BOTTOM SHEET ──────────────────── */
.modal-overlay {
  display: none; position: fixed; inset: 0;
  background: var(--modal-overlay);
  z-index: 500; align-items: center; justify-content: center;
}
.modal-overlay.show { display: flex; }

.modal-box {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 16px;
  width: 100%; max-width: 640px;
  max-height: 90vh; overflow: hidden;
  display: flex; flex-direction: column;
  box-shadow: 0 24px 64px rgba(0,0,0,0.4);
  animation: modalIn 0.25s ease;
}
@keyframes modalIn { from { opacity:0; transform:scale(0.96) translateY(8px); } to { opacity:1; transform:scale(1) translateY(0); } }

.modal-box.wide { max-width: 820px; }

.modal-hd {
  padding: 1.1rem 1.4rem;
  border-bottom: 1px solid var(--border);
  display: flex; align-items: center; justify-content: space-between;
  flex-shrink: 0;
}
.modal-hd h6 { color: var(--gold); font-weight: 700; font-size: 0.95rem; margin: 0; font-family: 'Roboto Mono', monospace; }
.modal-close { background: none; border: none; color: var(--text2); cursor: pointer; width: 28px; height: 28px; border-radius: 6px; display: flex; align-items: center; justify-content: center; transition: all 0.15s; }
.modal-close:hover { background: var(--bg3); color: var(--text); }

.modal-body { padding: 1.25rem 1.4rem; overflow-y: auto; flex: 1; }
.modal-body::-webkit-scrollbar { width: 5px; }
.modal-body::-webkit-scrollbar-thumb { background: var(--border2); border-radius: 4px; }

.modal-ft {
  padding: 0.9rem 1.4rem;
  border-top: 1px solid var(--border);
  display: flex; align-items: center; justify-content: flex-end; gap: 0.5rem;
  flex-shrink: 0;
}

/* Bottom sheet on mobile */
@media (max-width: 767px) {
  .modal-overlay { align-items: flex-end; }
  .modal-box {
    max-width: 100%; width: 100%;
    border-radius: 20px 20px 0 0;
    max-height: 92vh;
    animation: sheetIn 0.3s cubic-bezier(0.32, 0.72, 0, 1);
  }
  .modal-box::before {
    content: ''; display: block; width: 40px; height: 4px;
    background: var(--border2); border-radius: 2px;
    margin: 12px auto 4px; flex-shrink: 0;
  }
  @keyframes sheetIn { from { transform: translateY(100%); } to { transform: translateY(0); } }
}

/* ── TOAST ───────────────────────────────── */
.toast-stack { position: fixed; top: 1rem; right: 1rem; z-index: 9999; display: flex; flex-direction: column; gap: 0.5rem; pointer-events: none; }
.toast-item {
  background: var(--surface); border: 1px solid var(--border);
  border-radius: 10px; padding: 0.75rem 1rem;
  display: flex; align-items: center; gap: 0.6rem;
  min-width: 280px; max-width: 360px;
  box-shadow: 0 8px 24px rgba(0,0,0,0.2);
  animation: toastIn 0.3s ease; pointer-events: all;
  font-size: 0.875rem; color: var(--text);
}
@keyframes toastIn { from { opacity:0; transform:translateX(20px); } to { opacity:1; transform:translateX(0); } }
.toast-item.success .toast-dot { background: #22c55e; }
.toast-item.error   .toast-dot { background: #ef4444; }
.toast-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }

/* ── SKILL BAR ───────────────────────────── */
.skill-bar { background: var(--bg3); border-radius: 4px; height: 5px; }
.skill-fill { background: linear-gradient(90deg, var(--teal), var(--gold)); border-radius: 4px; height: 5px; }

/* ── CERT THUMB ──────────────────────────── */
.cert-thumb { width: 56px; height: 42px; object-fit: cover; border-radius: 7px; border: 1px solid var(--border2); cursor: pointer; transition: transform 0.15s; }
.cert-thumb:hover { transform: scale(1.08); }
.cert-no-img {
  width: 56px; height: 42px; border-radius: 7px;
  background: rgba(44,122,123,0.1);
  border: 1px solid var(--border2);
  display: flex; align-items: center; justify-content: center;
  color: var(--gold); font-size: 1rem; cursor: pointer;
}

/* ── UNREAD DOT ──────────────────────────── */
.unread-dot { width: 7px; height: 7px; background: #ef4444; border-radius: 50%; display: inline-block; margin-right: 5px; }

/* ── IMG PREVIEW ──────────────────────────── */
.img-preview-wrap { display: none; margin-top: 0.5rem; }
.img-preview-wrap img { max-height: 110px; border-radius: 8px; border: 1px solid var(--border2); }

/* ── MESSAGE CARD ─────────────────────────── */
.msg-card {
  border: 1px solid var(--border);
  border-radius: 10px; padding: 1rem;
  margin-bottom: 0.75rem;
  transition: background 0.2s;
}
.msg-card.unread { background: rgba(44,122,123,0.07); border-color: rgba(44,122,123,0.25); }
.msg-card.read { background: var(--surface); }
.msg-name { font-weight: 600; color: var(--text); }
.msg-email { color: var(--text2); font-size: 0.82rem; }
.msg-text { color: var(--text); margin-top: 0.35rem; font-size: 0.875rem; }
.msg-time { color: var(--text3); font-size: 0.78rem; margin-top: 0.25rem; }

/* ── RESPONSIVE ───────────────────────────── */
@media (max-width: 991px) {
  .main { margin-left: 0; }
  .sidebar { transform: translateX(-100%); }
  .sidebar.open { transform: translateX(0); }
  .hamburger { display: inline-flex; }
  .page-content { padding: 1rem; }
}
</style>
</head>
<body>

<!-- Sidebar overlay -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
  <div class="sidebar-brand">
    <div class="brand-title"><i class="fas fa-code me-2"></i>Portfolio Admin</div>
    <div class="brand-user">{{ auth()->user()->full_name ?? 'User' }}</div>
    <div class="role-badge">{{ auth()->user()->getRoleDisplayName() ?? 'User' }}</div>
  </div>

  <div class="sidebar-scroll">
    <div class="nav-group-label">Overview</div>
    <a href="{{ route('admin.dashboard') }}" class="sidebar-link @if(request()->routeIs('admin.dashboard')) active @endif">
      <i class="fas fa-home"></i> Dashboard
    </a>

    <div class="nav-group-label">My Profile</div>
    <a href="{{ route('admin.profile') }}" class="sidebar-link @if(request()->routeIs('admin.profile')) active @endif">
      <i class="fas fa-user-circle"></i> Profile & Settings
    </a>

    @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
    <div class="nav-group-label">Portfolio Content</div>
    <a href="{{ route('admin.education.index') }}" class="sidebar-link @if(request()->routeIs('admin.education.*')) active @endif">
      <i class="fas fa-graduation-cap"></i> Education
    </a>
    <a href="{{ route('admin.skills.index') }}" class="sidebar-link @if(request()->routeIs('admin.skills.*')) active @endif">
      <i class="fas fa-layer-group"></i> Skills
    </a>
    <a href="{{ route('admin.experience.index') }}" class="sidebar-link @if(request()->routeIs('admin.experience.*')) active @endif">
      <i class="fas fa-briefcase"></i> Experience
    </a>
    <a href="{{ route('admin.projects.index') }}" class="sidebar-link @if(request()->routeIs('admin.projects.*')) active @endif">
      <i class="fas fa-code-branch"></i> Projects
    </a>
    <a href="{{ route('admin.certificates.index') }}" class="sidebar-link @if(request()->routeIs('admin.certificates.*')) active @endif">
      <i class="fas fa-certificate"></i> Certificates
    </a>
    <a href="{{ route('admin.affiliations.index') }}" class="sidebar-link @if(request()->routeIs('admin.affiliations.*')) active @endif">
      <i class="fas fa-handshake"></i> Affiliations
    </a>
    @endif

    <div class="nav-group-label">Blog</div>
    <a href="{{ route('admin.blog.index') }}" class="sidebar-link @if(request()->routeIs('admin.blog.*')) active @endif">
      <i class="fas fa-newspaper"></i> Blog Posts
    </a>

    @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
    <div class="nav-group-label">System</div>
    @if(auth()->user()->isSuperAdmin())
    <a href="{{ route('admin.users.index') }}" class="sidebar-link @if(request()->routeIs('admin.users.*')) active @endif">
      <i class="fas fa-users-cog"></i> Users
    </a>
    @endif
    <a href="{{ route('admin.messages') }}" class="sidebar-link @if(request()->routeIs('admin.messages')) active @endif">
      <i class="fas fa-envelope"></i> Messages
      @php $unread = \App\Models\ContactMessage::where('is_read',0)->count() @endphp
      @if($unread > 0)<span class="badge bg-danger">{{ $unread }}</span>@endif
    </a>
    <a href="{{ route('admin.audit-logs') }}" class="sidebar-link @if(request()->routeIs('admin.audit-logs')) active @endif">
    <i class="fas fa-clipboard-list me-2"></i>Audit Logs
    </a>
    @endif
  </div>

  <div class="sidebar-footer">
    <a href="{{ url('/') }}" target="_blank" class="btn btn-ghost btn-sm w-100">
      <i class="fas fa-external-link-alt me-1"></i>View Portfolio
    </a>
    <a href="{{ route('logout') }}" class="btn btn-danger-soft btn-sm w-100"
       onclick="event.preventDefault();document.getElementById('logoutForm').submit()">
      <i class="fas fa-sign-out-alt me-1"></i>Logout
    </a>
    <form id="logoutForm" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
  </div>
</aside>

<!-- MAIN -->
<main class="main">
  <!-- TOPBAR -->
<header class="topbar">
  <div class="topbar-left">
    <button class="hamburger" onclick="openSidebar()"><i class="fas fa-bars"></i></button>
    <span class="topbar-title">@yield('page-title', 'Dashboard')</span>
  </div>

  <div class="topbar-right">
    <!-- NOTIFICATION BELL -->
    <a href="{{ route('admin.notifications') }}" class="btn btn-ghost btn-sm" style="position:relative;text-decoration:none;color:var(--text2);padding:0.4rem 0.6rem;">
      <i class="fas fa-bell" style="font-size:1.2rem"></i>
      @php
        $unreadCount = \App\Helpers\NotificationHelper::unreadCount(auth()->id());
      @endphp
      @if($unreadCount > 0)
        <span class="badge bg-danger" style="position:absolute;top:-4px;right:-4px;font-size:0.6rem;padding:2px 6px;border-radius:50%;min-width:18px;">{{ $unreadCount }}</span>
      @endif
    </a>

    <!-- THEME BUTTON -->
    <button class="theme-btn" id="themeBtn" onclick="toggleTheme()">
      <i class="fas fa-sun" id="themeIcon"></i>
      <span id="themeLabel">Light</span>
    </button>

    <!-- CLOCK -->
    <span style="color:var(--text3);font-size:0.78rem" id="clock"></span>
  </div>
</header>

  <!-- ALERTS from session -->
  @if(session('success') || session('error'))
  <div style="padding:0.75rem 1.5rem 0">
    @if(session('success'))<div class="alert alert-success py-2 mb-0">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert alert-danger py-2 mb-0">{{ session('error') }}</div>@endif
  </div>
  @endif

  <!-- PAGE CONTENT -->
  <div class="page-content">
    @yield('content')
  </div>
</main>

<!-- TOAST STACK -->
<div class="toast-stack" id="toastStack"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// ══════════════════════════════════════════════
// THEME
// ══════════════════════════════════════════════
function applyTheme(t) {
  document.documentElement.setAttribute('data-theme', t);
  localStorage.setItem('adminTheme', t);
  document.getElementById('themeIcon').className  = t === 'dark' ? 'fas fa-sun'  : 'fas fa-moon';
  document.getElementById('themeLabel').textContent = t === 'dark' ? 'Light' : 'Dark';
  // Sync to server for SSR
  fetch('/admin/set-theme?theme=' + t, { headers: {'X-CSRF-TOKEN': csrf()} }).catch(()=>{});
}
function toggleTheme() { applyTheme(document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark'); }
applyTheme(localStorage.getItem('adminTheme') || '{{ session("theme","dark") }}');

// ══════════════════════════════════════════════
// SIDEBAR
// ══════════════════════════════════════════════
function openSidebar()  { document.getElementById('sidebar').classList.add('open'); document.getElementById('sidebarOverlay').classList.add('show'); document.body.style.overflow='hidden'; }
function closeSidebar() { document.getElementById('sidebar').classList.remove('open'); document.getElementById('sidebarOverlay').classList.remove('show'); document.body.style.overflow=''; }
document.querySelectorAll('.sidebar-link').forEach(l => l.addEventListener('click', () => { if(window.innerWidth < 992) closeSidebar(); }));

// ══════════════════════════════════════════════
// CLOCK
// ══════════════════════════════════════════════
function tick() { document.getElementById('clock').textContent = new Date().toLocaleString('en-KE',{hour:'2-digit',minute:'2-digit',second:'2-digit'}); }
setInterval(tick,1000); tick();

// ══════════════════════════════════════════════
// CSRF
// ══════════════════════════════════════════════
function csrf() { return document.querySelector('meta[name="csrf-token"]').content; }

// ══════════════════════════════════════════════
// TOAST
// ══════════════════════════════════════════════
function showToast(msg, type='success') {
  const stack = document.getElementById('toastStack');
  const t = document.createElement('div');
  t.className = `toast-item ${type}`;
  t.innerHTML = `<div class="toast-dot"></div><span>${msg}</span>`;
  stack.appendChild(t);
  setTimeout(() => { t.style.opacity='0'; t.style.transform='translateX(20px)'; t.style.transition='all 0.3s'; setTimeout(()=>t.remove(),300); }, 3500);
}

// ══════════════════════════════════════════════
// MODAL SYSTEM
// ══════════════════════════════════════════════
// This is the ONLY place modal-close behaviour should live. Don't add
// duplicate click/Escape listeners in individual page scripts — that's
// what caused modals to close on outside-click even when they were
// marked data-backdrop="static" (the per-page listener had no idea
// about that attribute and closed it anyway).
function openModal(id) {
  const el = document.getElementById(id);
  if (!el) return;
  el.classList.add('show');
  document.body.style.overflow = 'hidden';
}
function closeModal(id) {
  const el = document.getElementById(id);
  if (!el) return;
  el.classList.remove('show');
  document.body.style.overflow = '';
}

// Close on overlay click — unless the modal opts out via data-backdrop="static".
document.addEventListener('click', e => {
  if (e.target.classList.contains('modal-overlay')) {
    if (e.target.dataset.backdrop === 'static') return;
    e.target.classList.remove('show');
    document.body.style.overflow = '';
  }
});

// Close on Esc — unless the modal opts out via data-keyboard="false".
document.addEventListener('keydown', e => {
  if (e.key !== 'Escape') return;
  document.querySelectorAll('.modal-overlay.show').forEach(m => {
    if (m.dataset.keyboard === 'false') return;
    m.classList.remove('show');
    document.body.style.overflow = '';
  });
});

// ══════════════════════════════════════════════
// AJAX HELPER
// ══════════════════════════════════════════════
async function ajaxPost(url, data) {
  const fd = data instanceof FormData ? data : (() => { const f = new FormData(); Object.entries(data).forEach(([k,v])=>f.set(k,v)); return f; })();
  fd.set('_token', csrf());
  const res = await fetch(url, { method:'POST', body: fd });
  return res.json();
}
</script>
@stack('scripts')
</body>
</html>