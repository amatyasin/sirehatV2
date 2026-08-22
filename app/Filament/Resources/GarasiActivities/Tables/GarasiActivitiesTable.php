<?php

namespace App\Filament\Resources\GarasiActivities\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GarasiActivitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('posyandu.nama_posyandu')
                    ->label('Posyandu')
                    ->searchable()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('activity_date')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('location')
                    ->label('Lokasi')
                    ->searchable(),
                \Filament\Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'scheduled' => 'gray',
                        'ongoing' => 'warning',
                        'completed' => 'success',
                        default => 'gray',
                    }),
                \Filament\Tables\Columns\TextColumn::make('participants_count')
                    ->label('Peserta')
                    ->counts('participants'),
                \Filament\Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                \Filament\Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\Action::make('peserta')
                    ->label('Kelola Peserta')
                    ->icon('heroicon-o-users')
                    ->color('success')
                    ->url(fn (\App\Models\GarasiActivity $record): string => \App\Filament\Resources\GarasiActivities\GarasiActivityResource::getUrl('participants', ['record' => $record])),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
