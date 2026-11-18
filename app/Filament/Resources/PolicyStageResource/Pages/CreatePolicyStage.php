<?php

namespace App\Filament\Resources\PolicyStageResource\Pages;

use App\Filament\Resources\PolicyStageResource;
use App\Models\Country;
use Filament\Resources\Pages\CreateRecord;

class CreatePolicyStage extends CreateRecord
{
    protected static string $resource = PolicyStageResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['country_id'])) {
            $data['country_id'] = Country::first()->id;
        }
        if (empty($data['slug'])) {
            $data['slug'] = \Illuminate\Support\Str::slug($data['name']);
        }
        if (empty($data['bg_color_hex'])) {
            $data['bg_color_hex'] = '#F8FAFC';
        }
        return $data;
    }
}
