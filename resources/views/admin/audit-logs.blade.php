@extends('layouts.admin')
@section('title', 'Audit Logs')
@section('page-title', 'Audit Logs')
@section('content')

<div class="c-card">
  <div class="c-card-header">
    <h6><i class="fas fa-clipboard-list me-2" style="color:var(--gold)"></i>Audit Logs <span id="logCount" style="color:var(--text2);font-size:0.8rem;font-weight:400"></span></h6>
    <button class="btn btn-ghost btn-sm" onclick="location.reload()"><i class="fas fa-sync-alt me-1"></i>Refresh</button>
  </div>
  <div class="c-card-body p-0">
    <div class="table-responsive">
      <table class="data-table">
        <thead>
          <tr>
            <th>Time</th>
            <th>User</th>
            <th>Role</th>
            <th>Action</th>
            <th>Module</th>
            <th>Description</th>
            <th>IP</th>
          </tr>
        </thead>
        <tbody id="auditBody">
          <tr><td colspan="7" style="text-align:center;padding:2rem;color:var(--text2)"><i class="fas fa-spinner fa-spin me-2"></i>Loading…</td></tr>
        </tbody>
      </table>
    </div>
  </div>
  <div class="c-card-body" id="paginationWrap"></div>
</div>

<!-- Filter Bar -->
<div class="c-card">
  <div class="c-card-body">
    <div class="row g-2">
      <div class="col-md-3">
        <select id="filterModule" class="form-control" onchange="loadAuditLogs()">
          <option value="">All Modules</option>
          <option value="auth">Authentication</option>
          <option value="projects">Projects</option>
          <option value="skills">Skills</option>
          <option value="education">Education</option>
          <option value="experience">Experience</option>
          <option value="certificates">Certificates</option>
          <option value="affiliations">Affiliations</option>
          <option value="blog">Blog</option>
          <option value="users">Users</option>
          <option value="profile">Profile</option>
        </select>
      </div>
      <div class="col-md-3">
        <select id="filterAction" class="form-control" onchange="loadAuditLogs()">
          <option value="">All Actions</option>
          <option value="login">Login</option>
          <option value="logout">Logout</option>
          <option value="create">Create</option>
          <option value="update">Update</option>
          <option value="delete">Delete</option>
          <option value="view">View</option>
        </select>
      </div>
      <div class="col-md-3">
        <input type="date" id="filterDate" class="form-control" onchange="loadAuditLogs()">
      </div>
      <div class="col-md-3">
        <button class="btn btn-teal w-100" onclick="loadAuditLogs()"><i class="fas fa-filter me-1"></i>Apply Filters</button>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
let currentPage = 1;

async function loadAuditLogs(page = 1) {
  currentPage = page;
  const module = document.getElementById('filterModule').value;
  const action = document.getElementById('filterAction').value;
  const date = document.getElementById('filterDate').value;
  
  const params = new URLSearchParams();
  params.append('json', '1');
  params.append('page', page);
  if (module) params.append('module', module);
  if (action) params.append('action', action);
  if (date) params.append('date', date);
  
  const r = await fetch(`/admin/audit-logs?${params}`);
  const j = await r.json();
  
  const body = document.getElementById('auditBody');
  if (!j.data?.length) {
    body.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:2rem;color:var(--text2)">No logs found.</td></tr>';
    document.getElementById('logCount').textContent = '';
    return;
  }
  
  document.getElementById('logCount').textContent = `(${j.data.length})`;
  
  body.innerHTML = j.data.map(log => `
    <tr>
      <td style="color:var(--text2);font-size:0.8rem;white-space:nowrap">${new Date(log.created_at).toLocaleString()}</td>
      <td style="font-weight:500">${log.user_name}</td>
      <td><span class="badge badge-role-viewer">${log.user_role}</span></td>
      <td>
        <span class="badge ${log.action_badge}">
          <i class="${log.action_icon} me-1"></i>${log.action}
        </span>
      </td>
      <td><span class="badge" style="background:rgba(44,122,123,0.12);color:var(--teal)">${log.module}</span></td>
      <td style="color:var(--text2);max-width:300px">${log.description || '—'}</td>
      <td style="color:var(--text3);font-size:0.75rem">${log.ip_address || '—'}</td>
    </tr>
  `).join('');
  
  // Pagination
  const pagWrap = document.getElementById('paginationWrap');
  if (j.meta && j.meta.last_page > 1) {
    let html = '<div style="display:flex;gap:0.4rem;justify-content:center;flex-wrap:wrap;margin-top:0.5rem">';
    for (let i = 1; i <= j.meta.last_page; i++) {
      html += `<button class="btn ${i === page ? 'btn-teal' : 'btn-ghost'}" style="padding:0.3rem 0.8rem;font-size:0.8rem" onclick="loadAuditLogs(${i})">${i}</button>`;
    }
    html += '</div>';
    pagWrap.innerHTML = html;
  } else {
    pagWrap.innerHTML = '';
  }
}

// Auto-load with filters
loadAuditLogs();
</script>
@endpush
@endsection