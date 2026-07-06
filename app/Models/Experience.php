<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Experience extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_title',
        'company',
        'start_date',
        'end_date',
        'is_current',
        'description',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_current' => 'boolean',
        'is_active' => 'boolean',
    ];
}