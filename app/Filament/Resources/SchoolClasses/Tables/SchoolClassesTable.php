<?php

namespace App\Filament\Resources\SchoolClasses\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SchoolClassesTable
{
    public static function configure(
        Table $table
    ): Table {

        return $table

            ->columns([

                TextColumn::make(
                    'school.nama_sekolah'
                )

                    ->label(
                        'Sekolah'
                    )

                    ->searchable()

                    ->sortable(),

                TextColumn::make(
                    'nama_kelas'
                )

                    ->label(
                        'Nama Kelas'
                    )

                    ->searchable()

                    ->sortable()

                    ->weight('bold'),

                TextColumn::make(
                    'urutan'
                )

                    ->label(
                        'Urutan'
                    )

                    ->numeric()

                    ->sortable()

                    ->badge()

                    ->color('primary'),

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

                Tables\Filters\SelectFilter::make(
                    'school_id'
                )

                    ->relationship(
                        'school',
                        'nama_sekolah'
                    )

                    ->label(
                        'Sekolah'
                    )

                    ->searchable()

                    ->preload(),

            ])

            ->defaultSort(
                'urutan'
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
