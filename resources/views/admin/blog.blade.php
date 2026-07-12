@extends('layouts.admin')
@section('title','Blog')
@section('page-title','Blog Posts')
@section('content')

<div class="c-card">
  <div class="c-card-header">
    <h6><i class="fas fa-newspaper me-2" style="color:var(--gold)"></i>Blog Posts</h6>
    <button class="btn btn-teal btn-sm" onclick="openBlogModal()"><i class="fas fa-plus me-1"></i>New Post</button>
  </div>
  <div class="c-card-body p-0">
    <div class="table-responsive">
      <table class="data-table">
        <thead>
          <tr>
            <th>Title</th>
            <th>Author</th>
            <th>Status</th>
            <th>Views</th>
            <th>Date</th>
            <th style="width:140px">Actions</th>
          </tr>
        </thead>
        <tbody id="blogBody"><tr><td colspan="6" style="text-align:center;padding:2rem;color:var(--text2)"><i class="fas fa-spinner fa-spin me-2"></i>Loading…</td></tr></tbody>
      </table>
    </div>
  </div>
</div>

<!-- BLOG MODAL -->
<div class="modal-overlay" id="blogModal" data-backdrop="static" data-keyboard="false">
  <div class="modal-box wide" onclick="event.stopPropagation();">
    <div class="modal-hd">
      <h6 id="blogModalTitle">New Blog Post</h6>
      <button class="modal-close" onclick="closeModal('blogModal')"><i class="fas fa-times"></i></button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="blogId">
      <div class="mb-3"><label class="form-label">Title <span style="color:#ef4444">*</span></label><input type="text" id="bTitle" class="form-control" required></div>
      <div class="mb-3"><label class="form-label">Excerpt <span style="color:var(--text2);font-size:0.78rem">(optional short summary)</span></label><textarea id="bExcerpt" class="form-control" rows="2"></textarea></div>
      <div class="mb-3"><label class="form-label">Body <span style="color:#ef4444">*</span></label><textarea id="bBody" class="form-control" rows="8" required></textarea></div>
      <div class="mb-3"><label class="form-label">Featured Image URL</label><input type="url" id="bImg" class="form-control" placeholder="https://…"></div>

      <!-- 🔥 Status - Role-based display -->
      <div class="row g-2 mb-3">
        <div class="col-md-6">
          <label class="form-label">Status</label>
          @php
            $user = auth()->user();
            $isAttache = $user->isAttache();
            $isAdmin = $user->isAdmin(); // covers both admin and super_admin
          @endphp

          @if($isAttache)
            <!-- 🔥 Attaché/Intern can ONLY save drafts -->
            <div class="status-toggle-group" style="display:flex;gap:0.5rem;flex-wrap:wrap;">
              <button type="button" class="status-btn status-draft active" data-value="draft" onclick="setStatus('draft')">
                <i class="fas fa-pencil-alt me-1"></i>Draft
              </button>
            </div>
            <small style="color:var(--text3);display:block;margin-top:6px;">
              <i class="fas fa-info-circle me-1"></i> You can only save drafts. Ask an Admin or SuperAdmin to publish.
            </small>
            <input type="hidden" id="bStatus" value="draft">

          @elseif($isAdmin)
            <!-- 🔥 Admins and SuperAdmins can choose Draft, Publish, or Archive -->
            <div class="status-toggle-group" style="display:flex;gap:0.5rem;flex-wrap:wrap;">
              <button type="button" class="status-btn status-draft active" data-value="draft" onclick="setStatus('draft')">
                <i class="fas fa-pencil-alt me-1"></i>Draft
              </button>
              <button type="button" class="status-btn status-published" data-value="published" onclick="setStatus('published')">
                <i class="fas fa-check-circle me-1"></i>Publish
              </button>
              <button type="button" class="status-btn status-archived" data-value="archived" onclick="setStatus('archived')">
                <i class="fas fa-archive me-1"></i>Archive
              </button>
            </div>
            <input type="hidden" id="bStatus" value="draft">
          @endif
        </div>
        <div class="col-md-6">
          <label class="form-label">Categories <span style="color:var(--text2);font-size:0.78rem">(comma-separated)</span></label>
          <input type="text" id="bCats" class="form-control" placeholder="Tech, Engineering…">
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Tags <span style="color:var(--text2);font-size:0.78rem">(comma-separated, with or without #)</span></label>
        <input type="text" id="bTags" class="form-control" placeholder="#SoftwareEngineering, #KonzaTechnopolis, #PHP">
      </div>
    </div>
    <div class="modal-ft">
      <button class="btn btn-ghost" onclick="closeModal('blogModal')">Cancel</button>
      <button class="btn btn-teal" id="blogSaveBtn" onclick="saveBlog()"><i class="fas fa-save me-1"></i>Save Post</button>
    </div>
  </div>
</div>

<!-- DELETE CONFIRMATION MODAL -->
<div class="modal-overlay" id="deleteModal" data-backdrop="static" data-keyboard="false">
  <div class="modal-box" style="max-width:440px;" onclick="event.stopPropagation();">
    <div class="modal-hd">
      <h6 style="color:#ef4444;"><i class="fas fa-exclamation-triangle me-2"></i>Confirm Delete</h6>
      <button class="modal-close" onclick="closeModal('deleteModal')"><i class="fas fa-times"></i></button>
    </div>
    <div class="modal-body text-center" style="padding:2rem 1.5rem;">
      <i class="fas fa-trash fa-3x" style="color:#ef4444;opacity:0.5;margin-bottom:1rem;"></i>
      <p style="color:var(--text);font-size:1.05rem;margin-bottom:0.5rem;">Are you sure you want to delete this post?</p>
      <p style="color:var(--text2);font-size:0.9rem;">This action cannot be undone.</p>
      <input type="hidden" id="deleteId">
    </div>
    <div class="modal-ft" style="justify-content:center;gap:0.75rem;">
      <button class="btn btn-ghost" onclick="closeModal('deleteModal')">Cancel</button>
      <button class="btn btn-danger-soft" id="confirmDeleteBtn" onclick="confirmDelete()">
        <i class="fas fa-trash me-1"></i>Delete
      </button>
    </div>
  </div>
</div>

<style>
/* 🔥 Status Button Styles */
.status-btn {
  padding: 0.4rem 1rem;
  border-radius: 8px;
  font-size: 0.8rem;
  font-weight: 500;
  cursor: pointer;
  border: 2px solid transparent;
  transition: all 0.2s ease;
  display: inline-flex;
  align-items: center;
  gap: 0.3rem;
  background: var(--bg3);
  color: var(--text2);
  border-color: var(--border2);
}

.status-btn:hover {
  transform: translateY(-1px);
  box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

.status-btn.active {
  border-color: var(--teal);
  box-shadow: 0 0 0 3px rgba(44,122,123,0.2);
}

.status-btn.status-draft.active {
  background: rgba(234,179,8,0.2);
  color: #eab308;
  border-color: #eab308;
}

.status-btn.status-published.active {
  background: rgba(34,197,94,0.2);
  color: #22c55e;
  border-color: #22c55e;
}

.status-btn.status-archived.active {
  background: rgba(107,114,128,0.2);
  color: #6b7280;
  border-color: #6b7280;
}

.status-btn:not(.active) {
  opacity: 0.6;
}

.status-btn:not(.active):hover {
  opacity: 0.9;
}

/* 🔥 Modal - prevent click outside closing */
.modal-overlay {
  cursor: default !important;
}

.modal-overlay .modal-box {
  cursor: default;
}

.modal-overlay .modal-box .modal-close {
  cursor: pointer;
}
</style>

@push('scripts')
<script>
let _deleteId = null;

function setStatus(value) {
  document.getElementById('bStatus').value = value;
  document.querySelectorAll('.status-btn').forEach(btn => {
    btn.classList.remove('active');
  });
  const activeBtn = document.querySelector(`.status-btn[data-value="${value}"]`);
  if (activeBtn) {
    activeBtn.classList.add('active');
  }
}

function openBlogModal(d = null) {
  document.getElementById('blogId').value     = d?.id ?? '';
  document.getElementById('bTitle').value     = d?.title ?? '';
  document.getElementById('bExcerpt').value   = d?.excerpt ?? '';
  document.getElementById('bBody').value      = d?.body ?? '';
  document.getElementById('bImg').value       = d?.featured_image ?? '';
  document.getElementById('bCats').value      = d?.categories_string ?? '';
  document.getElementById('bTags').value      = d?.tags_string ?? '';
  document.getElementById('blogModalTitle').textContent = d ? 'Edit Post' : 'New Blog Post';

  const status = d?.status || 'draft';
  document.getElementById('bStatus').value = status;

  // Highlight active status
  document.querySelectorAll('.status-btn').forEach(btn => {
    btn.classList.remove('active');
  });
  const activeBtn = document.querySelector(`.status-btn[data-value="${status}"]`);
  if (activeBtn) {
    activeBtn.classList.add('active');
  }

  openModal('blogModal');
}

async function saveBlog() {
  const id  = document.getElementById('blogId').value;
  const btn = document.getElementById('blogSaveBtn');
  btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Saving…';
  const fd  = new FormData();
  if (id) fd.set('_method','PUT');
  fd.set('_token', csrf());
  fd.set('title',   document.getElementById('bTitle').value);
  fd.set('excerpt', document.getElementById('bExcerpt').value);
  fd.set('body',    document.getElementById('bBody').value);
  fd.set('featured_image', document.getElementById('bImg').value);
  fd.set('status',  document.getElementById('bStatus').value);
  fd.set('categories', document.getElementById('bCats').value);
  fd.set('tags', document.getElementById('bTags').value);
  try {
    const url = id ? `/admin/blog/${id}` : '{{ route("admin.blog.store") }}';
    const r = await fetch(url, { method:'POST', body:fd });
    const j = await r.json();
    if (!j.success) throw new Error(j.message);
    showToast(j.message||'Saved!');
    closeModal('blogModal');
    loadBlog();
  } catch(e) {
    showToast(e.message||'Error','error');
  }
  btn.disabled = false;
  btn.innerHTML = '<i class="fas fa-save me-1"></i>Save Post';
}

function openDeleteModal(id) {
  _deleteId = id;
  document.getElementById('deleteId').value = id;
  openModal('deleteModal');
}

async function confirmDelete() {
  const id = document.getElementById('deleteId').value;
  if (!id) return;

  const btn = document.getElementById('confirmDeleteBtn');
  btn.disabled = true;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Deleting…';

  const fd = new FormData();
  fd.set('_method','DELETE');
  fd.set('_token', csrf());

  try {
    const r = await fetch(`/admin/blog/${id}`, { method:'POST', body:fd });
    const j = await r.json();
    if (j.success) {
      showToast('Deleted successfully', 'success');
      closeModal('deleteModal');
      loadBlog();
    } else {
      showToast(j.message||'Error', 'error');
    }
  } catch(e) {
    showToast('Something went wrong', 'error');
  }

  btn.disabled = false;
  btn.innerHTML = '<i class="fas fa-trash me-1"></i>Delete';
  _deleteId = null;
}

async function loadBlog() {
  try {
    const r = await fetch('/admin/blog?json=1');
    const j = await r.json();
    const body = document.getElementById('blogBody');
    if (!j.data?.length) {
      body.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:2rem;color:var(--text2)">No posts yet.</td></tr>';
      return;
    }

    let html = '';
    for (const p of j.data) {
      const isOwn = {{ auth()->id() }} === p.author_id;
      // 🔥 Attaché can only act on their own posts. Admins and SuperAdmins can act on everything.
      const showActions = ({{ auth()->user()->isAttache() ? 'true' : 'false' }} && isOwn) ||
                          ({{ auth()->user()->isAdmin() ? 'true' : 'false' }});

      const dataAttrs = [
        `data-id="${p.id}"`,
        `data-title="${escapeAttr(p.title || '')}"`,
        `data-excerpt="${escapeAttr(p.excerpt || '')}"`,
        `data-body="${escapeAttr(p.body || '')}"`,
        `data-featured_image="${escapeAttr(p.featured_image || '')}"`,
        `data-status="${p.status || 'draft'}"`,
        `data-categories="${escapeAttr((p.categories || []).map(c => c.name).join(', '))}"`,
        `data-tags="${escapeAttr((p.tags || []).map(t => t.name).join(', '))}"`,
        `data-author="${escapeAttr(p.author_name || 'Unknown')}"`
      ].join(' ');

      const statusBadge = p.status === 'published' ? 'badge-status-pub' : p.status === 'draft' ? 'badge-status-draft' : 'badge-status-arch';

      html += `<tr ${dataAttrs}>
        <td style="font-weight:500">${escapeHtml(p.title)}</td>
        <td style="color:var(--text2);font-size:0.85rem;">
          ${escapeHtml(p.author_name || 'Unknown')}
          ${isOwn ? ' <span class="badge" style="background:rgba(44,122,123,0.12);color:var(--teal);font-size:0.65rem;">You</span>' : ''}
        </td>
        <td><span class="badge ${statusBadge}">${p.status}</span></td>
        <td style="color:var(--text2)">${p.view_count||0}</td>
        <td style="color:var(--text2);font-size:0.82rem">${p.created_at_human||'–'}</td>
        <td>
          ${showActions ? `
            <button class="btn btn-ghost btn-icon btn-sm me-1" onclick="editBlog(this.closest('tr'))" title="Edit"><i class="fas fa-edit"></i></button>
            <button class="btn btn-danger-soft btn-icon btn-sm" onclick="openDeleteModal(${p.id})" title="Delete"><i class="fas fa-trash"></i></button>
          ` : '<span style="color:var(--text3);font-size:0.75rem;">Read-only</span>'}
        </td>
      </tr>`;
    }
    body.innerHTML = html;
  } catch(e) {
    console.error('Error loading blog:', e);
    document.getElementById('blogBody').innerHTML = '<tr><td colspan="6" style="text-align:center;padding:2rem;color:var(--text2)">Error loading posts. Please refresh.</td></tr>';
  }
}

function editBlog(row) {
  if (!row) return;
  const data = {
    id: parseInt(row.dataset.id || 0),
    title: row.dataset.title || '',
    excerpt: row.dataset.excerpt || '',
    body: row.dataset.body || '',
    featured_image: row.dataset.featured_image || '',
    status: row.dataset.status || 'draft',
    categories_string: row.dataset.categories || '',
    tags_string: row.dataset.tags || ''
  };
  openBlogModal(data);
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

function escapeHtml(text) {
  if (!text) return '';
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}

function escapeAttr(text) {
  if (!text) return '';
  return String(text)
    .replace(/&/g, '&amp;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/\n/g, '&#10;')
    .replace(/\r/g, '&#13;');
}

// Close modal on escape key only
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    document.querySelectorAll('.modal-overlay.show').forEach(m => {
      m.classList.remove('show');
      document.body.style.overflow = '';
    });
  }
});

loadBlog();
</script>
@endpush
@endsection