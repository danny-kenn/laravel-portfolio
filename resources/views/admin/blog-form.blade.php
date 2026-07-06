@extends('layouts.admin')

@section('title', isset($post) ? 'Edit Blog Post' : 'New Blog Post')
@section('page-title', isset($post) ? 'Edit Blog Post' : 'New Blog Post')

@section('content')
<div class="card" style="background:var(--bg-surface);border:1px solid var(--border);max-width:900px;">
    <div class="card-body">
        <form method="POST" action="{{ isset($post) ? route('admin.blog.update', $post->id) : route('admin.blog.store') }}">
            @csrf
            @if(isset($post)) @method('PUT') @endif
            
            <div class="mb-3">
                <label class="form-label text-muted">Title</label>
                <input type="text" name="title" class="form-control" value="{{ $post->title ?? '' }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted">Body</label>
                <textarea name="body" class="form-control" rows="8" required>{{ $post->body ?? '' }}</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted">Excerpt (optional)</label>
                <textarea name="excerpt" class="form-control" rows="2">{{ $post->excerpt ?? '' }}</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted">Featured Image URL</label>
                <input type="url" name="featured_image" class="form-control" value="{{ $post->featured_image ?? '' }}" placeholder="https://example.com/image.jpg">
            </div>
            <div class="row">
                <div class="col-6">
                    <div class="mb-3">
                        <label class="form-label text-muted">Status</label>
                        <select name="status" class="form-control">
                            <option value="draft" @if(isset($post) && $post->status === 'draft') selected @endif>Draft</option>
                            <option value="published" @if(isset($post) && $post->status === 'published') selected @endif>Published</option>
                            <option value="archived" @if(isset($post) && $post->status === 'archived') selected @endif>Archived</option>
                        </select>
                    </div>
                </div>
                <div class="col-6">
                    <div class="mb-3">
                        <label class="form-label text-muted">Categories</label>
                        <select name="categories[]" class="form-control" multiple>
                            @foreach($categories ?? [] as $category)
                                <option value="{{ $category->id }}" @if(isset($selectedCategories) && in_array($category->id, $selectedCategories)) selected @endif>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Hold Ctrl/Cmd to select multiple</small>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
            <a href="{{ route('admin.blog.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection