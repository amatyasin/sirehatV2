<?php

namespace App\Filament\Resources\AcademicYears\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AcademicYearsTable
{
    public static function configure(
        Table $table
    ): Table {

        return $table

            ->columns([

                TextColumn::make(
                    'nama'
                )

                    ->label(
                        'Tahun Ajaran'
                    )

                    ->searchable()

                    ->sortable()

                    ->weight('bold'),

                IconColumn::make(
                    'aktif'
                )

                    ->label(
                        'Status'
                    )

                    ->boolean()

                    ->sortable(),

                TextColumn::make(
                    'created_at'
                )

                    ->label(
                        'Dibuat'
                    )

                    ->dateTime(
                        'd M Y H:i'
                    )

                    ->sortable()

                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make(
                    'updated_at'
                )

                    ->label(
                        'Diupdate'
                    )

                    ->dateTime(
                        'd M Y H:i'
                    )

                    ->sortable()

                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

            ])

            ->filters([

                Tables\Filters\TernaryFilter::make(
                    'aktif'
                )

                    ->label(
                        'Status Aktif'
                    ),

            ])

            ->defaultSort(
                'created_at',
                'desc'
            )

            ->recordActions([

                EditAction::make()

                    ->visible(

                        auth()
                            ->user()
                            ->hasAnyRole([

                                'super_admin',

                                'admin_dinkes',

                            ])

                    ),

            ])

            ->toolbarActions([

                BulkActionGroup::make([

                    DeleteBulkAction::make()

                        ->visible(

                            auth()
                                ->user()
                                ->hasRole(
                                    'super_admin'
                                )

                        ),

                ]),

            ]);
    }
}
