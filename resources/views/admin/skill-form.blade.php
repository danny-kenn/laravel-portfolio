@extends('layouts.admin')

@section('title', isset($skill) ? 'Edit Skill' : 'Add Skill')
@section('page-title', isset($skill) ? 'Edit Skill' : 'Add Skill')

@section('content')
<div class="card" style="background:var(--bg-surface);border:1px solid var(--border);max-width:600px;">
    <div class="card-body">
        <form method="POST" action="{{ isset($skill) ? route('admin.skills.update', $skill->id) : route('admin.skills.store') }}">
            @csrf
            @if(isset($skill)) @method('PUT') @endif
            
            <div class="mb-3">
                <label class="form-label text-muted">Category</label>
                <input type="text" name="category" class="form-control" value="{{ $skill->category ?? '' }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted">Skill Name</label>
                <input type="text" name="skill_name" class="form-control" value="{{ $skill->skill_name ?? '' }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted">Proficiency Level (1-100)</label>
                <input type="number" name="proficiency_level" class="form-control" value="{{ $skill->proficiency_level ?? 80 }}" min="1" max="100">
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
            <a href="{{ route('admin.skills.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection