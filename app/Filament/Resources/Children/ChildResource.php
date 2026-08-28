<?php

namespace App\Filament\Resources\Children;

use App\Filament\Resources\Children\Pages\CreateChild;
use App\Filament\Resources\Children\Pages\EditChild;
use App\Filament\Resources\Children\Pages\ListChildren;
use App\Filament\Resources\Children\Schemas\ChildForm;
use App\Filament\Resources\Children\Tables\ChildrenTable;
use App\Models\Child;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class ChildResource extends Resource
{
    protected static ?string $model =
        Child::class;

    protected static string|BackedEnum|null $navigationIcon =
        'heroicon-o-user-group';

    protected static string|UnitEnum|null $navigationGroup =
        'Posyandu';

    protected static ?int $navigationSort =
        1;

    protected static ?string $navigationLabel =
        'Data Balita & Apras';

    protected static ?string $modelLabel =
        'Anak';

    protected static ?string $pluralModelLabel =
        'Balita & Apras';

    public static function form(
        Schema $schema
    ): Schema {

        return ChildForm::configure(
            $schema
        );
    }

    public static function infolist(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Informasi Anak')
                    ->components([
                        \Filament\Infolists\Components\TextEntry::make('nama_lengkap')
                            ->label('Nama Lengkap'),
                        \Filament\Infolists\Components\TextEntry::make('nik')
                            ->label('NIK')
                            ->copyable()
                            ->formatStateUsing(function ($state) {
                                if (stripos((string) $state, 'E') !== false) {
                                    return number_format((float) $state, 0, '', '');
                                }
                                return $state;
                            }),
                        \Filament\Infolists\Components\TextEntry::make('jenis_kelamin')
                            ->label('Jenis Kelamin')
                            ->formatStateUsing(fn ($state) => $state === 'L' ? 'Laki-laki' : 'Perempuan'),
                        \Filament\Infolists\Components\TextEntry::make('tanggal_lahir')
                            ->label('Tanggal Lahir')
                            ->date('d F Y'),
                        \Filament\Infolists\Components\TextEntry::make('umur_bulan')
                            ->label('Umur')
                            ->formatStateUsing(fn ($state) => $state . ' Bulan'),
                        \Filament\Infolists\Components\TextEntry::make('alamat')
                            ->label('Alamat'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                \Filament\Schemas\Components\Section::make('Informasi Orang Tua')
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('orangTua.nama_lengkap')
                            ->label('Nama Lengkap'),
                        \Filament\Infolists\Components\TextEntry::make('orangTua.nik')
                            ->label('NIK')
                            ->copyable()
                            ->formatStateUsing(function ($state) {
                                if (stripos((string) $state, 'E') !== false) {
                                    return number_format((float) $state, 0, '', '');
                                }
                                return $state;
                            }),
                        \Filament\Infolists\Components\TextEntry::make('orangTua.no_wa')
                            ->label('Nomor WhatsApp')
                            ->copyable(),
                        \Filament\Infolists\Components\TextEntry::make('orangTua.alamat')
                            ->label('Alamat'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(
        Table $table
    ): Table {

        return ChildrenTable::configure(
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

            $user->hasAnyRole([

                'super_admin',

                'admin_dinkes',

            ])

        ) {

            return $query;
        }

        if (

            $user->hasRole(
                'admin_kecamatan'
            )

        ) {

            return $query->whereHas(
                'posyandu.kelurahan',

                fn ($q) => $q->where(

                    'kecamatan_id',

                    $user->kecamatan_id

                )

            );
        }

        if (

            $user->hasRole(
                'admin_instansi'
            )

        ) {

            return $query->whereHas(

                'posyandu',

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

            return $query->where(

                'posyandu_id',

                $user->posyandu_id

            );
        }

        return $query->whereRaw(
            '1 = 0'
        );
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Resources\Children\RelationManagers\PemeriksaanBalitasRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [

            'index' => ListChildren::route('/'),

            'create' => CreateChild::route('/create'),
            
            'view' => \App\Filament\Resources\Children\Pages\ViewChild::route('/{record}'),

            'edit' => EditChild::route('/{record}/edit'),

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

    public static function canViewAny(): bool
    {
        return static::canAccess();
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
                $record->posyandu?->instansi_id
                ===
                $user->instansi_id;
        }

        if (

            $user->hasRole(
                'petugas_posyandu'
            )

        ) {

            return
                $record->posyandu_id
                ===
                $user->posyandu_id;
        }

        return false;
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
                $record->posyandu?->instansi_id
                ===
                $user->instansi_id;
        }

        if (

            $user->hasRole(
                'petugas_posyandu'
            )

        ) {

            return
                $record->posyandu_id
                ===
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
