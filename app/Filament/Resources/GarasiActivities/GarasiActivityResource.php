<?php

namespace App\Filament\Resources\GarasiActivities;

use App\Filament\Resources\GarasiActivities\Pages\CreateGarasiActivity;
use App\Filament\Resources\GarasiActivities\Pages\EditGarasiActivity;
use App\Filament\Resources\GarasiActivities\Pages\ListGarasiActivities;
use App\Filament\Resources\GarasiActivities\Schemas\GarasiActivityForm;
use App\Filament\Resources\GarasiActivities\Tables\GarasiActivitiesTable;
use App\Models\GarasiActivity;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class GarasiActivityResource extends Resource
{
    protected static ?string $model = GarasiActivity::class;

    protected static \UnitEnum|string|null $navigationGroup = 'UKGM (Upaya Kesehatan Gigi Masyarakat)';

    protected static ?string $navigationLabel = 'Kegiatan UKGM';

    protected static ?string $modelLabel = 'Kegiatan UKGM';

    protected static ?string $pluralModelLabel = 'Kegiatan UKGM';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user && ($user->hasAnyRole(['super_admin', 'admin_dinkes', 'admin_instansi', 'admin_kecamatan', 'petugas_posyandu'])
            || $user->can('garasi.activity.view'));
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();

        return $user && ($user->hasAnyRole(['super_admin', 'admin_dinkes', 'admin_instansi', 'admin_kecamatan', 'petugas_posyandu'])
            || $user->can('garasi.activity.create'));
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        if ($user->hasRole('super_admin') || $user->hasRole('admin_dinkes')) {
            return true;
        }

        if ($user->hasRole('admin_kecamatan')) {
            return $record->posyandu?->kelurahan?->kecamatan_id === $user->kecamatan_id;
        }

        if ($user->hasRole('admin_instansi')) {
            return $record->instansi_id === $user->instansi_id;
        }

        if ($user->hasRole('petugas_posyandu')) {
            return $record->posyandu_id === $user->posyandu_id;
        }

        return $user->can('garasi.activity.update');
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        $user = auth()->user();

        return $user && ($user->hasAnyRole(['super_admin', 'admin_dinkes', 'admin_instansi'])
            || $user->can('garasi.activity.delete'));
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user->hasRole('super_admin') || $user->hasRole('admin_dinkes')) {
            return $query;
        }

        if ($user->hasRole('admin_kecamatan')) {
            return $query->whereHas('posyandu.kelurahan', fn ($q) => $q->where('kecamatan_id', $user->kecamatan_id));
        }

        if ($user->hasRole('admin_instansi')) {
            return $query->where('instansi_id', $user->instansi_id);
        }

        if ($user->hasRole('petugas_posyandu')) {
            return $query->where('posyandu_id', $user->posyandu_id);
        }

        return $query->whereRaw('1 = 0');
    }

    public static function form(Schema $schema): Schema
    {
        return GarasiActivityForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GarasiActivitiesTable::configure($table);
    }

    public static function infolist(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Informasi Kegiatan')
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('posyandu.nama_posyandu')
                            ->label('Posyandu'),
                        \Filament\Infolists\Components\TextEntry::make('posyandu.instansi.nama_instansi')
                            ->label('Puskesmas'),
                        \Filament\Infolists\Components\TextEntry::make('activity_date')
                            ->label('Tanggal Kegiatan')
                            ->date(),
                        \Filament\Infolists\Components\TextEntry::make('location')
                            ->label('Lokasi'),
                        \Filament\Infolists\Components\TextEntry::make('officer.name')
                            ->label('Petugas'),
                        \Filament\Infolists\Components\TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'scheduled' => 'gray',
                                'ongoing' => 'warning',
                                'completed' => 'success',
                                'cancelled' => 'danger',
                                default => 'gray',
                            }),
                        \Filament\Infolists\Components\TextEntry::make('notes')
                            ->label('Keterangan')
                            ->columnSpanFull(),
                    ])->columns(2)
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGarasiActivities::route('/'),
            'create' => CreateGarasiActivity::route('/create'),
            'view' => Pages\ViewGarasiActivity::route('/{record}'),
            'edit' => EditGarasiActivity::route('/{record}/edit'),
            'participants' => Pages\ManageGarasiParticipants::route('/{record}/participants'),
        ];
    }
}
