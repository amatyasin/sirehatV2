<?php

namespace App\Filament\Resources\RekapPemeriksaans\Tables;

use Filament\Tables;
use Filament\Tables\Table;

class RekapPemeriksaansTable
{
    public static function configure(
        Table $table
    ): Table {

        return $table

            ->modifyQueryUsing(function (
                $query
            ) {

                $query

                    ->where(
                        'aktif',
                        true
                    )

                    ->whereNotNull(
                        'school_id'
                    )

                    ->whereHas(
                        'school'
                    )

                    ->whereHas(
                        'student'
                    );

            })

            ->columns([

                Tables\Columns\TextColumn::make(
                    'student.nama_lengkap'
                )

                    ->label('Nama')

                    ->searchable()

                    ->sortable()

                    ->weight('bold'),

                Tables\Columns\TextColumn::make(
                    'student.nisn'
                )

                    ->label('NISN')

                    ->searchable(),

                Tables\Columns\TextColumn::make(
                    'school.nama_sekolah'
                )

                    ->label('Sekolah')

                    ->searchable()

                    ->sortable(),

                Tables\Columns\TextColumn::make(
                    'schoolClass.nama_kelas'
                )

                    ->label('Kelas')

                    ->badge()

                    ->color('primary')

                    ->sortable(),

                Tables\Columns\TextColumn::make(
                    'academicYear.nama'
                )

                    ->label('Tahun Ajaran')

                    ->badge()

                    ->color('success')

                    ->sortable(),

                Tables\Columns\TextColumn::make(
                    'semester'
                )

                    ->label('Semester')

                    ->badge()

                    ->color('warning')

                    ->sortable(),

                Tables\Columns\TextColumn::make(
                    'aktif'
                )

                    ->label('Status')

                    ->formatStateUsing(
                        fn ($state) => $state
                                ? 'Aktif'
                                : 'Tidak Aktif'
                    )

                    ->badge()

                    ->color(
                        fn ($state) => $state
                                ? 'success'
                                : 'danger'
                    ),

                Tables\Columns\IconColumn::make(
                    'pemeriksaan_umum'
                )

                    ->label('Umum')

                    ->boolean()

                    ->getStateUsing(
                        fn ($record) => $record
                            ->pemeriksaanUmums
                            ->isNotEmpty()
                    ),

                Tables\Columns\IconColumn::make(
                    'pemeriksaan_gigi'
                )

                    ->label('Gigi')

                    ->boolean()

                    ->getStateUsing(
                        fn ($record) => $record
                            ->pemeriksaanGigis
                            ->isNotEmpty()
                    ),

                Tables\Columns\IconColumn::make(
                    'pemeriksaan_mata'
                )

                    ->label('Mata')

                    ->boolean()

                    ->getStateUsing(
                        fn ($record) => $record
                            ->pemeriksaanMatas
                            ->isNotEmpty()
                    ),

                Tables\Columns\IconColumn::make(
                    'pemeriksaan_gizi'
                )

                    ->label('Gizi')

                    ->boolean()

                    ->getStateUsing(
                        fn ($record) => $record
                            ->pemeriksaanGizis
                            ->isNotEmpty()
                    ),

            ])

            ->filters([

                Tables\Filters\SelectFilter::make(
                    'school'
                )

                    ->label('Sekolah')

                    ->relationship(
                        'school',
                        'nama_sekolah'
                    )

                    ->searchable()

                    ->preload(),

                Tables\Filters\SelectFilter::make(
                    'school_class'
                )

                    ->label('Kelas')

                    ->relationship(
                        'schoolClass',
                        'nama_kelas'
                    )

                    ->searchable()

                    ->preload(),

                Tables\Filters\SelectFilter::make(
                    'academic_year'
                )

                    ->label('Tahun Ajaran')

                    ->relationship(
                        'academicYear',
                        'nama'
                    )

                    ->searchable()

                    ->preload(),

                Tables\Filters\SelectFilter::make(
                    'semester'
                )

                    ->label('Semester')

                    ->options([

                        'Ganjil' => 'Ganjil',

                        'Genap' => 'Genap',

                    ]),

            ])

            ->defaultSort(
                'created_at',
                'desc'
            );
    }
}
