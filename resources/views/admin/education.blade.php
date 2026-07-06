@extends('layouts.admin')
@section('title','Education')
@section('page-title','Education & Certifications')
@section('content')

<div class="c-card">
  <div class="c-card-header">
    <h6><i class="fas fa-graduation-cap me-2" style="color:var(--teal)"></i>Education</h6>
    <button class="btn btn-teal btn-sm" onclick="openEduModal()"><i class="fas fa-plus me-1"></i>Add Entry</button>
  </div>
  <div class="c-card-body p-0">
    <div class="table-responsive">
      <table class="data-table">
        <thead><tr><th>Institution</th><th>Degree</th><th>Years</th><th style="width:110px">Actions</th></tr></thead>
        <tbody id="eduBody"><tr><td colspan="4" style="text-align:center;padding:2rem;color:var(--text2)"><i class="fas fa-spinner fa-spin me-2"></i>Loading…</td></tr></tbody>
      </table>
    </div>
  </div>
</div>

<!-- EDUCATION MODAL -->
<div class="modal-overlay" id="eduModal">
  <div class="modal-box wide">
    <div class="modal-hd">
      <h6 id="eduModalTitle">Add Education</h6>
      <button class="modal-close" onclick="closeModal('eduModal')"><i class="fas fa-times"></i></button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="eduId">
      <div class="mb-3"><label class="form-label">Institution <span style="color:#ef4444">*</span></label><input type="text" id="eInst" class="form-control" required placeholder="University name…"></div>
      <div class="mb-3"><label class="form-label">Degree / Qualification <span style="color:#ef4444">*</span></label><input type="text" id="eDeg" class="form-control" required placeholder="BSc Software Engineering…"></div>
      <div class="row g-2 mb-3">
        <div class="col-6"><label class="form-label">Start Year</label><input type="text" id="eStart" class="form-control" placeholder="2023"></div>
        <div class="col-6"><label class="form-label">End Year</label><input type="text" id="eEnd" class="form-control" placeholder="Present / 2027"></div>
      </div>
      <div class="mb-3"><label class="form-label">Description / Coursework</label><textarea id="eDesc" class="form-control" rows="3"></textarea></div>
      <div class="mb-3"><label class="form-label">Sort Order</label><input type="number" id="eOrder" class="form-control" value="0"></div>
    </div>
    <div class="modal-ft">
      <button class="btn btn-ghost" onclick="closeModal('eduModal')">Cancel</button>
      <button class="btn btn-teal" id="eduSaveBtn" onclick="saveEdu()"><i class="fas fa-save me-1"></i>Save</button>
    </div>
  </div>
</div>

@push('scripts')
<script>
function openEduModal(d = null) {
  document.getElementById('eduId').value  = d?.id ?? '';
  document.getElementById('eInst').value  = d?.institution ?? '';
  document.getElementById('eDeg').value   = d?.degree ?? '';
  document.getElementById('eStart').value = d?.start_year ?? '';
  document.getElementById('eEnd').value   = d?.end_year ?? '';
  document.getElementById('eDesc').value  = d?.description ?? '';
  document.getElementById('eOrder').value = d?.sort_order ?? 0;
  document.getElementById('eduModalTitle').textContent = d ? 'Edit Education' : 'Add Education';
  openModal('eduModal');
}

async function saveEdu() {
  const id = document.getElementById('eduId').value;
  const btn = document.getElementById('eduSaveBtn');
  btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Saving…';
  const fd = new FormData();
  if (id) fd.set('_method','PUT');
  fd.set('_token', csrf());
  fd.set('institution',  document.getElementById('eInst').value);
  fd.set('degree',       document.getElementById('eDeg').value);
  fd.set('start_year',   document.getElementById('eStart').value);
  fd.set('end_year',     document.getElementById('eEnd').value);
  fd.set('description',  document.getElementById('eDesc').value);
  fd.set('sort_order',   document.getElementById('eOrder').value);
  try {
    const url = id ? `/admin/education/${id}` : '{{ route("admin.education.store") }}';
    const r = await fetch(url, { method:'POST', body:fd }); const j = await r.json();
    if (!j.success) throw new Error(j.message);
    showToast(j.message||'Saved!'); closeModal('eduModal'); loadEdu();
  } catch(e) { showToast(e.message||'Error','error'); }
  btn.disabled = false; btn.innerHTML = '<i class="fas fa-save me-1"></i>Save';
}
async function deleteEdu(id) {
  if (!confirm('Delete?')) return;
  const fd = new FormData(); fd.set('_method','DELETE'); fd.set('_token',csrf());
  const r = await fetch(`/admin/education/${id}`, { method:'POST', body:fd }); const j = await r.json();
  if (j.success) { showToast('Deleted'); loadEdu(); } else showToast(j.message||'Error','error');
}
async function loadEdu() {
  const r = await fetch('/admin/education?json=1'); const j = await r.json();
  const body = document.getElementById('eduBody');
  if (!j.data?.length) { body.innerHTML = '<tr><td colspan="4" style="text-align:center;padding:2rem;color:var(--text2)">No education entries yet.</td></tr>'; return; }
  body.innerHTML = j.data.map(e => `<tr>
    <td style="font-weight:500">${e.institution}</td>
    <td style="color:var(--text2);font-size:0.875rem">${e.degree}</td>
    <td style="color:var(--text2);font-size:0.875rem">${e.start_year} – ${e.end_year||'Present'}</td>
    <td>
      <button class="btn btn-ghost btn-icon btn-sm me-1" onclick='openEduModal(${JSON.stringify(e)})'><i class="fas fa-edit"></i></button>
      <button class="btn btn-danger-soft btn-icon btn-sm" onclick="deleteEdu(${e.id})"><i class="fas fa-trash"></i></button>
    </td></tr>`).join('');
}
loadEdu();
</script>
@endpush
@endsection   