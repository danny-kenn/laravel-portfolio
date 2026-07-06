@extends('layouts.admin')
@section('title', 'Notifications')
@section('page-title', 'Notifications')

@section('content')

<div class="c-card">
  <div class="c-card-header">
    <h6><i class="fas fa-bell me-2" style="color:var(--gold)"></i>Notifications</h6>
    <div>
      <button class="btn btn-teal btn-sm me-2" onclick="markAllRead()"><i class="fas fa-check-double me-1"></i>Mark All Read</button>
      <button class="btn btn-ghost btn-sm" onclick="location.reload()"><i class="fas fa-sync-alt me-1"></i>Refresh</button>
    </div>
  </div>
  <div class="c-card-body" id="notificationList">
    <div style="text-align:center;padding:2rem;color:var(--text2)"><i class="fas fa-spinner fa-spin me-2"></i>Loading…</div>
  </div>
</div>

@push('scripts')
<script>
let notificationPage = 1;

async function loadNotifications(page = 1) {
  notificationPage = page;
  const r = await fetch(`/admin/notifications?json=1&page=${page}`);
  const j = await r.json();
  
  const container = document.getElementById('notificationList');
  if (!j.data?.length) {
    container.innerHTML = '<div style="text-align:center;padding:3rem;color:var(--text2)"><i class="fas fa-bell-slash fa-3x d-block mb-3" style="opacity:0.3"></i>No notifications</div>';
    return;
  }
  
  container.innerHTML = j.data.map(n => `
    <div class="notification-item ${n.is_read ? '' : 'unread'}" style="border-bottom:1px solid var(--border);padding:1rem;background:${n.is_read ? 'var(--surface)' : 'rgba(44,122,123,0.08)'};border-left:3px solid ${n.is_read ? 'transparent' : 'var(--teal)'};transition:background 0.2s;cursor:pointer" onclick="markRead(${n.id})">
      <div class="d-flex justify-content-between align-items-start gap-3">
        <div style="flex:1;min-width:0">
          <div style="display:flex;align-items:center;gap:0.5rem;flex-wrap:wrap">
            <span class="badge badge-${n.type || 'info'}">${n.module || 'system'}</span>
            <span class="badge badge-${n.action || 'info'}">${n.action || 'info'}</span>
            ${!n.is_read ? '<span class="badge bg-danger">New</span>' : ''}
          </div>
          <div style="font-weight:600;color:var(--text);margin-top:0.3rem">${n.title}</div>
          <div style="color:var(--text2);font-size:0.875rem;margin-top:0.2rem">${n.message}</div>
          <div style="color:var(--text3);font-size:0.75rem;margin-top:0.3rem">${new Date(n.created_at).toLocaleString()}</div>
        </div>
        ${!n.is_read ? `<button class="btn btn-ghost btn-sm" onclick="event.stopPropagation();markRead(${n.id})"><i class="fas fa-check"></i></button>` : ''}
      </div>
    </div>
  `).join('');
  
  // Pagination
  if (j.meta && j.meta.last_page > 1) {
    let html = '<div style="display:flex;gap:0.4rem;justify-content:center;flex-wrap:wrap;padding:1rem 0">';
    for (let i = 1; i <= j.meta.last_page; i++) {
      html += `<button class="btn ${i === page ? 'btn-teal' : 'btn-ghost'}" style="padding:0.3rem 0.8rem;font-size:0.8rem" onclick="loadNotifications(${i})">${i}</button>`;
    }
    html += '</div>';
    container.innerHTML += html;
  }
}

async function markRead(id) {
  const fd = new FormData(); fd.set('_token', csrf());
  const r = await fetch(`/admin/notifications/${id}/read`, { method:'POST', body:fd });
  const j = await r.json();
  if (j.success) loadNotifications(notificationPage);
}

async function markAllRead() {
  if (!confirm('Mark all notifications as read?')) return;
  const fd = new FormData(); fd.set('_token', csrf());
  const r = await fetch('/admin/notifications/read-all', { method:'POST', body:fd });
  const j = await r.json();
  if (j.success) { showToast('All marked as read','success'); loadNotifications(notificationPage); }
}

loadNotifications();
</script>
@endpush
@endsection