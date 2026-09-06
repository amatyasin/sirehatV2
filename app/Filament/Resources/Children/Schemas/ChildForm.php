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
                                        ->with([
                                            'instansi',
                                            'kelurahan',
                                        ]);

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

                                    ->mapWithKeys(function ($item) {
                                        $puskesmas = $item->instansi?->nama_instansi;
                                        $kelurahan = $item->kelurahan?->nama_kelurahan;

                                        if ($puskesmas && $kelurahan) {
                                            $label = "{$item->nama_posyandu} - {$puskesmas} (Kel. {$kelurahan})";
                                        } elseif ($puskesmas) {
                                            $label = "{$item->nama_posyandu} - {$puskesmas}";
                                        } elseif ($kelurahan) {
                                            $label = "{$item->nama_posyandu} - Kel. {$kelurahan}";
                                        } else {
                                            $label = $item->nama_posyandu;
                                        }

                                        return [$item->id => $label];
                                    });

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
                                $set('orang_tua_id', null);

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

                            ->options(function (Get $get) {
                                $posyanduId = $get('posyandu_id');

                                if (! $posyanduId) {
                                    return [];
                                }

                                return OrangTua::query()
                                    ->where('posyandu_id', $posyanduId)
                                    ->orderBy('nama_lengkap')
                                    ->get()
                                    ->mapWithKeys(function ($item) {
                                        $label = $item->nama_lengkap;
                                        if ($item->nik) {
                                            $label .= ' (NIK: ' . $item->nik . ')';
                                        }
                                        if ($item->no_wa) {
                                            $label .= ' - HP: ' . $item->no_wa;
                                        }
                                        return [$item->id => $label];
                                    });
                            })

                            ->createOptionForm([
                                Forms\Components\TextInput::make('nama_lengkap')
                                    ->label('Nama Orang Tua')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('nik')
                                    ->label('NIK Orang Tua')
                                    ->rule('numeric')
                                    ->minLength(16)
                                    ->maxLength(16)
                                    ->unique('orang_tuas', 'nik')
                                    ->validationMessages([
                                        'unique' => 'NIK orang tua sudah terdaftar.',
                                        'minLength' => 'NIK harus 16 digit.',
                                        'maxLength' => 'NIK harus 16 digit.',
                                    ]),

                                Forms\Components\TextInput::make('no_wa')
                                    ->label('Nomor HP / WhatsApp')
                                    ->tel()
                                    ->maxLength(20),

                                Forms\Components\Textarea::make('alamat')
                                    ->label('Alamat')
                                    ->rows(3),
                            ])

                            ->createOptionAction(function ($action, Get $get) {
                                return $action
                                    ->modalHeading('Buat Orang Tua Baru')
                                    ->modalSubmitActionLabel('Simpan Orang Tua')
                                    ->modalWidth('md')
                                    ->disabled(! $get('posyandu_id'));
                            })

                            ->createOptionUsing(function (array $data, Get $get) {
                                $posyanduId = $get('posyandu_id');
                                if (! $posyanduId) {
                                    throw new \Exception('Posyandu harus dipilih terlebih dahulu.');
                                }

                                $posyandu = Posyandu::find($posyanduId);

                                $data['posyandu_id'] = $posyanduId;
                                $data['instansi_id'] = $posyandu?->instansi_id;

                                return OrangTua::create($data)->id;
                            })

                            ->helperText(function (Get $get) {
                                if (! $get('posyandu_id')) {
                                    return '⚠️ Pilih Posyandu terlebih dahulu untuk memilih atau membuat Orang Tua baru.';
                                }
                                return null;
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

                                if ($orangTua->alamat) {
                                    $set(
                                        'alamat',
                                        $orangTua->alamat
                                    );
                                }
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

                        Forms\Components\TextInput::make('nik')
                            ->label('NIK Anak')
                            ->tel()
                            ->unique(ignoreRecord: true)
                            ->validationMessages([
                                'unique' => 'NIK anak sudah digunakan.',
                            ])
                            ->minLength(16)
                            ->maxLength(16)
                            ->rule('numeric'),

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

                                $tanggalLahirStr = $get('tanggal_lahir');
                                if (! $tanggalLahirStr) {
                                    return '-';
                                }

                                try {
                                    $tanggalLahir = Carbon::parse($tanggalLahirStr);
                                    if ($tanggalLahir->isFuture()) {
                                        return 'Tanggal lahir tidak boleh di masa mendatang';
                                    }

                                    $now = now()->startOfDay();
                                    $diffDays = (int) $tanggalLahir->diffInDays($now);
                                    $diffMonths = (int) $tanggalLahir->diffInMonths($now);
                                    $diffYears = (int) $tanggalLahir->diffInYears($now);

                                    if ($diffYears >= 1) {
                                        $remMonths = $diffMonths % 12;
                                        return $remMonths > 0 ? "{$diffYears} tahun {$remMonths} bulan" : "{$diffYears} tahun";
                                    } elseif ($diffMonths >= 1) {
                                        return "{$diffMonths} bulan";
                                    } else {
                                        return "{$diffDays} hari";
                                    }
                                } catch (\Throwable $e) {
                                    return '-';
                                }
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
