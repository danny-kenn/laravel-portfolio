@extends('layouts.admin')
@section('title','Dashboard')
@section('page-title','Dashboard')
@section('content')

<div class="row g-3 mb-4">
  <div class="col-6 col-md-3">
    <div class="stat-card">
      <div class="d-flex justify-content-between align-items-start">
        <div><div class="stat-num">{{ $stats['total_projects'] ?? 0 }}</div><div class="stat-lbl">Projects</div></div>
        <i class="fas fa-code-branch stat-icon text-info"></i>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-card">
      <div class="d-flex justify-content-between align-items-start">
        <div><div class="stat-num">{{ $stats['total_blog_posts'] ?? 0 }}</div><div class="stat-lbl">Blog Posts</div></div>
        <i class="fas fa-newspaper stat-icon" style="color:var(--gold)"></i>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-card">
      <div class="d-flex justify-content-between align-items-start">
        <div><div class="stat-num">{{ $stats['total_certificates'] ?? 0 }}</div><div class="stat-lbl">Certificates</div></div>
        <i class="fas fa-certificate stat-icon" style="color:var(--teal)"></i>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-card">
      <div class="d-flex justify-content-between align-items-start">
        <div><div class="stat-num">{{ $stats['total_skills'] ?? 0 }}</div><div class="stat-lbl">Skills</div></div>
        <i class="fas fa-layer-group stat-icon" style="color:#a855f7"></i>
      </div>
    </div>
  </div>
</div>

@if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
<div class="row g-3 mb-4">
  <div class="col-6 col-md-3">
    <div class="stat-card">
      <div class="d-flex justify-content-between align-items-start">
        <div><div class="stat-num">{{ $stats['total_users'] ?? 0 }}</div><div class="stat-lbl">Users</div></div>
        <i class="fas fa-users stat-icon" style="color:#f59e0b"></i>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-card">
      <div class="d-flex justify-content-between align-items-start">
        <div><div class="stat-num">{{ $stats['active_users'] ?? 0 }}</div><div class="stat-lbl">Active Users</div></div>
        <i class="fas fa-user-check stat-icon" style="color:#22c55e"></i>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-card">
      <div class="d-flex justify-content-between align-items-start">
        <div><div class="stat-num">{{ $stats['unread_messages'] ?? 0 }}</div><div class="stat-lbl">Unread Msgs</div></div>
        <i class="fas fa-envelope stat-icon" style="color:#ef4444"></i>
      </div>
    </div>
  </div>
</div>
@endif

<div class="row g-3">
  <!-- Recent Projects -->
  <div class="col-md-6">
    <div class="c-card">
      <div class="c-card-header">
        <h6><i class="fas fa-code-branch me-2" style="color:var(--teal)"></i>Recent Projects</h6>
        @if(auth()->user()->isEditor() || auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
        <a href="{{ route('admin.projects.index') }}" class="btn btn-ghost btn-sm">View All</a>
        @endif
      </div>
      <div class="c-card-body p-0">
        @forelse($stats['recent_projects'] ?? [] as $p)
        <div class="d-flex align-items-center justify-content-between px-4 py-3" style="border-bottom:1px solid var(--border)">
          <div>
            <div style="font-weight:500;color:var(--text);font-size:0.875rem">
              @if($p->is_featured)<span style="color:var(--gold)">⭐ </span>@endif{{ $p->title }}
            </div>
            <div style="font-size:0.78rem;color:var(--text2);margin-top:2px">{{ Str::limit($p->description ?? '', 60) }}</div>
          </div>
        </div>
        @empty
        <div class="px-4 py-4 text-center" style="color:var(--text2);font-size:0.875rem">No projects yet.</div>
        @endforelse
      </div>
    </div>
  </div>

  <!-- Recent Messages (admin+) or Blog (author+) -->
  <div class="col-md-6">
    @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
    <div class="c-card">
      <div class="c-card-header">
        <h6><i class="fas fa-envelope me-2" style="color:#ef4444"></i>Recent Messages</h6>
        <a href="{{ route('admin.messages') }}" class="btn btn-ghost btn-sm">View All</a>
      </div>
      <div class="c-card-body p-0">
        @forelse($stats['recent_messages'] ?? [] as $m)
        <div class="px-4 py-3" style="border-bottom:1px solid var(--border)">
          <div class="d-flex align-items-center justify-content-between">
            <span style="font-weight:500;color:var(--text);font-size:0.875rem">
              @if(!$m->is_read)<span class="unread-dot"></span>@endif{{ $m->sender_name }}
            </span>
            <span style="font-size:0.75rem;color:var(--text3)">{{ $m->created_at->diffForHumans() }}</span>
          </div>
          <div style="font-size:0.8rem;color:var(--text2);margin-top:2px">{{ Str::limit($m->message, 80) }}</div>
        </div>
        @empty
        <div class="px-4 py-4 text-center" style="color:var(--text2);font-size:0.875rem">No messages yet.</div>
        @endforelse
      </div>
    </div>
    @else
    <div class="c-card">
      <div class="c-card-header">
        <h6><i class="fas fa-newspaper me-2" style="color:var(--gold)"></i>Recent Blog Posts</h6>
        <a href="{{ route('admin.blog.index') }}" class="btn btn-ghost btn-sm">View All</a>
      </div>
      <div class="c-card-body p-0">
        @forelse($stats['recent_blog_posts'] ?? [] as $post)
        <div class="d-flex align-items-center justify-content-between px-4 py-3" style="border-bottom:1px solid var(--border)">
          <div>
            <div style="font-weight:500;color:var(--text);font-size:0.875rem">{{ $post->title }}</div>
            <div style="font-size:0.78rem;color:var(--text2);margin-top:2px">{{ $post->created_at->format('M d, Y') }}</div>
          </div>
          <span class="badge @if($post->status==='published') badge-status-pub @elseif($post->status==='draft') badge-status-draft @else badge-status-arch @endif">{{ $post->status }}</span>
        </div>
        @empty
        <div class="px-4 py-4 text-center" style="color:var(--text2);font-size:0.875rem">No posts yet.</div>
        @endforelse
      </div>
    </div>
    @endif
  </div>

  <!-- Quick Start -->
  <div class="col-12">
    <div class="c-card">
      <div class="c-card-header"><h6><i class="fas fa-bolt me-2" style="color:var(--gold)"></i>Quick Start</h6></div>
      <div class="c-card-body">
        <p style="color:var(--text);margin:0;font-size:0.875rem;line-height:1.7">
          Use the sidebar to manage every section of your portfolio. Changes are <strong style="color:var(--teal)">saved instantly</strong> — forms open as modals (bottom sheets on mobile) so you never leave the page.
          Upload certificate images and PDFs directly from the Certificates section. Use the <strong style="color:var(--gold)">☀ / 🌙</strong> button in the top right to switch between light and dark mode.
        </p>
      </div>
    </div>
  </div>
</div>
@endsection