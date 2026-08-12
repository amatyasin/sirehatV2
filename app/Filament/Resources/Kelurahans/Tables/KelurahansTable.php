<?php

namespace App\Filament\Resources\Kelurahans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use App\Models\Kecamatan;

class KelurahansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_kelurahan')
                    ->label('Nama Kelurahan / Desa')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('kecamatan.nama_kecamatan')
                    ->label('Kecamatan')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                TextColumn::make('instansi.nama_instansi')
                    ->label('Puskesmas')
                    ->placeholder('-')
                    ->searchable()
                    ->badge()
                    ->color('success'),

                IconColumn::make('aktif')
                    ->label('Aktif')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('nama_kelurahan')
            ->filters([
                SelectFilter::make('kecamatan_id')
                    ->label('Kecamatan')
                    ->options(
                        Kecamatan::orderBy('nama_kecamatan')->pluck('nama_kecamatan', 'id')
                    )
                    ->searchable()
                    ->preload(),

                TernaryFilter::make('aktif')
                    ->label('Status Aktif'),
            ])
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
