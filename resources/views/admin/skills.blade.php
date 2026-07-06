@extends('layouts.admin')
@section('title','Skills')
@section('page-title','Skills & Expertise')
@section('content')

<div class="c-card">
  <div class="c-card-header">
    <h6><i class="fas fa-layer-group me-2" style="color:var(--teal)"></i>Skills <span id="skillCount" style="color:var(--text2);font-size:0.8rem;font-weight:400"></span></h6>
    <button class="btn btn-teal btn-sm" onclick="openSkillModal()"><i class="fas fa-plus me-1"></i>Add Skill</button>
  </div>
  <div class="c-card-body p-0">
    <div class="table-responsive">
      <table class="data-table" id="skillsTable">
        <thead><tr><th>Category</th><th>Skill</th><th>Level</th><th style="width:120px">Actions</th></tr></thead>
        <tbody id="skillsBody">
          <tr><td colspan="4" style="text-align:center;padding:2rem;color:var(--text2)"><i class="fas fa-spinner fa-spin me-2"></i>Loading…</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- SKILL MODAL -->
<div class="modal-overlay" id="skillModal">
  <div class="modal-box">
    <div class="modal-hd">
      <h6 id="skillModalTitle">Add Skill</h6>
      <button class="modal-close" onclick="closeModal('skillModal')"><i class="fas fa-times"></i></button>
    </div>
    <div class="modal-body">
      <form id="skillForm">
        <input type="hidden" id="skillId">
        <div class="mb-3">
          <label class="form-label">Category</label>
          <input type="text" id="sCategory" class="form-control" list="catOptions" placeholder="e.g. Technical Skills" required>
          <datalist id="catOptions">
            <option>Technical Skills</option><option>Quality & Standards</option><option>Soft Skills</option>
          </datalist>
        </div>
        <div class="mb-3">
          <label class="form-label">Skill Name</label>
          <input type="text" id="sName" class="form-control" placeholder="e.g. Python" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Proficiency: <span id="sLevelVal" style="color:var(--gold);font-weight:600">80</span>%</label>
          <input type="range" class="form-range" id="sLevel" min="10" max="100" value="80" oninput="document.getElementById('sLevelVal').textContent=this.value">
          <div class="skill-bar mt-2"><div class="skill-fill" id="sLevelBar" style="width:80%"></div></div>
        </div>
        <div class="mb-3">
          <label class="form-label">Sort Order</label>
          <input type="number" id="sOrder" class="form-control" value="0">
        </div>
      </form>
    </div>
    <div class="modal-ft">
      <button class="btn btn-ghost" onclick="closeModal('skillModal')">Cancel</button>
      <button class="btn btn-teal" id="skillSaveBtn" onclick="saveSkill()"><i class="fas fa-save me-1"></i>Save Skill</button>
    </div>
  </div>
</div>

@push('scripts')
<script>
const SKILLS_URL = '{{ route("admin.skills.store") }}';

// Live bar update
document.getElementById('sLevel').addEventListener('input', function() {
  document.getElementById('sLevelBar').style.width = this.value + '%';
});

function openSkillModal(data = null) {
  document.getElementById('skillId').value    = data?.id ?? '';
  document.getElementById('sCategory').value  = data?.category ?? '';
  document.getElementById('sName').value       = data?.skill_name ?? '';
  document.getElementById('sLevel').value      = data?.proficiency_level ?? 80;
  document.getElementById('sLevelVal').textContent = data?.proficiency_level ?? 80;
  document.getElementById('sLevelBar').style.width = (data?.proficiency_level ?? 80) + '%';
  document.getElementById('sOrder').value      = data?.sort_order ?? 0;
  document.getElementById('skillModalTitle').textContent = data ? 'Edit Skill' : 'Add Skill';
  openModal('skillModal');
}

async function saveSkill() {
  const id   = document.getElementById('skillId').value;
  const btn  = document.getElementById('skillSaveBtn');
  btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Saving…';

  const url  = id ? `/admin/skills/${id}` : SKILLS_URL;
  const fd   = new FormData();
  if (id) fd.set('_method', 'PUT');
  fd.set('category',          document.getElementById('sCategory').value);
  fd.set('skill_name',        document.getElementById('sName').value);
  fd.set('proficiency_level', document.getElementById('sLevel').value);
  fd.set('sort_order',        document.getElementById('sOrder').value);
  fd.set('_token', csrf());

  try {
    const r = await fetch(url, { method:'POST', body:fd });
    const j = await r.json();
    if (!j.success) throw new Error(j.message);
    showToast(j.message || 'Saved!', 'success');
    closeModal('skillModal');
    loadSkills();
  } catch(e) { showToast(e.message || 'Error', 'error'); }
  btn.disabled = false; btn.innerHTML = '<i class="fas fa-save me-1"></i>Save Skill';
}

async function deleteSkill(id) {
  if (!confirm('Delete this skill?')) return;
  const fd = new FormData(); fd.set('_method','DELETE'); fd.set('_token',csrf());
  try {
    const r = await fetch(`/admin/skills/${id}`, { method:'POST', body:fd });
    const j = await r.json();
    if (!j.success) throw new Error(j.message);
    showToast(j.message || 'Deleted', 'success');
    loadSkills();
  } catch(e) { showToast(e.message || 'Error','error'); }
}

async function loadSkills() {
  try {
    const r = await fetch('/admin/skills?json=1');
    const j = await r.json();
    const body = document.getElementById('skillsBody');
    if (!j.data || !j.data.length) {
      body.innerHTML = '<tr><td colspan="4" style="text-align:center;padding:2rem;color:var(--text2)">No skills yet. Add your first skill!</td></tr>';
      document.getElementById('skillCount').textContent = '';
      return;
    }
    document.getElementById('skillCount').textContent = `(${j.data.length})`;
    body.innerHTML = j.data.map(s => `<tr>
      <td><span class="badge" style="background:rgba(44,122,123,0.15);color:var(--teal);border:1px solid rgba(44,122,123,0.3)">${s.category}</span></td>
      <td style="font-weight:500">${s.skill_name}</td>
      <td style="min-width:160px">
        <div class="d-flex align-items-center gap-2">
          <div class="skill-bar flex-grow-1"><div class="skill-fill" style="width:${s.proficiency_level}%"></div></div>
          <span style="color:var(--text2);font-size:0.8rem;min-width:36px">${s.proficiency_level}%</span>
        </div>
      </td>
      <td>
        <button class="btn btn-ghost btn-icon btn-sm me-1" onclick='openSkillModal(${JSON.stringify(s)})' title="Edit"><i class="fas fa-edit"></i></button>
        <button class="btn btn-danger-soft btn-icon btn-sm" onclick="deleteSkill(${s.id})" title="Delete"><i class="fas fa-trash"></i></button>
      </td></tr>`).join('');
  } catch(e) { console.error(e); }
}
loadSkills();
</script>
@endpush
@endsection