<?php

namespace App\Filament\Resources\PemeriksaanGigis;

use App\Filament\Resources\PemeriksaanGigis\Pages\CreatePemeriksaanGigi;
use App\Filament\Resources\PemeriksaanGigis\Pages\EditPemeriksaanGigi;
use App\Filament\Resources\PemeriksaanGigis\Pages\ListPemeriksaanGigis;
use App\Filament\Resources\PemeriksaanGigis\Schemas\PemeriksaanGigiForm;
use App\Filament\Resources\PemeriksaanGigis\Tables\PemeriksaanGigisTable;
use App\Models\PemeriksaanGigi;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class PemeriksaanGigiResource extends Resource
{
    protected static ?string $model =
        PemeriksaanGigi::class;

    protected static string|BackedEnum|null $navigationIcon =
        'heroicon-o-face-smile';

    protected static string|UnitEnum|null $navigationGroup =
        'Pemeriksaan Kesehatan';

    protected static ?string $modelLabel =
        'Pemeriksaan Gigi';

    protected static ?string $pluralModelLabel =
        'Pemeriksaan Gigi';

    protected static ?string $recordTitleAttribute =
        'tanggal_pemeriksaan';

    public static function form(
        Schema $schema
    ): Schema {

        return PemeriksaanGigiForm::configure(
            $schema
        );
    }

    public static function table(
        Table $table
    ): Table {

        return PemeriksaanGigisTable::configure(
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

            'index' => ListPemeriksaanGigis::route('/'),

            'create' => CreatePemeriksaanGigi::route(
                '/create'
            ),

            'edit' => EditPemeriksaanGigi::route(
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

            ]);
    }

    public static function canViewAny(): bool
    {
        return static::canAccess();
    }

    public static function canCreate(): bool
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

            $user->hasAnyRole([

                'admin_instansi',

                'petugas_pemeriksaan',

            ])

        ) {

            return

                $record
                    ->studentClassHistory
                    ?->school
                    ?->instansi_id

                ===

                $user->instansi_id;
        }

        if (

            $user->hasRole(
                'admin_sekolah'
            )

        ) {

            return

                $record
                    ->studentClassHistory
                    ?->school_id

                ===

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

    public static function getEloquentQuery(): Builder
    {
        $query =
            parent::getEloquentQuery()

                ->with([

                    'studentClassHistory.student',

                    'studentClassHistory.school',

                    'studentClassHistory.schoolClass',

                    'studentClassHistory.academicYear',

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

            $user->hasAnyRole([

                'admin_instansi',

                'petugas_pemeriksaan',

            ])

        ) {

            return $query->whereHas(

                'studentClassHistory.school',

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

            return $query->whereHas(

                'studentClassHistory',

                fn ($q) => $q->where(

                    'school_id',

                    $user->school_id

                )

            );
        }

        return $query->whereRaw(
            '1 = 0'
        );
    }
}
