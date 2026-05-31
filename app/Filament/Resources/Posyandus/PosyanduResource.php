<?php

namespace App\Filament\Resources\Posyandus;

use App\Filament\Resources\Posyandus\Pages\CreatePosyandu;
use App\Filament\Resources\Posyandus\Pages\EditPosyandu;
use App\Filament\Resources\Posyandus\Pages\ListPosyandus;
use App\Filament\Resources\Posyandus\Schemas\PosyanduForm;
use App\Filament\Resources\Posyandus\Tables\PosyandusTable;
use App\Models\Posyandu;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class PosyanduResource extends Resource
{
    protected static ?string $model =
        Posyandu::class;

    protected static string|BackedEnum|null $navigationIcon =
        'heroicon-o-heart';

    protected static string|UnitEnum|null $navigationGroup =
        'Posyandu';

    protected static ?string $modelLabel =
        'Posyandu';

    protected static ?string $pluralModelLabel =
        'Posyandu';

    protected static ?string $recordTitleAttribute =
        'nama_posyandu';

    public static function form(
        Schema $schema
    ): Schema {

        return PosyanduForm::configure(
            $schema
        );
    }

    public static function table(
        Table $table
    ): Table {

        return PosyandusTable::configure(
            $table
        );
    }

    public static function getEloquentQuery(): Builder
    {
        $query =
            parent::getEloquentQuery()

                ->with([

                    'instansi',

                    'kelurahan',

                    'kelurahan.kecamatan',

                ]);

        $user =
            auth()->user();

        if (

            $user->hasAnyRole([

                'super_admin',

                'admin_dinkes',

            ])

        ) {

            return $query;
        }

        if (

            $user->hasRole(
                'admin_instansi'
            )

        ) {

            return $query->where(

                'instansi_id',

                $user->instansi_id

            );
        }

        if (

            $user->hasRole(
                'petugas_posyandu'
            )

        ) {

            return $query->where(

                'id',

                $user->posyandu_id

            );
        }

        return $query->whereRaw(
            '1 = 0'
        );
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [

            'index' => ListPosyandus::route('/'),

            'create' => CreatePosyandu::route('/create'),

            'edit' => EditPosyandu::route('/{record}/edit'),

        ];
    }

    public static function canAccess(): bool
    {
        return auth()
            ->user()
            ->hasAnyRole([

                'super_admin',

                'admin_dinkes',

                'admin_instansi',

                'petugas_posyandu',

            ]);
    }

    public static function canViewAny(): bool
    {
        return static::canAccess();
    }

    public static function canView(
        $record
    ): bool {

        return static::canEdit(
            $record
        );
    }

    public static function canCreate(): bool
    {
        return auth()
            ->user()
            ->hasAnyRole([

                'super_admin',

                'admin_dinkes',

                'admin_instansi',

            ]);
    }

    public static function canEdit(
        $record
    ): bool {

        $user =
            auth()->user();

        if (

            $user->hasAnyRole([

                'super_admin',

                'admin_dinkes',

            ])

        ) {

            return true;
        }

        if (

            $user->hasRole(
                'admin_instansi'
            )

        ) {

            return
                $record->instansi_id ===
                $user->instansi_id;
        }

        return false;
    }

    public static function canDelete(
        $record
    ): bool {

        return auth()
            ->user()
            ->hasAnyRole([

                'super_admin',

                'admin_dinkes',

            ]);
    }

    public static function canDeleteAny(): bool
    {
        return auth()
            ->user()
            ->hasAnyRole([

                'super_admin',

                'admin_dinkes',

            ]);
    }
}
