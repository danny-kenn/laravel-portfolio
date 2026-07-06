@extends('layouts.admin')
@section('title', 'Affiliations')
@section('page-title', 'Professional Affiliations')
@section('content')

<div class="c-card">
  <div class="c-card-header">
    <h6><i class="fas fa-handshake me-2" style="color:var(--gold)"></i>Affiliations <span id="affCount" style="color:var(--text2);font-size:0.8rem;font-weight:400"></span></h6>
    <button class="btn btn-teal btn-sm" onclick="openAffModal()"><i class="fas fa-plus me-1"></i>Add Affiliation</button>
  </div>
  <div class="c-card-body p-0">
    <div class="table-responsive">
      <table class="data-table">
        <thead><tr><th>Organization</th><th>Status</th><th>Member Since</th><th style="width:120px">Actions</th></tr></thead>
        <tbody id="affBody">
          <tr><td colspan="4" style="text-align:center;padding:2rem;color:var(--text2)"><i class="fas fa-spinner fa-spin me-2"></i>Loading…</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- AFFILIATION MODAL -->
<div class="modal-overlay" id="affModal">
  <div class="modal-box wide">
    <div class="modal-hd">
      <h6 id="affModalTitle">Add Affiliation</h6>
      <button class="modal-close" onclick="closeModal('affModal')"><i class="fas fa-times"></i></button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="affId">
      <div class="mb-3">
        <label class="form-label">Organization <span style="color:#ef4444">*</span></label>
        <input type="text" id="affOrg" class="form-control" placeholder="e.g. Quality Society of Kenya" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea id="affDesc" class="form-control" rows="2" placeholder="Brief description of the affiliation…"></textarea>
      </div>
      <div class="row g-2 mb-3">
        <div class="col-md-6">
          <label class="form-label">Status</label>
          <input type="text" id="affStatus" class="form-control" placeholder="Active / Inactive / Pending" value="Active">
        </div>
        <div class="col-md-6">
          <label class="form-label">Member Since</label>
          <input type="text" id="affSince" class="form-control" placeholder="2024">
        </div>
      </div>
      <div class="row g-2 mb-3">
        <div class="col-md-6">
          <label class="form-label">Icon Class</label>
          <input type="text" id="affIcon" class="form-control" placeholder="fas fa-users" value="fas fa-users">
        </div>
        <div class="col-md-6">
          <label class="form-label">Badge Text</label>
          <input type="text" id="affBadge" class="form-control" placeholder="QSK Member">
        </div>
      </div>
      <div class="mb-3">
        <label class="form-label">Benefits <span style="color:var(--text2);font-size:0.78rem">(one per line)</span></label>
        <textarea id="affBenefits" class="form-control" rows="3" placeholder="Networking opportunities&#10;Professional development&#10;Industry events"></textarea>
      </div>
      <div class="mb-3">
        <label class="form-label">Sort Order</label>
        <input type="number" id="affOrder" class="form-control" value="0">
      </div>
    </div>
    <div class="modal-ft">
      <button class="btn btn-ghost" onclick="closeModal('affModal')">Cancel</button>
      <button class="btn btn-teal" id="affSaveBtn" onclick="saveAff()"><i class="fas fa-save me-1"></i>Save</button>
    </div>
  </div>
</div>

@push('scripts')
<script>
function openAffModal(data = null) {
  document.getElementById('affId').value     = data?.id ?? '';
  document.getElementById('affOrg').value    = data?.organization ?? '';
  document.getElementById('affDesc').value   = data?.description ?? '';
  document.getElementById('affStatus').value = data?.status ?? 'Active';
  document.getElementById('affSince').value  = data?.member_since ?? '';
  document.getElementById('affIcon').value   = data?.icon_class ?? 'fas fa-users';
  document.getElementById('affBadge').value  = data?.badge_text ?? '';
  document.getElementById('affBenefits').value = data?.benefits ? (Array.isArray(data.benefits) ? data.benefits.join('\n') : data.benefits) : '';
  document.getElementById('affOrder').value  = data?.sort_order ?? 0;
  document.getElementById('affModalTitle').textContent = data ? 'Edit Affiliation' : 'Add Affiliation';
  openModal('affModal');
}

async function saveAff() {
  const id  = document.getElementById('affId').value;
  const btn = document.getElementById('affSaveBtn');
  btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Saving…';

  const fd = new FormData();
  if (id) fd.set('_method', 'PUT');
  fd.set('_token', csrf());
  fd.set('organization', document.getElementById('affOrg').value);
  fd.set('description',  document.getElementById('affDesc').value);
  fd.set('status',       document.getElementById('affStatus').value);
  fd.set('member_since', document.getElementById('affSince').value);
  fd.set('icon_class',   document.getElementById('affIcon').value);
  fd.set('badge_text',   document.getElementById('affBadge').value);
  fd.set('benefits',     document.getElementById('affBenefits').value);
  fd.set('sort_order',   document.getElementById('affOrder').value);

  try {
    const url = id ? `/admin/affiliations/${id}` : '{{ route("admin.affiliations.store") }}';
    const r = await fetch(url, { method:'POST', body:fd });
    const j = await r.json();
    if (!j.success) throw new Error(j.message);
    showToast(j.message || 'Saved!', 'success');
    closeModal('affModal');
    loadAffiliations();
  } catch(e) { showToast(e.message || 'Error', 'error'); }
  btn.disabled = false; btn.innerHTML = '<i class="fas fa-save me-1"></i>Save';
}

async function deleteAff(id) {
  if (!confirm('Delete this affiliation?')) return;
  const fd = new FormData(); fd.set('_method','DELETE'); fd.set('_token',csrf());
  const r = await fetch(`/admin/affiliations/${id}`, { method:'POST', body:fd });
  const j = await r.json();
  if (j.success) { showToast('Deleted','success'); loadAffiliations(); }
  else showToast(j.message||'Error','error');
}

async function loadAffiliations() {
  const r = await fetch('/admin/affiliations?json=1');
  const j = await r.json();
  const body = document.getElementById('affBody');
  if (!j.data?.length) {
    body.innerHTML = '<tr><td colspan="4" style="text-align:center;padding:2rem;color:var(--text2)">No affiliations yet.</td></tr>';
    document.getElementById('affCount').textContent = '';
    return;
  }
  document.getElementById('affCount').textContent = `(${j.data.length})`;
  body.innerHTML = j.data.map(a => `<tr>
    <td>
      <div style="font-weight:600;color:var(--text)">${a.organization}</div>
      <div style="font-size:0.78rem;color:var(--text2);margin-top:2px">${a.description || ''}</div>
    </td>
    <td><span class="badge" style="background:rgba(44,122,123,0.12);color:var(--teal)">${a.status || 'Active'}</span></td>
    <td style="color:var(--text2)">${a.member_since || '–'}</td>
    <td>
      <button class="btn btn-ghost btn-icon btn-sm me-1" onclick='openAffModal(${JSON.stringify(a)})'><i class="fas fa-edit"></i></button>
      <button class="btn btn-danger-soft btn-icon btn-sm" onclick="deleteAff(${a.id})"><i class="fas fa-trash"></i></button>
    </td></tr>`).join('');
}
loadAffiliations();
</script>
@endpush
@endsection