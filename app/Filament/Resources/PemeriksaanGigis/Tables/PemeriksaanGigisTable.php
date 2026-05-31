<?php

namespace App\Filament\Resources\PemeriksaanGigis\Tables;

use App\Filament\Resources\PemeriksaanGigis\PemeriksaanGigiResource;
use App\Models\AcademicYear;
use App\Models\School;
use App\Models\SchoolClass;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PemeriksaanGigisTable
{
    public static function configure(
        Table $table
    ): Table {
        return $table
            ->columns([
                TextColumn::make(
                    'studentClassHistory.student.nama_lengkap')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->grow(false),
                TextColumn::make(
                    'studentClassHistory.student.nisn')
                    ->label('NISN')
                    ->searchable(),
                TextColumn::make(
                    'studentClassHistory.school.nama_sekolah')
                    ->label('Sekolah')
                    ->searchable()
                    ->sortable(),
                TextColumn::make(
                    'studentClassHistory.schoolClass.nama_kelas')
                    ->label('Kelas')
                    ->badge()
                    ->color('primary')
                    ->sortable(),
                TextColumn::make(
                    'studentClassHistory.academicYear.nama')
                    ->label('Tahun Ajaran')
                    ->badge()
                    ->color('success')
                    ->sortable(),
                TextColumn::make(
                    'studentClassHistory.semester')
                    ->label('Semester')
                    ->badge()
                    ->color('warning')
                    ->sortable(),
                TextColumn::make(
                    'studentClassHistory.student.jenis_kelamin')
                    ->label('Jenis Kelamin')
                    ->badge()
                    ->formatStateUsing(
                        fn ($state) => $state === 'L'
                            ? 'Laki-laki'
                            : 'Perempuan')
                    ->color(
                        fn ($state) => match ($state) {
                            'L' => 'primary',
                            'P' => 'danger',
                            default => 'gray',
                        }),
                TextColumn::make(
                    'tanggal_pemeriksaan')
                    ->label('Tanggal Pemeriksaan')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make(
                    'created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('school_id')
                    ->label('Sekolah')
                    ->options(function () {
                        $user = auth()->user();
                        $query = School::query();
                        if (
                            $user->hasAnyRole(['admin_instansi','petugas_pemeriksaan',])) 
                            {
                            $query->where('instansi_id', $user->instansi_id);
                        }
                        if (
                            $user->hasRole('admin_sekolah')) {
                            $query->where('id', $user->school_id);
                        }
                        return $query
                            ->orderBy('nama_sekolah')
                            ->pluck('nama_sekolah', 'id');
                    })
                    ->searchable()
                    ->preload()
                    ->query(function ($query,array $data) 
                    {
                        if (! $data['value']) {return $query;
                        }
                        return $query->whereHas(
                            'studentClassHistory',
                            fn ($q) => $q->where('school_id',$data['value']));
                    }),
                Tables\Filters\SelectFilter::make('school_class_id')
                    ->label('Kelas')
                    ->options(function () {
                        $user =
                            auth()->user();
                        $query =
                            SchoolClass::query();
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
                    ->query(function (
                        $query,
                        array $data
                    ) {
                        if (! $data['value']) {
                            return $query;
                        }
                        return $query->whereHas(
                            'studentClassHistory',
                            fn ($q) => $q->where(
                                'school_class_id',
                                $data['value']
                            )
                        );
                    }),
                Tables\Filters\SelectFilter::make(
                    'academic_year_id'
                )
                    ->label('Tahun Ajaran')
                    ->options(
                        AcademicYear::query()
                            ->orderByDesc('id')
                            ->pluck(
                                'nama',
                                'id'
                            )
                    )
                    ->searchable()
                    ->preload()
                    ->query(function (
                        $query,
                        array $data
                    ) {
                        if (! $data['value']) {
                            return $query;
                        }
                        return $query->whereHas(
                            'studentClassHistory',
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
                        $query,
                        array $data
                    ) {
                        if (! $data['value']) {
                            return $query;
                        }
                        return $query->whereHas(
                            'studentClassHistory',
                            fn ($q) => $q->where(
                                'semester',
                                $data['value']
                            )
                        );
                    }),
                Tables\Filters\TernaryFilter::make(
                    'aktif'
                )
                    ->label(
                        'History Aktif'
                    )
                    ->queries(
                        true: fn ($query) => $query->whereHas(
                            'studentClassHistory',
                            fn ($q) => $q->where(
                                'aktif',
                                true
                            )
                        ),
                        false: fn ($query) => $query->whereHas(
                            'studentClassHistory',
                            fn ($q) => $q->where(
                                'aktif',
                                false
                            )
                        ),
                        blank: fn ($query) => $query
                    ),
                Tables\Filters\SelectFilter::make(
                    'jenis_kelamin'
                )
                    ->label('Jenis Kelamin')
                    ->options([
                        'L' => 'Laki-laki',
                        'P' => 'Perempuan',
                    ])
                    ->query(function (
                        $query,
                        array $data
                    ) {
                        if (! $data['value']) {
                            return $query;
                        }
                        return $query->whereHas(
                            'studentClassHistory.student',
                            fn ($q) => $q->where(
                                'jenis_kelamin',
                                $data['value']
                            )
                        );
                    }),
            ])
            ->defaultSort(
                'tanggal_pemeriksaan',
                'desc'
            )
            ->recordActions([
                EditAction::make()
                    ->visible(
                        fn ($record) => PemeriksaanGigiResource::canEdit(
                            $record
                        )
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(
                            auth()
                                ->user()
                                ->hasAnyRole([
                                    'super_admin',
                                    'admin_dinkes',
                                ])
                        ),
                ]),
            ]);
    }
}
