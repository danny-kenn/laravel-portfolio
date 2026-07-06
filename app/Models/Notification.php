<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    public $timestamps = false;
    
    protected $fillable = [
        'user_id',        // ← ADD THIS
        'title',
        'message',
        'type',
        'module',
        'action',
        'related_id',
        'related_type',
        'is_read',
        'is_emailed',
        'created_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'is_emailed' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    public function scopeModule($query, $module)
    {
        return $query->where('module', $module);
    }
}