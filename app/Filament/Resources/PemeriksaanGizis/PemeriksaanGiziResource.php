<?php

namespace App\Filament\Resources\PemeriksaanGizis;

use App\Filament\Resources\PemeriksaanGizis\Pages\CreatePemeriksaanGizi;
use App\Filament\Resources\PemeriksaanGizis\Pages\EditPemeriksaanGizi;
use App\Filament\Resources\PemeriksaanGizis\Pages\ListPemeriksaanGizis;
use App\Filament\Resources\PemeriksaanGizis\Schemas\PemeriksaanGiziForm;
use App\Filament\Resources\PemeriksaanGizis\Tables\PemeriksaanGizisTable;
use App\Models\PemeriksaanGizi;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class PemeriksaanGiziResource extends Resource
{
    protected static ?string $model =
        PemeriksaanGizi::class;

    protected static string|BackedEnum|null $navigationIcon =
        'heroicon-o-scale';

    protected static string|UnitEnum|null $navigationGroup =
        'Pemeriksaan Kesehatan';

    protected static ?string $modelLabel =
        'Pemeriksaan Gizi';

    protected static ?string $pluralModelLabel =
        'Pemeriksaan Gizi';

    protected static ?string $recordTitleAttribute =
        'tanggal_pemeriksaan';

    public static function form(
        Schema $schema
    ): Schema {

        return PemeriksaanGiziForm::configure(
            $schema
        );
    }

    public static function table(
        Table $table
    ): Table {

        return PemeriksaanGizisTable::configure(
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

            'index' => ListPemeriksaanGizis::route('/'),

            'create' => CreatePemeriksaanGizi::route('/create'),

            'edit' => EditPemeriksaanGizi::route(
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
