@extends('layouts.admin')

@section('title', isset($certificate) ? 'Edit Certificate' : 'Add Certificate')
@section('page-title', isset($certificate) ? 'Edit Certificate' : 'Add Certificate')

@section('content')
<div class="card" style="background:var(--bg-surface);border:1px solid var(--border);max-width:800px;">
    <div class="card-body">
        <form method="POST" action="{{ isset($certificate) ? route('admin.certificates.update', $certificate->id) : route('admin.certificates.store') }}" enctype="multipart/form-data">
            @csrf
            @if(isset($certificate)) @method('PUT') @endif
            
            <div class="mb-3">
                <label class="form-label text-muted">Title</label>
                <input type="text" name="title" class="form-control" value="{{ $certificate->title ?? '' }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted">Issuer</label>
                <input type="text" name="issuer" class="form-control" value="{{ $certificate->issuer ?? '' }}">
            </div>
            <div class="mb-3">
                <label class="form-label text-muted">Description</label>
                <textarea name="description" class="form-control" rows="3">{{ $certificate->description ?? '' }}</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted">Badge Label</label>
                <input type="text" name="badge_label" class="form-control" value="{{ $certificate->badge_label ?? '' }}">
            </div>
            <div class="row">
                <div class="col-6">
                    <div class="mb-3">
                        <label class="form-label text-muted">Image (JPG, PNG, WEBP)</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        @if(isset($certificate) && $certificate->image_path)
                            <small class="text-muted">Current: {{ $certificate->image_path }}</small>
                        @endif
                    </div>
                </div>
                <div class="col-6">
                    <div class="mb-3">
                        <label class="form-label text-muted">PDF</label>
                        <input type="file" name="pdf" class="form-control" accept=".pdf">
                        @if(isset($certificate) && $certificate->pdf_path)
                            <small class="text-muted">Current: {{ $certificate->pdf_path }}</small>
                        @endif
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
            <a href="{{ route('admin.certificates.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection