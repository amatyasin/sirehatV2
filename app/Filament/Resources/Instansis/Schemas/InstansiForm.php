<?php

namespace App\Filament\Resources\Instansis\Schemas;

use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class InstansiForm
{
    public static function configure(
        Schema $schema
    ): Schema {

        return $schema
            ->components([

                Section::make(
                    'Informasi Instansi'
                )

                    ->description(
                        'Data puskesmas / instansi kesehatan'
                    )

                    ->schema([

                        Forms\Components\TextInput::make(
                            'nama_instansi'
                        )

                            ->label(
                                'Nama Instansi'
                            )

                            ->required()

                            ->maxLength(255)

                            ->unique(
                                ignoreRecord: true
                            )

                            ->placeholder(
                                'Contoh: Puskesmas Pasundan'
                            ),

                        Forms\Components\TextInput::make(
                            'telepon'
                        )

                            ->label(
                                'No. Telepon'
                            )

                            ->tel()

                            ->maxLength(20)

                            ->placeholder(
                                '08123456789'
                            ),

                        Forms\Components\Toggle::make(
                            'status'
                        )

                            ->label(
                                'Aktif'
                            )

                            ->default(true)

                            ->inline(false),

                        Forms\Components\Textarea::make(
                            'alamat'
                        )

                            ->label(
                                'Alamat'
                            )

                            ->rows(4)

                            ->columnSpanFull()

                            ->placeholder(
                                'Masukkan alamat instansi'
                            ),

                    ])

                    ->columns(2),

            ]);
    }
}
