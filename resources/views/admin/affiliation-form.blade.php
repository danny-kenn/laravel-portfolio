@extends('layouts.admin')

@section('title', isset($affiliation) ? 'Edit Affiliation' : 'Add Affiliation')
@section('page-title', isset($affiliation) ? 'Edit Affiliation' : 'Add Affiliation')

@section('content')
<div class="card" style="background:var(--bg-surface);border:1px solid var(--border);max-width:700px;">
    <div class="card-body">
        <form method="POST" action="{{ isset($affiliation) ? route('admin.affiliations.update', $affiliation->id) : route('admin.affiliations.store') }}">
            @csrf
            @if(isset($affiliation)) @method('PUT') @endif
            
            <div class="mb-3">
                <label class="form-label text-muted">Organization</label>
                <input type="text" name="organization" class="form-control" value="{{ $affiliation->organization ?? '' }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted">Description</label>
                <textarea name="description" class="form-control" rows="3">{{ $affiliation->description ?? '' }}</textarea>
            </div>
            <div class="row">
                <div class="col-6">
                    <div class="mb-3">
                        <label class="form-label text-muted">Status</label>
                        <input type="text" name="status" class="form-control" value="{{ $affiliation->status ?? 'Active' }}">
                    </div>
                </div>
                <div class="col-6">
                    <div class="mb-3">
                        <label class="form-label text-muted">Member Since</label>
                        <input type="text" name="member_since" class="form-control" value="{{ $affiliation->member_since ?? '' }}" placeholder="2024">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <div class="mb-3">
                        <label class="form-label text-muted">Icon Class</label>
                        <input type="text" name="icon_class" class="form-control" value="{{ $affiliation->icon_class ?? 'fas fa-users' }}" placeholder="fas fa-users">
                    </div>
                </div>
                <div class="col-6">
                    <div class="mb-3">
                        <label class="form-label text-muted">Badge Text</label>
                        <input type="text" name="badge_text" class="form-control" value="{{ $affiliation->badge_text ?? '' }}" placeholder="QSK Member">
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted">Benefits (one per line)</label>
                <textarea name="benefits" class="form-control" rows="4" placeholder="Networking opportunities&#10;Professional development&#10;Industry events">@if(isset($benefits)){{ $benefits }}@endif</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted">Sort Order</label>
                <input type="number" name="sort_order" class="form-control" value="{{ $affiliation->sort_order ?? 0 }}">
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
            <a href="{{ route('admin.affiliations.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection