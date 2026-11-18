<?php

namespace App\Filament\Resources\RegionResource\Pages;

use App\Filament\Resources\RegionResource;
use App\Models\Country;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateRegion extends CreateRecord
{
    protected static string $resource = RegionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        if (empty($data['country_id'])) {
            $data['country_id'] = Country::first()->id;
        }
        return $data;
    }
}
