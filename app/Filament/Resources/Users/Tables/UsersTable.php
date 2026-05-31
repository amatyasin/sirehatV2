<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\Instansi;
use App\Models\Kecamatan;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(
        Table $table
    ): Table {

        return $table

            ->columns([

                Tables\Columns\TextColumn::make(
                    'name'
                )

                    ->label('Nama')

                    ->searchable()

                    ->sortable()

                    ->weight('bold'),

                Tables\Columns\TextColumn::make(
                    'email'
                )

                    ->label('Email')

                    ->searchable()

                    ->copyable()

                    ->sortable(),

                Tables\Columns\TextColumn::make(
                    'roles.name'
                )

                    ->label('Role')

                    ->badge()

                    ->color(
                        fn ($state) => match ($state) {

                            'super_admin' => 'danger',

                            'admin_dinkes' => 'success',

                            'admin_kecamatan' => 'warning',

                            'admin_instansi' => 'primary',

                            'admin_sekolah' => 'info',

                            'petugas_sekolah' => 'success',

                            'petugas_posyandu' => 'gray',

                            default => 'secondary',
                        }
                    )

                    ->formatStateUsing(
                        fn ($state) => str(
                            $state
                        )
                            ->replace('_', ' ')
                            ->title()
                    ),

                Tables\Columns\TextColumn::make(
                    'kecamatan.nama_kecamatan'
                )

                    ->label('Kecamatan')

                    ->searchable()

                    ->sortable()

                    ->toggleable(),

                Tables\Columns\TextColumn::make(
                    'instansi.nama_instansi'
                )

                    ->label('Puskesmas')

                    ->searchable()

                    ->sortable()

                    ->toggleable(),

                Tables\Columns\TextColumn::make(
                    'school.nama_sekolah'
                )

                    ->label('Sekolah')

                    ->searchable()

                    ->sortable()

                    ->toggleable(),

                Tables\Columns\TextColumn::make(
                    'posyandu.nama_posyandu'
                )

                    ->label('Posyandu')

                    ->searchable()

                    ->sortable()

                    ->toggleable(),

                Tables\Columns\TextColumn::make(
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
                    'roles'
                )

                    ->relationship(
                        'roles',
                        'name'
                    )

                    ->label('Role')

                    ->searchable()

                    ->preload(),

                Tables\Filters\SelectFilter::make(
                    'instansi_id'
                )

                    ->label('Puskesmas')

                    ->options(function () {

                        $user =
                            auth()->user();

                        $query =
                            Instansi::query();

                        if (

                            $user->hasRole(
                                'admin_puskesmas'
                            )

                        ) {

                            $query->where(
                                'id',
                                $user->instansi_id
                            );
                        }

                        return $query->pluck(
                            'nama_instansi',
                            'id'
                        );

                    })

                    ->searchable()

                    ->preload(),

                Tables\Filters\SelectFilter::make(
                    'kecamatan_id'
                )

                    ->label('Kecamatan')

                    ->options(function () {

                        $user =
                            auth()->user();

                        $query =
                            Kecamatan::query();

                        if (

                            $user->hasRole(
                                'admin_puskesmas'
                            )

                        ) {

                            $query->whereHas(

                                'instansis',

                                function ($q) use ($user) {

                                    $q->where(
                                        'id',
                                        $user->instansi_id
                                    );

                                }

                            );

                        }

                        return $query->pluck(
                            'nama_kecamatan',
                            'id'
                        );

                    })

                    ->searchable()

                    ->preload(),

            ])

            ->defaultSort(
                'name'
            )

            ->actions([

                EditAction::make(),

                DeleteAction::make()

                    ->visible(function () {

                        return auth()
                            ->user()
                            ->hasAnyRole([

                                'super_admin',

                                'admin_dinkes',

                            ]);

                    }),

            ])

            ->bulkActions([

                BulkActionGroup::make([

                    DeleteBulkAction::make()

                        ->visible(function () {

                            return auth()
                                ->user()
                                ->hasAnyRole([

                                    'super_admin',

                                    'admin_dinkes',

                                ]);

                        }),

                ]),

            ]);
    }
}
