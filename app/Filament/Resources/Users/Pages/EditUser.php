<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource =
        UserResource::class;

    protected function mutateFormDataBeforeFill(
        array $data
    ): array {

        $data['role'] =

            $this->record
                ->roles
                ->first()
                ?->name;

        return $data;
    }

    protected function afterSave(): void
    {
        $this->record->syncRoles([
            $this->data['role'],
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [

            DeleteAction::make(),

        ];
    }
}
