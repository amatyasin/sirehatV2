<?php

namespace App\Filament\Resources\Schools\Tables;

use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables;
use Filament\Tables\Table;

class SchoolsTable
{
    public static function configure(
        Table $table
    ): Table {

        return $table

            ->columns([

                Tables\Columns\TextColumn::make(
                    'instansi.nama_instansi'
                )

                    ->label('Puskesmas')

                    ->searchable()

                    ->sortable(),

                Tables\Columns\TextColumn::make(
                    'nama_sekolah'
                )

                    ->label(
                        'Nama Sekolah'
                    )

                    ->searchable()

                    ->sortable()

                    ->weight('bold'),

                Tables\Columns\TextColumn::make(
                    'npsn'
                )

                    ->label('NPSN')

                    ->searchable(),

                Tables\Columns\TextColumn::make(
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

            ])

            ->filters([])

            ->defaultSort(
                'nama_sekolah'
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

                DeleteBulkAction::make()

                    ->visible(

                        auth()
                            ->user()
                            ->hasRole(
                                'super_admin'
                            )

                    ),

            ]);
    }
}
