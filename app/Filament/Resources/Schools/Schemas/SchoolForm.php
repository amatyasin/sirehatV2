<?php

namespace App\Filament\Resources\Schools\Schemas;

use App\Models\Instansi;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class SchoolForm
{
    public static function configure(
        Schema $schema
    ): Schema {

        return $schema
            ->components([

                Section::make(
                    'Informasi Sekolah'
                )

                    ->schema([

                        Forms\Components\Select::make(
                            'kecamatan_id'
                        )

                            ->label(
                                'Kecamatan'
                            )

                            ->options(

                                Kecamatan::pluck(
                                    'nama_kecamatan',
                                    'id'
                                )

                            )

                            ->searchable()

                            ->preload()

                            ->native(false)

                            ->live()

                            ->afterStateUpdated(
                                fn (
                                    callable $set
                                ) => [

                                    $set(
                                        'kelurahan_id',
                                        null
                                    ),

                                ]
                            )

                            ->required(),

                        Forms\Components\Select::make(
                            'kelurahan_id'
                        )

                            ->label(
                                'Kelurahan'
                            )

                            ->options(

                                fn (Get $get) => Kelurahan::query()
                                    ->when(

                                        $get(
                                            'kecamatan_id'
                                        ),

                                        fn ($query, $id) => $query->where(
                                            'kecamatan_id',
                                            $id
                                        )

                                    )
                                    ->pluck(
                                        'nama_kelurahan',
                                        'id'
                                    )

                            )

                            ->searchable()

                            ->preload()

                            ->native(false)

                            ->required(),

                        Forms\Components\Select::make(
                            'instansi_id'
                        )

                            ->label(
                                'Puskesmas'
                            )

                            ->options(function () {

                                $user =
                                    auth()->user();

                                if (

                                    $user->hasAnyRole([

                                        'super_admin',

                                        'admin_dinkes',

                                    ])

                                ) {

                                    return Instansi::pluck(
                                        'nama_instansi',
                                        'id'
                                    );
                                }

                                return Instansi::where(
                                    'id',
                                    $user->instansi_id
                                )->pluck(
                                    'nama_instansi',
                                    'id'
                                );
                            })

                            ->default(
                                auth()->user()->instansi_id
                            )

                            ->disabled(

                                auth()
                                    ->user()
                                    ->hasRole(
                                        'admin_instansi'
                                    )

                            )
                             ->dehydrated() 

                            ->searchable()

                            ->preload()

                            ->native(false)

                            ->required(),

                        Forms\Components\TextInput::make(
                            'nama_sekolah'
                        )

                            ->label(
                                'Nama Sekolah'
                            )

                            ->required()

                            ->maxLength(255),

                        Forms\Components\TextInput::make(
                            'npsn'
                        )

                            ->label('NPSN')

                            ->maxLength(50),

                        Forms\Components\Textarea::make(
                            'alamat'
                        )

                            ->label(
                                'Alamat'
                            )

                            ->rows(3)

                            ->columnSpanFull(),

                    ])

                    ->columns(2),

            ]);
    }
}
