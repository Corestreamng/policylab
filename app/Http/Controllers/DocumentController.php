<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class DocumentController extends Controller
{
    /**
     * Download document securely via signed URL.
     */
    public function download(Request $request, Document $document)
    {
        if (! $request->hasValidSignature()) {
            abort(Response::HTTP_FORBIDDEN, 'Invalid or expired download signature.');
        }

        if (! $document->is_public && ! auth()->check()) {
            abort(Response::HTTP_FORBIDDEN, 'This document is restricted.');
        }

        $document->increment('download_count');

        $path = $document->file_path;
        if (! Storage::disk('local')->exists($path)) {
            abort(Response::HTTP_NOT_FOUND, 'Document file not found on private storage.');
        }

        return Storage::disk('local')->download($path, $document->file_name, [
            'Content-Type' => $document->mime_type ?: 'application/pdf',
        ]);
    }

    /**
     * Stream document inline for PDF.js preview via signed URL.
     */
    public function stream(Request $request, Document $document)
    {
        if (! $request->hasValidSignature()) {
            abort(Response::HTTP_FORBIDDEN, 'Invalid or expired preview signature.');
        }

        if (! $document->is_public && ! auth()->check()) {
            abort(Response::HTTP_FORBIDDEN, 'This document is restricted.');
        }

        $path = $document->file_path;
        if (! Storage::disk('local')->exists($path)) {
            abort(Response::HTTP_NOT_FOUND, 'Document file not found on private storage.');
        }

        return Storage::disk('local')->response($path, $document->file_name, [
            'Content-Type' => $document->mime_type ?: 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . addslashes($document->file_name) . '"',
        ]);
    }
}
