@extends('layouts.admin')
@section('title', 'Experience')
@section('page-title', 'Work Experience')
@section('content')

<div class="c-card">
  <div class="c-card-header">
    <h6><i class="fas fa-briefcase me-2" style="color:var(--teal)"></i>Work Experience <span id="expCount" style="color:var(--text2);font-size:0.8rem;font-weight:400"></span></h6>
    <button class="btn btn-teal btn-sm" onclick="openExpModal()"><i class="fas fa-plus me-1"></i>Add Experience</button>
  </div>
  <div class="c-card-body p-0">
    <div class="table-responsive">
      <table class="data-table">
        <thead><tr><th>Job Title</th><th>Company</th><th>Period</th><th style="width:120px">Actions</th></tr></thead>
        <tbody id="expBody">
          <tr><td colspan="4" style="text-align:center;padding:2rem;color:var(--text2)"><i class="fas fa-spinner fa-spin me-2"></i>Loading…</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- EXPERIENCE MODAL -->
<div class="modal-overlay" id="expModal">
  <div class="modal-box wide">
    <div class="modal-hd">
      <h6 id="expModalTitle">Add Experience</h6>
      <button class="modal-close" onclick="closeModal('expModal')"><i class="fas fa-times"></i></button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="expId">
      <div class="mb-3">
        <label class="form-label">Job Title <span style="color:#ef4444">*</span></label>
        <input type="text" id="expTitle" class="form-control" placeholder="Software Engineer" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Company <span style="color:#ef4444">*</span></label>
        <input type="text" id="expCompany" class="form-control" placeholder="Company name" required>
      </div>
      <div class="row g-2 mb-3">
        <div class="col-md-6">
          <label class="form-label">Start Date <span style="color:#ef4444">*</span></label>
          <input type="text" id="expStart" class="form-control" placeholder="January 2024" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">End Date</label>
          <input type="text" id="expEnd" class="form-control" placeholder="December 2024 (or leave blank if present)">
        </div>
      </div>
      <div class="mb-3 form-check">
        <input type="checkbox" id="expCurrent" class="form-check-input" value="1">
        <label class="form-check-label" for="expCurrent" style="color:var(--text2);font-size:0.875rem;">Currently working here</label>
      </div>
      <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea id="expDesc" class="form-control" rows="4" placeholder="Key responsibilities and achievements…"></textarea>
      </div>
      <div class="mb-3">
        <label class="form-label">Sort Order</label>
        <input type="number" id="expOrder" class="form-control" value="0">
      </div>
    </div>
    <div class="modal-ft">
      <button class="btn btn-ghost" onclick="closeModal('expModal')">Cancel</button>
      <button class="btn btn-teal" id="expSaveBtn" onclick="saveExperience()"><i class="fas fa-save me-1"></i>Save</button>
    </div>
  </div>
</div>

@push('scripts')
<script>
function openExpModal(data = null) {
  document.getElementById('expId').value     = data?.id ?? '';
  document.getElementById('expTitle').value  = data?.job_title ?? '';
  document.getElementById('expCompany').value = data?.company ?? '';
  document.getElementById('expStart').value  = data?.start_date ?? '';
  document.getElementById('expEnd').value    = data?.end_date ?? '';
  document.getElementById('expDesc').value   = data?.description ?? '';
  document.getElementById('expOrder').value  = data?.sort_order ?? 0;
  document.getElementById('expCurrent').checked = data?.is_current == 1;
  document.getElementById('expModalTitle').textContent = data ? 'Edit Experience' : 'Add Experience';
  openModal('expModal');
}

async function saveExperience() {
  const id  = document.getElementById('expId').value;
  const btn = document.getElementById('expSaveBtn');
  btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Saving…';

  const fd = new FormData();
  if (id) fd.set('_method', 'PUT');
  fd.set('_token', csrf());
  fd.set('job_title',  document.getElementById('expTitle').value);
  fd.set('company',    document.getElementById('expCompany').value);
  fd.set('start_date', document.getElementById('expStart').value);
  fd.set('end_date',   document.getElementById('expEnd').value);
  fd.set('is_current', document.getElementById('expCurrent').checked ? 1 : 0);
  fd.set('description', document.getElementById('expDesc').value);
  fd.set('sort_order', document.getElementById('expOrder').value);

  try {
    const url = id ? `/admin/experience/${id}` : '{{ route("admin.experience.store") }}';
    const r = await fetch(url, { method:'POST', body:fd });
    const j = await r.json();
    if (!j.success) throw new Error(j.message);
    showToast(j.message || 'Saved!', 'success');
    closeModal('expModal');
    loadExperience();
  } catch(e) { showToast(e.message || 'Error', 'error'); }
  btn.disabled = false; btn.innerHTML = '<i class="fas fa-save me-1"></i>Save';
}

async function deleteExperience(id) {
  if (!confirm('Delete this experience entry?')) return;
  const fd = new FormData(); fd.set('_method','DELETE'); fd.set('_token',csrf());
  const r = await fetch(`/admin/experience/${id}`, { method:'POST', body:fd });
  const j = await r.json();
  if (j.success) { showToast('Deleted','success'); loadExperience(); }
  else showToast(j.message||'Error','error');
}

async function loadExperience() {
  const r = await fetch('/admin/experience?json=1');
  const j = await r.json();
  const body = document.getElementById('expBody');
  if (!j.data?.length) {
    body.innerHTML = '<tr><td colspan="4" style="text-align:center;padding:2rem;color:var(--text2)">No experience entries yet.</td></tr>';
    document.getElementById('expCount').textContent = '';
    return;
  }
  document.getElementById('expCount').textContent = `(${j.data.length})`;
  body.innerHTML = j.data.map(e => `<tr>
    <td style="font-weight:500">${e.job_title}</td>
    <td style="color:var(--text2)">${e.company}</td>
    <td>
      <span style="color:var(--text2)">${e.start_date}</span>
      ${e.is_current 
        ? ' <span class="badge badge-status-pub">Present</span>' 
        : ` <span style="color:var(--text3)">–</span> <span style="color:var(--text2)">${e.end_date || ''}</span>`}
    </td>
    <td>
      <button class="btn btn-ghost btn-icon btn-sm me-1" onclick='openExpModal(${JSON.stringify(e)})' title="Edit"><i class="fas fa-edit"></i></button>
      <button class="btn btn-danger-soft btn-icon btn-sm" onclick="deleteExperience(${e.id})" title="Delete"><i class="fas fa-trash"></i></button>
    </td></tr>`).join('');
}
loadExperience();
</script>
@endpush
@endsection