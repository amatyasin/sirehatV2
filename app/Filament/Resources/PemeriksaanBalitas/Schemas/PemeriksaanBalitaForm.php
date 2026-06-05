<?php

namespace App\Filament\Resources\PemeriksaanBalitas\Schemas;

use App\Models\Child;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class PemeriksaanBalitaForm
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
                        'Informasi dasar balita'
                    )

                    ->icon(
                        'heroicon-o-user'
                    )

                    ->schema([

                        Forms\Components\Select::make(
                            'child_id'
                        )
                            ->label('Anak')
                            ->options(function () {

                                $user =
                                    auth()->user();

                                $query =
                                    Child::query()

                                        ->with([

                                            'posyandu',

                                        ]);

                                if (

                                    $user->hasAnyRole([

                                        'super_admin',

                                        'admin_dinkes',

                                    ])

                                ) {

                                    return $query

                                        ->orderBy(
                                            'nama_lengkap'
                                        )

                                        ->get()

                                        ->mapWithKeys(
                                            fn ($item) => [

                                                $item->id => ($item
                                                    ->posyandu
                                                    ?->nama_posyandu
                                                    ?? '-')

                                                    .' | '.

                                                    ($item->nik ?? '-')

                                                    .' | '.

                                                    $item->nama_lengkap,

                                            ]
                                        );
                                }

                                if (

                                    $user->hasRole(
                                        'admin_instansi'
                                    )

                                ) {

                                    $query->whereHas(

                                        'posyandu',

                                        fn ($q) => $q->where(

                                            'instansi_id',

                                            $user->instansi_id

                                        )

                                    );
                                }

                                if (

                                    $user->hasRole(
                                        'petugas_posyandu'
                                    )

                                ) {

                                    $query->where(

                                        'posyandu_id',

                                        $user->posyandu_id

                                    );
                                }

                                return $query

                                    ->orderBy(
                                        'nama_lengkap'
                                    )

                                    ->get()

                                    ->mapWithKeys(
                                        fn ($item) => [

                                            $item->id => ($item
                                                ->posyandu
                                                ?->nama_posyandu
                                                ?? '-')

                                                .' | '.

                                                ($item->nik ?? '-')

                                                .' | '.

                                                $item->nama_lengkap,

                                        ]
                                    );

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
                                $child =
                                    Child::with([
                                        'posyandu',
                                        'orangTua',
                                    ])->find($state);
                                if (! $child) {
                                    return;
                                }
                                $set('jenis_kelamin', $child->jenis_kelamin);
                                $set('nik', $child->nik);
                                $set('alamat', $child->alamat);
                                $set('nama_orang_tua', $child->orangTua?->nama_lengkap);
                                $set('posyandu', $child->posyandu?->nama_posyandu);
                                $umur = Carbon::parse($child->tanggal_lahir);
                                $set(
                                    'umur_bulan',
                                    $umur->diff(now())->y . ' tahun ' . $umur->diff(now())->m . ' bulan'
                                );
                            })
                            ->afterStateUpdated(function (
                                $state,
                                Set $set
                            ) {

                                $child =
                                    Child::with([

                                        'posyandu',

                                        'orangTua',

                                    ])->find($state);

                                if (! $child) {

                                    return;
                                }

                                $user =
                                    auth()->user();

                                if (

                                    $user->hasRole(
                                        'admin_instansi'
                                    )

                                ) {

                                    abort_unless(

                                        $child->instansi_id ===
                                        $user->instansi_id,

                                        403

                                    );
                                }

                                if (

                                    $user->hasRole(
                                        'petugas_posyandu'
                                    )

                                ) {

                                    abort_unless(

                                        $child->posyandu_id ===
                                        $user->posyandu_id,

                                        403

                                    );
                                }

                                $set(
                                    'jenis_kelamin',
                                    $child->jenis_kelamin
                                );

                                $set(
                                    'nik',
                                    $child->nik
                                );

                                $set(
                                    'alamat',
                                    $child->alamat
                                );

                                $set(
                                    'nama_orang_tua',
                                    $child
                                        ->orangTua
                                        ?->nama_lengkap
                                );

                                $set(
                                    'posyandu',
                                    $child
                                        ->posyandu
                                        ?->nama_posyandu
                                );

                                $umur =
                                    Carbon::parse(
                                        $child->tanggal_lahir
                                    );

                                $set(

                                    'umur_bulan',

                                    $umur
                                        ->diff(now())
                                        ->y

                                    .' tahun '

                                    .

                                    $umur
                                        ->diff(now())
                                        ->m

                                    .' bulan'

                                );

                            })
                            ->required(),

                        Forms\Components\DatePicker::make(
                            'tanggal_pemeriksaan'
                        )

                            ->label(
                                'Tanggal Pemeriksaan'
                            )

                            ->default(now())

                            ->required(),

                        Forms\Components\TextInput::make(
                            'nik'
                        )

                            ->label('NIK')

                            ->disabled(),

                        Forms\Components\TextInput::make(
                            'umur_bulan'
                        )

                            ->label('Umur')

                            ->disabled(),

                        Forms\Components\TextInput::make(
                            'nama_orang_tua'
                        )

                            ->label(
                                'Orang Tua'
                            )

                            ->disabled(),

                        Forms\Components\TextInput::make(
                            'posyandu'
                        )

                            ->label(
                                'Posyandu'
                            )

                            ->disabled(),

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

                        Forms\Components\Textarea::make(
                            'alamat'
                        )

                            ->label(
                                'Alamat'
                            )

                            ->disabled()

                            ->columnSpanFull(),

                    ])

                    ->columns(2),

                Tabs::make(
                    'Pemeriksaan Balita'
                )
                    ->tabs([
                        Tab::make(
                            'Antropometri'
                        )
                            ->icon(
                                'heroicon-o-scale'
                            )
                            ->schema([
                                Forms\Components\TextInput::make(
                                    'berat_badan'
                                )
                                    ->label(
                                        'Berat Badan (kg)'
                                    )
                                    ->numeric()
                                    ->required()
                                    ->placeholder(
                                        'Contoh: 12.5'
                                    )
                                    ->suffix('kg')
                                    ->live(
                                        onBlur: true
                                    )
                                    ->afterStateUpdated(function (
                                        Get $get,
                                        Set $set
                                    ) {
                                        self::calculateImt(
                                            $get,
                                            $set
                                        );
                                        self::calculateStatus(
                                            $get,
                                            $set
                                        );
                                    }),
                                Forms\Components\TextInput::make(
                                    'tinggi_badan'
                                )
                                    ->label(
                                        'Tinggi Badan (cm)'
                                    )
                                    ->numeric()
                                    ->required()
                                    ->placeholder(
                                        'Contoh: 85.5'
                                    )
                                    ->suffix('cm')
                                    ->live(
                                        onBlur: true
                                    )
                                    ->afterStateUpdated(function (
                                        Get $get,
                                        Set $set
                                    ) {
                                        self::calculateImt(
                                            $get,
                                            $set
                                        );

                                        self::calculateStatus(
                                            $get,
                                            $set
                                        );
                                    }),
                                Forms\Components\TextInput::make(
                                    'imt'
                                )
                                    ->label(
                                        'IMT (BB/TB²)'
                                    )
                                    ->disabled()
                                    ->dehydrated()
                                    ->suffix('kg/m²'),

                                Forms\Components\TextInput::make(
                                    'status_imt_u'
                                )
                                    ->label(
                                        'Kategori IMT'
                                    )
                                    ->disabled()
                                    ->dehydrated(),

                                Forms\Components\TextInput::make(
                                    'status_tb_u'
                                )
                                    ->label(
                                        'TB/U (Stunting)'
                                    )
                                    ->disabled()
                                    ->dehydrated()
                                    ->placeholder(
                                        'Hasil otomatis berdasarkan standar WHO'
                                    )
                                    ->helperText(
                                        'Status stunting dihitung otomatis'
                                    ),

                                Forms\Components\Radio::make(
                                    'status_lingkar_kepala'
                                )
                                    ->label(
                                        'Lingkar Kepala'
                                    )
                                    ->options([
                                        'Makrosefali' => 'Makrosefali',
                                        'Normal' => 'Normal',
                                        'Mikrosefali' => 'Mikrosefali',
                                    ])
                                    ->default('Normal')
                                    ->required()
                                    ->inline(),
                            ])
                            ->columns(2),

                        Tab::make(
                            'Demografi & Risiko'
                        )
                            ->icon(
                                'heroicon-o-question-mark-circle'
                            )
                            ->schema([

                                Forms\Components\Radio::make(
                                    'disabilitas'
                                )
                                    ->label(
                                        'Apakah Anak Penyandang Disabilitas?'
                                    )
                                    ->options([

                                        'N' => 'Non Disabilitas',
                                        'Y' => 'Disabilitas',
                                    ])
                                    ->default('N')
                                    ->required()
                                    ->inline(),

                                Forms\Components\Radio::make(
                                    'riwayat_kencing_manis'
                                )
                                    ->label(
                                        'Pernah Didiagnosis Diabetes Oleh Dokter?'
                                    )
                                    ->options([
                                        'Y' => 'Iya',
                                        'N' => 'Tidak',
                                    ])
                                    ->default('N')
                                    ->required()
                                    ->inline(),

                                Forms\Components\Radio::make(
                                    'makan_pagi_sudah_banyak'
                                )
                                    ->label(
                                        'Sering Merasa Sangat Lapar?'
                                    )
                                    ->options([
                                            'Y' => 'Iya',
                                            'N' => 'Tidak',
                                        ])
                                    ->default('N')
                                    ->required()
                                    ->inline(),

                                Forms\Components\Radio::make(
                                    'makan_banyak_makanan_manis'
                                )
                                    ->label(
                                        'Sering Merasa Haus?'
                                    )
                                    ->options([
                                        'Y' => 'Iya',
                                        'N' => 'Tidak',
                                    ])
                                    ->default('N')
                                    ->required()
                                    ->inline(),

                                Forms\Components\Radio::make(
                                    'mengalami_penurunan_berat_badan'
                                )
                                    ->label(
                                        'Penurunan Berat Badan?'
                                    )
                                    ->options([
                                        'Y' => 'Iya',
                                        'N' => 'Tidak',
                                    ])
                                    ->default('N')
                                    ->required()
                                    ->inline(),

                                Forms\Components\Radio::make(
                                    'riwayat_diabetes_orangtua'
                                )
                                    ->label(
                                        'Riwayat Keluarga Diabetes?'
                                    )
                                    ->options([
                                        'Y' => 'Iya',
                                        'N' => 'Tidak',
                                    ])
                                    ->default('N')
                                    ->required()
                                    ->inline(),

                            ])
                            ->columns(2),

                        Tab::make(
                            'Imunisasi'
                        )

                            ->icon(
                                'heroicon-o-shield-check'
                            )

                            ->schema([

                                Forms\Components\Radio::make(
                                    'imunisasi_hepatitis_b'
                                )
                                    ->label(
                                        'Imunisasi Hepatitis B'
                                    )
                                    ->options([
                                        'N' => 'Tidak',
                                        'Y' => 'Ya',
                                    ])
                                    ->default('N')
                                    ->required()
                                    ->inline(),

                                Forms\Components\Radio::make(
                                    'imunisasi_bcg_bulan_1'
                                )
                                    ->label(
                                        'Imunisasi BCG Bulan 1'
                                    )
                                    ->options([
                                        'N' => 'Tidak',
                                        'Y' => 'Ya',
                                    ])
                                    ->default('N')
                                    ->required()
                                    ->inline(),

                                Forms\Components\Radio::make(
                                    'imunisasi_polio_dosis_1'
                                )
                                    ->label(
                                        'Imunisasi Polio Dosis 1'
                                    )
                                    ->options([
                                        'N' => 'Tidak',
                                        'Y' => 'Ya',
                                    ])
                                    ->default('N')
                                    ->required()
                                    ->inline(),

                                Forms\Components\Radio::make(
                                    'imunisasi_polio_dosis_2'
                                )
                                    ->label(
                                        'Imunisasi Polio Dosis 2'
                                    )
                                    ->options([
                                        'N' => 'Tidak',
                                        'Y' => 'Ya',
                                    ])
                                    ->default('N')
                                    ->required()
                                    ->inline(),

                                Forms\Components\Radio::make(
                                    'imunisasi_polio_dosis_3'
                                )
                                    ->label(
                                        'Imunisasi Polio Dosis 3'
                                    )
                                    ->options([
                                        'N' => 'Tidak',
                                        'Y' => 'Ya',
                                    ])
                                    ->default('N')
                                    ->required()
                                    ->inline(),

                                Forms\Components\Radio::make(
                                    'imunisasi_polio_dosis_4'
                                )
                                    ->label(
                                        'Imunisasi Polio Dosis 4'
                                    )
                                    ->options([
                                        'N' => 'Tidak',
                                        'Y' => 'Ya',
                                    ])
                                    ->default('N')
                                    ->required()
                                    ->inline(),

                                Forms\Components\Radio::make(
                                    'imunisasi_dpt_hb_hib_dosis_1'
                                )
                                    ->label(
                                        'Imunisasi DPT-HB-Hib Dosis 1'
                                    )
                                    ->options([
                                        'N' => 'Tidak',
                                        'Y' => 'Ya',
                                    ])
                                    ->default('N')
                                    ->required()
                                    ->inline(),

                                Forms\Components\Radio::make(
                                    'imunisasi_dpt_hb_hib_dosis_2'
                                )
                                    ->label(
                                        'Imunisasi DPT-HB-Hib Dosis 2'
                                    )
                                    ->options([
                                        'N' => 'Tidak',
                                        'Y' => 'Ya',
                                    ])
                                    ->default('N')
                                    ->required()
                                    ->inline(),

                                Forms\Components\Radio::make(
                                    'imunisasi_dpt_hb_hib_dosis_3'
                                )
                                    ->label(
                                        'Imunisasi DPT-HB-Hib Dosis 3'
                                    )
                                    ->options([
                                        'N' => 'Tidak',
                                        'Y' => 'Ya',
                                    ])
                                    ->default('N')
                                    ->required()
                                    ->inline(),

                                Forms\Components\Radio::make(
                                    'imunisasi_dpt_hb_hib_dosis_4'
                                )
                                    ->label(
                                        'Imunisasi DPT-HB-Hib Dosis 4'
                                    )
                                    ->options([
                                        'N' => 'Tidak',
                                        'Y' => 'Ya',
                                    ])
                                    ->default('N')
                                    ->required()
                                    ->inline(),

                                Forms\Components\Radio::make(
                                    'imunisasi_pcv_dosis_1'
                                )
                                    ->label(
                                        'Imunisasi PCV Dosis 1'
                                    )
                                    ->options([
                                        'N' => 'Tidak',
                                        'Y' => 'Ya',
                                    ])
                                    ->default('N')
                                    ->required()
                                    ->inline(),

                                Forms\Components\Radio::make(
                                    'imunisasi_pcv_dosis_2'
                                )
                                    ->label(
                                        'Imunisasi PCV Dosis 2'
                                    )
                                    ->options([
                                        'N' => 'Tidak',
                                        'Y' => 'Ya',
                                    ])
                                    ->default('N')
                                    ->required()
                                    ->inline(),

                                Forms\Components\Radio::make(
                                    'imunisasi_rotavirus_dosis_1'
                                )
                                    ->label(
                                        'Imunisasi Rotavirus Dosis 1'
                                    )
                                    ->options([
                                        'N' => 'Tidak',
                                        'Y' => 'Ya',
                                    ])
                                    ->default('N')
                                    ->required()
                                    ->inline(),

                                Forms\Components\Radio::make(
                                    'imunisasi_rotavirus_dosis_2'
                                )
                                    ->label(
                                        'Imunisasi Rotavirus Dosis 2'
                                    )
                                    ->options([
                                        'N' => 'Tidak',
                                        'Y' => 'Ya',
                                    ])
                                    ->default('N')
                                    ->required()
                                    ->inline(),

                                Forms\Components\Radio::make(
                                    'imunisasi_rotavirus_dosis_3'
                                )
                                    ->label(
                                        'Imunisasi Rotavirus Dosis 3'
                                    )
                                    ->options([
                                        'N' => 'Tidak',
                                        'Y' => 'Ya',
                                    ])
                                    ->default('N')
                                    ->required()
                                    ->inline(),

                                Forms\Components\Radio::make(
                                    'imunisasi_campak_rubella_dosis_1'
                                )
                                    ->label(
                                        'Imunisasi Campak Rubella Dosis 1'
                                    )
                                    ->options([
                                        'N' => 'Tidak',
                                        'Y' => 'Ya',
                                    ])
                                    ->default('N')
                                    ->required()
                                    ->inline(),

                                Forms\Components\Radio::make(
                                    'imunisasi_campak_rubella_dosis_2'
                                )
                                    ->label(
                                        'Imunisasi Campak Rubella Dosis 2'
                                    )
                                    ->options([
                                        'N' => 'Tidak',
                                        'Y' => 'Ya',
                                    ])
                                    ->default('N')
                                    ->required()
                                    ->inline(),
                            ])
                            ->columns(3),

                        Tab::make(
                            'Tumbuh Kembang'
                        )
                            ->icon(
                                'heroicon-o-heart'
                            )
                            ->schema([
                                Forms\Components\Radio::make(
                                    'indikasi_gpph'
                                )
                                    ->label(
                                        'Apakah Dicurigai GPPH?'
                                    )
                                    ->options([
                                        'N' => 'Tidak',
                                        'Y' => 'Ya',
                                    ])
                                    ->default('N')
                                    ->required()
                                    ->inline()
                                    ->live(),

                                Forms\Components\Radio::make(
                                    'hasil_gpph'
                                )
                                    ->label(
                                        'Hasil Pemeriksaan GPPH'
                                    )
                                    ->options([
                                        'Kurang dari 13' => 'Nilai < 13',
                                        'Ragu' => 'Nilai < 13 (ragu)',
                                        'Lebih dari 13' => 'Nilai ≥ 13',
                                    ])
                                    ->visible(fn (Get $get) => $get('indikasi_gpph') === 'Y'
                                    )
                                    ->inline(),

                                Forms\Components\Radio::make(
                                    'indikasi_kmpe'
                                )
                                    ->label(
                                        'Apakah Dicurigai KMPE?'
                                    )
                                    ->options([
                                        'N' => 'Tidak',
                                        'Y' => 'Ya',
                                    ])
                                    ->default('N')
                                    ->required()
                                    ->inline()
                                    ->live(),

                                Forms\Components\Radio::make(
                                    'hasil_kmpe'
                                )
                                    ->label(
                                        'Hasil Pemeriksaan KMPE'
                                    )
                                    ->options([
                                        'Tidak Ada' => 'Tidak ada jawaban "Ya"',
                                        '1 Ya' => 'Ada 1 jawaban "Ya"',
                                        '2 Ya' => '≥2 jawaban "Ya"',
                                    ])
                                    ->visible(fn (Get $get) => $get('indikasi_kmpe') === 'Y'
                                    )
                                    ->inline(),

                                Forms\Components\Radio::make(
                                    'hasil_kpsp'
                                )
                                    ->label(
                                        'Hasil KPSP'
                                    )
                                    ->options([
                                        'Sesuai' => 'Sesuai',
                                        'Meragukan' => 'Meragukan',
                                        'Penyimpangan' => 'Penyimpangan',
                                    ])
                                    ->default('Sesuai')
                                    ->required()
                                    ->inline(),

                                Forms\Components\Radio::make(
                                    'hasil_perilaku'
                                )
                                    ->label(
                                        'Hasil Pemeriksaan Perilaku'
                                    )
                                    ->options([
                                        'Normal' => 'Normal',
                                        'Perlu Pemeriksaan Lanjutan' => 'Perlu pemeriksaan lanjutan',
                                    ])
                                    ->default('Normal')
                                    ->required()
                                    ->inline(),
                            ])
                            ->columns(1),

                        Tab::make(
                            'Pemeriksaan Balita & Apras'
                        )
                            ->icon(
                                'heroicon-o-heart'
                            )
                            ->schema([

                                Forms\Components\Radio::make(
                                    'hasil_tes_daya_dengar'
                                )
                                    ->label(
                                        'Hasil Tes Daya Dengar'
                                    )
                                    ->options([
                                        'Sesuai umur' => 'Sesuai umur',
                                        'Kemungkinan penyimpangan' => 'Kemungkinan penyimpangan',
                                    ])
                                    ->default('Sesuai umur')
                                    ->required()
                                    ->inline(),

                                Forms\Components\Radio::make(
                                    'hasil_pemeriksaan_tes_daya_lihat'
                                )
                                    ->label(
                                        'Hasil Tes Daya Lihat'
                                    )
                                    ->options([
                                        'Visus baik' => 'Visus baik',
                                        'Kurang' => 'Kurang',
                                    ])
                                    ->default('Visus baik')
                                    ->required()
                                    ->inline(),

                                Forms\Components\Radio::make(
                                    'pemeriksaan_mata'
                                )
                                    ->label(
                                        'Pemeriksaan Mata'
                                    )
                                    ->options([
                                        'Normal' => 'Normal',
                                        'Curiga kelainan' => 'Curiga kelainan',
                                    ])
                                    ->default('Normal')
                                    ->required()
                                    ->inline(),

                                Forms\Components\Radio::make(
                                    'serumen_impaksi'
                                )
                                    ->label(
                                        'Serumen Impaksi'
                                    )
                                    ->options([
                                        'Tidak ada' => 'Tidak ada',
                                        'Ada' => 'Ada',
                                    ])
                                    ->default('Tidak ada')
                                    ->required()
                                    ->inline(),

                                Forms\Components\Radio::make(
                                    'infeksi_telinga'
                                )
                                    ->label(
                                        'Infeksi Telinga'
                                    )
                                    ->options([
                                        'Tidak ada' => 'Tidak ada',
                                        'Ada' => 'Ada',
                                    ])
                                    ->default('Tidak ada')
                                    ->required()
                                    ->inline(),

                                Forms\Components\Radio::make(
                                    'jumlah_gigi_karies'
                                )
                                    ->label(
                                        'Jumlah Gigi Karies'
                                    )
                                    ->options([
                                        '0' => 'Tidak ada',
                                        '1' => '1',
                                        '2' => '2',
                                        '3' => '3',
                                        '>=3' => '≥3',
                                    ])
                                    ->default('0')
                                    ->required()
                                    ->inline(),

                            ])
                            ->columns(2),

                        Tab::make(
                            'Skrining TB'
                        )
                            ->icon(
                                'heroicon-o-bug-ant'
                            )
                            ->schema([

                                Forms\Components\Radio::make(
                                    'tb_batuk'
                                )
                                    ->label(
                                        'Batuk'
                                    )
                                    ->options([
                                        'Ya > 2 minggu' => 'Ya > 2 minggu',
                                        'Ya < 2 minggu' => 'Ya < 2 minggu',
                                        'Tidak batuk' => 'Tidak batuk',
                                    ])
                                    ->default('Tidak batuk')
                                    ->required()
                                    ->inline(),

                                Forms\Components\Radio::make(
                                    'tb_bb_turun'
                                )
                                    ->label(
                                        'BB turun / tidak naik / nafsu makan turun'
                                    )
                                    ->options([
                                        'Y' => 'Ya',
                                        'N' => 'Tidak',
                                    ])
                                    ->default('N')
                                    ->required()
                                    ->inline(),

                                Forms\Components\Radio::make(
                                    'tb_demam'
                                )
                                    ->label(
                                        'Demam hilang timbul > 2 minggu'
                                    )
                                    ->options([
                                        'Y' => 'Ya',
                                        'N' => 'Tidak',
                                    ])
                                    ->default('N')
                                    ->required()
                                    ->inline(),

                                Forms\Components\Radio::make(
                                    'tb_lesu'
                                )
                                    ->label(
                                        'Anak lesu / kurang aktif'
                                    )
                                    ->options([
                                        'Y' => 'Ya',
                                        'N' => 'Tidak',
                                    ])
                                    ->default('N')
                                    ->required()
                                    ->inline(),

                                Forms\Components\Radio::make(
                                    'tb_kelenjar'
                                )
                                    ->label(
                                        'Pembesaran kelenjar getah bening'
                                    )
                                    ->options([
                                        'Y' => 'Ya',
                                        'N' => 'Tidak',
                                    ])
                                    ->default('N')
                                    ->required()
                                    ->inline(),

                                Forms\Components\Radio::make(
                                    'tb_rontgen'
                                )
                                    ->label(
                                        'Radiografi Toraks'
                                    )
                                    ->options([
                                        'Y' => 'Ya',
                                        'N' => 'Tidak',
                                    ])
                                    ->default('N')
                                    ->required()
                                    ->inline(),

                                Forms\Components\Radio::make(
                                    'tb_kontak'
                                )
                                    ->label(
                                        'Riwayat kontak TB'
                                    )
                                    ->options([
                                        'Kontak serumah' => 'Kontak serumah',
                                        'Kontak erat' => 'Kontak erat',
                                        'Tidak ada' => 'Tidak ada',
                                        'Tidak diketahui' => 'Tidak diketahui',
                                    ])
                                    ->default('Tidak diketahui')
                                    ->required()
                                    ->inline(),

                                Forms\Components\Radio::make(
                                    'tb_metode'
                                )
                                    ->label(
                                        'Metode Pemeriksaan TB'
                                    )
                                    ->options([
                                        'TCM' => 'TCM',
                                        'BTA' => 'BTA',
                                        'NPOC' => 'NPOC',
                                        'Skoring Anak' => 'Skoring Anak',
                                        'Tidak dilakukan' => 'Tidak dilakukan',
                                    ])
                                    ->default('Tidak dilakukan')
                                    ->required()
                                    ->inline(),

                            ])
                            ->columns(2),

                        Tab::make(
                            'Penyakit Tropis'
                        )
                            ->icon(
                                'heroicon-o-beaker'
                            )
                            ->schema([

                                Forms\Components\Radio::make(
                                    'hasil_frambusia'
                                )
                                    ->label(
                                        'Apakah ada papul/nodul/ulkus/krusta papiloma?'
                                    )
                                    ->options([
                                        'Suspek frambusia' => 'Suspek frambusia',
                                        'Bukan frambusia' => 'Bukan frambusia',
                                        'Tidak ada' => 'Tidak ada',
                                    ])
                                    ->default('Tidak ada')
                                    ->required()
                                    ->inline(),

                                Forms\Components\Radio::make(
                                    'hasil_kusta'
                                )
                                    ->label(
                                        'Apakah ada bercak putih/merah kebal (tidak terasa panas/dingin)?'
                                    )
                                    ->options([
                                        'Kusta' => 'Kusta',
                                        'Bukan Kusta' => 'Bukan Kusta',
                                        'Meragukan' => 'Meragukan',
                                        'Tidak ada' => 'Tidak ada',
                                    ])
                                    ->default('Tidak ada')
                                    ->required()
                                    ->inline(),

                                Forms\Components\Radio::make(
                                    'hasil_skabies'
                                )
                                    ->label(
                                        'Apakah ada ruam/kudis bergerombol yang gatal di malam hari?'
                                    )
                                    ->options([
                                        'Skabies' => 'Skabies',
                                        'Meragukan' => 'Meragukan',
                                        'Bukan skabies' => 'Bukan skabies',
                                        'Tidak ada' => 'Tidak ada',
                                    ])
                                    ->default('Tidak ada')
                                    ->required()
                                    ->inline(),

                            ])
                            ->columns(1),

                        Tab::make(
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
                                    ->visible(fn (Get $get) => $get(
                                        'dirujuk_ke_fasyankes'
                                    ) === 'Y'
                                    )
                                    ->required(fn (Get $get) => $get(
                                        'dirujuk_ke_fasyankes'
                                    ) === 'Y'
                                    )
                                    ->rows(3)
                                    ->columnSpanFull(),

                            ])

                            ->columns(2),

                    ])

                    ->columnSpanFull(),

            ]);
    }

    protected static function calculateImt(
        Get $get,
        Set $set
    ): void {

        $bb = $get(
            'berat_badan'
        );

        $tb = $get(
            'tinggi_badan'
        );

        if (! $bb || ! $tb) {
            return;
        }

        $tbMeter = $tb / 100;

        $imt = $bb / (
            $tbMeter * $tbMeter
        );

        $set(
            'imt',
            round($imt, 2)
        );

        if ($imt < 14) {

            $statusImt =
                'Kurus';

        } elseif ($imt >= 14 && $imt <= 17) {

            $statusImt =
                'Normal';

        } elseif ($imt > 17 && $imt <= 19) {

            $statusImt =
                'Gemuk';

        } else {

            $statusImt =
                'Obesitas';

        }

        $set(
            'status_imt_u',
            $statusImt
        );
    }

    protected static function calculateStatus(
        Get $get,
        Set $set
    ): void {

        $umur =
            $get('umur_bulan');

        $tb =
            $get('tinggi_badan');

        if (! $umur || ! $tb) {
            return;
        }

        if ($umur <= 12) {

            if ($tb < 70) {

                $status =
                    'Sangat Pendek';

            } elseif ($tb < 74) {

                $status =
                    'Pendek';

            } else {

                $status =
                    'Normal';

            }

        } elseif ($umur <= 24) {

            if ($tb < 78) {

                $status =
                    'Sangat Pendek';

            } elseif ($tb < 82) {

                $status =
                    'Pendek';

            } else {

                $status =
                    'Normal';

            }

        } elseif ($umur <= 36) {

            if ($tb < 86) {

                $status =
                    'Sangat Pendek';

            } elseif ($tb < 90) {

                $status =
                    'Pendek';

            } else {

                $status =
                    'Normal';

            }

        } else {

            if ($tb < 95) {

                $status =
                    'Sangat Pendek';

            } elseif ($tb < 100) {

                $status =
                    'Pendek';

            } else {

                $status =
                    'Normal';

            }

        }

        $set(
            'status_tb_u',
            $status
        );

        $set(
            'status_stunting',
            $status
        );
    }

    protected static function calculateLingkarKepala(
        Get $get,
        Set $set
    ): void {

        $lk = $get(
            'lingkar_kepala'
        );

        if (! $lk) {
            return;
        }

        if ($lk < 42) {

            $set(
                'status_lingkar_kepala',
                'Mikrosefali'
            );

        } elseif ($lk > 52) {

            $set(
                'status_lingkar_kepala',
                'Makrosefali'
            );

        } else {

            $set(
                'status_lingkar_kepala',
                'Normal'
            );
        }
    }
}
