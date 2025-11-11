<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\AnnouncementImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class AnnouncementImageController extends Controller
{
    /**
     * Serve announcement images with authentication
     */
    public function show(Request $request, $imagePath)
    {
        // Ensure user is authenticated
        if (!auth()->check()) {
            abort(403, 'Unauthorized access to announcement image');
        }

        // Decode the image path
        $decodedPath = base64_decode($imagePath);

        // Validate the image belongs to an active announcement or announcement_images table
        $isValid = false;

        // Check if it's a legacy main image
        $announcement = Announcement::where('image_path', $decodedPath)->first();
        if ($announcement) {
            $isValid = true;
        }

        // Check if it's in the announcement_images table
        if (!$isValid) {
            $announcementImage = AnnouncementImage::where('image_path', $decodedPath)->first();
            if ($announcementImage) {
                $isValid = true;
            }
        }

        if (!$isValid) {
            abort(404, 'Image not found or access denied');
        }

        // Get the full path
        $fullPath = storage_path('app/public/' . $decodedPath);

        if (!file_exists($fullPath)) {
            abort(404, 'Image file not found');
        }

        // Determine MIME type
        $mimeType = mime_content_type($fullPath);

        // Return the image with appropriate headers
        return response()->file($fullPath, [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'private, max-age=3600',
        ]);
    }
}
