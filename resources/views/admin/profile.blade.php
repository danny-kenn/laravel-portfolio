@extends('layouts.admin')
@section('title', 'Profile')
@section('page-title', 'Profile Settings')
@section('content')

<div class="row g-4">
  <div class="col-lg-6">
    <div class="c-card">
      <div class="c-card-header">
        <h6><i class="fas fa-user-circle me-2" style="color:var(--gold)"></i>Personal Information</h6>
      </div>
      <div class="c-card-body">
        @if(session('success'))
        <div class="alert alert-success py-2">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('admin.profile.update') }}">
          @csrf
          @method('PUT')
          <div class="mb-3">
            <label class="form-label">Full Name <span style="color:#ef4444">*</span></label>
            <input type="text" name="full_name" class="form-control" value="{{ $user->full_name }}" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Tagline</label>
            <input type="text" name="tagline" class="form-control" value="{{ $profile->tagline ?? '' }}" placeholder="Software Engineer | Full-Stack Developer">
          </div>
          <div class="mb-3">
            <label class="form-label">Bio</label>
            <textarea name="bio" class="form-control" rows="4" placeholder="Tell the world about yourself…">{{ $profile->bio ?? '' }}</textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" class="form-control" value="{{ $profile->phone ?? '' }}" placeholder="+254 700 000 000">
          </div>
          <div class="mb-3">
            <label class="form-label">GitHub URL</label>
            <input type="url" name="github_url" class="form-control" value="{{ $profile->github_url ?? '' }}" placeholder="https://github.com/yourusername">
          </div>
          <div class="mb-3">
            <label class="form-label">WhatsApp</label>
            <input type="text" name="whatsapp" class="form-control" value="{{ $profile->whatsapp ?? '' }}" placeholder="254700000000">
          </div>
          <div class="mb-3">
            <label class="form-label">Address</label>
            <input type="text" name="address" class="form-control" value="{{ $profile->address ?? '' }}" placeholder="Nairobi, Kenya">
          </div>
          <div class="mb-3 form-check">
            <input type="checkbox" name="availability_status" class="form-check-input" value="1" id="availCheck" @if(($profile->availability_status ?? false)) checked @endif>
            <label class="form-check-label" for="availCheck">Available for opportunities</label>
          </div>
          <div class="mb-3">
            <label class="form-label">Availability Note</label>
            <input type="text" name="availability_note" class="form-control" value="{{ $profile->availability_note ?? '' }}" placeholder="Open to work / Currently employed…">
          </div>
          <button type="submit" class="btn btn-teal"><i class="fas fa-save me-1"></i>Save Profile</button>
        </form>
      </div>
    </div>
  </div>

  <div class="col-lg-6">
    <!-- Password Change -->
    <div class="c-card">
      <div class="c-card-header">
        <h6><i class="fas fa-shield-alt me-2" style="color:var(--gold)"></i>Account Security</h6>
      </div>
      <div class="c-card-body">
        <div class="alert alert-info">
          <i class="fas fa-info-circle me-1"></i>
          Change your password here. Passwords are hashed using bcrypt for security.
        </div>
        <form method="POST" action="{{ route('admin.profile.password.update') }}">
          @csrf
          @method('PUT')
          <div class="mb-3">
            <label class="form-label">Current Password <span style="color:#ef4444">*</span></label>
            <input type="password" name="current_password" class="form-control" placeholder="Enter current password" required>
          </div>
          <div class="mb-3">
            <label class="form-label">New Password <span style="color:#ef4444">*</span></label>
            <input type="password" name="password" class="form-control" placeholder="Minimum 8 characters" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Confirm New Password <span style="color:#ef4444">*</span></label>
            <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm password" required>
          </div>
          <button type="submit" class="btn btn-gold"><i class="fas fa-key me-1"></i>Change Password</button>
        </form>
      </div>
    </div>

    <!-- Notification Preferences -->
    <div class="c-card mt-4">
      <div class="c-card-header">
        <h6><i class="fas fa-bell me-2" style="color:var(--gold)"></i>Notification Preferences</h6>
      </div>
      <div class="c-card-body">
        <div class="alert alert-info">
          <i class="fas fa-info-circle me-1"></i>
          Choose which notifications you want to receive via email. In-app notifications are always enabled.
        </div>

        @php
          $emailPrefs = json_decode($user->email_notifications ?? '{}', true);
        @endphp

        <form method="POST" action="{{ route('admin.profile.update') }}">
          @csrf
          @method('PUT')

          <div class="mb-2">
            <div class="form-check">
              <input type="checkbox" name="email_notifications[blog]" class="form-check-input" value="1" id="notif_blog" 
                     @if(isset($emailPrefs['blog']) && $emailPrefs['blog']) checked @endif>
              <label class="form-check-label" for="notif_blog">
                <i class="fas fa-newspaper me-1" style="color:var(--gold)"></i>
                <strong>Blog</strong> - New drafts, published posts, updates
              </label>
            </div>
          </div>

          <div class="mb-2">
            <div class="form-check">
              <input type="checkbox" name="email_notifications[projects]" class="form-check-input" value="1" id="notif_projects" 
                     @if(isset($emailPrefs['projects']) && $emailPrefs['projects']) checked @endif>
              <label class="form-check-label" for="notif_projects">
                <i class="fas fa-project-diagram me-1" style="color:var(--teal)"></i>
                <strong>Projects</strong> - New projects, updates, deletions
              </label>
            </div>
          </div>

          <div class="mb-2">
            <div class="form-check">
              <input type="checkbox" name="email_notifications[users]" class="form-check-input" value="1" id="notif_users" 
                     @if(isset($emailPrefs['users']) && $emailPrefs['users']) checked @endif>
              <label class="form-check-label" for="notif_users">
                <i class="fas fa-users-cog me-1" style="color:#3b82f6"></i>
                <strong>Users</strong> - New users, account updates
              </label>
            </div>
          </div>

          <div class="mb-2">
            <div class="form-check">
              <input type="checkbox" name="email_notifications[security]" class="form-check-input" value="1" id="notif_security" 
                     @if(isset($emailPrefs['security']) && $emailPrefs['security']) checked @endif>
              <label class="form-check-label" for="notif_security">
                <i class="fas fa-shield-alt me-1" style="color:#ef4444"></i>
                <strong>Security</strong> - Password changes, account security alerts
              </label>
            </div>
          </div>

          <div class="mb-2">
            <div class="form-check">
              <input type="checkbox" name="email_notifications[education]" class="form-check-input" value="1" id="notif_education" 
                     @if(isset($emailPrefs['education']) && $emailPrefs['education']) checked @endif>
              <label class="form-check-label" for="notif_education">
                <i class="fas fa-graduation-cap me-1" style="color:#a855f7"></i>
                <strong>Education</strong> - New/updated education entries
              </label>
            </div>
          </div>

          <div class="mb-2">
            <div class="form-check">
              <input type="checkbox" name="email_notifications[certificates]" class="form-check-input" value="1" id="notif_certificates" 
                     @if(isset($emailPrefs['certificates']) && $emailPrefs['certificates']) checked @endif>
              <label class="form-check-label" for="notif_certificates">
                <i class="fas fa-certificate me-1" style="color:#d4a017"></i>
                <strong>Certificates</strong> - New/updated certificates
              </label>
            </div>
          </div>

          <div class="mt-3">
            <button type="submit" class="btn btn-teal"><i class="fas fa-save me-1"></i>Save Preferences</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Account Details -->
    <div class="c-card mt-4">
      <div class="c-card-header">
        <h6><i class="fas fa-info-circle me-2" style="color:var(--gold)"></i>Account Details</h6>
      </div>
      <div class="c-card-body">
        <div style="font-size:0.875rem;color:var(--text2)">
          <div style="display:flex;justify-content:space-between;padding:0.3rem 0;border-bottom:1px solid var(--border)">
            <span>Username</span>
            <span style="color:var(--text);font-weight:500">{{ $user->username }}</span>
          </div>
          <div style="display:flex;justify-content:space-between;padding:0.3rem 0;border-bottom:1px solid var(--border)">
            <span>Email</span>
            <span style="color:var(--text);font-weight:500">{{ $user->email }}</span>
          </div>
          <div style="display:flex;justify-content:space-between;padding:0.3rem 0;border-bottom:1px solid var(--border)">
            <span>Role</span>
            <span style="color:var(--text);font-weight:500">{{ $user->getRoleDisplayName() }}</span>
          </div>
          <div style="display:flex;justify-content:space-between;padding:0.3rem 0">
            <span>Member Since</span>
            <span style="color:var(--text);font-weight:500">{{ $user->created_at->format('M d, Y') }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection