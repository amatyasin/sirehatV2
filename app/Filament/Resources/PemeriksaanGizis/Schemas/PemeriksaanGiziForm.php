<?php

namespace App\Filament\Resources\PemeriksaanGizis\Schemas;

use App\Models\StudentClassHistory;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class PemeriksaanGiziForm
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

                            ->label(
                                'Siswa'
                            )

                            ->disabledOn('edit')

                            ->dehydrated(
                                fn ($operation) => $operation === 'create'
                            )

                            ->unique(

                                table: 'pemeriksaan_gizis',

                                column: 'student_class_history_id',

                                ignoreRecord: true

                            )

                            ->validationMessages([

                                'unique' => 'Pemeriksaan gizi semester ini sudah ada.',

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

                                    $user->hasAnyRole([

                                        'admin_instansi',

                                        'petugas_pemeriksaan',

                                    ])

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
                                    $student
                                        ?->tanggal_lahir

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

                            ->maxDate(
                                now()
                            )

                            ->required(),

                        Forms\Components\TextInput::make(
                            'nisn'
                        )

                            ->label('NISN')

                            ->disabled(),

                        Forms\Components\TextInput::make(
                            'kelas'
                        )

                            ->disabled(),

                        Forms\Components\TextInput::make(
                            'semester'
                        )

                            ->disabled(),

                        Forms\Components\TextInput::make(
                            'tahun_ajaran'
                        )

                            ->label(
                                'Tahun Ajaran'
                            )

                            ->disabled(),

                        Forms\Components\TextInput::make(
                            'umur'
                        )

                            ->disabled()

                            ->suffix(
                                'tahun'
                            ),

                        Forms\Components\TextInput::make(
                            'sekolah'
                        )

                            ->disabled(),

                        Forms\Components\Select::make(
                            'jenis_kelamin'
                        )

                            ->options([

                                'L' => 'Laki-laki',

                                'P' => 'Perempuan',

                            ])

                            ->disabled(),

                        Forms\Components\Textarea::make(
                            'alamat'
                        )

                            ->disabled()

                            ->columnSpanFull(),

                    ])

                    ->columns(2),

                Section::make(
                    'Pengukuran Fisik'
                )

                    ->description(
                        'Pengukuran antropometri siswa'
                    )

                    ->icon(
                        'heroicon-o-chart-bar'
                    )

                    ->schema([

                        Forms\Components\TextInput::make(
                            'berat_badan'
                        )

                            ->label(
                                'Berat Badan'
                            )

                            ->placeholder(
                                'Contoh: 45.5'
                            )

                            ->numeric()

                            ->minValue(1)

                            ->maxValue(300)

                            ->step(0.1)

                            ->suffix('kg')

                            ->required()

                            ->live()

                            ->afterStateUpdated(function (
                                Get $get,
                                Set $set
                            ) {

                                self::calculateImt(
                                    $get,
                                    $set
                                );

                            }),

                        Forms\Components\TextInput::make(
                            'tinggi_badan'
                        )

                            ->label(
                                'Tinggi Badan'
                            )

                            ->placeholder(
                                'Contoh: 160.5'
                            )

                            ->numeric()

                            ->minValue(30)

                            ->maxValue(250)

                            ->step(0.1)

                            ->suffix('cm')

                            ->required()

                            ->live()

                            ->afterStateUpdated(function (
                                Get $get,
                                Set $set
                            ) {

                                self::calculateImt(
                                    $get,
                                    $set
                                );

                            }),

                        Forms\Components\TextInput::make(
                            'imt'
                        )

                            ->label(
                                'IMT'
                            )

                            ->disabled()

                            ->dehydrated()

                            ->suffix('kg/m²'),

                        Forms\Components\TextInput::make(
                            'status_gizi'
                        )

                            ->label(
                                'Status Gizi'
                            )

                            ->disabled()

                            ->dehydrated(),

                        Forms\Components\Radio::make(
                            'tanda_klinis_anemia'
                        )

                            ->label(
                                'Tanda Klinis Anemia'
                            )

                            ->options([

                                'Y' => 'Ya',

                                'N' => 'Tidak',

                            ])

                            ->default('N')

                            ->inline()

                            ->required(),

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
                                'Dirujuk Ke Fasyankes'
                            )

                            ->options([

                                'N' => 'Tidak',

                                'Y' => 'Ya',

                            ])

                            ->default('N')

                            ->inline()

                            ->live()

                            ->required(),

                        Forms\Components\Textarea::make(
                            'keterangan_rujukan'
                        )

                            ->label(
                                'Keterangan Rujukan'
                            )

                            ->visible(fn (
                                Get $get
                            ) => $get(
                                'dirujuk_ke_fasyankes'
                            ) === 'Y'

                            )

                            ->required(fn (
                                Get $get
                            ) => $get(
                                'dirujuk_ke_fasyankes'
                            ) === 'Y'

                            )

                            ->columnSpanFull(),

                    ])

                    ->columns(1),

            ]);
    }

    protected static function calculateImt(
        Get $get,
        Set $set
    ): void {

        $bb =
            (float) $get(
                'berat_badan'
            );

        $tb =
            (float) $get(
                'tinggi_badan'
            );

        if (

            $bb <= 0

            || $tb <= 0

        ) {

            $set('imt', null);

            $set(
                'status_gizi',
                null
            );

            return;
        }

        $tbMeter =
            $tb / 100;

        $imt =
            $bb / (
                $tbMeter * $tbMeter
            );

        $imt =
            round($imt, 2);

        $set(
            'imt',
            $imt
        );

        if ($imt < 17) {

            $status =
                'Sangat Kurus';

        } elseif ($imt < 18.5) {

            $status =
                'Kurus';

        } elseif ($imt < 25) {

            $status =
                'Normal';

        } elseif ($imt < 27) {

            $status =
                'Gemuk';

        } else {

            $status =
                'Obesitas';
        }

        $set(
            'status_gizi',
            $status
        );
    }
}
