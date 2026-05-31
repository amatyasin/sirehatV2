<?php

namespace App\Filament\Resources\Instansis;

use App\Filament\Resources\Instansis\Pages\CreateInstansi;
use App\Filament\Resources\Instansis\Pages\EditInstansi;
use App\Filament\Resources\Instansis\Pages\ListInstansis;
use App\Filament\Resources\Instansis\Schemas\InstansiForm;
use App\Filament\Resources\Instansis\Tables\InstansisTable;
use App\Models\Instansi;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InstansiResource extends Resource
{
    protected static ?string $model =
        Instansi::class;

    protected static string|BackedEnum|null $navigationIcon =
        'heroicon-o-building-office';

    protected static ?string $modelLabel =
        'Instansi';

    protected static ?string $pluralModelLabel =
        'Instansi';

    protected static ?string $recordTitleAttribute =
        'nama_instansi';

    public static function form(
        Schema $schema
    ): Schema {

        return InstansiForm::configure(
            $schema
        );

    }

    public static function table(
        Table $table
    ): Table {

        return InstansisTable::configure(
            $table
        );

    }

    public static function getEloquentQuery(): Builder
    {
        $query =
            parent::getEloquentQuery();

        $user =
            auth()->user();

        if (

            $user->hasRole(
                'super_admin'
            )

            ||

            $user->hasRole(
                'admin_dinkes'
            )

        ) {

            return $query;

        }

        if (

            $user->hasRole(
                'admin_instansi'
            )

        ) {

            return $query->where(

                'id',

                $user->instansi_id

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

            'index' => ListInstansis::route('/'),

            'create' => CreateInstansi::route('/create'),

            'edit' => EditInstansi::route('/{record}/edit'),

        ];
    }

    public static function canAccess(): bool
    {
        return auth()
            ->user()
            ->hasAnyRole([

                'super_admin',

                'admin_dinkes',

            ]);
    }

    public static function canCreate(): bool
    {
        return auth()
            ->user()
            ->hasAnyRole([

                'super_admin',

                'admin_dinkes',

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
                $record->id ===
                $user->instansi_id;

        }

        return false;
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
}
