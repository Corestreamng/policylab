<?php

namespace App\Filament\Resources\PolicyStageResource\Pages;

use App\Filament\Resources\PolicyStageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPolicyStage extends EditRecord
{
    protected static string $resource = PolicyStageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function ($record, Actions\DeleteAction $action) {
                    if ($record->policyStatuses()->exists()) {
                        \Filament\Notifications\Notification::make()
                            ->title('Deletion Blocked')
                            ->body("Cannot delete stage '{$record->name}' because it is currently assigned to one or more states. Reassign the states before deleting.")
                            ->danger()
                            ->send();

                        $action->halt();
                    }
                }),
        ];
    }
}
