@extends('layouts.admin')

@section('title', isset($experience) ? 'Edit Experience' : 'Add Experience')
@section('page-title', isset($experience) ? 'Edit Experience' : 'Add Experience')

@section('content')
<div class="card" style="background:var(--bg-surface);border:1px solid var(--border);max-width:700px;">
    <div class="card-body">
        <form method="POST" action="{{ isset($experience) ? route('admin.experience.update', $experience->id) : route('admin.experience.store') }}">
            @csrf
            @if(isset($experience)) @method('PUT') @endif
            
            <div class="mb-3">
                <label class="form-label text-muted">Job Title</label>
                <input type="text" name="job_title" class="form-control" value="{{ $experience->job_title ?? '' }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted">Company</label>
                <input type="text" name="company" class="form-control" value="{{ $experience->company ?? '' }}" required>
            </div>
            <div class="row">
                <div class="col-6">
                    <div class="mb-3">
                        <label class="form-label text-muted">Start Date</label>
                        <input type="text" name="start_date" class="form-control" value="{{ $experience->start_date ?? '' }}" placeholder="January 2024" required>
                    </div>
                </div>
                <div class="col-6">
                    <div class="mb-3">
                        <label class="form-label text-muted">End Date</label>
                        <input type="text" name="end_date" class="form-control" value="{{ $experience->end_date ?? '' }}" placeholder="December 2024">
                    </div>
                </div>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" name="is_current" class="form-check-input" value="1" @if(isset($experience) && $experience->is_current) checked @endif>
                <label class="form-check-label text-muted">Currently working here</label>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted">Description</label>
                <textarea name="description" class="form-control" rows="4">{{ $experience->description ?? '' }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
            <a href="{{ route('admin.experience.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection