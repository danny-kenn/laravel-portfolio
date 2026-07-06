<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class FileHelper
{
    public static function getFileUrl($path)
    {
        if (!$path) {
            return '';
        }
        
        // Check if file exists
        if (Storage::disk('public')->exists('certificates/' . $path)) {
            return asset('storage/certificates/' . $path);
        }
        
        return '';
    }
    
    public static function getImageData($path)
    {
        if (!$path) {
            return null;
        }
        
        if (Storage::disk('public')->exists('certificates/' . $path)) {
            $contents = Storage::disk('public')->get('certificates/' . $path);
            $mime = Storage::disk('public')->mimeType('certificates/' . $path);
            return 'data:' . $mime . ';base64,' . base64_encode($contents);
        }
        
        return null;
    }
}