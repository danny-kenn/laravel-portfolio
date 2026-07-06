@extends('layouts.admin')
@section('title', 'Users')
@section('page-title', 'User Management')
@section('content')

<div class="c-card">
  <div class="c-card-header">
    <h6><i class="fas fa-users-cog me-2" style="color:var(--teal)"></i>All Users <span id="userCount" style="color:var(--text2);font-size:0.8rem;font-weight:400"></span></h6>
    <button class="btn btn-teal btn-sm" onclick="openUserModal()"><i class="fas fa-plus me-1"></i>Create User</button>
  </div>
  <div class="c-card-body p-0">
    <div class="table-responsive">
      <table class="data-table">
        <thead><tr><th>Name</th><th>Username</th><th>Email</th><th>Role</th><th>Status</th><th style="width:180px">Actions</th></tr></thead>
        <tbody id="userBody">
          <tr><td colspan="6" style="text-align:center;padding:2rem;color:var(--text2)"><i class="fas fa-spinner fa-spin me-2"></i>Loading…</td></tr>
        </tbody>
      </table>
    </div>
  </div>
  <div class="c-card-body" id="paginationWrap"></div>
</div>

<!-- USER MODAL -->
<div class="modal-overlay" id="userModal">
  <div class="modal-box wide">
    <div class="modal-hd">
      <h6 id="userModalTitle">Create User</h6>
      <button class="modal-close" onclick="closeModal('userModal')"><i class="fas fa-times"></i></button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="userId">
      <div class="mb-3">
        <label class="form-label">Full Name <span style="color:#ef4444">*</span></label>
        <input type="text" id="uName" class="form-control" placeholder="John Doe" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Username <span style="color:#ef4444">*</span></label>
        <input type="text" id="uUsername" class="form-control" placeholder="johndoe" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Email <span style="color:#ef4444">*</span></label>
        <input type="email" id="uEmail" class="form-control" placeholder="john@example.com" required>
      </div>
      <div class="row g-2 mb-3">
        <div class="col-md-6">
          <label class="form-label">Role <span style="color:#ef4444">*</span></label>
          <select id="uRole" class="form-control">
            <option value="admin">Admin</option>
            <option value="editor">Editor</option>
            <option value="author">Author</option>
            <option value="viewer" selected>Viewer</option>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Status</label>
          <select id="uStatus" class="form-control">
            <option value="1">Active</option>
            <option value="0">Inactive</option>
          </select>
        </div>
      </div>
      <div class="alert alert-info">
        <i class="fas fa-info-circle me-1"></i>
        A random password will be auto-generated and sent to the user's email.
      </div>
    </div>
    <div class="modal-ft">
      <button class="btn btn-ghost" onclick="closeModal('userModal')">Cancel</button>
      <button class="btn btn-teal" id="userSaveBtn" onclick="saveUser()"><i class="fas fa-save me-1"></i>Create User</button>
    </div>
  </div>
</div>

@push('scripts')
<script>
function openUserModal(data = null) {
  document.getElementById('userId').value    = data?.id ?? '';
  document.getElementById('uName').value     = data?.full_name ?? '';
  document.getElementById('uUsername').value = data?.username ?? '';
  document.getElementById('uEmail').value    = data?.email ?? '';
  document.getElementById('uRole').value     = data?.role ?? 'viewer';
  document.getElementById('uStatus').value   = data?.is_active ? '1' : '0';
  document.getElementById('userModalTitle').textContent = data ? 'Edit User' : 'Create User';
  openModal('userModal');
}

async function saveUser() {
  const id  = document.getElementById('userId').value;
  const btn = document.getElementById('userSaveBtn');
  btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Saving…';

  const fd = new FormData();
  if (id) fd.set('_method', 'PUT');
  fd.set('_token', csrf());
  fd.set('full_name', document.getElementById('uName').value);
  fd.set('username',  document.getElementById('uUsername').value);
  fd.set('email',     document.getElementById('uEmail').value);
  fd.set('role',      document.getElementById('uRole').value);
  fd.set('is_active', document.getElementById('uStatus').value);

  try {
    const url = id ? `/admin/users/${id}` : '{{ route("admin.users.store") }}';
    const r = await fetch(url, { method:'POST', body:fd });
    const j = await r.json();
    if (!j.success) throw new Error(j.message);
    showToast(j.message || 'Saved!', 'success');
    closeModal('userModal');
    loadUsers();
  } catch(e) { showToast(e.message || 'Error', 'error'); }
  btn.disabled = false; btn.innerHTML = id ? '<i class="fas fa-save me-1"></i>Update' : '<i class="fas fa-save me-1"></i>Create User';
}

async function resetPassword(id) {
  if (!confirm('Reset password for this user? A new random password will be emailed to them.')) return;
  const fd = new FormData(); fd.set('_token', csrf());
  const r = await fetch(`/admin/users/${id}/reset-password`, { method:'POST', body:fd });
  const j = await r.json();
  if (j.success) showToast(j.message || 'Password reset!', 'success');
  else showToast(j.message || 'Error', 'error');
}

async function deleteUser(id) {
  if (!confirm('Delete this user?')) return;
  const fd = new FormData(); fd.set('_method','DELETE'); fd.set('_token',csrf());
  const r = await fetch(`/admin/users/${id}`, { method:'POST', body:fd });
  const j = await r.json();
  if (j.success) { showToast('Deleted','success'); loadUsers(); }
  else showToast(j.message||'Error','error');
}

async function loadUsers(page = 1) {
  const r = await fetch(`/admin/users?json=1&page=${page}`);
  const j = await r.json();
  const body = document.getElementById('userBody');
  if (!j.data?.length) {
    body.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:2rem;color:var(--text2)">No users yet.</td></tr>';
    document.getElementById('userCount').textContent = '';
    return;
  }
  document.getElementById('userCount').textContent = `(${j.data.length})`;
  body.innerHTML = j.data.map(u => {
    const roleBadge = {
      'super_admin': 'badge-role-super',
      'admin': 'badge-role-admin',
      'editor': 'badge-role-editor',
      'author': 'badge-role-author',
      'viewer': 'badge-role-viewer'
    }[u.role] || 'badge-role-viewer';
    
    return `<tr>
      <td style="font-weight:500">${u.full_name}</td>
      <td style="color:var(--text2)">${u.username}</td>
      <td style="color:var(--text2)">${u.email}</td>
      <td><span class="badge ${roleBadge}">${u.role}</span></td>
      <td>${u.is_active ? '<span class="badge badge-status-pub">Active</span>' : '<span class="badge badge-status-arch">Inactive</span>'}</td>
      <td>
        <button class="btn btn-ghost btn-icon btn-sm me-1" onclick='openUserModal(${JSON.stringify(u)})' title="Edit"><i class="fas fa-edit"></i></button>
        <button class="btn btn-info-soft btn-icon btn-sm me-1" onclick="resetPassword(${u.id})" title="Reset Password"><i class="fas fa-key"></i></button>
        <button class="btn btn-danger-soft btn-icon btn-sm" onclick="deleteUser(${u.id})" title="Delete"><i class="fas fa-trash"></i></button>
      </td>
    </tr>`;
  }).join('');
}
loadUsers();
</script>
@endpush
@endsection