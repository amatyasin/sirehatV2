<?php

namespace App\Filament\Resources\Children\Schemas;

use App\Models\OrangTua;
use App\Models\Posyandu;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class ChildForm
{
    public static function configure(
        Schema $schema
    ): Schema {

        return $schema
            ->components([

                Section::make(
                    'Informasi Anak'
                )

                    ->description(
                        'Data identitas anak posyandu'
                    )

                    ->schema([

                        Forms\Components\Select::make(
                            'posyandu_id'
                        )

                            ->label(
                                'Posyandu'
                            )

                            ->options(function () {

                                $user =
                                    auth()->user();

                                $query =
                                    Posyandu::query()
                                        ->with(
                                            'kelurahan'
                                        );

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

                                    $user->hasRole(
                                        'petugas_posyandu'
                                    )

                                ) {

                                    $query->where(

                                        'id',

                                        $user->posyandu_id

                                    );
                                }

                                return $query

                                    ->orderBy(
                                        'nama_posyandu'
                                    )

                                    ->get()

                                    ->mapWithKeys(
                                        fn ($item) => [

                                            $item->id => $item->nama_posyandu

                                                .' - '.

                                                (

                                                    $item
                                                        ->kelurahan
                                                        ?->nama_kelurahan

                                                    ?? '-'

                                                ),

                                        ]
                                    );

                            })

                            ->default(
                                auth()->user()->posyandu_id
                            )

                            ->disabled(

                                auth()
                                    ->user()
                                    ->hasRole(
                                        'petugas_posyandu'
                                    )

                            )

                            ->searchable()

                            ->preload()

                            ->native(false)

                            ->live()

                            ->afterStateUpdated(function (
                                $state,
                                Set $set
                            ) {

                                $posyandu =
                                    Posyandu::find(
                                        $state
                                    );

                                if (! $posyandu) {
                                    return;
                                }

                                $set(

                                    'instansi_id',

                                    $posyandu->instansi_id

                                );

                            })

                            ->required(),

                        Forms\Components\Hidden::make(
                            'instansi_id'
                        )

                            ->dehydrated()

                            ->default(
                                auth()->user()->instansi_id
                            ),

                        Forms\Components\Select::make(
                            'orang_tua_id'
                        )

                            ->label(
                                'Orang Tua'
                            )

                            ->options(function () {

                                $user =
                                    auth()->user();

                                $query =
                                    OrangTua::query();

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

                                    $user->hasRole(
                                        'petugas_posyandu'
                                    )

                                ) {

                                    $query->whereHas(

                                        'children',

                                        fn ($q) => $q->where(

                                            'posyandu_id',

                                            $user->posyandu_id

                                        )

                                    );
                                }

                                return $query

                                    ->orderBy(
                                        'nama_lengkap'
                                    )

                                    ->get()

                                    ->mapWithKeys(
                                        fn ($item) => [

                                            $item->id => $item->nama_lengkap

                                                .' | '.

                                                (

                                                    $item->no_wa
                                                    ?? '-'

                                                ),

                                        ]
                                    );

                            })

                            ->searchable()

                            ->preload()

                            ->native(false)

                            ->live()

                            ->afterStateUpdated(function (
                                $state,
                                Set $set
                            ) {

                                $orangTua =
                                    OrangTua::find(
                                        $state
                                    );

                                if (! $orangTua) {
                                    return;
                                }

                                $set(
                                    'alamat',
                                    $orangTua->alamat
                                );

                            })

                            ->required(),

                        Forms\Components\TextInput::make(
                            'nama_lengkap'
                        )

                            ->label(
                                'Nama Anak'
                            )

                            ->required()

                            ->maxLength(255),

                        Forms\Components\TextInput::make(
                            'nik'
                        )

                            ->label(
                                'NIK Anak'
                            )

                            ->tel()

                            ->unique(
                                ignoreRecord: true
                            )

                            ->validationMessages([

                                'unique' => 'NIK anak sudah digunakan.',

                            ])

                            ->minLength(16)

                            ->maxLength(16)

                            ->numeric(),

                        Forms\Components\Select::make(
                            'jenis_kelamin'
                        )

                            ->label(
                                'Jenis Kelamin'
                            )

                            ->options([

                                'L' => 'Laki-laki',

                                'P' => 'Perempuan',

                            ])

                            ->native(false)

                            ->required(),

                        Forms\Components\DatePicker::make(
                            'tanggal_lahir'
                        )

                            ->label(
                                'Tanggal Lahir'
                            )

                            ->maxDate(
                                now()
                            )

                            ->live()

                            ->required(),

                        Placeholder::make(
                            'umur_bulan'
                        )

                            ->label(
                                'Umur'
                            )

                            ->content(function (
                                Get $get
                            ) {

                                if (! $get(
                                    'tanggal_lahir'
                                )) {

                                    return '-';
                                }

                                $tanggalLahir =
                                    Carbon::parse(

                                        $get(
                                            'tanggal_lahir'
                                        )

                                    );

                                $tahun =
                                    $tanggalLahir
                                        ->diff(now())
                                        ->y;

                                $bulan =
                                    $tanggalLahir
                                        ->diff(now())
                                        ->m;

                                return

                                    $tahun
                                    .' tahun '

                                    .

                                    $bulan
                                    .' bulan';

                            }),

                        Forms\Components\Textarea::make(
                            'alamat'
                        )

                            ->label(
                                'Alamat'
                            )

                            ->rows(3)

                            ->columnSpanFull(),

                        Forms\Components\Toggle::make(
                            'aktif'
                        )

                            ->label(
                                'Status Aktif'
                            )

                            ->default(true),

                    ])

                    ->columns(2),

            ]);
    }
}
