<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\Instansi;
use App\Models\Kecamatan;
use App\Models\Posyandu;
use App\Models\School;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(
        Schema $schema
    ): Schema {

        return $schema
            ->components([

                Section::make(
                    'Informasi User'
                )

                    ->schema([

                        Forms\Components\TextInput::make(
                            'name'
                        )

                            ->label('Nama')

                            ->required()

                            ->maxLength(255),

                        Forms\Components\TextInput::make(
                            'email'
                        )

                            ->label('Email')

                            ->email()

                            ->required()

                            ->unique(
                                ignoreRecord: true
                            ),

                        Forms\Components\TextInput::make(
                            'password'
                        )

                            ->label('Password')

                            ->password()

                            ->revealable()

                            ->required(
                                fn (
                                    string $operation
                                ) => $operation === 'create'
                            )

                            ->dehydrated(
                                fn ($state) => filled($state)
                            )

                            ->dehydrateStateUsing(
                                fn ($state) => bcrypt($state)
                            )

                            ->helperText(
                                'Kosongkan jika tidak ingin mengganti password'
                            ),

                        Forms\Components\Select::make(
                            'role'
                        )

                            ->label('Role')

                            ->options(function () {

                                $user =
                                    auth()->user();

                                if (

                                    $user->hasRole(
                                        'super_admin'
                                    )

                                ) {

                                    return [

                                        'super_admin' => 'Super Admin',

                                        'admin_dinkes' => 'Admin Dinkes',

                                        'admin_kecamatan' => 'Admin Kecamatan',

                                        'admin_instansi' => 'Admin Puskesmas',

                                        'admin_sekolah' => 'Admin Sekolah',

                                        'petugas_posyandu' => 'Petugas Posyandu',

                                    ];
                                }

                                if (

                                    $user->hasRole(
                                        'admin_dinkes'
                                    )

                                ) {

                                    return [

                                        'admin_kecamatan' => 'Admin Kecamatan',

                                        'admin_instansi' => 'Admin Puskesmas',

                                        'admin_sekolah' => 'Admin Sekolah',

                                        'petugas_posyandu' => 'Petugas Posyandu',

                                    ];
                                }

                                if (

                                    $user->hasRole(
                                        'admin_instansi'
                                    )

                                ) {

                                    return [

                                        'admin_sekolah' => 'Admin Sekolah',

                                        'petugas_posyandu' => 'Petugas Posyandu',

                                    ];
                                }

                                return [];

                            })

                            ->searchable()

                            ->live()

                            ->required()

                            ->dehydrated(false)

                            ->afterStateUpdated(
                                fn (
                                    callable $set
                                ) => [

                                    $set(
                                        'kecamatan_id',
                                        null
                                    ),

                                    $set(
                                        'instansi_id',
                                        null
                                    ),

                                    $set(
                                        'school_id',
                                        null
                                    ),

                                    $set(
                                        'posyandu_id',
                                        null
                                    ),

                                ]
                            ),

                        Forms\Components\Select::make(
                            'kecamatan_id'
                        )

                            ->label('Kecamatan')

                            ->options(function () {

                                $user =
                                    auth()->user();

                                $query =
                                    Kecamatan::query();

                                if (

                                    $user->hasRole(
                                        'admin_kecamatan'
                                    )

                                ) {

                                    $query->where(
                                        'id',
                                        $user->kecamatan_id
                                    );

                                }

                                return $query->pluck(
                                    'nama_kecamatan',
                                    'id'
                                );

                            })

                            ->searchable()

                            ->preload()

                            ->default(
                                auth()
                                    ->user()
                                    ->kecamatan_id
                            )

                            ->disabled(
                                auth()
                                    ->user()
                                    ->hasRole(
                                        'admin_kecamatan'
                                    )
                            )

                            ->visible(function (
                                Get $get
                            ) {

                                return in_array(

                                    $get('role'),

                                    [

                                        'admin_kecamatan',

                                        'admin_instansi',

                                    ]

                                );

                            }),

                        Forms\Components\Select::make(
                            'instansi_id'
                        )

                            ->label('Puskesmas')

                            ->options(function () {

                                $user =
                                    auth()->user();

                                $query =
                                    Instansi::query();

                                if (

                                    $user->hasRole(
                                        'admin_instansi'
                                    )

                                ) {

                                    $query->where(
                                        'id',
                                        $user->instansi_id
                                    );

                                }

                                return $query->pluck(
                                    'nama_instansi',
                                    'id'
                                );

                            })

                            ->searchable()

                            ->preload()

                            ->live()

                            ->default(
                                auth()
                                    ->user()
                                    ->instansi_id
                            )

                            ->disabled(
                                auth()
                                    ->user()
                                    ->hasAnyRole([

                                        'admin_instansi',

                                        'admin_sekolah',

                                        'petugas_posyandu',

                                    ])
                            )

                            ->visible(function (
                                Get $get
                            ) {

                                return in_array(

                                    $get('role'),

                                    [

                                        'admin_instansi',

                                        'admin_sekolah',

                                        'petugas_posyandu',

                                    ]

                                );

                            })

                            ->afterStateUpdated(
                                fn (
                                    callable $set
                                ) => [

                                    $set(
                                        'school_id',
                                        null
                                    ),

                                    $set(
                                        'posyandu_id',
                                        null
                                    ),

                                ]
                            ),

                        Forms\Components\Select::make(
                            'school_id'
                        )

                            ->label('Sekolah')

                            ->options(function (
                                Get $get
                            ) {

                                $user =
                                    auth()->user();

                                $query =
                                    School::query();

                                if (

                                    $user->hasRole(
                                        'admin_instansi'
                                    )

                                ) {

                                    $query->where(
                                        'instansi_id',
                                        $user->instansi_id
                                    );

                                }

                                if (

                                    $get(
                                        'instansi_id'
                                    )

                                ) {

                                    $query->where(
                                        'instansi_id',
                                        $get(
                                            'instansi_id'
                                        )
                                    );

                                }

                                return $query->pluck(
                                    'nama_sekolah',
                                    'id'
                                );

                            })

                            ->searchable()

                            ->preload()

                            ->default(
                                auth()
                                    ->user()
                                    ->school_id
                            )

                            ->disabled(
                                auth()
                                    ->user()
                                    ->hasRole(
                                        'admin_sekolah'
                                    )
                            )

                            ->visible(function (
                                Get $get
                            ) {

                                return $get(
                                    'role'
                                ) === 'admin_sekolah';

                            }),

                        Forms\Components\Select::make(
                            'posyandu_id'
                        )

                            ->label('Posyandu')

                            ->options(function (
                                Get $get
                            ) {

                                $user =
                                    auth()->user();

                                $query =
                                    Posyandu::query();

                                if (

                                    $user->hasRole(
                                        'admin_instansi'
                                    )

                                ) {

                                    $query->where(
                                        'instansi_id',
                                        $user->instansi_id
                                    );

                                }

                                if (

                                    $get(
                                        'instansi_id'
                                    )

                                ) {

                                    $query->where(
                                        'instansi_id',
                                        $get(
                                            'instansi_id'
                                        )
                                    );

                                }

                                return $query->pluck(
                                    'nama_posyandu',
                                    'id'
                                );

                            })

                            ->searchable()

                            ->preload()

                            ->default(
                                auth()
                                    ->user()
                                    ->posyandu_id
                            )

                            ->disabled(
                                auth()
                                    ->user()
                                    ->hasRole(
                                        'petugas_posyandu'
                                    )
                            )

                            ->visible(function (
                                Get $get
                            ) {

                                return $get(
                                    'role'
                                ) === 'petugas_posyandu';

                            }),

                    ])

                    ->columns(2),

            ]);
    }
}
