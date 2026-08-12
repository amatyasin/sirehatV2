<?php

namespace App\Filament\Resources\Kelurahans;

use App\Filament\Resources\Kelurahans\Pages\CreateKelurahan;
use App\Filament\Resources\Kelurahans\Pages\EditKelurahan;
use App\Filament\Resources\Kelurahans\Pages\ListKelurahans;
use App\Filament\Resources\Kelurahans\Schemas\KelurahanForm;
use App\Filament\Resources\Kelurahans\Tables\KelurahansTable;
use App\Models\Kelurahan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class KelurahanResource extends Resource
{
    protected static ?string $model = Kelurahan::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-map-pin';

    protected static ?string $modelLabel = 'Kelurahan';

    protected static ?string $pluralModelLabel = 'Kelurahan';

    protected static ?string $recordTitleAttribute = 'nama_kelurahan';

    protected static string|UnitEnum|null $navigationGroup = 'Manajemen Wilayah';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return KelurahanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KelurahansTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListKelurahans::route('/'),
            'create' => CreateKelurahan::route('/create'),
            'edit'   => EditKelurahan::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('super_admin');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->hasRole('super_admin');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->hasRole('super_admin');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->hasRole('super_admin');
    }
}
