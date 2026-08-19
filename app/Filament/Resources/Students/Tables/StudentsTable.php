<?php

namespace App\Filament\Resources\Students\Tables;

use App\Exports\StudentsExport;
use App\Imports\StudentsImport;
use App\Models\AcademicYear;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\StudentClassHistory;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;

class StudentsTable
{
    public static function configure(
        Table $table
    ): Table {

        return $table

            ->columns([

                Tables\Columns\TextColumn::make(
                    'school.instansi.nama_instansi'
                )

                    ->label('Puskesmas')

                    ->searchable()

                    ->sortable(),

                Tables\Columns\TextColumn::make(
                    'activeClassHistory.school.nama_sekolah'
                )

                    ->label('Sekolah')

                    ->searchable()

                    ->sortable(),

                Tables\Columns\TextColumn::make(
                    'nama_lengkap'
                )

                    ->label('Nama Siswa')

                    ->searchable()

                    ->sortable()

                    ->weight('bold'),

                Tables\Columns\TextColumn::make(
                    'activeClassHistory.schoolClass.nama_kelas'
                )

                    ->label('Kelas')

                    ->badge()

                    ->color('primary'),

                Tables\Columns\TextColumn::make(
                    'activeClassHistory.academicYear.nama'
                )

                    ->label('Tahun Ajaran')

                    ->badge()

                    ->color('success'),

                Tables\Columns\TextColumn::make(
                    'activeClassHistory.semester'
                )

                    ->label('Semester')

                    ->badge()

                    ->color('warning'),

                Tables\Columns\TextColumn::make(
                    'jenis_kelamin'
                )

                    ->label('Jenis Kelamin')

                    ->badge()

                    ->formatStateUsing(

                        fn ($state) => $state === 'L'

                                ? 'Laki-laki'

                                : 'Perempuan'

                    ),

                Tables\Columns\TextColumn::make(
                    'tanggal_lahir'
                )

                    ->label('Tanggal Lahir')

                    ->date(),

                Tables\Columns\IconColumn::make(
                    'aktif'
                )

                    ->label('Aktif')

                    ->boolean(),

            ])

            ->filters([

                Tables\Filters\SelectFilter::make(
                    'school'
                )

                    ->label('Sekolah')

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

                        return $query->pluck(
                            'nama_sekolah',
                            'id'
                        );

                    })

                    ->searchable()

                    ->query(function (
                        Builder $query,
                        array $data
                    ) {

                        if (! $data['value']) {
                            return $query;
                        }

                        return $query->whereHas(

                            'activeClassHistory',

                            fn ($q) => $q->where(
                                'school_id',
                                $data['value']
                            )

                        );

                    }),

                Tables\Filters\SelectFilter::make(
                    'academic_year'
                )

                    ->label('Tahun Ajaran')

                    ->options(

                        AcademicYear::pluck(
                            'nama',
                            'id'
                        )

                    )

                    ->query(function (
                        Builder $query,
                        array $data
                    ) {

                        if (! $data['value']) {
                            return $query;
                        }

                        return $query->whereHas(

                            'activeClassHistory',

                            fn ($q) => $q->where(
                                'academic_year_id',
                                $data['value']
                            )

                        );

                    }),

                Tables\Filters\SelectFilter::make(
                    'semester'
                )

                    ->label('Semester')

                    ->options([

                        'Ganjil' => 'Ganjil',

                        'Genap' => 'Genap',

                    ])

                    ->query(function (
                        Builder $query,
                        array $data
                    ) {

                        if (! $data['value']) {
                            return $query;
                        }

                        return $query->whereHas(

                            'activeClassHistory',

                            fn ($q) => $q->where(
                                'semester',
                                $data['value']
                            )

                        );

                    }),

                Tables\Filters\SelectFilter::make(
                    'school_class'
                )

                    ->label('Kelas')

                    ->options(

                        SchoolClass::query()

                            ->orderBy(
                                'urutan'
                            )

                            ->pluck(
                                'nama_kelas',
                                'id'
                            )

                    )

                    ->query(function (
                        Builder $query,
                        array $data
                    ) {

                        if (! $data['value']) {
                            return $query;
                        }

                        return $query->whereHas(

                            'activeClassHistory',

                            fn ($q) => $q->where(
                                'school_class_id',
                                $data['value']
                            )

                        );

                    }),

            ])

            ->headerActions([

                Action::make(
                    'download_template'
                )

                    ->label(
                        'Download Template'
                    )

                    ->icon(
                        'heroicon-o-arrow-down-tray'
                    )

                    ->color('gray')

                    ->url(

                        asset(
                            'templates/template_siswa.xlsx'
                        )

                    )

                    ->openUrlInNewTab(),

                Action::make(
                    'import'
                )

                    ->label(
                        'Import Excel'
                    )

                    ->icon(
                        'heroicon-o-arrow-up-tray'
                    )

                    ->form([

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

                                return $query->pluck(
                                    'nama_sekolah',
                                    'id'
                                );

                            })

                            ->searchable()

                            ->required(),

                        Forms\Components\Select::make(
                            'academic_year_id'
                        )

                            ->label(
                                'Tahun Ajaran'
                            )

                            ->options(

                                AcademicYear::pluck(
                                    'nama',
                                    'id'
                                )

                            )

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

                            ->required(),

                        Forms\Components\Select::make(
                            'school_class_id'
                        )

                            ->label(
                                'Kelas'
                            )

                            ->options(

                                SchoolClass::query()

                                    ->orderBy(
                                        'urutan'
                                    )

                                    ->pluck(
                                        'nama_kelas',
                                        'id'
                                    )

                            )

                            ->required(),

                        Forms\Components\FileUpload::make(
                            'file'
                        )

                            ->disk('local')

                            ->directory(
                                'imports'
                            )

                            ->acceptedFileTypes([

                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',

                                'application/vnd.ms-excel',

                                'text/csv',

                                'text/plain',

                                'application/csv',

                                'application/x-csv',

                                'application/zip',

                                'application/x-zip-compressed',

                                'application/octet-stream',

                            ])

                            ->required(),

                    ])

                    ->action(function (
                        $data
                    ) {

                        $school =
                            School::find(
                                $data['school_id']
                            );

                        $relativeFile = $data['file'];
                        if (\Illuminate\Support\Facades\Storage::disk('local')->exists($relativeFile)) {
                            $file = \Illuminate\Support\Facades\Storage::disk('local')->path($relativeFile);
                        } elseif (file_exists(storage_path('app/private/' . $relativeFile))) {
                            $file = storage_path('app/private/' . $relativeFile);
                        } else {
                            $file = storage_path('app/' . $relativeFile);
                        }

                        Excel::import(

                            new StudentsImport(

                                $school->instansi_id,

                                $school->id,

                                $data['academic_year_id'],

                                $data['semester'],

                                $data['school_class_id']

                            ),

                            $file

                        );

                        Notification::make()

                            ->title(
                                'Import berhasil'
                            )

                            ->success()

                            ->send();

                    }),

                Action::make(
                    'export'
                )

                    ->label(
                        'Export Excel'
                    )

                    ->icon(
                        'heroicon-o-document-arrow-down'
                    )

                    ->color('success')

                    ->form([

                        Forms\Components\Select::make(
                            'school_id'
                        )

                            ->label('Sekolah')

                            ->options(function () {

                                $user = auth()->user();
                                $query = School::query();

                                if ($user->hasRole('admin_instansi')) {
                                    $query->where('instansi_id', $user->instansi_id);
                                }

                                if ($user->hasRole('admin_sekolah')) {
                                    $query->where('id', $user->school_id);
                                }

                                return $query->pluck('nama_sekolah', 'id');

                            })

                            ->searchable()

                            ->placeholder('Semua Sekolah')

                            ->nullable(),

                        Forms\Components\Select::make(
                            'academic_year_id'
                        )

                            ->label('Tahun Ajaran')

                            ->options(
                                AcademicYear::orderByDesc('id')->pluck('nama', 'id')
                            )

                            ->placeholder('Semua Tahun Ajaran')

                            ->nullable(),

                        Forms\Components\Select::make(
                            'semester'
                        )

                            ->label('Semester')

                            ->options([
                                'Ganjil' => 'Ganjil',
                                'Genap'  => 'Genap',
                            ])

                            ->placeholder('Semua Semester')

                            ->nullable(),

                        Forms\Components\Select::make(
                            'school_class_id'
                        )

                            ->label('Kelas')

                            ->options(
                                SchoolClass::query()
                                    ->orderBy('urutan')
                                    ->pluck('nama_kelas', 'id')
                            )

                            ->placeholder('Semua Kelas')

                            ->nullable(),

                        Forms\Components\Select::make(
                            'jenis_kelamin'
                        )

                            ->label('Jenis Kelamin')

                            ->options([
                                'L' => 'Laki-laki',
                                'P' => 'Perempuan',
                            ])

                            ->placeholder('Semua')

                            ->nullable(),

                    ])

                    ->action(function (array $data) {

                        $filters = array_filter(
                            $data,
                            fn ($v) => $v !== null && $v !== ''
                        );

                        $filename = 'siswa_' . now()->format('Ymd_His') . '.xlsx';

                        return Excel::download(
                            new StudentsExport($filters),
                            $filename
                        );

                    }),

                CreateAction::make(),

            ])

            ->recordActions([

                ViewAction::make(),

                EditAction::make(),

            ])

            ->toolbarActions([

                BulkAction::make(
                    'naik_kelas'
                )

                    ->label(
                        'Update Riwayat Akademik'
                    )

                    ->icon(
                        'heroicon-o-arrow-up'
                    )

                    ->color('success')

                    ->form([

                        Forms\Components\Select::make(
                            'school_class_id'
                        )

                            ->label(
                                'Kelas Tujuan'
                            )

                            ->options(

                                SchoolClass::query()

                                    ->orderBy(
                                        'urutan'
                                    )

                                    ->pluck(
                                        'nama_kelas',
                                        'id'
                                    )

                            )

                            ->required(),

                        Forms\Components\Select::make(
                            'academic_year_id'
                        )

                            ->label(
                                'Tahun Ajaran'
                            )

                            ->options(

                                AcademicYear::pluck(
                                    'nama',
                                    'id'
                                )

                            )

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

                            ->required(),

                    ])

                    ->action(function (
                        array $data,
                        $records
                    ) {

                        foreach (
                            $records as $student
                        ) {

                            $exists =
                                StudentClassHistory::query()

                                    ->where(
                                        'student_id',
                                        $student->id
                                    )

                                    ->where(
                                        'school_id',
                                        $student
                                            ->activeClassHistory
                                            ?->school_id
                                    )

                                    ->where(
                                        'school_class_id',
                                        $data['school_class_id']
                                    )

                                    ->where(
                                        'academic_year_id',
                                        $data['academic_year_id']
                                    )

                                    ->where(
                                        'semester',
                                        $data['semester']
                                    )

                                    ->first();

                            if ($exists) {

                                $exists->update([

                                    'aktif' => true,

                                ]);

                            } else {

                                StudentClassHistory::where(
                                    'student_id',
                                    $student->id
                                )->update([

                                    'aktif' => false,

                                ]);

                                StudentClassHistory::create([

                                    'student_id' => $student->id,

                                    'school_id' => $student
                                        ->activeClassHistory
                                        ?->school_id,

                                    'school_class_id' => $data['school_class_id'],

                                    'academic_year_id' => $data['academic_year_id'],

                                    'semester' => $data['semester'],

                                    'aktif' => true,

                                ]);

                            }

                        }

                        Notification::make()

                            ->title(
                                'Riwayat akademik berhasil diperbarui'
                            )

                            ->success()

                            ->send();

                    }),

                DeleteBulkAction::make()

                    ->visible(

                        auth()
                            ->user()
                            ->hasAnyRole([

                                'super_admin',

                                'admin_dinkes',

                            ])

                    ),

            ]);
    }
}
