<?php

namespace App\Filament\Resources\PolicyStageResource\Pages;

use App\Filament\Resources\PolicyStageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPolicyStages extends ListRecords
{
    protected static string $resource = PolicyStageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
