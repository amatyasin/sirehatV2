<?php

namespace App\Filament\Resources\PemeriksaanGizis\Schemas;

use App\Models\StudentClassHistory;
use Carbon\Carbon;
use Filament\Forms;
use Illuminate\Support\HtmlString;
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
                    'Identitas Siswa')
                    ->description(
                        'Informasi dasar siswa')
                    ->icon(
                        'heroicon-o-user')
                    ->schema([
                        Forms\Components\Select::make(
                            'student_class_history_id')
                            ->label('Siswa')
                            ->disabledOn('edit')
                            ->dehydrated(
                                fn ($operation) => $operation === 'create')
                            ->unique(
                                table: 'pemeriksaan_gizis',
                                column: 'student_class_history_id',
                                ignoreRecord: true)
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
                                        ->where('aktif',true);
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
                                            $user->instansi_id));
                                }
                                if (
                                    $user->hasRole(
                                        'admin_sekolah')
                                ) {
                                    $query->where(
                                        'school_id',
                                        $user->school_id);
                                }
                                return $query
                                    ->orderByDesc('id')
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
                                                    ?->nama_lengkap,];
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
                                if (($student?->jenis_kelamin ?? '') !== 'P') {
                                    $set('hemoglobin', null);
                                    $set('status_anemia', null);
                                }
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
                                        ?->tanggal_lahir?
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

                    ])

                    ->columns(2),

                Section::make(
                    'Pemeriksaan Anemia'
                )

                    ->icon('heroicon-o-beaker')

                    ->description(
                        'Hanya ditampilkan untuk siswa perempuan'
                    )

                    ->visible(
                        fn (Get $get) =>
                            $get('jenis_kelamin') === 'P'
                    )

                    ->schema([

                        Forms\Components\TextInput::make(
                            'hemoglobin'
                        )

                            ->label('Kadar Hemoglobin (Hb)')

                            ->placeholder('Contoh: 12.5')

                            ->numeric()

                            ->step(0.1)

                            ->minValue(0)

                            ->maxValue(30)

                            ->suffix('g/dL')

                            ->live()

                            ->afterStateUpdated(function ($state, Set $set) {
                                $hb = (float) $state;
                                if ($hb <= 0) {
                                    $set('status_anemia', null);
                                    return;
                                }
                                if ($hb > 12) {
                                    $status = 'Normal';
                                } elseif ($hb >= 11) {
                                    $status = 'Anemia Ringan';
                                } elseif ($hb >= 8) {
                                    $status = 'Anemia Sedang';
                                } else {
                                    $status = 'Anemia Berat';
                                }
                                $set('status_anemia', $status);
                            }),

                        Forms\Components\Hidden::make(
                            'status_anemia'
                        ),

                        Forms\Components\Placeholder::make(
                            'status_anemia_label'
                        )

                            ->label('Status Anemia')

                            ->content(function (Get $get) {

                                $hb = (float) $get('hemoglobin');

                                if ($hb <= 0) {
                                    return new HtmlString(
                                        '<span style="color:#6b7280;font-style:italic;">— Isi kadar Hb terlebih dahulu</span>'
                                    );
                                }

                                if ($hb > 12) {
                                    return new HtmlString(
                                        '<span style="display:inline-flex;align-items:center;gap:6px;background:#dcfce7;color:#166534;padding:6px 14px;border-radius:9999px;font-weight:600;">
                                            🟢 Normal
                                            <small style="font-weight:400;opacity:.8;">(Hb &gt; 12 g/dL)</small>
                                        </span>'
                                    );
                                }

                                if ($hb >= 11) {
                                    return new HtmlString(
                                        '<span style="display:inline-flex;align-items:center;gap:6px;background:#fef9c3;color:#854d0e;padding:6px 14px;border-radius:9999px;font-weight:600;">
                                            🟡 Anemia Ringan
                                            <small style="font-weight:400;opacity:.8;">(Hb 11 – 11,9 g/dL)</small>
                                        </span>'
                                    );
                                }

                                if ($hb >= 8) {
                                    return new HtmlString(
                                        '<span style="display:inline-flex;align-items:center;gap:6px;background:#ffedd5;color:#9a3412;padding:6px 14px;border-radius:9999px;font-weight:600;">
                                            🟠 Anemia Sedang
                                            <small style="font-weight:400;opacity:.8;">(Hb 8 – 10,9 g/dL)</small>
                                        </span>'
                                    );
                                }

                                return new HtmlString(
                                    '<span style="display:inline-flex;align-items:center;gap:6px;background:#fee2e2;color:#991b1b;padding:6px 14px;border-radius:9999px;font-weight:600;">
                                        🔴 Anemia Berat
                                        <small style="font-weight:400;opacity:.8;">(Hb &lt; 8 g/dL)</small>
                                    </span>'
                                );
                            }),

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
