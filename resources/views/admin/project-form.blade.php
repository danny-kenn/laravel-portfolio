@extends('layouts.admin')

@section('title', isset($project) ? 'Edit Project' : 'Add Project')
@section('page-title', isset($project) ? 'Edit Project' : 'Add Project')

@section('content')
<div class="card" style="background:var(--bg-surface);border:1px solid var(--border);max-width:800px;">
    <div class="card-body">
        <form method="POST" action="{{ isset($project) ? route('admin.projects.update', $project->id) : route('admin.projects.store') }}">
            @csrf
            @if(isset($project)) @method('PUT') @endif
            
            <div class="mb-3">
                <label class="form-label text-muted">Title</label>
                <input type="text" name="title" class="form-control" value="{{ $project->title ?? '' }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted">Description</label>
                <textarea name="description" class="form-control" rows="4">{{ $project->description ?? '' }}</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted">GitHub URL</label>
                <input type="url" name="github_url" class="form-control" value="{{ $project->github_url ?? '' }}">
            </div>
            <div class="mb-3">
                <label class="form-label text-muted">Live URL</label>
                <input type="url" name="live_url" class="form-control" value="{{ $project->live_url ?? '' }}">
            </div>
            <div class="mb-3">
                <label class="form-label text-muted">Tags (comma separated)</label>
                <input type="text" name="tags" class="form-control" value="{{ isset($project) ? $project->tags->pluck('name')->implode(', ') : '' }}">
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" name="is_featured" class="form-check-input" value="1" @if(isset($project) && $project->is_featured) checked @endif>
                <label class="form-check-label text-muted">Featured Project</label>
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
            <a href="{{ route('admin.projects.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection