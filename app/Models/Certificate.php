<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'issuer',
        'description',
        'badge_label',
        'image_path',
        'pdf_path',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // 🔥 Get full URL for image
    public function getImageUrlAttribute()
    {
        if (!$this->image_path) {
            return null;
        }
        // Check if path already has 'certificates/' prefix
        if (str_starts_with($this->image_path, 'certificates/')) {
            return asset($this->image_path);
        }
        return asset('certificates/' . $this->image_path);
    }

    // 🔥 Get full URL for PDF
    public function getPdfUrlAttribute()
    {
        if (!$this->pdf_path) {
            return null;
        }
        if (str_starts_with($this->pdf_path, 'certificates/')) {
            return asset($this->pdf_path);
        }
        return asset('certificates/' . $this->pdf_path);
    }

    // 🔥 Get the actual file path
    public function getImagePathAttribute($value)
    {
        return $value;
    }

    public function getPdfPathAttribute($value)
    {
        return $value;
    }
}