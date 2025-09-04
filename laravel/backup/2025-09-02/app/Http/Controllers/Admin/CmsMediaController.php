<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsMedia;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CmsMediaController extends Controller
{
    public function index(Request $request): View
    {
        $query = CmsMedia::with('uploader')->orderBy('created_at', 'desc');

        // Filter by type
        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('original_name', 'like', "%{$search}%")
                    ->orWhere('alt_text', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $media = $query->paginate(20);

        return view('admin.cms.media.index', compact('media'));
    }

    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'file' => [
                'required',
                'file',
                'max:10240', // 10MB
                'mimes:jpeg,jpg,png,gif,webp,pdf,doc,docx,xls,xlsx,ppt,pptx',
            ],
            'alt_text' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            $filename = Str::random(40).'.'.$file->getClientOriginalExtension();
            $path = 'cms/media/'.date('Y/m');

            // Store file
            $filePath = $file->storeAs($path, $filename, 'public');

            // Save to database
            $media = CmsMedia::create([
                'filename' => $filename,
                'original_name' => $originalName,
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'path' => $filePath,
                'alt_text' => $request->alt_text,
                'description' => $request->description,
                'metadata' => [
                    'extension' => $file->getClientOriginalExtension(),
                    'uploaded_at' => now()->toISOString(),
                ],
            ]);

            $media->load('uploader');

            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully',
                'media' => $media,
                'url' => $media->url,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Upload failed: '.$e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, CmsMedia $media): JsonResponse
    {
        $validated = $request->validate([
            'alt_text' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $media->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Media updated successfully',
            'media' => $media,
        ]);
    }

    public function destroy(CmsMedia $media): JsonResponse
    {
        try {
            // Delete file from storage
            if (Storage::disk('public')->exists($media->path)) {
                Storage::disk('public')->delete($media->path);
            }

            // Delete from database
            $media->delete();

            return response()->json([
                'success' => true,
                'message' => 'Media deleted successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Delete failed: '.$e->getMessage(),
            ], 500);
        }
    }

    public function show(CmsMedia $media): JsonResponse
    {
        $media->load('uploader');

        return response()->json([
            'success' => true,
            'media' => $media,
            'url' => $media->url,
        ]);
    }
}
