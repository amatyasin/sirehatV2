<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource =
        UserResource::class;

    protected function afterCreate(): void
    {
        if (
            filled(
                $this->data['role']
            )
        ) {

            $this->record->syncRoles([

                $this->data['role'],

            ]);
        }
    }
}
