<?php

namespace App\Filament\Resources\PemeriksaanMatas\Schemas;

use App\Models\StudentClassHistory;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class PemeriksaanMataForm
{
    public static function configure(
        Schema $schema
    ): Schema {

        return $schema
            ->components([

                Section::make(
                    'Identitas Siswa'
                )

                    ->description(
                        'Informasi dasar siswa'
                    )

                    ->icon(
                        'heroicon-o-user'
                    )

                    ->schema([

                        Forms\Components\Select::make(
                            'student_class_history_id'
                        )

                            ->label('Siswa')

                            ->disabledOn('edit')

                            ->dehydrated(
                                fn ($operation) => $operation === 'create'
                            )

                            ->unique(

                                table: 'pemeriksaan_matas',

                                column: 'student_class_history_id',

                                ignoreRecord: true

                            )

                            ->validationMessages([

                                'unique' => 'Pemeriksaan mata siswa pada semester ini sudah ada.',

                            ])

                            ->options(function () {

                                $user =
                                    auth()->user();

                                $query =
                                    StudentClassHistory::query()

                                        ->with([

                                            'student',

                                            'school',

                                            'schoolClass',

                                            'academicYear',

                                        ])

                                        ->where(
                                            'aktif',
                                            true
                                        );

                                if (

                                    $user->hasRole(
                                        'admin_instansi'
                                    )

                                ) {

                                    $query->whereHas(

                                        'school',

                                        fn ($q) => $q->where(
                                            'instansi_id',
                                            $user->instansi_id
                                        )

                                    );
                                }

                                if (

                                    $user->hasRole(
                                        'admin_sekolah'
                                    )

                                ) {

                                    $query->where(
                                        'school_id',
                                        $user->school_id
                                    );
                                }

                                return $query

                                    ->orderByDesc(
                                        'id'
                                    )

                                    ->get()

                                    ->mapWithKeys(function (
                                        $item
                                    ) {

                                        return [

                                            $item->id => $item
                                                ->school
                                                ?->nama_sekolah

                                                .' | '

                                                .$item
                                                    ->schoolClass
                                                    ?->nama_kelas

                                                .' | '

                                                .$item
                                                    ->semester

                                                .' | '

                                                .$item
                                                    ->student
                                                    ?->nama_lengkap,

                                        ];

                                    });

                            })

                            ->searchable()

                            ->preload()

                            ->live()

                            ->afterStateUpdated(function (
                                $state,
                                Set $set
                            ) {

                                $history =
                                    StudentClassHistory::with([

                                        'student',

                                        'school',

                                        'schoolClass',

                                        'academicYear',

                                    ])->find($state);

                                if (! $history) {
                                    return;
                                }

                                $student =
                                    $history->student;

                                $set(
                                    'nisn',
                                    $student?->nisn
                                );

                                $set(
                                    'kelas',
                                    $history
                                        ->schoolClass
                                        ?->nama_kelas
                                );

                                $set(
                                    'semester',
                                    $history
                                        ?->semester
                                );

                                $set(
                                    'tahun_ajaran',
                                    $history
                                        ->academicYear
                                        ?->nama
                                );

                                $set(
                                    'jenis_kelamin',
                                    $student
                                        ?->jenis_kelamin
                                );

                                $set(
                                    'alamat',
                                    $student
                                        ?->alamat
                                );

                                $set(
                                    'sekolah',
                                    $history
                                        ->school
                                        ?->nama_sekolah
                                );

                                $umur =
                                    $student?->tanggal_lahir

                                    ?

                                    Carbon::parse(
                                        $student
                                            ->tanggal_lahir
                                    )->age

                                    : null;

                                $set(
                                    'umur',
                                    $umur
                                );

                            })

                            ->required(),

                        Forms\Components\DatePicker::make(
                            'tanggal_pemeriksaan'
                        )

                            ->label(
                                'Tanggal Pemeriksaan'
                            )

                            ->default(
                                now()
                            )

                            ->required(),

                        Forms\Components\TextInput::make(
                            'nisn'
                        )

                            ->label('NISN')

                            ->disabled()

                            ->dehydrated(false),

                        Forms\Components\TextInput::make(
                            'kelas'
                        )

                            ->disabled()

                            ->dehydrated(false),

                        Forms\Components\TextInput::make(
                            'semester'
                        )

                            ->disabled()

                            ->dehydrated(false),

                        Forms\Components\TextInput::make(
                            'tahun_ajaran'
                        )

                            ->label(
                                'Tahun Ajaran'
                            )

                            ->disabled()

                            ->dehydrated(false),

                        Forms\Components\TextInput::make(
                            'umur'
                        )

                            ->disabled()

                            ->dehydrated(false)

                            ->suffix(
                                'tahun'
                            ),

                        Forms\Components\TextInput::make(
                            'sekolah'
                        )

                            ->disabled()

                            ->dehydrated(false),

                        Forms\Components\Select::make(
                            'jenis_kelamin'
                        )

                            ->options([

                                'L' => 'Laki-laki',

                                'P' => 'Perempuan',

                            ])

                            ->disabled()

                            ->dehydrated(false),

                        Forms\Components\Textarea::make(
                            'alamat'
                        )

                            ->disabled()

                            ->dehydrated(false)

                            ->columnSpanFull(),

                    ])

                    ->columns(2),

                Section::make(
                    'Pemeriksaan Penglihatan'
                )

                    ->description(
                        'Pemeriksaan fungsi penglihatan siswa'
                    )

                    ->icon(
                        'heroicon-o-eye'
                    )

                    ->schema([

                        Forms\Components\Select::make(
                            'visus_kanan'
                        )

                            ->label(
                                'Visus Mata Kanan'
                            )

                            ->options([

                                '6/6' => '6/6 Normal',

                                '6/9' => '6/9',

                                '6/12' => '6/12',

                                '6/18' => '6/18',

                                '6/60' => '6/60',

                            ])

                            ->default('6/6')

                            ->required()

                            ->native(false)

                            ->live(),

                        Forms\Components\Select::make(
                            'visus_kiri'
                        )

                            ->label(
                                'Visus Mata Kiri'
                            )

                            ->options([

                                '6/6' => '6/6 Normal',

                                '6/9' => '6/9',

                                '6/12' => '6/12',

                                '6/18' => '6/18',

                                '6/60' => '6/60',

                            ])

                            ->default('6/6')

                            ->required()

                            ->native(false)

                            ->live(),

                        Forms\Components\Radio::make(
                            'pakai_kacamata'
                        )

                            ->label(
                                'Menggunakan Kacamata'
                            )

                            ->options([

                                'Y' => 'Ya',

                                'N' => 'Tidak',

                            ])

                            ->default('N')

                            ->required()

                            ->inline(),

                        Forms\Components\Radio::make(
                            'buta_warna'
                        )

                            ->label(
                                'Buta Warna'
                            )

                            ->options([

                                'Y' => 'Ya',

                                'N' => 'Tidak',

                            ])

                            ->default('N')

                            ->required()

                            ->inline(),

                        Forms\Components\Radio::make(
                            'mata_merah'
                        )

                            ->label(
                                'Mata Merah'
                            )

                            ->options([

                                'Y' => 'Ya',

                                'N' => 'Tidak',

                            ])

                            ->default('N')

                            ->inline(),

                        Forms\Components\Radio::make(
                            'mata_berair'
                        )

                            ->label(
                                'Mata Berair'
                            )

                            ->options([

                                'Y' => 'Ya',

                                'N' => 'Tidak',

                            ])

                            ->default('N')

                            ->inline(),

                        Forms\Components\Radio::make(
                            'nyeri_mata'
                        )

                            ->label(
                                'Mata Nyeri'
                            )

                            ->options([

                                'Y' => 'Ya',

                                'N' => 'Tidak',

                            ])

                            ->default('N')

                            ->inline(),

                        Forms\Components\Radio::make(
                            'gatal_mata'
                        )

                            ->label(
                                'Mata Gatal'
                            )

                            ->options([

                                'Y' => 'Ya',

                                'N' => 'Tidak',

                            ])

                            ->default('N')

                            ->inline(),

                        Forms\Components\Radio::make(
                            'mata_bengkak'
                        )

                            ->label(
                                'Mata Bengkak'
                            )

                            ->options([

                                'Y' => 'Ya',

                                'N' => 'Tidak',

                            ])

                            ->default('N')

                            ->inline(),

                        Forms\Components\Radio::make(
                            'mata_belekan'
                        )

                            ->label(
                                'Mata Belekan'
                            )

                            ->options([

                                'Y' => 'Ya',

                                'N' => 'Tidak',

                            ])

                            ->default('N')

                            ->inline(),

                    ])

                    ->columns(2),

                Section::make(
                    'Rujukan'
                )

                    ->icon(
                        'heroicon-o-building-office-2'
                    )

                    ->schema([

                        Forms\Components\Radio::make(
                            'dirujuk_ke_fasyankes'
                        )

                            ->label(
                                'Apakah Dirujuk ke Fasyankes'
                            )

                            ->options([

                                'Y' => 'Ya',

                                'N' => 'Tidak',

                            ])

                            ->default('N')

                            ->inline()

                            ->live(),

                        Forms\Components\Textarea::make(
                            'keterangan_rujukan'
                        )

                            ->label(
                                'Keterangan Rujukan'
                            )

                            ->visible(fn (Get $get) => $get(
                                'dirujuk_ke_fasyankes'
                            ) === 'Y'

                            )

                            ->columnSpanFull(),

                    ])

                    ->columns(2),

            ]);
    }

    protected static function calculateHasil(
        Get $get,
        Set $set
    ): void {

        $kanan =
            $get('visus_kanan');

        $kiri =
            $get('visus_kiri');

        if (! $kanan || ! $kiri) {

            $set(
                'hasil_pemeriksaan',
                null
            );

            return;
        }

        $normal =
            ['6/6', '6/9'];

        if (

            in_array($kanan, $normal)

            &&

            in_array($kiri, $normal)

        ) {

            $hasil =
                'Normal';

        } elseif (

            $kanan === '6/12'

            ||

            $kiri === '6/12'

        ) {

            $hasil =
                'Gangguan Refraksi';

        } else {

            $hasil =
                'Gangguan Penglihatan';
        }

        $set(
            'hasil_pemeriksaan',
            $hasil
        );
    }
}
