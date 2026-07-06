@extends('layouts.admin')
@section('title', 'Messages')
@section('page-title', 'Contact Messages')
@section('content')

<div class="c-card">
  <div class="c-card-header">
    <h6><i class="fas fa-envelope me-2" style="color:#ef4444"></i>All Messages <span id="msgCount" style="color:var(--text2);font-size:0.8rem;font-weight:400"></span></h6>
    <button class="btn btn-ghost btn-sm" onclick="location.reload()"><i class="fas fa-sync-alt me-1"></i>Refresh</button>
  </div>
  <div class="c-card-body" id="messagesList">
    <div style="text-align:center;padding:2rem;color:var(--text2)"><i class="fas fa-spinner fa-spin me-2"></i>Loading messages…</div>
  </div>
</div>

@push('scripts')
<script>
async function loadMessages() {
  const r = await fetch('/admin/messages?json=1');
  const j = await r.json();
  const container = document.getElementById('messagesList');
  if (!j.data?.length) {
    container.innerHTML = '<div style="text-align:center;padding:3rem;color:var(--text2)"><i class="fas fa-inbox fa-3x d-block mb-3" style="opacity:0.3"></i>No messages yet.</div>';
    document.getElementById('msgCount').textContent = '';
    return;
  }
  document.getElementById('msgCount').textContent = `(${j.data.length})`;
  container.innerHTML = j.data.map(m => `
    <div class="msg-card ${m.is_read ? 'read' : 'unread'}">
      <div class="d-flex justify-content-between align-items-start gap-3">
        <div style="flex:1;min-width:0">
          <div>
            ${!m.is_read ? '<span class="unread-dot"></span>' : ''}
            <span class="msg-name">${m.sender_name}</span>
            <span class="msg-email">&lt;${m.sender_email}&gt;</span>
          </div>
          <div class="msg-text">${m.message}</div>
          <div class="msg-time">${m.created_at_human || m.created_at}</div>
        </div>
        <div style="display:flex;gap:0.4rem;flex-shrink:0">
          ${!m.is_read ? `<button class="btn btn-ghost btn-sm" onclick="markRead(${m.id})">Mark Read</button>` : ''}
          <button class="btn btn-danger-soft btn-sm" onclick="deleteMsg(${m.id})">Delete</button>
        </div>
      </div>
    </div>
  `).join('');
}

async function markRead(id) {
  const fd = new FormData(); fd.set('_token', csrf());
  const r = await fetch(`/admin/messages/${id}/read`, { method:'POST', body:fd });
  const j = await r.json();
  if (j.success) { showToast('Marked as read', 'success'); loadMessages(); }
  else showToast(j.message || 'Error', 'error');
}

async function deleteMsg(id) {
  if (!confirm('Delete this message?')) return;
  const fd = new FormData(); fd.set('_method','DELETE'); fd.set('_token',csrf());
  const r = await fetch(`/admin/messages/${id}`, { method:'POST', body:fd });
  const j = await r.json();
  if (j.success) { showToast('Deleted', 'success'); loadMessages(); }
  else showToast(j.message || 'Error', 'error');
}
loadMessages();
</script>
@endpush
@endsection