<?php

namespace App\Filament\Resources\FunderResource\Pages;

use App\Filament\Resources\FunderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFunder extends EditRecord
{
    protected static string $resource = FunderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
