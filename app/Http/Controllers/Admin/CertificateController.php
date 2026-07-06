<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Helpers\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CertificateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if ($request->has('json') && $request->json == 1) {
            $certificates = Certificate::orderBy('sort_order')->get();
            
            // 🔥 Add full URLs for images and PDFs
            $certificates->each(function($cert) {
                // Build image URL
                if ($cert->image_path) {
                    if (str_starts_with($cert->image_path, 'certificates/')) {
                        $cert->image_url = asset($cert->image_path);
                    } else {
                        $cert->image_url = asset('certificates/' . $cert->image_path);
                    }
                } else {
                    $cert->image_url = null;
                }
                
                // Build PDF URL
                if ($cert->pdf_path) {
                    if (str_starts_with($cert->pdf_path, 'certificates/')) {
                        $cert->pdf_url = asset($cert->pdf_path);
                    } else {
                        $cert->pdf_url = asset('certificates/' . $cert->pdf_path);
                    }
                } else {
                    $cert->pdf_url = null;
                }
            });
            
            return response()->json(['success' => true, 'data' => $certificates]);
        }
        
        $certificates = Certificate::orderBy('sort_order')->get();
        return view('admin.certificates', compact('certificates'));
    }

    public function create()
    {
        return view('admin.certificate-form');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'issuer' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'badge_label' => 'nullable|string|max:50',
                'sort_order' => 'nullable|integer',
                'image' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:5120',
                'pdf' => 'nullable|file|mimes:pdf|max:10240',
            ]);

            $data = [
                'title' => $validated['title'],
                'issuer' => $validated['issuer'] ?? null,
                'description' => $validated['description'] ?? '',
                'badge_label' => $validated['badge_label'] ?? null,
                'sort_order' => $validated['sort_order'] ?? 0,
                'is_active' => true,
            ];

            // Handle image upload - store in public/certificates
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $image = $request->file('image');
                $filename = 'cert_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('certificates'), $filename);
                $data['image_path'] = 'certificates/' . $filename;
            }

            // Handle PDF upload - store in public/certificates
            if ($request->hasFile('pdf') && $request->file('pdf')->isValid()) {
                $pdf = $request->file('pdf');
                $filename = 'cert_' . uniqid() . '.pdf';
                $pdf->move(public_path('certificates'), $filename);
                $data['pdf_path'] = 'certificates/' . $filename;
            }

            $certificate = Certificate::create($data);

            AuditLogger::create('certificates', "Added certificate: {$certificate->title}", $certificate->toArray());

            return response()->json(['success' => true, 'message' => 'Certificate added successfully!']);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function edit($id)
    {
        $certificate = Certificate::findOrFail($id);
        return view('admin.certificate-form', compact('certificate'));
    }

    public function update(Request $request, $id)
    {
        try {
            $certificate = Certificate::findOrFail($id);
            $oldData = $certificate->toArray();

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'issuer' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'badge_label' => 'nullable|string|max:50',
                'sort_order' => 'nullable|integer',
                'is_active' => 'nullable|boolean',
                'image' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:5120',
                'pdf' => 'nullable|file|mimes:pdf|max:10240',
            ]);

            $data = $validated;
            unset($data['image']);
            unset($data['pdf']);

            // Handle image upload
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                // Delete old image
                if ($certificate->image_path) {
                    $oldPath = public_path($certificate->image_path);
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
                $image = $request->file('image');
                $filename = 'cert_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('certificates'), $filename);
                $data['image_path'] = 'certificates/' . $filename;
            }

            // Handle PDF upload
            if ($request->hasFile('pdf') && $request->file('pdf')->isValid()) {
                // Delete old PDF
                if ($certificate->pdf_path) {
                    $oldPath = public_path($certificate->pdf_path);
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
                $pdf = $request->file('pdf');
                $filename = 'cert_' . uniqid() . '.pdf';
                $pdf->move(public_path('certificates'), $filename);
                $data['pdf_path'] = 'certificates/' . $filename;
            }

            $certificate->update($data);

            AuditLogger::update('certificates', "Updated certificate: {$certificate->title}", $oldData, $certificate->toArray());

            return response()->json(['success' => true, 'message' => 'Certificate updated successfully!']);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $certificate = Certificate::findOrFail($id);
            $data = $certificate->toArray();
            $title = $certificate->title;
            
            // Delete files
            if ($certificate->image_path) {
                $path = public_path($certificate->image_path);
                if (file_exists($path)) {
                    unlink($path);
                }
            }
            if ($certificate->pdf_path) {
                $path = public_path($certificate->pdf_path);
                if (file_exists($path)) {
                    unlink($path);
                }
            }
            
            $certificate->delete();

            AuditLogger::delete('certificates', "Deleted certificate: {$title}", $data);

            return response()->json(['success' => true, 'message' => 'Certificate deleted!']);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}