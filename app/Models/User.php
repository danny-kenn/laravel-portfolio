<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'email',
        'password',
        'full_name',
        'role',
        'is_active',
        'last_login',
        'email_notifications',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_login' => 'datetime',
        'email_notifications' => 'array',
    ];

    // ============ ROLE CHECKING METHODS ============

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['super_admin', 'admin']);
    }

    public function isAttache(): bool
    {
        return $this->role === 'attache';
    }

    public function canManageUsers(): bool
    {
        return in_array($this->role, ['super_admin', 'admin']);
    }

    public function canPublish(): bool
    {
        return in_array($this->role, ['super_admin', 'admin']);
    }

    public function canManageContent(): bool
    {
        return in_array($this->role, ['super_admin', 'admin']);
    }

    public function canCreateDrafts(): bool
    {
        return in_array($this->role, ['super_admin', 'admin', 'attache']);
    }

    public function getRoleDisplayName(): string
    {
        return match($this->role) {
            'super_admin' => 'Super Administrator',
            'admin'       => 'Administrator',
            'attache'     => 'Attaché / Intern',
            default       => 'User'
        };
    }

    // ============ RELATIONSHIPS ============

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function education()
    {
        return $this->hasMany(Education::class);
    }

    public function skills()
    {
        return $this->hasMany(Skill::class);
    }

    public function experiences()
    {
        return $this->hasMany(Experience::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    public function affiliations()
    {
        return $this->hasMany(Affiliation::class);
    }

    public function blogPosts()
    {
        return $this->hasMany(BlogPost::class);
    }

    public function contactMessages()
    {
        return $this->hasMany(ContactMessage::class);
    }

    // ============ SCOPES ============

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRole($query, $role)
    {
        return $query->where('role', $role);
    }
}