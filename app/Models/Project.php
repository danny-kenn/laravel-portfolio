<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'github_url',
        'live_url',
        'image_url',
        'is_featured',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'project_tags');
    }

    // Auto-generate slug
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($project) {
            $project->slug = Str::slug($project->title . '-' . Str::random(6));
        });
    }

    public function getTagListAttribute()
    {
        return $this->tags->pluck('name')->implode(', ');
    }
}