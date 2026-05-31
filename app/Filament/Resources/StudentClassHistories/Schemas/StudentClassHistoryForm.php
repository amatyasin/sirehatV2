<?php

namespace App\Filament\Resources\StudentClassHistories\Schemas;

use App\Models\AcademicYear;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Student;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class StudentClassHistoryForm
{
    public static function configure(
        Schema $schema
    ): Schema {

        return $schema
            ->components([

                Section::make(
                    'Riwayat Kelas'
                )

                    ->description(
                        'Riwayat akademik siswa'
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

                            ->required()

                            ->live()

                            ->default(
                                auth()->user()->school_id
                            )

                            ->disabled(

                                fn () => auth()
                                    ->user()
                                    ->hasRole(
                                        'admin_sekolah'
                                    )

                            )

                            ->dehydrated(true)

                            ->afterStateUpdated(
                                fn (
                                    Set $set
                                ) => [

                                    $set(
                                        'student_id',
                                        null
                                    ),

                                    $set(
                                        'school_class_id',
                                        null
                                    ),

                                ]
                            ),

                        Forms\Components\Select::make(
                            'student_id'
                        )

                            ->label(
                                'Siswa'
                            )

                            ->options(function (
                                Get $get
                            ) {

                                if (
                                    ! $get(
                                        'school_id'
                                    )
                                ) {

                                    return [];
                                }

                                return Student::query()

                                    ->where(
                                        'school_id',
                                        $get(
                                            'school_id'
                                        )
                                    )

                                    ->orderBy(
                                        'nama_lengkap'
                                    )

                                    ->pluck(
                                        'nama_lengkap',
                                        'id'
                                    );

                            })

                            ->searchable()

                            ->preload()

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

                                if (
                                    ! $get(
                                        'school_id'
                                    )
                                ) {

                                    return [];
                                }

                                return SchoolClass::query()

                                    ->where(
                                        'school_id',
                                        $get(
                                            'school_id'
                                        )
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

                            ->required(),

                        Forms\Components\Select::make(
                            'academic_year_id'
                        )

                            ->label(
                                'Tahun Ajaran'
                            )

                            ->options(

                                AcademicYear::query()

                                    ->orderByDesc(
                                        'aktif'
                                    )

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
