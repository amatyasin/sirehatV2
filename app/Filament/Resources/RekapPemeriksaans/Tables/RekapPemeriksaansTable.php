<?php

namespace App\Filament\Resources\RekapPemeriksaans\Tables;

use App\Exports\RekapPemeriksaanExport;
use App\Exports\RekapPemeriksaanQueuedExport;
use App\Jobs\NotifyUserExportReady;
use App\Models\AcademicYear;
use App\Models\School;
use App\Models\SchoolClass;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;
use Maatwebsite\Excel\Facades\Excel;

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

                Tables\Filters\Filter::make('tanggal_pemeriksaan')
                    ->form([
                        Forms\Components\DatePicker::make('dari')
                            ->label('Tanggal Pemeriksaan Dari'),
                        Forms\Components\DatePicker::make('sampai')
                            ->label('Tanggal Pemeriksaan Sampai'),
                    ])
                    ->query(function (\Illuminate\Database\Eloquent\Builder $query, array $data): \Illuminate\Database\Eloquent\Builder {
                        return $query
                            ->when(
                                $data['dari'],
                                fn (\Illuminate\Database\Eloquent\Builder $query, $date): \Illuminate\Database\Eloquent\Builder => $query->where(function ($sub) use ($date) {
                                    $sub->whereHas('pemeriksaanUmums', fn ($p) => $p->whereDate('tanggal_pemeriksaan', '>=', $date))
                                        ->orWhereHas('pemeriksaanGigis', fn ($p) => $p->whereDate('tanggal_pemeriksaan', '>=', $date))
                                        ->orWhereHas('pemeriksaanGizis', fn ($p) => $p->whereDate('tanggal_pemeriksaan', '>=', $date))
                                        ->orWhereHas('pemeriksaanMatas', fn ($p) => $p->whereDate('tanggal_pemeriksaan', '>=', $date));
                                })
                            )
                            ->when(
                                $data['sampai'],
                                fn (\Illuminate\Database\Eloquent\Builder $query, $date): \Illuminate\Database\Eloquent\Builder => $query->where(function ($sub) use ($date) {
                                    $sub->whereHas('pemeriksaanUmums', fn ($p) => $p->whereDate('tanggal_pemeriksaan', '<=', $date))
                                        ->orWhereHas('pemeriksaanGigis', fn ($p) => $p->whereDate('tanggal_pemeriksaan', '<=', $date))
                                        ->orWhereHas('pemeriksaanGizis', fn ($p) => $p->whereDate('tanggal_pemeriksaan', '<=', $date))
                                        ->orWhereHas('pemeriksaanMatas', fn ($p) => $p->whereDate('tanggal_pemeriksaan', '<=', $date));
                                })
                            );
                    }),

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

            ->defaultSort('created_at', 'desc')

            ->headerActions([

                Action::make('export_rekap')
                    ->label('Export Excel')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->form([

                        Forms\Components\DatePicker::make('tanggal_pemeriksaan_dari')
                            ->label('Tanggal Pemeriksaan Dari')
                            ->nullable(),

                        Forms\Components\DatePicker::make('tanggal_pemeriksaan_sampai')
                            ->label('Tanggal Pemeriksaan Sampai')
                            ->nullable(),

                        Forms\Components\Select::make('school_id')
                            ->label('Sekolah')
                            ->options(function () {
                                $user = auth()->user();
                                $query = School::query();
                                if ($user->hasRole('admin_instansi')) {
                                    $query->where('instansi_id', $user->instansi_id);
                                } elseif ($user->hasRole('admin_sekolah')) {
                                    $query->where('id', $user->school_id);
                                }
                                return $query->orderBy('nama_sekolah')->pluck('nama_sekolah', 'id');
                            })
                            ->searchable()->preload()->nullable()
                            ->placeholder('Semua Sekolah'),

                        Forms\Components\Select::make('school_class_id')
                            ->label('Kelas')
                            ->options(
                                SchoolClass::query()->orderBy('urutan')->pluck('nama_kelas', 'id')
                            )
                            ->searchable()->preload()->nullable()
                            ->placeholder('Semua Kelas'),

                        Forms\Components\Select::make('academic_year_id')
                            ->label('Tahun Ajaran')
                            ->options(
                                AcademicYear::orderByDesc('id')->pluck('nama', 'id')
                            )
                            ->searchable()->preload()->nullable()
                            ->placeholder('Semua Tahun Ajaran'),

                        Forms\Components\Select::make('semester')
                            ->label('Semester')
                            ->options(['Ganjil' => 'Ganjil', 'Genap' => 'Genap'])
                            ->nullable()->placeholder('Semua Semester'),

                        Forms\Components\Select::make('jenis_kelamin')
                            ->label('Jenis Kelamin')
                            ->options(['L' => 'Laki-laki', 'P' => 'Perempuan'])
                            ->nullable()->placeholder('Semua'),

                        Forms\Components\Toggle::make('hanya_sudah_diperiksa')
                            ->label('Hanya yang sudah diperiksa')
                            ->default(false),

                        Forms\Components\Radio::make('mode')
                            ->label('Mode Export')
                            ->options([
                                'sync'  => '⚡ Langsung (data < 1.000 baris)',
                                'queue' => '🔄 Antrian/Queue (data besar, notifikasi saat selesai)',
                            ])
                            ->default('sync')
                            ->required(),

                    ])
                    ->action(function (array $data) {

                        // Hapus key kosong/null dari filter
                        $filters = array_filter(
                            $data,
                            fn ($v, $k) => $k !== 'mode' && $v !== null && $v !== '',
                            ARRAY_FILTER_USE_BOTH
                        );

                        $filename = 'rekap_pemeriksaan_' . now()->format('Ymd_His') . '.xlsx';
                        $user     = auth()->user();

                        if ($data['mode'] === 'queue') {
                            // ── QUEUE MODE ────────────────────────────────
                            // Proses di background, notifikasi database saat selesai
                            Excel::queue(
                                new RekapPemeriksaanQueuedExport($filters, $user->id),
                                $filename,
                                'public'
                            )->chain([
                                new NotifyUserExportReady($user->id, $filename),
                            ]);

                            Notification::make()
                                ->title('Export dijadwalkan')
                                ->body('File sedang diproses. Anda akan mendapat notifikasi ketika selesai.')
                                ->info()
                                ->send();

                            return;
                        }

                        // ── SYNC MODE ─────────────────────────────────────
                        // Set memory & timeout lebih tinggi untuk sync besar
                        ini_set('memory_limit', '512M');
                        set_time_limit(300); // 5 menit

                        return Excel::download(
                            new RekapPemeriksaanExport($filters),
                            $filename
                        );

                    }),

            ]);
    }
}
