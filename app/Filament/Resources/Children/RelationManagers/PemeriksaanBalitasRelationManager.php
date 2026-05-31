<?php

namespace App\Filament\Resources\Children\RelationManagers;

use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PemeriksaanBalitasRelationManager extends RelationManager
{
    protected static string $relationship = 'pemeriksaanBalitas';

    public function form(Schema $schema): Schema
    {
        return \App\Filament\Resources\PemeriksaanBalitas\Schemas\PemeriksaanBalitaForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('tanggal_pemeriksaan')
            ->columns([
                TextColumn::make('tanggal_pemeriksaan')
                    ->label('Tanggal Pemeriksaan')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('umur_saat_pemeriksaan')
                    ->label('Umur'),
                TextColumn::make('berat_badan')
                    ->label('BB (kg)'),
                TextColumn::make('tinggi_badan')
                    ->label('TB (cm)'),
                TextColumn::make('status_stunting')
                    ->label('Status Stunting')
                    ->badge()
                    ->color(fn (string $state): string => match (strtolower($state)) {
                        'normal' => 'success',
                        'pendek', 'sangat pendek', 'stunting', 'severely stunted' => 'danger',
                        default => 'warning',
                    }),
                TextColumn::make('status_imt_u')
                    ->label('Status Gizi (IMT/U)')
                    ->badge()
                    ->color(fn (string $state): string => match (strtolower($state)) {
                        'gizi baik (normal)' => 'success',
                        'gizi kurang', 'gizi buruk', 'berisiko gizi lebih', 'gizi lebih', 'obesitas' => 'danger',
                        default => 'warning',
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Remove Create/Attach actions from view page
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                // Remove Bulk actions
            ]);
    }
}
