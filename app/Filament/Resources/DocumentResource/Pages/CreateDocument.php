<?php

namespace App\Filament\Resources\DocumentResource\Pages;

use App\Filament\Resources\DocumentResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CreateDocument extends CreateRecord
{
    protected static string $resource = DocumentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['uuid'] = (string) Str::uuid();
        $path = $data['file_path'] ?? null;

        if ($path && Storage::disk('local')->exists($path)) {
            $data['file_size_bytes'] = Storage::disk('local')->size($path);
            $data['mime_type'] = Storage::disk('local')->mimeType($path) ?: 'application/pdf';
            $data['file_name'] = basename($path);
        } else {
            $data['file_size_bytes'] = 0;
            $data['mime_type'] = 'application/pdf';
            $data['file_name'] = Str::slug($data['title']) . '.pdf';
        }

        return $data;
    }
}
