<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',  // ← ADD THIS
        'bio',
        'tagline',
        'phone',
        'address',
        'github_url',
        'whatsapp',
        'availability_status',
        'availability_note',
        'avatar_url',
    ];

    protected $casts = [
        'availability_status' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}