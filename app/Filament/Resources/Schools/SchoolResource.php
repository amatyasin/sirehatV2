<?php

namespace App\Filament\Resources\Schools;

use App\Filament\Resources\Schools\Pages\CreateSchool;
use App\Filament\Resources\Schools\Pages\EditSchool;
use App\Filament\Resources\Schools\Pages\ListSchools;
use App\Filament\Resources\Schools\Schemas\SchoolForm;
use App\Filament\Resources\Schools\Tables\SchoolsTable;
use App\Models\School;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class SchoolResource extends Resource
{
    protected static ?string $model =
        School::class;

    protected static string|BackedEnum|null $navigationIcon =
        'heroicon-o-academic-cap';

    protected static string|UnitEnum|null $navigationGroup =
        'Akademik';

    protected static ?string $modelLabel =
        'Sekolah';

    protected static ?string $pluralModelLabel =
        'Sekolah';

    protected static ?string $recordTitleAttribute =
        'nama_sekolah';

    public static function form(
        Schema $schema
    ): Schema {

        return SchoolForm::configure(
            $schema
        );
    }

    public static function table(
        Table $table
    ): Table {

        return SchoolsTable::configure(
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
                'admin_sekolah'
            )

        ) {

            return $query->where(

                'id',

                $user->school_id

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

            'index' => ListSchools::route('/'),

            'create' => CreateSchool::route('/create'),

            'edit' => EditSchool::route('/{record}/edit'),

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

                'admin_sekolah',

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
