<?php

namespace App\Filament\Resources\PemeriksaanGigis\Schemas;

use App\Models\StudentClassHistory;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class PemeriksaanGigiForm
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

                                table: 'pemeriksaan_gigis',

                                column: 'student_class_history_id',

                                ignoreRecord: true

                            )

                            ->validationMessages([

                                'unique' => 'Pemeriksaan gigi siswa ini sudah ada.',

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

                                    ->orderByDesc('id')

                                    ->get()

                                    ->mapWithKeys(function (
                                        $item
                                    ) {

                                        return [

                                            $item->id => ($item
                                                ->school
                                                ?->nama_sekolah
                                                ?? '-')

                                                .' | '

                                                .

                                                ($item
                                                    ->schoolClass
                                                    ?->nama_kelas
                                                    ?? '-')

                                                    .' | '

                                                    .

                                                    ($item
                                                        ->academicYear
                                                        ?->nama
                                                    ?? '-')

                                                .' | '

                                                .

                                                ($item
                                                    ->semester
                                                ?? '-')

                                                .' | '

                                                .

                                                ($item
                                                    ->student
                                                    ?->nama_lengkap
                                                ?? '-'),

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

                                $user =
                                    auth()->user();

                                if (

                                    $user->hasAnyRole([

                                        'admin_instansi',

                                        'petugas_pemeriksaan',

                                    ])

                                ) {

                                    abort_unless(

                                        $history
                                            ->school
                                            ?->instansi_id

                                        ===

                                        $user->instansi_id,

                                        403

                                    );
                                }

                                if (

                                    $user->hasRole(
                                        'admin_sekolah'
                                    )

                                ) {

                                    abort_unless(

                                        $history
                                            ->school_id

                                        ===

                                        $user->school_id,

                                        403

                                    );
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
                                        ->semester
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

                                if (
                                    $student?->tanggal_lahir
                                ) {

                                    $umur =
                                        Carbon::parse(

                                            $student
                                                ->tanggal_lahir

                                        )->age;

                                    $set(
                                        'umur',
                                        $umur
                                    );
                                }

                            })

                            ->required(),

                        Forms\Components\DatePicker::make(
                            'tanggal_pemeriksaan'
                        )

                            ->label(
                                'Tanggal Pemeriksaan'
                            )

                            ->required()

                            ->default(
                                now()
                            )

                            ->maxDate(
                                now()
                            ),

                        Forms\Components\TextInput::make(
                            'nisn'
                        )

                            ->label('NISN')

                            ->disabled(),

                        Forms\Components\TextInput::make(
                            'kelas'
                        )

                            ->label('Kelas')

                            ->disabled(),

                        Forms\Components\TextInput::make(
                            'semester'
                        )

                            ->label('Semester')

                            ->disabled(),

                        Forms\Components\TextInput::make(
                            'tahun_ajaran'
                        )

                            ->label('Tahun Ajaran')

                            ->disabled(),

                        Forms\Components\TextInput::make(
                            'umur'
                        )

                            ->label('Umur')

                            ->disabled()

                            ->suffix('tahun'),

                        Forms\Components\TextInput::make(
                            'sekolah'
                        )

                            ->label('Sekolah')

                            ->disabled(),

                        Forms\Components\Textarea::make(
                            'alamat'
                        )

                            ->label('Alamat')

                            ->disabled()

                            ->columnSpanFull(),

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

                            ->disabled(),

                    ])

                    ->columns(2),

                Section::make(
                    'Pemeriksaan Gigi dan Mulut'
                )

                    ->icon(
                        'heroicon-o-face-smile'
                    )

                    ->schema([

                        Forms\Components\Radio::make(
                            'celah_bibir_langit'
                        )

                            ->label(
                                'Celah Bibir / Langit-langit'
                            )

                            ->options([

                                'Y' => 'Ya',

                                'N' => 'Tidak',

                            ])

                            ->default('N')

                            ->inline(),

                        Forms\Components\Radio::make(
                            'luka_sudut_mulut'
                        )

                            ->label(
                                'Luka Pada Sudut Mulut'
                            )

                            ->options([

                                'Y' => 'Ya',

                                'N' => 'Tidak',

                            ])

                            ->default('N')

                            ->inline(),

                        Forms\Components\Radio::make(
                            'sariawan'
                        )

                            ->label(
                                'Sariawan'
                            )

                            ->options([

                                'Y' => 'Ya',

                                'N' => 'Tidak',

                            ])

                            ->default('N')

                            ->inline(),

                        Forms\Components\Radio::make(
                            'lidah_kotor'
                        )

                            ->label(
                                'Lidah Kotor'
                            )

                            ->options([

                                'Y' => 'Ya',

                                'N' => 'Tidak',

                            ])

                            ->default('N')

                            ->inline(),

                        Forms\Components\Radio::make(
                            'luka_lain_di_mulut'
                        )

                            ->label(
                                'Luka Lainnya di Mulut'
                            )

                            ->options([

                                'Y' => 'Ya',

                                'N' => 'Tidak',

                            ])

                            ->default('N')

                            ->inline(),

                        Forms\Components\Radio::make(
                            'gigi_berlubang'
                        )

                            ->label(
                                'Gigi Berlubang'
                            )

                            ->options([

                                'Y' => 'Ya',

                                'N' => 'Tidak',

                            ])

                            ->default('N')

                            ->inline()

                            ->live(),

                        Forms\Components\Select::make(
                            'jumlah_gigi_berlubang'
                        )

                            ->label(
                                'Jumlah Gigi Berlubang'
                            )

                            ->options([

                                '1' => '1',

                                '2' => '2',

                                '3' => '3',

                                '4' => '> 3',

                            ])

                            ->visible(fn (
                                Get $get
                            ) => $get(
                                'gigi_berlubang'
                            ) === 'Y'

                            ),

                        Forms\Components\Radio::make(
                            'gusi_berdarah'
                        )

                            ->label(
                                'Gusi Mudah Berdarah'
                            )

                            ->options([

                                'Y' => 'Ya',

                                'N' => 'Tidak',

                            ])

                            ->default('N')

                            ->inline(),

                        Forms\Components\Radio::make(
                            'gusi_bengkak'
                        )

                            ->label(
                                'Gusi Bengkak'
                            )

                            ->options([

                                'Y' => 'Ya',

                                'N' => 'Tidak',

                            ])

                            ->default('N')

                            ->inline(),

                        Forms\Components\Radio::make(
                            'gigi_kotor_plak'
                        )

                            ->label(
                                'Gigi Kotor'
                            )

                            ->options([

                                'Y' => 'Ya',

                                'N' => 'Tidak',

                            ])

                            ->default('N')

                            ->inline(),

                        Forms\Components\Radio::make(
                            'karang_gigi'
                        )

                            ->label(
                                'Karang Gigi'
                            )

                            ->options([

                                'Y' => 'Ya',

                                'N' => 'Tidak',

                            ])

                            ->default('N')

                            ->inline(),

                        Forms\Components\Radio::make(
                            'susunan_gigi_tidak_teratur'
                        )

                            ->label(
                                'Susunan Gigi Tidak Teratur'
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
                    'Pemeriksaan Tambahan'
                )

                    ->schema([

                        Forms\Components\Radio::make(
                            'penglihatan_loupe'
                        )

                            ->label(
                                'Penglihatan dengan Loupe'
                            )

                            ->options([

                                'Y' => 'Ya',

                                'N' => 'Tidak',

                            ])

                            ->default('N')

                            ->inline(),

                        Forms\Components\Radio::make(
                            'pendengaran'
                        )

                            ->label(
                                'Pendengaran'
                            )

                            ->options([

                                'Y' => 'Ya',

                                'N' => 'Tidak',

                            ])

                            ->default('N')

                            ->inline(),

                        Forms\Components\Radio::make(
                            'kursi_roda'
                        )

                            ->label(
                                'Kursi Roda'
                            )

                            ->options([

                                'Y' => 'Ya',

                                'N' => 'Tidak',

                            ])

                            ->default('N')

                            ->inline(),

                        Forms\Components\Radio::make(
                            'tongkat_kruk'
                        )

                            ->label(
                                'Tongkat / Kruk'
                            )

                            ->options([

                                'Y' => 'Ya',

                                'N' => 'Tidak',

                            ])

                            ->default('N')

                            ->inline(),

                        Forms\Components\Radio::make(
                            'kaki_tangan_mata_protese'
                        )

                            ->label(
                                'Kaki / Tangan / Mata Protese'
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
                                'Dirujuk ke Fasyankes'
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

                            ->visible(fn (
                                Get $get
                            ) => $get(
                                'dirujuk_ke_fasyankes'
                            ) === 'Y'

                            )

                            ->columnSpanFull(),

                    ])

                    ->columns(2),

            ]);
    }
}
