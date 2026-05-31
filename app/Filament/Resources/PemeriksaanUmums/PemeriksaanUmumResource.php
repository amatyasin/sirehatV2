<?php

namespace App\Filament\Resources\PemeriksaanUmums;

use App\Filament\Resources\PemeriksaanUmums\Pages\CreatePemeriksaanUmum;
use App\Filament\Resources\PemeriksaanUmums\Pages\EditPemeriksaanUmum;
use App\Filament\Resources\PemeriksaanUmums\Pages\ListPemeriksaanUmums;
use App\Filament\Resources\PemeriksaanUmums\Schemas\PemeriksaanUmumForm;
use App\Filament\Resources\PemeriksaanUmums\Tables\PemeriksaanUmumsTable;
use App\Models\PemeriksaanUmum;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class PemeriksaanUmumResource extends Resource
{
    protected static ?string $model =
        PemeriksaanUmum::class;

    protected static string|BackedEnum|null $navigationIcon =
        'heroicon-o-heart';

    protected static string|UnitEnum|null $navigationGroup =
        'Pemeriksaan Kesehatan';

    protected static ?string $modelLabel =
        'Pemeriksaan Umum';

    protected static ?string $pluralModelLabel =
        'Pemeriksaan Umum';

    protected static ?string $recordTitleAttribute =
        'tanggal_pemeriksaan';

    public static function form(
        Schema $schema
    ): Schema {

        return PemeriksaanUmumForm::configure(
            $schema
        );
    }

    public static function table(
        Table $table
    ): Table {

        return PemeriksaanUmumsTable::configure(
            $table
        );
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

            $user->hasRole(
                'admin_instansi'
            )

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

        if (

            $user->hasRole(
                'petugas_pemeriksaan'
            )

        ) {

            return $query->whereHas(

                'studentClassHistory.school',

                fn ($q) => $q->where(
                    'instansi_id',
                    $user->instansi_id
                )

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

            'index' => ListPemeriksaanUmums::route('/'),

            'create' => CreatePemeriksaanUmum::route('/create'),

            'edit' => EditPemeriksaanUmum::route(
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

            return optional(
                $record
                    ->studentClassHistory
                    ?->school
            )->instansi_id ===
                $user->instansi_id;
        }

        if (

            $user->hasRole(
                'admin_sekolah'
            )

        ) {

            return $record
                ->studentClassHistory
                ?->school_id ===
                    $user->school_id;
        }

        if (

            $user->hasRole(
                'petugas_pemeriksaan'
            )

        ) {

            return optional(
                $record
                    ->studentClassHistory
                    ?->school
            )->instansi_id ===
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
}
