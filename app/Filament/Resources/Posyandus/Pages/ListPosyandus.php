<?php

namespace App\Filament\Resources\Posyandus\Pages;

use App\Filament\Resources\Posyandus\PosyanduResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPosyandus extends ListRecords
{
    protected static string $resource =
        PosyanduResource::class;

    protected function getHeaderActions(): array
    {
        return [

            CreateAction::make()

                ->visible(

                    auth()
                        ->user()
                        ->hasAnyRole([

                            'super_admin',

                            'admin_dinkes',

                            'admin_instansi',

                        ])

                ),

        ];
    }
}
