@extends('layouts.admin')

@section('title', isset($user) ? 'Edit User' : 'Create New User')
@section('page-title', isset($user) ? 'Edit User' : 'Create New User')

@section('content')
<div class="card" style="background:var(--bg-surface);border:1px solid var(--border);max-width:600px;">
    <div class="card-body">
        @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if(session('info'))
        <div class="alert alert-info">{{ session('info') }}</div>
        @endif

        <form method="POST" action="{{ isset($user) ? route('admin.users.update', $user->id) : route('admin.users.store') }}">
            @csrf
            @if(isset($user)) @method('PUT') @endif

            <!-- Show info for SuperAdmin editing -->
            @if(isset($user) && $user->isSuperAdmin())
            <div class="alert alert-warning">
                <i class="fas fa-shield-alt me-2"></i>
                <strong>SuperAdmin Account</strong> - This is the system owner. Cannot be modified here.
            </div>
            @endif

            <div class="mb-3">
                <label class="form-label text-muted">Full Name <span class="text-danger">*</span></label>
                <input type="text" name="full_name" class="form-control" value="{{ $user->full_name ?? '' }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label text-muted">Username <span class="text-danger">*</span></label>
                <input type="text" name="username" class="form-control" value="{{ $user->username ?? '' }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label text-muted">Email <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control" value="{{ $user->email ?? '' }}" required>
            </div>

            <div class="row">
                <div class="col-6">
                    <div class="mb-3">
                        <label class="form-label text-muted">Role <span class="text-danger">*</span></label>
                        @if(isset($user) && $user->isSuperAdmin())
                            <!-- SuperAdmin - show as read-only -->
                            <input type="text" class="form-control" value="Super Administrator" disabled>
                            <input type="hidden" name="role" value="super_admin">
                        @else
                            <select name="role" class="form-control" required>
                                <option value="admin" @if(isset($user) && $user->role === 'admin') selected @endif>Admin</option>
                                <option value="editor" @if(isset($user) && $user->role === 'editor') selected @endif>Editor</option>
                                <option value="author" @if(isset($user) && $user->role === 'author') selected @endif>Author</option>
                                <option value="viewer" @if(isset($user) && $user->role === 'viewer') selected @endif>Viewer</option>
                            </select>
                            <small class="text-muted">SuperAdmin role is reserved for the system owner.</small>
                        @endif
                    </div>
                </div>
                <div class="col-6">
                    <div class="mb-3">
                        <label class="form-label text-muted">Status</label>
                        <select name="is_active" class="form-control">
                            <option value="1" @if(isset($user) && $user->is_active) selected @endif>Active</option>
                            <option value="0" @if(isset($user) && !$user->is_active) selected @endif>Inactive</option>
                        </select>
                    </div>
                </div>
            </div>

            @if(isset($user) && $user->isSuperAdmin())
            <!-- SuperAdmin - no password fields -->
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-1"></i>
                Password changes for SuperAdmin should be done from the <a href="{{ route('admin.profile') }}" style="color:#d4a017;">Profile section</a>.
            </div>
            @elseif(isset($user))
            <!-- For existing users - show password reset info -->
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-1"></i>
                <strong>Password Management:</strong> Users can change their own passwords from their 
                <a href="{{ route('admin.profile') }}" style="color:#d4a017;">Profile section</a>.
                To reset a user's password, use the <strong>"Reset Password"</strong> button on the users list.
            </div>
            @else
            <!-- For new users - password auto-generated -->
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-1"></i>
                <strong>Auto-generated Password:</strong> A random password will be generated and sent to the user's email.
                The user can change it after first login from their Profile section.
            </div>
            @endif

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">{{ isset($user) ? 'Update' : 'Create' }} User</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection