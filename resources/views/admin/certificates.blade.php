@extends('layouts.admin')
@section('title','Certificates')
@section('page-title','Certificates & Credentials')
@section('content')

<div class="c-card">
  <div class="c-card-header">
    <h6><i class="fas fa-certificate me-2" style="color:var(--gold)"></i>Certificates</h6>
    <button class="btn btn-teal btn-sm" onclick="openCertModal()"><i class="fas fa-plus me-1"></i>Add Certificate</button>
  </div>
  <div class="c-card-body p-0">
    <div class="table-responsive">
      <table class="data-table">
        <thead><tr><th style="width:70px">Image</th><th>Title</th><th>Issuer</th><th>Badge</th><th style="width:140px">Actions</th></tr></thead>
        <tbody id="certBody">
          <tr><td colspan="5" style="text-align:center;padding:2rem;color:var(--text2)"><i class="fas fa-spinner fa-spin me-2"></i>Loading…</td></tr></tbody>
      </table>
    </div>
  </div>
</div>

<!-- CERT EDIT/ADD MODAL -->
<div class="modal-overlay" id="certModal" onclick="return false;">
  <div class="modal-box wide" onclick="event.stopPropagation();">
    <div class="modal-hd">
      <h6 id="certModalTitle">Add Certificate</h6>
      <button class="modal-close" onclick="closeModal('certModal')"><i class="fas fa-times"></i></button>
    </div>
    <div class="modal-body">
      <form id="certForm" enctype="multipart/form-data">
        <input type="hidden" id="certId">
        <div class="row g-3 mb-3">
          <div class="col-md-6">
            <label class="form-label">Title <span style="color:#ef4444">*</span></label>
            <input type="text" id="cTitle" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Issuer</label>
            <input type="text" id="cIssuer" class="form-control" placeholder="IRLA, Alison, QSK…">
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label">Description</label>
          <textarea id="cDesc" class="form-control" rows="2"></textarea>
        </div>
        <div class="row g-3 mb-3">
          <div class="col-md-6">
            <label class="form-label">Badge Label</label>
            <input type="text" id="cBadge" class="form-control" placeholder="ISO, QSK, Cybersecurity…">
          </div>
          <div class="col-md-6">
            <label class="form-label">Sort Order</label>
            <input type="number" id="cOrder" class="form-control" value="0">
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label">Certificate Image (JPG / PNG / WEBP)</label>
          <input type="file" id="cImage" class="form-control" accept="image/jpeg,image/png,image/webp">
          <div class="img-preview-wrap" id="imgPreviewWrap">
            <img id="imgPreviewEl" src="" alt="Preview">
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label">Certificate PDF</label>
          <input type="file" id="cPdf" class="form-control" accept="application/pdf">
          <div id="pdfNameWrap" style="display:none;margin-top:0.4rem">
            <small style="color:var(--teal)"><i class="fas fa-file-pdf me-1"></i><span id="pdfNameEl"></span></small>
          </div>
        </div>
      </form>
    </div>
    <div class="modal-ft">
      <button class="btn btn-ghost" onclick="closeModal('certModal')">Cancel</button>
      <button class="btn btn-teal" id="certSaveBtn" onclick="saveCert()"><i class="fas fa-save me-1"></i>Save</button>
    </div>
  </div>
</div>

<!-- CERT PREVIEW MODAL -->
<div class="modal-overlay" id="certPreviewModal" onclick="return false;">
  <div class="modal-box wide" onclick="event.stopPropagation();">
    <div class="modal-hd">
      <h6 id="certPreviewTitle">Certificate Preview</h6>
      <button class="modal-close" onclick="closeModal('certPreviewModal')"><i class="fas fa-times"></i></button>
    </div>
    <div class="modal-body text-center" id="certPreviewBody" style="min-height:200px"></div>
    <div class="modal-ft">
      <a id="certPreviewDownload" href="#" download class="btn btn-teal" style="display:none"><i class="fas fa-download me-1"></i>Download</a>
      <button class="btn btn-ghost" onclick="closeModal('certPreviewModal')">Close</button>
    </div>
  </div>
</div>

@push('scripts')
<script>
// Image preview on file select
document.getElementById('cImage').addEventListener('change', function() {
  if (this.files[0]) {
    document.getElementById('imgPreviewEl').src = URL.createObjectURL(this.files[0]);
    document.getElementById('imgPreviewWrap').style.display = 'block';
  }
});
document.getElementById('cPdf').addEventListener('change', function() {
  if (this.files[0]) {
    document.getElementById('pdfNameEl').textContent = this.files[0].name;
    document.getElementById('pdfNameWrap').style.display = 'block';
  }
});

let _editCertImageUrl = '', _editCertPdfUrl = '';

function openCertModal(data = null) {
  document.getElementById('certId').value   = data?.id ?? '';
  document.getElementById('cTitle').value   = data?.title ?? '';
  document.getElementById('cIssuer').value  = data?.issuer ?? '';
  document.getElementById('cDesc').value    = data?.description ?? '';
  document.getElementById('cBadge').value   = data?.badge_label ?? '';
  document.getElementById('cOrder').value   = data?.sort_order ?? 0;
  document.getElementById('certModalTitle').textContent = data ? 'Edit Certificate' : 'Add Certificate';
  document.getElementById('cImage').value   = '';
  document.getElementById('cPdf').value     = '';
  document.getElementById('pdfNameWrap').style.display = 'none';

  const wrap = document.getElementById('imgPreviewWrap');
  const prev = document.getElementById('imgPreviewEl');
  
  if (data?.image_url) { 
    prev.src = data.image_url; 
    wrap.style.display='block'; 
  } else if (data?.image_path) {
    // 🔥 Use the same logic as the table column
    if (data.image_path.startsWith('certificates/')) {
      prev.src = '/' + data.image_path;
    } else {
      prev.src = '/certificates/' + data.image_path;
    }
    wrap.style.display='block';
  } else {
    wrap.style.display = 'none';
  }

  _editCertImageUrl = data?.image_url ?? '';
  _editCertPdfUrl   = data?.pdf_url   ?? '';
  openModal('certModal');
}

async function saveCert() {
  const id  = document.getElementById('certId').value;
  const btn = document.getElementById('certSaveBtn');
  btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Saving…';

  const fd = new FormData();
  if (id) fd.set('_method','PUT');
  fd.set('_token',      csrf());
  fd.set('title',       document.getElementById('cTitle').value);
  fd.set('issuer',      document.getElementById('cIssuer').value);
  fd.set('description', document.getElementById('cDesc').value);
  fd.set('badge_label', document.getElementById('cBadge').value);
  fd.set('sort_order',  document.getElementById('cOrder').value);
  const img = document.getElementById('cImage').files[0];
  const pdf = document.getElementById('cPdf').files[0];
  if (img) fd.set('image', img);
  if (pdf) fd.set('pdf',   pdf);

  try {
    const url = id ? `/admin/certificates/${id}` : '{{ route("admin.certificates.store") }}';
    const r   = await fetch(url, { method:'POST', body:fd });
    const j   = await r.json();
    if (!j.success) throw new Error(j.message);
    showToast(j.message || 'Saved!', 'success');
    closeModal('certModal');
    loadCerts();
  } catch(e) { 
    showToast(e.message||'Error uploading. Check file size/type.','error'); 
  }
  btn.disabled = false; btn.innerHTML = '<i class="fas fa-save me-1"></i>Save';
}

async function deleteCert(id) {
  if (!confirm('Delete this certificate and its files?')) return;
  const fd = new FormData(); fd.set('_method','DELETE'); fd.set('_token',csrf());
  const r  = await fetch(`/admin/certificates/${id}`, { method:'POST', body:fd });
  const j  = await r.json();
  if (j.success) { showToast('Deleted','success'); loadCerts(); }
  else showToast(j.message||'Error','error');
}

