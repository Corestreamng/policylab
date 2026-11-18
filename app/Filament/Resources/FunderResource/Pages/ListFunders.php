<?php

namespace App\Filament\Resources\FunderResource\Pages;

use App\Filament\Resources\FunderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFunders extends ListRecords
{
    protected static string $resource = FunderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
