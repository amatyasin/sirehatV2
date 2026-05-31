<?php

namespace App\Filament\Resources\SchoolClasses\Schemas;

use App\Models\School;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SchoolClassForm
{
    public static function configure(
        Schema $schema
    ): Schema {

        return $schema
            ->components([

                Section::make(
                    'Informasi Kelas'
                )

                    ->schema([

                        Forms\Components\Select::make(
                            'school_id'
                        )
                            ->label(
                                'Sekolah'
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

                                    return School::orderBy(
                                        'nama_sekolah'
                                    )->pluck(
                                        'nama_sekolah',
                                        'id'
                                    );
                                }

                                if (

                                    $user->hasRole(
                                        'admin_instansi'
                                    )

                                ) {

                                    return School::where(
                                        'instansi_id',
                                        $user->instansi_id
                                    )
                                        ->orderBy(
                                            'nama_sekolah'
                                        )
                                        ->pluck(
                                            'nama_sekolah',
                                            'id'
                                        );
                                }

                                return School::where(
                                    'id',
                                    $user->school_id
                                )->pluck(
                                    'nama_sekolah',
                                    'id'
                                );

                            })
                            ->default(
                                auth()->user()->school_id
                            )
                            ->disabled(

                                auth()
                                    ->user()
                                    ->hasRole(
                                        'admin_sekolah'
                                    )

                            )
                            ->dehydrated(true)
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\TextInput::make(
                            'nama_kelas'
                        )

                            ->label(
                                'Nama Kelas'
                            )

                            ->required()

                            ->maxLength(100),

                        Forms\Components\TextInput::make(
                            'urutan'
                        )

                            ->label(
                                'Urutan'
                            )

                            ->required()

                            ->numeric()

                            ->default(1),

                    ])

                    ->columns(2),

            ]);
    }
}
