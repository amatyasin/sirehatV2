<?php

namespace App\Filament\Resources\Posyandus\Schemas;

use App\Models\Instansi;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class PosyanduForm
{
    public static function configure(
        Schema $schema
    ): Schema {

        return $schema
            ->components([

                Section::make(
                    'Informasi Posyandu'
                )

                    ->description(
                        'Data utama posyandu'
                    )

                    ->icon(
                        'heroicon-o-heart'
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

                                $user = auth()->user();

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

                                auth()->user()->hasRole(
                                    'admin_instansi'
                                )

                            )

                            ->searchable()

                            ->preload()

                            ->native(false)

                            ->required(),

                        Forms\Components\TextInput::make(
                            'nama_posyandu'
                        )

                            ->label(
                                'Nama Posyandu'
                            )

                            ->placeholder(
                                'Contoh: Posyandu Melati'
                            )

                            ->required()

                            ->maxLength(255),

                        Forms\Components\TextInput::make(
                            'penanggung_jawab'
                        )

                            ->label(
                                'Penanggung Jawab'
                            )

                            ->placeholder(
                                'Nama kader / bidan'
                            )

                            ->maxLength(255),

                        Forms\Components\TextInput::make(
                            'no_wa'
                        )

                            ->label(
                                'Nomor WhatsApp'
                            )

                            ->tel()

                            ->placeholder(
                                '08xxxxxxxxxx'
                            )

                            ->maxLength(20),

                        Forms\Components\TextInput::make(
                            'rt'
                        )

                            ->label('RT')

                            ->numeric(),

                        Forms\Components\TextInput::make(
                            'rw'
                        )

                            ->label('RW')

                            ->numeric(),

                        Forms\Components\TextInput::make(
                            'kode_pos'
                        )

                            ->label(
                                'Kode Pos'
                            )

                            ->numeric(),

                        Forms\Components\Textarea::make(
                            'alamat'
                        )

                            ->label(
                                'Alamat'
                            )

                            ->placeholder(
                                'Alamat lengkap posyandu'
                            )

                            ->rows(3)

                            ->columnSpanFull(),

                        Forms\Components\Toggle::make(
                            'aktif'
                        )

                            ->label(
                                'Posyandu Aktif'
                            )

                            ->default(true)

                            ->inline(false),

                    ])

                    ->columns(2),

            ]);
    }
}
