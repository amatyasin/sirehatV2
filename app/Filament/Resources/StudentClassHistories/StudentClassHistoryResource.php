<?php

namespace App\Filament\Resources\StudentClassHistories;

use App\Filament\Resources\StudentClassHistories\Pages\CreateStudentClassHistory;
use App\Filament\Resources\StudentClassHistories\Pages\ListStudentClassHistories;
use App\Filament\Resources\StudentClassHistories\Schemas\StudentClassHistoryForm;
use App\Filament\Resources\StudentClassHistories\Tables\StudentClassHistoriesTable;
use App\Models\StudentClassHistory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class StudentClassHistoryResource extends Resource
{
    protected static ?string $model =
        StudentClassHistory::class;

    protected static string|BackedEnum|null $navigationIcon =
        'heroicon-o-user-group';

    protected static string|UnitEnum|null $navigationGroup =
        'Akademik';

    protected static ?int $navigationSort =
        5;

    protected static ?string $modelLabel =
        'Riwayat Siswa';

    protected static ?string $pluralModelLabel =
        'Riwayat Siswa';

    public static function form(
        Schema $schema
    ): Schema {

        return StudentClassHistoryForm::configure(
            $schema
        );
    }

    public static function table(
        Table $table
    ): Table {

        return StudentClassHistoriesTable::configure(
            $table
        );
    }

    public static function getEloquentQuery(): Builder
    {
        $query =
            parent::getEloquentQuery()

                ->with([

                    'student',

                    'school',

                    'schoolClass',

                    'academicYear',

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

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [

            'index' => ListStudentClassHistories::route('/'),

            'create' => CreateStudentClassHistory::route(
                '/create'
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

    public static function canViewAny(): bool
    {
        return static::canAccess();
    }

    public static function canView(
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
                $record->school?->instansi_id
                ===
                $user->instansi_id;
        }

        if (

            $user->hasRole(
                'admin_sekolah'
            )

        ) {

            return
                $record->school_id
                ===
                $user->school_id;
        }

        return false;
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
