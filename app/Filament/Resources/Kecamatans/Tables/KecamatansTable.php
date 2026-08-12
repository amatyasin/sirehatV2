<?php

namespace App\Filament\Resources\Kecamatans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class KecamatansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_kecamatan')
                    ->label('Nama Kecamatan')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('jumlah_kelurahan')
                    ->label('Jumlah Kelurahan')
                    ->getStateUsing(fn ($record) => $record->kelurahans()->count())
                    ->badge()
                    ->color('info'),

                TextColumn::make('jumlah_puskesmas')
                    ->label('Jumlah Puskesmas')
                    ->getStateUsing(
                        fn ($record) => $record->kelurahans()
                            ->whereNotNull('instansi_id')
                            ->distinct('instansi_id')
                            ->count('instansi_id')
                    )
                    ->badge()
                    ->color('success'),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('nama_kecamatan')
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
