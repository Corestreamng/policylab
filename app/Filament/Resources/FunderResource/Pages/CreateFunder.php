<?php

namespace App\Filament\Resources\FunderResource\Pages;

use App\Filament\Resources\FunderResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateFunder extends CreateRecord
{
    protected static string $resource = FunderResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        return $data;
    }
}
