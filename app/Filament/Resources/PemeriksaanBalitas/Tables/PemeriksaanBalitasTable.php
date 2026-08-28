<?php

namespace App\Filament\Resources\PemeriksaanBalitas\Tables;

use App\Exports\PemeriksaanBalitaExport;
use App\Models\Posyandu;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Facades\Excel;

class PemeriksaanBalitasTable
{
    public static function configure(
        Table $table
    ): Table {

        return $table

            ->columns([

                Tables\Columns\TextColumn::make(
                    'child.instansi.nama_instansi'
                )

                    ->label('Puskesmas')

                    ->searchable()

                    ->sortable(),

                Tables\Columns\TextColumn::make(
                    'child.posyandu.nama_posyandu'
                )

                    ->label('Posyandu')

                    ->searchable()

                    ->sortable(),

                Tables\Columns\TextColumn::make(
                    'child.nama_lengkap'
                )

                    ->label('Nama Anak')

                    ->searchable()

                    ->sortable()

                    ->weight('bold'),

                Tables\Columns\TextColumn::make(
                    'child.nik'
                )

                    ->label('NIK')

                    ->searchable(),

                Tables\Columns\TextColumn::make(
                    'child.jenis_kelamin'
                )

                    ->label(
                        'Jenis Kelamin'
                    )

                    ->badge()

                    ->formatStateUsing(
                        fn ($state) => $state === 'L'

                                ? 'Laki-laki'

                                : 'Perempuan'
                    )

                    ->color(
                        fn ($state) => $state === 'L'

                                ? 'primary'

                                : 'danger'
                    ),

                Tables\Columns\TextColumn::make(
                    'child.tanggal_lahir'
                )

                    ->label('Umur')

                    ->formatStateUsing(function (
                        $state
                    ) {

                        if (! $state) {
                            return '-';
                        }

                        $umur =
                            Carbon::parse(
                                $state
                            );

                        return

                            $umur
                                ->diff(now())
                                ->y

                            .' tahun '

                            .

                            $umur
                                ->diff(now())
                                ->m

                            .' bulan';

                    }),

                Tables\Columns\TextColumn::make(
                    'status_tb_u'
                )

                    ->label('TB/U')

                    ->badge()

                    ->color(
                        fn ($state) => match ($state) {

                            'Pendek' => 'danger',

                            'Sangat Pendek' => 'danger',

                            'Normal' => 'success',

                            default => 'gray',

                        }
                    ),

                Tables\Columns\IconColumn::make(
                    'dirujuk_ke_fasyankes'
                )

                    ->label('Rujukan')

                    ->boolean(),

                Tables\Columns\TextColumn::make(
                    'tanggal_pemeriksaan'
                )

                    ->label(
                        'Tanggal Pemeriksaan'
                    )

                    ->date('d M Y')

                    ->sortable(),

            ])

            ->filters([

                Tables\Filters\SelectFilter::make(
                    'posyandu'
                )

                    ->label('Posyandu')

                    ->options(function () {

                        $user =
                            auth()->user();

                        $query =
                            Posyandu::query();

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
                                'petugas_posyandu'
                            )

                        ) {

                            $query->where(
                                'id',
                                $user->posyandu_id
                            );
                        }

                        return $query->pluck(
                            'nama_posyandu',
                            'id'
                        );
                    })

                    ->searchable()

                    ->query(function (
                        $query,
                        $data
                    ) {

                        if (! $data['value']) {
                            return $query;
                        }

                        return $query->whereHas(

                            'child',

                            function ($q) use ($data) {

                                $q->where(

                                    'posyandu_id',

                                    $data['value']

                                );

                            }

                        );
                    }),

                Tables\Filters\SelectFilter::make(
                    'jenis_kelamin'
                )

                    ->label(
                        'Jenis Kelamin'
                    )

                    ->options([

                        'L' => 'Laki-laki',

                        'P' => 'Perempuan',

                    ])

                    ->query(function (
                        $query,
                        $data
                    ) {

                        if (! $data['value']) {
                            return $query;
                        }

                        return $query->whereHas(

                            'child',

                            function ($q) use ($data) {

                                $q->where(

                                    'jenis_kelamin',

                                    $data['value']

                                );

                            }

                        );
                    }),

            ])

            ->defaultSort(

                'tanggal_pemeriksaan',

                'desc'

            )

            ->headerActions([

                Action::make('export')

                    ->label('Export Excel')

                    ->icon('heroicon-o-document-arrow-down')

                    ->color('success')

                    ->form([

                        Forms\Components\Select::make('posyandu')

                            ->label('Posyandu')

                            ->options(function () {

                                $user = auth()->user();

                                $query = Posyandu::query();

                                if ($user->hasRole('admin_instansi')) {

                                    $query->where('instansi_id', $user->instansi_id);

                                }

                                if ($user->hasRole('petugas_posyandu')) {

                                    $query->where('id', $user->posyandu_id);

                                }

                                return $query->pluck('nama_posyandu', 'id');

                            })

                            ->searchable()

                            ->placeholder('Semua Posyandu')

                            ->nullable(),

                        Forms\Components\Select::make('jenis_kelamin')

                            ->label('Jenis Kelamin')

                            ->options([

                                'L' => 'Laki-laki',

                                'P' => 'Perempuan',

                            ])

                            ->placeholder('Semua Jenis Kelamin')

                            ->nullable(),

                        Forms\Components\Select::make('status_stunting')

                            ->label('Status Stunting')

                            ->options([

                                'Normal' => 'Normal',

                                'Pendek' => 'Pendek',

                                'Sangat Pendek' => 'Sangat Pendek',

                            ])

                            ->placeholder('Semua Status Stunting')

                            ->nullable(),

                    ])

                    ->action(function (array $data) {

                        $filters = array_filter(

                            $data,

                            fn ($v) => $v !== null && $v !== ''

                        );

                        $filename = 'pemeriksaan_anak_'.now()->format('Ymd_His').'.xlsx';

                        return Excel::download(

                            new PemeriksaanBalitaExport($filters),

                            $filename

                        );

                    }),

            ])

            ->recordActions([

                EditAction::make(),

            ])

            ->toolbarActions([

                BulkActionGroup::make([

                    BulkAction::make('export_selected')

                        ->label('Export Selected')

                        ->icon('heroicon-o-document-arrow-down')

                        ->color('success')

                        ->action(function (Collection $records) {

                            $recordIds = $records->pluck('id')->toArray();

                            $filename = 'pemeriksaan_anak_selected_'.now()->format('Ymd_His').'.xlsx';

                            return Excel::download(

                                new PemeriksaanBalitaExport([], $recordIds),

                                $filename

                            );

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

                ]),

            ]);
    }
}
