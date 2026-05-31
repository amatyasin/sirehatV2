<?php

namespace App\Filament\Resources\AcademicYears;

use App\Filament\Resources\AcademicYears\Pages\CreateAcademicYear;
use App\Filament\Resources\AcademicYears\Pages\EditAcademicYear;
use App\Filament\Resources\AcademicYears\Pages\ListAcademicYears;
use App\Filament\Resources\AcademicYears\Schemas\AcademicYearForm;
use App\Filament\Resources\AcademicYears\Tables\AcademicYearsTable;
use App\Models\AcademicYear;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class AcademicYearResource extends Resource
{
    protected static ?string $model =
        AcademicYear::class;

    protected static string|BackedEnum|null $navigationIcon =
        'heroicon-o-calendar-days';

    protected static string|UnitEnum|null $navigationGroup =
        'Akademik';

    protected static ?int $navigationSort =
        1;

    protected static ?string $modelLabel =
        'Tahun Ajaran';

    protected static ?string $pluralModelLabel =
        'Tahun Ajaran';

    protected static ?string $recordTitleAttribute =
        'nama';

    public static function form(
        Schema $schema
    ): Schema {
        return AcademicYearForm::configure(
            $schema
        );
    }

    public static function table(
        Table $table
    ): Table {
        return AcademicYearsTable::configure(
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
            'index' => ListAcademicYears::route('/'),
            'create' => CreateAcademicYear::route('/create'),
            'edit' => EditAcademicYear::route(
                '/{record}/edit'
            ),
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
