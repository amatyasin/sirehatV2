<?php

namespace App\Filament\Resources\PemeriksaanMatas;

use App\Filament\Resources\PemeriksaanMatas\Pages\CreatePemeriksaanMata;
use App\Filament\Resources\PemeriksaanMatas\Pages\EditPemeriksaanMata;
use App\Filament\Resources\PemeriksaanMatas\Pages\ListPemeriksaanMatas;
use App\Filament\Resources\PemeriksaanMatas\Schemas\PemeriksaanMataForm;
use App\Filament\Resources\PemeriksaanMatas\Tables\PemeriksaanMatasTable;
use App\Models\PemeriksaanMata;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class PemeriksaanMataResource extends Resource
{
    protected static ?string $model =
        PemeriksaanMata::class;

    protected static string|BackedEnum|null $navigationIcon =
        'heroicon-o-eye';

    protected static string|UnitEnum|null $navigationGroup =
        'Pemeriksaan Kesehatan';

    protected static ?string $modelLabel =
        'Pemeriksaan Mata';

    protected static ?string $pluralModelLabel =
        'Pemeriksaan Mata';

    protected static ?string $recordTitleAttribute =
        'tanggal_pemeriksaan';

    public static function form(
        Schema $schema
    ): Schema {

        return PemeriksaanMataForm::configure(
            $schema
        );
    }

    public static function table(
        Table $table
    ): Table {

        return PemeriksaanMatasTable::configure(
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

            'index' => ListPemeriksaanMatas::route('/'),

            'create' => CreatePemeriksaanMata::route('/create'),

            'edit' => EditPemeriksaanMata::route(
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

                function ($q) use ($user) {

                    $q->where(

                        'instansi_id',

                        $user->instansi_id

                    );

                }

            );
        }

        if (

            $user->hasRole(
                'admin_sekolah'
            )

        ) {

            return $query->whereHas(

                'studentClassHistory',

                function ($q) use ($user) {

                    $q->where(

                        'school_id',

                        $user->school_id

                    );

                }

            );
        }

        return $query->whereRaw(
            '1 = 0'
        );
    }
}
