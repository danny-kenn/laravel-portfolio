@extends('layouts.admin')
@section('title','Projects')
@section('page-title','Projects')
@section('content')

<div class="c-card">
  <div class="c-card-header">
    <h6><i class="fas fa-code-branch me-2" style="color:var(--teal)"></i>Projects <span id="projCount" style="color:var(--text2);font-size:0.8rem;font-weight:400"></span></h6>
    <button class="btn btn-teal btn-sm" onclick="openProjModal()"><i class="fas fa-plus me-1"></i>Add Project</button>
  </div>
  <div class="c-card-body p-0">
    <div class="table-responsive">
      <table class="data-table">
        <thead><tr><th>Title</th><th>Tech Stack</th><th>Featured</th><th style="width:130px">Actions</th></tr></thead>
        <tbody id="projBody">
          <tr><td colspan="4" style="text-align:center;padding:2rem;color:var(--text2)"><i class="fas fa-spinner fa-spin me-2"></i>Loading…</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- PROJECT MODAL -->
<div class="modal-overlay" id="projModal">
  <div class="modal-box wide">
    <div class="modal-hd">
      <h6 id="projModalTitle">Add Project</h6>
      <button class="modal-close" onclick="closeModal('projModal')"><i class="fas fa-times"></i></button>
    </div>
    <div class="modal-body">
      <form id="projForm">
        <input type="hidden" id="projId">
        <div class="mb-3">
          <label class="form-label">Title <span style="color:#ef4444">*</span></label>
          <input type="text" id="pTitle" class="form-control" placeholder="Project name" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Description</label>
          <textarea id="pDesc" class="form-control" rows="4" placeholder="What did this project do? Key features, challenges..."></textarea>
        </div>
        <div class="row g-2 mb-3">
          <div class="col-md-6">
            <label class="form-label">GitHub URL</label>
            <input type="url" id="pGithub" class="form-control" placeholder="https://github.com/...">
          </div>
          <div class="col-md-6">
            <label class="form-label">Live URL</label>
            <input type="url" id="pLive" class="form-control" placeholder="https://...">
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label">Tags / Tech Stack <span style="color:var(--text2);font-size:0.78rem">(comma-separated)</span></label>
          <input type="text" id="pTags" class="form-control" placeholder="PHP, MySQL, Laravel, HTML">
        </div>
        <div class="d-flex align-items-center gap-3">
          <label style="display:flex;align-items:center;gap:0.5rem;cursor:pointer;color:var(--text2);font-size:0.875rem">
            <input type="checkbox" id="pFeatured" class="form-check-input" style="width:18px;height:18px">
            <span>⭐ Mark as Featured Project</span>
          </label>
        </div>
      </form>
    </div>
    <div class="modal-ft">
      <button class="btn btn-ghost" onclick="closeModal('projModal')">Cancel</button>
      <button class="btn btn-teal" id="projSaveBtn" onclick="saveProject()"><i class="fas fa-save me-1"></i>Save Project</button>
    </div>
  </div>
</div>

@push('scripts')
<script>
function openProjModal(data = null) {
  document.getElementById('projId').value   = data?.id ?? '';
  document.getElementById('pTitle').value   = data?.title ?? '';
  document.getElementById('pDesc').value    = data?.description ?? '';
  document.getElementById('pGithub').value  = data?.github_url ?? '';
  document.getElementById('pLive').value    = data?.live_url ?? '';
  document.getElementById('pTags').value    = data?.tags_string ?? '';
  document.getElementById('pFeatured').checked = data?.is_featured == 1;
  document.getElementById('projModalTitle').textContent = data ? 'Edit Project' : 'Add Project';
  openModal('projModal');
}

async function saveProject() {
  const id  = document.getElementById('projId').value;
  const btn = document.getElementById('projSaveBtn');
  btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Saving…';

  const fd = new FormData();
  if (id) fd.set('_method','PUT');
  fd.set('_token',    csrf());
  fd.set('title',     document.getElementById('pTitle').value);
  fd.set('description', document.getElementById('pDesc').value);
  fd.set('github_url',  document.getElementById('pGithub').value);
  fd.set('live_url',    document.getElementById('pLive').value);
  fd.set('tags',        document.getElementById('pTags').value);
  fd.set('is_featured', document.getElementById('pFeatured').checked ? 1 : 0);

  try {
    const url = id ? `/admin/projects/${id}` : '{{ route("admin.projects.store") }}';
    const r = await fetch(url, { method:'POST', body:fd });
    const j = await r.json();
    if (!j.success) throw new Error(j.message);
    showToast(j.message || 'Saved!', 'success');
    closeModal('projModal');
    loadProjects();
  } catch(e) { showToast(e.message||'Error','error'); }
  btn.disabled = false; btn.innerHTML = '<i class="fas fa-save me-1"></i>Save Project';
}

async function deleteProject(id) {
  if (!confirm('Delete this project?')) return;
  const fd = new FormData(); fd.set('_method','DELETE'); fd.set('_token',csrf());
  const r = await fetch(`/admin/projects/${id}`, { method:'POST', body:fd });
  const j = await r.json();
  if (j.success) { showToast('Deleted','success'); loadProjects(); }
  else showToast(j.message||'Error','error');
}

async function loadProjects() {
  const r = await fetch('/admin/projects?json=1');
  const j = await r.json();
  const body = document.getElementById('projBody');
  if (!j.data?.length) {
    body.innerHTML = '<tr><td colspan="4" style="text-align:center;padding:2rem;color:var(--text2)">No projects yet.</td></tr>';
    document.getElementById('projCount').textContent = '';
    return;
  }
  document.getElementById('projCount').textContent = `(${j.data.length})`;
  body.innerHTML = j.data.map(p => {
    const tags = (p.tags || []).map(t => `<span class="badge" style="background:rgba(255,255,255,0.07);color:var(--text2);margin-right:3px">${t.name}</span>`).join('');
    return `<tr>
      <td>
        <div style="font-weight:600;color:var(--text)">${p.is_featured?'⭐ ':''}${p.title}</div>
        <div style="font-size:0.78rem;color:var(--text2);margin-top:2px">${(p.description||'').substring(0,70)}${p.description?.length>70?'…':''}</div>
      </td>
      <td>${tags || '<span style="color:var(--text3)">–</span>'}</td>
      <td>${p.is_featured?'<span class="badge badge-status-pub">Featured</span>':'<span style="color:var(--text3)">–</span>'}</td>
      <td>
        <button class="btn btn-ghost btn-icon btn-sm me-1" onclick='openProjModal(${JSON.stringify({...p, tags_string: (p.tags||[]).map(t=>t.name).join(", ")})})' title="Edit"><i class="fas fa-edit"></i></button>
        <button class="btn btn-danger-soft btn-icon btn-sm" onclick="deleteProject(${p.id})" title="Delete"><i class="fas fa-trash"></i></button>
      </td></tr>`;
  }).join('');
}
loadProjects();
</script>
@endpush
@endsection