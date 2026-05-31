<?php

namespace App\Filament\Resources\StudentClassHistories\Pages;

use App\Filament\Resources\StudentClassHistories\StudentClassHistoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListStudentClassHistories extends ListRecords
{
    protected static string $resource =
        StudentClassHistoryResource::class;

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

                        ])

                ),

        ];
    }
}
