<?php

namespace App\Filament\Resources\Posyandus\Tables;

use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables;
use Filament\Tables\Table;

class PosyandusTable
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
                    'nama_posyandu'
                )

                    ->label(
                        'Nama Posyandu'
                    )

                    ->searchable()

                    ->sortable()

                    ->weight('bold'),

                Tables\Columns\TextColumn::make(
                    'kelurahan.nama_kelurahan'
                )

                    ->label('Kelurahan')

                    ->searchable()

                    ->sortable(),

                Tables\Columns\TextColumn::make(
                    'kelurahan.kecamatan.nama_kecamatan'
                )

                    ->label('Kecamatan')

                    ->searchable()

                    ->sortable(),

                Tables\Columns\TextColumn::make(
                    'alamat'
                )

                    ->label('Alamat')

                    ->limit(40)

                    ->searchable()

                    ->toggleable(),

                Tables\Columns\TextColumn::make(
                    'penanggung_jawab'
                )

                    ->label(
                        'Penanggung Jawab'
                    )

                    ->searchable(),

                Tables\Columns\TextColumn::make(
                    'no_wa'
                )

                    ->label('No WA')

                    ->searchable()

                    ->copyable(),

                Tables\Columns\TextColumn::make(
                    'children_count'
                )

                    ->counts('children')

                    ->label(
                        'Jumlah Anak'
                    )

                    ->badge()

                    ->color('warning'),

                Tables\Columns\IconColumn::make(
                    'aktif'
                )

                    ->label('Aktif')

                    ->boolean(),

                Tables\Columns\TextColumn::make(
                    'created_at'
                )

                    ->label('Dibuat')

                    ->dateTime('d M Y')

                    ->sortable()

                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

            ])

            ->filters([

                Tables\Filters\SelectFilter::make(
                    'instansi'
                )

                    ->label('Puskesmas')

                    ->relationship(
                        'instansi',
                        'nama_instansi'
                    )

                    ->searchable(),

                Tables\Filters\TernaryFilter::make(
                    'aktif'
                )

                    ->label(
                        'Status Aktif'
                    ),

            ])

            ->defaultSort(
                'nama_posyandu'
            )

            ->recordActions([

                EditAction::make(),

            ])

            ->toolbarActions([

                DeleteBulkAction::make()

                    ->visible(

                        auth()
                            ->user()
                            ->hasAnyRole([

                                'super_admin',

                                'admin_dinkes',

                            ])

                    ),

            ]);
    }
}
