<?php

namespace App\Filament\Resources\PemeriksaanUmums\Schemas;

use App\Models\StudentClassHistory;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class PemeriksaanUmumForm
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

                                table: 'pemeriksaan_umums',

                                column: 'student_class_history_id',

                                ignoreRecord: true

                            )

                            ->validationMessages([

                                'unique' => 'Pemeriksaan umum siswa pada semester ini sudah ada.',

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

                                if (

                                    $user->hasRole(
                                        'petugas_pemeriksaan'
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

                            ->afterStateHydrated(function (
                                $state,
                                Set $set
                            ) {
                                if (! $state) {
                                    return;
                                }
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
                                $set('nisn', $student?->nisn);
                                $set('kelas', $history->schoolClass?->nama_kelas);
                                $set('semester', $history?->semester);
                                $set('tahun_ajaran', $history->academicYear?->nama);
                                $set('jenis_kelamin', $student?->jenis_kelamin);
                                $set('alamat', $student?->alamat);
                                $set('sekolah', $history->school?->nama_sekolah);
                                $umur = $student?->tanggal_lahir
                                    ? Carbon::parse($student->tanggal_lahir)->age
                                    : null;
                                $set('umur', $umur);
                            })
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
                    'Vital Sign'
                )

                    ->description(
                        'Pemeriksaan tanda vital'
                    )

                    ->icon(
                        'heroicon-o-heart'
                    )

                    ->schema([

                        Forms\Components\TextInput::make(
                            'tekanan_darah'
                        )

                            ->label(
                                'Tekanan Darah'
                            )

                            ->suffix('mmHg')

                            ->maxLength(20),

                        Forms\Components\TextInput::make(
                            'denyut_nadi'
                        )

                            ->label(
                                'Denyut Nadi'
                            )

                            ->numeric()

                            ->suffix('/menit'),

                        Forms\Components\TextInput::make(
                            'frekuensi_pernapasan'
                        )

                            ->label(
                                'Frekuensi Pernapasan'
                            )

                            ->numeric()

                            ->suffix('/menit'),

                        Forms\Components\TextInput::make(
                            'suhu'
                        )

                            ->label(
                                'Suhu Tubuh'
                            )

                            ->numeric()

                            ->suffix('°C'),

                        Forms\Components\Select::make(
                            'bising_jantung'
                        )

                            ->label(
                                'Bising Jantung'
                            )

                            ->options([

                                'normal' => 'Normal',

                                'abnormal' => 'Abnormal',

                            ])

                            ->native(false),

                        Forms\Components\Select::make(
                            'bising_paru'
                        )

                            ->label(
                                'Bising Paru'
                            )

                            ->options([

                                'normal' => 'Normal',

                                'abnormal' => 'Abnormal',

                            ])

                            ->native(false),

                    ])

                    ->columns(2),

                Section::make(
                    'Pemeriksaan Fisik'
                )

                    ->description(
                        'Pemeriksaan fisik umum'
                    )

                    ->icon(
                        'heroicon-o-clipboard-document-check'
                    )

                    ->schema([

                        Forms\Components\Select::make(
                            'keadaan_rambut'
                        )

                            ->label(
                                'Keadaan Rambut'
                            )

                            ->options([

                                'bersih' => 'Bersih',

                                'kotor' => 'Kotor',

                                'rontok' => 'Rontok',

                            ])

                            ->native(false),

                        Forms\Components\Radio::make(
                            'bercak_keputihan'
                        )

                            ->label(
                                'Bercak Keputihan'
                            )

                            ->options([

                                'Y' => 'Ya',

                                'N' => 'Tidak',

                            ])

                            ->default('N')

                            ->inline(),

                        Forms\Components\Radio::make(
                            'kulit_bersisik'
                        )

                            ->label(
                                'Kulit Bersisik'
                            )

                            ->options([

                                'Y' => 'Ya',

                                'N' => 'Tidak',

                            ])

                            ->default('N')

                            ->inline(),

                        Forms\Components\Radio::make(
                            'kulit_ada_memar'
                        )

                            ->label(
                                'Kulit Ada Memar'
                            )

                            ->options([

                                'Y' => 'Ya',

                                'N' => 'Tidak',

                            ])

                            ->default('N')

                            ->inline(),

                        Forms\Components\Radio::make(
                            'kulit_ada_luka_sayatan'
                        )

                            ->label(
                                'Luka Sayatan'
                            )

                            ->options([

                                'Y' => 'Ya',

                                'N' => 'Tidak',

                            ])

                            ->default('N')

                            ->inline(),

                        Forms\Components\Radio::make(
                            'kulit_ada_luka_koreng'
                        )

                            ->label(
                                'Luka Koreng'
                            )

                            ->options([

                                'Y' => 'Ya',

                                'N' => 'Tidak',

                            ])

                            ->default('N')

                            ->inline(),

                        Forms\Components\Radio::make(
                            'luka_koreng_sukar_sembuh'
                        )

                            ->label(
                                'Luka Sukar Sembuh'
                            )

                            ->options([

                                'Y' => 'Ya',

                                'N' => 'Tidak',

                            ])

                            ->default('N')

                            ->inline(),

                        Forms\Components\Radio::make(
                            'bekas_suntikan'
                        )

                            ->label(
                                'Bekas Suntikan'
                            )

                            ->options([

                                'Y' => 'Ya',

                                'N' => 'Tidak',

                            ])

                            ->default('N')

                            ->inline(),

                        Forms\Components\Select::make(
                            'telinga_luar'
                        )

                            ->label(
                                'Telinga Luar'
                            )

                            ->options([

                                'normal' => 'Normal',

                                'abnormal' => 'Abnormal',

                            ])

                            ->native(false),

                        Forms\Components\Select::make(
                            'kondisi_kuku'
                        )

                            ->label(
                                'Kondisi Kuku'
                            )

                            ->options([

                                'bersih' => 'Bersih',

                                'kotor' => 'Kotor',

                            ])

                            ->native(false),

                    ])

                    ->columns(2),

                Section::make(
                    'Perilaku'
                )

                    ->icon(
                        'heroicon-o-fire'
                    )

                    ->schema([

                        Forms\Components\Radio::make(
                            'merokok_setahun'
                        )

                            ->label(
                                'Merokok dalam 1 tahun terakhir'
                            )

                            ->options([

                                'Y' => 'Ya',

                                'N' => 'Tidak',

                            ])

                            ->default('N')

                            ->inline()

                            ->live(),

                        Forms\Components\Select::make(
                            'risiko_merokok'
                        )

                            ->label(
                                'Risiko Merokok'
                            )

                            ->options([

                                'rendah' => 'Rendah',

                                'sedang' => 'Sedang',

                                'tinggi' => 'Tinggi',

                            ])

                            ->native(false)

                            ->visible(fn (
                                Get $get
                            ) => $get(
                                'merokok_setahun'
                            ) === 'Y'

                            ),

                        Forms\Components\Select::make(
                            'jenis_rokok'
                        )

                            ->label(
                                'Jenis Rokok'
                            )

                            ->options([

                                'konvensional' => 'Konvensional',

                                'elektrik' => 'Elektrik',

                                'keduanya' => 'Keduanya',

                            ])

                            ->native(false)

                            ->visible(fn (
                                Get $get
                            ) => $get(
                                'merokok_setahun'
                            ) === 'Y'

                            ),

                        Forms\Components\TextInput::make(
                            'jumlah_rokok'
                        )

                            ->label(
                                'Jumlah Rokok'
                            )

                            ->numeric()

                            ->suffix('batang')

                            ->visible(fn (
                                Get $get
                            ) => $get(
                                'merokok_setahun'
                            ) === 'Y'

                            ),

                        Forms\Components\TextInput::make(
                            'lama_merokok'
                        )

                            ->label(
                                'Lama Merokok'
                            )

                            ->numeric()

                            ->suffix('tahun')

                            ->visible(fn (
                                Get $get
                            ) => $get(
                                'merokok_setahun'
                            ) === 'Y'

                            ),

                        Forms\Components\Select::make(
                            'sarapan'
                        )

                            ->label(
                                'Kebiasaan Sarapan'
                            )

                            ->options([

                                'selalu' => 'Selalu',

                                'kadang' => 'Kadang',

                                'tidak' => 'Tidak Pernah',

                            ])

                            ->native(false),

                    ])

                    ->columns(2),

                Section::make(
                    'Kesehatan Remaja'
                )

                    ->icon(
                        'heroicon-o-user-group'
                    )

                    ->visible(fn (
                        Get $get
                    ) => $get(
                        'jenis_kelamin'
                    ) === 'P'

                    )

                    ->schema([

                        Forms\Components\Radio::make(
                            'sudah_menstruasi'
                        )

                            ->label(
                                'Sudah Menstruasi'
                            )

                            ->options([

                                'Y' => 'Ya',

                                'N' => 'Tidak',

                            ])

                            ->default('N')

                            ->inline(),

                        Forms\Components\Radio::make(
                            'mengalami_keputihan'
                        )

                            ->label(
                                'Mengalami Keputihan'
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
