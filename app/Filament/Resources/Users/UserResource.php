<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\Tables\UsersTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UserResource extends Resource
{
    protected static ?string $model =
        User::class;

    protected static string|BackedEnum|null $navigationIcon =
        'heroicon-o-users';

    protected static ?string $modelLabel =
        'User';

    protected static ?string $pluralModelLabel =
        'Users';

    public static function form(
        Schema $schema
    ): Schema {

        return UserForm::configure(
            $schema
        );
    }

    public static function table(
        Table $table
    ): Table {

        return UsersTable::configure(
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

            'index' => ListUsers::route('/'),

            'create' => CreateUser::route('/create'),

            'edit' => EditUser::route('/{record}/edit'),

        ];
    }

    public static function canAccess(): bool
    {
        return auth()
            ->user()
            ->hasAnyRole([

                'super_admin',

                'admin_dinkes',

                'admin_kecamatan',

                'admin_instansi',

            ]);
    }

    public static function canViewAny(): bool
    {
        return static::canAccess();
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
                'admin_kecamatan'
            )

        ) {

            return $query->where(
                'kecamatan_id',
                $user->kecamatan_id
            );
        }

        if (

            $user->hasRole(
                'admin_instansi'
            )

        ) {

            return $query->where(
                'instansi_id',
                $user->instansi_id
            );
        }

        return $query->whereRaw(
            '1 = 0'
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

            ]);
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
            $user->hasRole(
                'super_admin'
            )
        ) {

            return true;
        }

        if (
            $user->hasRole(
                'admin_dinkes'
            )
        ) {

            return ! $record->hasRole(
                'super_admin'
            );
        }

        if (
            $user->hasRole(
                'admin_kecamatan'
            )
        ) {

            if (

                $record->hasAnyRole([

                    'super_admin',

                    'admin_dinkes',

                ])

            ) {

                return false;
            }

            return
                $record->kecamatan_id
                ===
                $user->kecamatan_id;
        }

        if (
            $user->hasRole(
                'admin_instansi'
            )
        ) {

            if (

                ! $record->hasAnyRole([

                    'admin_sekolah',

                    'petugas_posyandu',

                ])

            ) {

                return false;
            }

            return
                $record->instansi_id
                ===
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

    public static function canDeleteAny(): bool
    {
        return auth()
            ->user()
            ->hasRole(
                'super_admin'
            );
    }
}
