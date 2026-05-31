<?php

namespace App\Filament\Resources\RekapPemeriksaans;

use App\Filament\Resources\RekapPemeriksaans\Pages\ListRekapPemeriksaans;
use App\Filament\Resources\RekapPemeriksaans\Tables\RekapPemeriksaansTable;
use App\Models\StudentClassHistory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class RekapPemeriksaanResource extends Resource
{
    protected static ?string $model =
        StudentClassHistory::class;

    protected static ?string $modelLabel =
        'Rekap Pemeriksaan';

    protected static ?string $pluralModelLabel =
        'Rekap Pemeriksaan';

    protected static string|UnitEnum|null $navigationGroup =
        'Pemeriksaan Kesehatan';

    protected static string|BackedEnum|null $navigationIcon =
        'heroicon-o-clipboard-document-check';

    public static function table(
        Table $table
    ): Table {

        return RekapPemeriksaansTable::configure(
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

                    'pemeriksaanUmums',

                    'pemeriksaanGigis',

                    'pemeriksaanGizis',

                    'pemeriksaanMatas',

                ])

                ->where(
                    'aktif',
                    true
                );

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

        if (

            $user->hasRole(
                'petugas_pemeriksaan'
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

            'index' => ListRekapPemeriksaans::route('/'),

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

                'petugas_pemeriksaan',

            ]);
    }
}
