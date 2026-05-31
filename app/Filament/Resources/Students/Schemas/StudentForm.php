<?php

namespace App\Filament\Resources\Students\Schemas;

use App\Models\AcademicYear;
use App\Models\School;
use App\Models\SchoolClass;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class StudentForm
{
    public static function configure(
        Schema $schema
    ): Schema {
        return $schema
            ->components([
                Section::make(
                    'Informasi Siswa'
                )
                    ->description(
                        'Data identitas siswa'
                    )
                    ->schema([
                        Forms\Components\Select::make(
                            'instansi_id'
                        )
                            ->label(
                                'Puskesmas'
                            )
                            ->relationship(
                                'instansi',
                                'nama_instansi'
                            )
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->live()
                            ->required()
                            ->default(
                                auth()->user()->instansi_id
                            )
                            ->disabled(
                                auth()
                                    ->user()
                                    ->hasAnyRole([
                                        'admin_instansi',
                                        'admin_sekolah',
                                    ])
                            )
                            ->visible(
                                auth()
                                    ->user()
                                    ->hasAnyRole([
                                        'super_admin',
                                        'admin_dinkes',
                                        'admin_instansi',
                                        'admin_sekolah',
                                    ])
                            )
                            ->afterStateUpdated(
                                fn (Set $set) => [
                                    $set(
                                        'school_id',
                                        null
                                    ),
                                    $set(
                                        'school_class_id',
                                        null
                                    ),
                                ]
                            ),
                        Forms\Components\Select::make(
                            'school_id'
                        )
                            ->label(
                                'Sekolah'
                            )
                            ->options(function (
                                Get $get
                            ) {
                                $user =
                                    auth()->user();
                                $query =
                                    School::query();
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
                                        'admin_sekolah'
                                    )
                                ) {
                                    $query->where(
                                        'id',
                                        $user->school_id
                                    );
                                }
                                if (
                                    $get(
                                        'instansi_id'
                                    )
                                ) {
                                    $query->where(
                                        'instansi_id',
                                        $get(
                                            'instansi_id'
                                        )
                                    );
                                }
                                return $query
                                    ->orderBy(
                                        'nama_sekolah'
                                    )
                                    ->pluck(
                                        'nama_sekolah',
                                        'id'
                                    );
                            })
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->required()
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
                            ->live()
                            ->afterStateUpdated(function (
                                $state,
                                Set $set
                            ) {
                                $school =
                                    School::find(
                                        $state
                                    );
                                if (! $school) {
                                    return;
                                }
                                $set(
                                    'instansi_id',
                                    $school->instansi_id
                                );
                                $set(
                                    'school_class_id',
                                    null
                                );
                            }),
                        Forms\Components\Select::make(
                            'academic_year_id'
                        )
                            ->label(
                                'Tahun Ajaran'
                            )
                            ->options(
                                AcademicYear::query()
                                    ->orderByDesc(
                                        'id'
                                    )
                                    ->pluck(
                                        'nama',
                                        'id'
                                    )
                            )
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->required(),
                        Forms\Components\Select::make(
                            'semester'
                        )
                            ->label(
                                'Semester'
                            )
                            ->options([
                                'Ganjil' => 'Ganjil',
                                'Genap' => 'Genap',
                            ])
                            ->native(false)
                            ->required(),
                        Forms\Components\Select::make(
                            'school_class_id'
                        )
                            ->label(
                                'Kelas'
                            )
                            ->options(function (
                                Get $get
                            ) {
                                if (! $get(
                                    'school_id'
                                )) {
                                    return [];
                                }
                                return SchoolClass::query()
                                    ->where(
                                        'school_id',
                                        $get('school_id')
                                    )
                                    ->orderBy(
                                        'urutan'
                                    )
                                    ->pluck(
                                        'nama_kelas',
                                        'id'
                                    );
                            })
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->required(),
                        Forms\Components\TextInput::make(
                            'nama_lengkap'
                        )
                            ->label(
                                'Nama Lengkap'
                            )
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make(
                            'nik'
                        )
                            ->label('NIK')
                            ->nullable()
                            ->dehydrateStateUsing(
                                fn ($state) => filled($state)
                                        ? trim($state)
                                        : null
                            )
                            ->unique(
                                ignoreRecord: true
                            )
                            ->validationMessages([
                                'unique' => 'NIK sudah digunakan.',
                            ])
                            ->tel()
                            ->maxLength(20),
                        Forms\Components\TextInput::make(
                            'nisn'
                        )
                            ->label('NISN')
                            ->required()
                            ->unique(
                                ignoreRecord: true
                            )
                            ->validationMessages([
                                'required' => 'NISN wajib diisi.',
                                'unique' => 'NISN sudah digunakan.',
                            ])
                            ->dehydrateStateUsing(
                                fn ($state) => trim($state)
                            )
                            ->tel()
                            ->maxLength(20),
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
                        Forms\Components\TextInput::make(
                            'tempat_lahir'
                        )
                            ->label(
                                'Tempat Lahir'
                            )
                            ->required()
                            ->maxLength(255),
                        Forms\Components\DatePicker::make(
                            'tanggal_lahir'
                        )
                            ->label(
                                'Tanggal Lahir'
                            )
                            ->maxDate(
                                now()
                            )
                            ->required(),
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