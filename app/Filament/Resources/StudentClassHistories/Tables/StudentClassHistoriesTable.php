<?php

namespace App\Filament\Resources\StudentClassHistories\Tables;

use App\Models\School;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StudentClassHistoriesTable
{
    public static function configure(
        Table $table
    ): Table {

        return $table

            ->columns([

                TextColumn::make(
                    'school.nama_sekolah'
                )

                    ->label('Sekolah')

                    ->searchable()

                    ->sortable(),

                TextColumn::make(
                    'student.nama_lengkap'
                )

                    ->label('Nama Siswa')

                    ->searchable()

                    ->sortable()

                    ->weight('bold'),

                TextColumn::make(
                    'schoolClass.nama_kelas'
                )

                    ->label('Kelas')

                    ->badge()

                    ->color('primary')

                    ->sortable(),

                TextColumn::make(
                    'academicYear.nama'
                )

                    ->label('Tahun Ajaran')

                    ->badge()

                    ->color('success')

                    ->sortable(),

                TextColumn::make(
                    'semester'
                )

                    ->label('Semester')

                    ->badge()

                    ->color('warning')

                    ->sortable(),

                IconColumn::make(
                    'aktif'
                )

                    ->label('Aktif')

                    ->boolean(),

                TextColumn::make(
                    'created_at'
                )

                    ->label('Dibuat')

                    ->dateTime(
                        'd M Y H:i'
                    )

                    ->sortable()

                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

            ])

            ->filters([

                Tables\Filters\SelectFilter::make(
                    'school_id'
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

                    ->query(function (
                        $query,
                        array $data
                    ) {

                        if (! $data['value']) {
                            return $query;
                        }

                        return $query->where(
                            'school_id',
                            $data['value']
                        );

                    }),

                Tables\Filters\SelectFilter::make(
                    'school_class_id'
                )

                    ->relationship(
                        'schoolClass',
                        'nama_kelas'
                    )

                    ->label('Kelas')

                    ->searchable()

                    ->preload(),

                Tables\Filters\SelectFilter::make(
                    'academic_year_id'
                )

                    ->relationship(
                        'academicYear',
                        'nama'
                    )

                    ->label('Tahun Ajaran')

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

                Tables\Filters\TernaryFilter::make(
                    'aktif'
                )

                    ->label(
                        'Status Aktif'
                    ),

            ])

            ->defaultSort(
                'created_at',
                'desc'
            )

            ->recordActions([

                EditAction::make()

                    ->visible(

                        auth()
                            ->user()
                            ->hasAnyRole([

                                'super_admin',

                                'admin_dinkes',

                            ])

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
