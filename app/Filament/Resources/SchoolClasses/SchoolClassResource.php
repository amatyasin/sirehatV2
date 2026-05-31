<?php

namespace App\Filament\Resources\SchoolClasses;

use App\Filament\Resources\SchoolClasses\Pages\CreateSchoolClass;
use App\Filament\Resources\SchoolClasses\Pages\EditSchoolClass;
use App\Filament\Resources\SchoolClasses\Pages\ListSchoolClasses;
use App\Filament\Resources\SchoolClasses\Schemas\SchoolClassForm;
use App\Filament\Resources\SchoolClasses\Tables\SchoolClassesTable;
use App\Models\SchoolClass;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class SchoolClassResource extends Resource
{
    protected static ?string $model =
        SchoolClass::class;

    protected static string|BackedEnum|null $navigationIcon =
        'heroicon-o-building-library';

    protected static string|UnitEnum|null $navigationGroup =
        'Akademik';

    protected static ?string $modelLabel =
        'Kelas';

    protected static ?string $pluralModelLabel =
        'Kelas';

    public static function form(
        Schema $schema
    ): Schema {

        return SchoolClassForm::configure(
            $schema
        );
    }

    public static function table(
        Table $table
    ): Table {

        return SchoolClassesTable::configure(
            $table
        );
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [

            'index' => ListSchoolClasses::route('/'),

            'create' => CreateSchoolClass::route('/create'),

            'edit' => EditSchoolClass::route('/{record}/edit'),

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

    public static function canCreate(): bool
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

    public static function getEloquentQuery(): Builder
    {
        $query =
            parent::getEloquentQuery();

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

            return $query->whereHas(

                'school',

                fn ($q) => $q->where(
                    'instansi_id',
                    $user->instansi_id
                )

            );
        }

        if (

            $user->hasRole(
                'admin_sekolah'
            )

        ) {

            return $query->where(
                'school_id',
                $user->school_id
            );
        }

        return $query->whereRaw(
            '1 = 0'
        );
    }

    public static function canView(
        $record
    ): bool {

        return static::canAccess();
    }

    public static function canEdit(
        $record
    ): bool {

        return auth()
            ->user()
            ->hasAnyRole([

                'super_admin',

                'admin_dinkes',

            ]);
    }

    public static function canDelete(
        $record
    ): bool {

        return auth()
            ->user()
            ->hasRole(
                'super_admin'
            );
    }

    public static function canDeleteAny(): bool
    {
        return auth()
            ->user()
            ->hasRole(
                'super_admin'
            );
    }
}
