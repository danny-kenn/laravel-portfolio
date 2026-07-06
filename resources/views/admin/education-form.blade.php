@extends('layouts.admin')

@section('title', isset($education) ? 'Edit Education' : 'Add Education')
@section('page-title', isset($education) ? 'Edit Education' : 'Add Education')

@section('content')
<div class="card" style="background:var(--bg-surface);border:1px solid var(--border);max-width:600px;">
    <div class="card-body">
        <form method="POST" action="{{ isset($education) ? route('admin.education.update', $education->id) : route('admin.education.store') }}">
            @csrf
            @if(isset($education)) @method('PUT') @endif
            
            <div class="mb-3">
                <label class="form-label text-muted">Institution</label>
                <input type="text" name="institution" class="form-control" value="{{ $education->institution ?? '' }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted">Degree</label>
                <input type="text" name="degree" class="form-control" value="{{ $education->degree ?? '' }}" required>
            </div>
            <div class="row">
                <div class="col-6">
                    <div class="mb-3">
                        <label class="form-label text-muted">Start Year</label>
                        <input type="text" name="start_year" class="form-control" value="{{ $education->start_year ?? '' }}" required>
                    </div>
                </div>
                <div class="col-6">
                    <div class="mb-3">
                        <label class="form-label text-muted">End Year</label>
                        <input type="text" name="end_year" class="form-control" value="{{ $education->end_year ?? '' }}">
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted">Description</label>
                <textarea name="description" class="form-control" rows="3">{{ $education->description ?? '' }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
            <a href="{{ route('admin.education.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection