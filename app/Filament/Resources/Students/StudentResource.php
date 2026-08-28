<?php

namespace App\Filament\Resources\Students;

use App\Filament\Resources\Students\Pages\CreateStudent;
use App\Filament\Resources\Students\Pages\EditStudent;
use App\Filament\Resources\Students\Pages\ListStudents;
use App\Filament\Resources\Students\Pages\ViewStudent;
use App\Filament\Resources\Students\Schemas\StudentForm;
use App\Filament\Resources\Students\Schemas\StudentInfolist;
use App\Filament\Resources\Students\Tables\StudentsTable;
use App\Models\Student;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class StudentResource extends Resource
{
    protected static ?string $model =
        Student::class;

    protected static string|BackedEnum|null $navigationIcon =
        'heroicon-o-user-group';

    protected static string|UnitEnum|null $navigationGroup =
        'Akademik';

    protected static ?string $modelLabel =
        'Siswa';

    protected static ?string $pluralModelLabel =
        'Siswa';

    public static function form(
        Schema $schema
    ): Schema {

        return StudentForm::configure(
            $schema
        );
    }

    public static function table(
        Table $table
    ): Table {

        return StudentsTable::configure(
            $table
        );
    }

    public static function infolist(
        Schema $infolist
    ): Schema {

        return StudentInfolist::configure(
            $infolist
        );
    }


    public static function getEloquentQuery(): Builder
    {
        $query =
            parent::getEloquentQuery()

                ->with([

                    'school',

                    'school.instansi',

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

            $user->hasAnyRole([
                'admin_sekolah',
                'petugas_sekolah',
            ])

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

            'index' => ListStudents::route('/'),

            'create' => CreateStudent::route('/create'),

            'view' => ViewStudent::route('/{record}'),

            'edit' => EditStudent::route('/{record}/edit'),

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

                'petugas_sekolah',

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

                'admin_sekolah',

                'petugas_sekolah',

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
                $record->school?->instansi_id ===
                $user->instansi_id;
        }

        if (

            $user->hasAnyRole([
                'admin_sekolah',
                'petugas_sekolah',
            ])

        ) {

            return
                $record->school_id ===
                $user->school_id;
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
