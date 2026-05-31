<?php

namespace App\Filament\Resources\PemeriksaanBalitas;

use App\Filament\Resources\PemeriksaanBalitas\Pages\CreatePemeriksaanBalita;
use App\Filament\Resources\PemeriksaanBalitas\Pages\EditPemeriksaanBalita;
use App\Filament\Resources\PemeriksaanBalitas\Pages\ListPemeriksaanBalitas;
use App\Filament\Resources\PemeriksaanBalitas\Schemas\PemeriksaanBalitaForm;
use App\Filament\Resources\PemeriksaanBalitas\Tables\PemeriksaanBalitasTable;
use App\Models\PemeriksaanBalita;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class PemeriksaanBalitaResource extends Resource
{
    protected static ?string $model =
        PemeriksaanBalita::class;

    protected static string|BackedEnum|null $navigationIcon =
        'heroicon-o-heart';

    protected static string|UnitEnum|null $navigationGroup =
        'Posyandu';

    protected static ?string $navigationLabel =
        'Pemeriksaan Balita & Apras';

    protected static ?int $navigationSort =
        3;

    protected static ?string $modelLabel =
        'Pemeriksaan Balita & Apras';

    protected static ?string $pluralModelLabel =
        'Pemeriksaan Balita & Apras';

    protected static ?string $recordTitleAttribute =
        'tanggal_pemeriksaan';

    public static function form(
        Schema $schema
    ): Schema {

        return PemeriksaanBalitaForm::configure(
            $schema
        );
    }

    public static function table(
        Table $table
    ): Table {

        return PemeriksaanBalitasTable::configure(
            $table
        );
    }

    public static function getEloquentQuery(): Builder
    {
        $query =
            parent::getEloquentQuery()

                ->with([

                    'child',

                    'child.posyandu',

                    'child.orangTua',

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

                'child.posyandu',

                fn ($q) => $q->where(

                    'instansi_id',

                    $user->instansi_id

                )

            );
        }

        if (
            $user->hasRole(
                'petugas_posyandu'
            )
        ) {

            return $query->whereHas(

                'child',

                fn ($q) => $q->where(

                    'posyandu_id',

                    $user->posyandu_id

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

            'index' => ListPemeriksaanBalitas::route('/'),

            'create' => CreatePemeriksaanBalita::route(
                '/create'
            ),

            'edit' => EditPemeriksaanBalita::route(
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

                'petugas_posyandu',

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

                'petugas_posyandu',

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
                $record->child?->instansi_id ===
                $user->instansi_id;
        }

        if (

            $user->hasRole(
                'petugas_posyandu'
            )

        ) {

            return
                $record->child?->posyandu_id ===
                $user->posyandu_id;
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