// 🔥 FIXED: Preview function that uses the same logic as the table column
function previewCert(id) {
  const c = window._certs?.find(x => x.id == id);
  if (!c) return;
  document.getElementById('certPreviewTitle').textContent = c.title;
  const body = document.getElementById('certPreviewBody');
  const dlBtn = document.getElementById('certPreviewDownload');
  
  // 🔥 Build image URL - SAME LOGIC as table column
  let imgSrc = c.image_url || '';
  if (!imgSrc && c.image_path) {
    if (c.image_path.startsWith('certificates/')) {
      imgSrc = '/' + c.image_path;
    } else {
      imgSrc = '/certificates/' + c.image_path;
    }
  }
  
  let pdfSrc = c.pdf_url || '';
  if (!pdfSrc && c.pdf_path) {
    if (c.pdf_path.startsWith('certificates/')) {
      pdfSrc = '/' + c.pdf_path;
    } else {
      pdfSrc = '/certificates/' + c.pdf_path;
    }
  }
  
  // 🔥 Check if it's an old missing file (from previous system)
  const isOldMissing = c.image_path && c.image_path.includes('cert_6a211');
  const hasValidImage = imgSrc && !isOldMissing;
  
  if (hasValidImage) {
    body.innerHTML = `<img src="${imgSrc}" style="max-width:100%;max-height:70vh;border-radius:10px;object-fit:contain" alt="${c.title}" onerror="this.style.display='none'; this.parentElement.innerHTML='<div style=\\'padding:3rem;color:var(--text2)\\'>Image file not found</div>'">`;
  } else if (pdfSrc) {
    body.innerHTML = `<iframe src="${pdfSrc}" style="width:100%;height:70vh;border:none;border-radius:8px"></iframe>`;
  } else {
    body.innerHTML = `<div style="padding:3rem;color:var(--text2)"><i class="fas fa-image fa-3x mb-3 d-block"></i>No image or PDF uploaded yet.</div>`;
  }
  
  if (pdfSrc) { 
    dlBtn.href = pdfSrc;   
    dlBtn.style.display = ''; 
  } else if (hasValidImage) { 
    dlBtn.href = imgSrc; 
    dlBtn.style.display = ''; 
  } else {
    dlBtn.style.display = 'none';
  }
  openModal('certPreviewModal');
}

async function loadCerts() {
  try {
    const r = await fetch('/admin/certificates?json=1');
    const j = await r.json();
    window._certs = j.data || [];
    const body = document.getElementById('certBody');
    if (!j.data?.length) {
      body.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:2rem;color:var(--text2)">No certificates yet.</td></tr>';
      return;
    }
    body.innerHTML = j.data.map(c => {
      // 🔥 Build image URL - SAME LOGIC as preview
      let imgSrc = c.image_url || '';
      if (!imgSrc && c.image_path) {
        if (c.image_path.startsWith('certificates/')) {
          imgSrc = '/' + c.image_path;
        } else {
          imgSrc = '/certificates/' + c.image_path;
        }
      }
      
      // 🔥 Check if it's an old missing file
      const isOldMissing = c.image_path && c.image_path.includes('cert_6a211');
      const hasValidImage = imgSrc && !isOldMissing;
      
      return `<tr>
        <td>
          ${hasValidImage
            ? `<img src="${imgSrc}" class="cert-thumb" onclick="previewCert(${c.id})" onerror="this.outerHTML='<div class=cert-no-img onclick=previewCert(${c.id})><i class=fas\\ fa-image></i></div>'">`
            : `<div class="cert-no-img" onclick="previewCert(${c.id})"><i class="fas fa-certificate"></i></div>`}
        </td>
        <td style="font-weight:600">${c.title}</td>
        <td style="color:var(--text2)">${c.issuer||'–'}</td>
        <td><span class="badge" style="background:rgba(44,122,123,0.12);color:var(--teal);border:1px solid rgba(44,122,123,0.25)">${c.badge_label||'–'}</span></td>
        <td>
          <button class="btn btn-ghost btn-icon btn-sm me-1" onclick="previewCert(${c.id})" title="Preview"><i class="fas fa-eye"></i></button>
          <button class="btn btn-ghost btn-icon btn-sm me-1" onclick='openCertModal(${JSON.stringify(c)})' title="Edit"><i class="fas fa-edit"></i></button>
          <button class="btn btn-danger-soft btn-icon btn-sm" onclick="deleteCert(${c.id})" title="Delete"><i class="fas fa-trash"></i></button>
        </td>
      </tr>`;
    }).join('');
  } catch(e) {
    console.error('Error loading certificates:', e);
    document.getElementById('certBody').innerHTML = '<tr><td colspan="5" style="text-align:center;padding:2rem;color:var(--text2)">Error loading certificates.</td></tr>';
  }
}

function openModal(id) {
  const el = document.getElementById(id);
  if (!el) return;
  el.classList.add('show');
  document.body.style.overflow = 'hidden';
}

function closeModal(id) {
  const el = document.getElementById(id);
  if (!el) return;
  el.classList.remove('show');
  document.body.style.overflow = '';
}

// Close modal on Escape key only
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    document.querySelectorAll('.modal-overlay.show').forEach(m => {
      m.classList.remove('show');
      document.body.style.overflow = '';
    });
  }
});

loadCerts();
</script>
@endpush
@endsection