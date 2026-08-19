<?php

namespace App\Filament\Resources\Children\Tables;

use App\Exports\ChildrenExport;
use App\Imports\ChildrenImport;
use App\Models\Posyandu;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;
use Maatwebsite\Excel\Facades\Excel;

class ChildrenTable
{
    public static function configure(
        Table $table
    ): Table {

        return $table

            ->defaultSort(
                'nama_lengkap'
            )

            ->columns([

                Tables\Columns\TextColumn::make(
                    'posyandu.instansi.nama_instansi'
                )

                    ->label('Puskesmas')

                    ->searchable()

                    ->sortable()

                    ->toggleable(),

                Tables\Columns\TextColumn::make(
                    'posyandu.nama_posyandu'
                )

                    ->label('Posyandu')

                    ->searchable()

                    ->sortable()

                    ->badge()

                    ->color('primary'),

                Tables\Columns\TextColumn::make(
                    'nama_lengkap'
                )

                    ->label('Nama Anak')

                    ->searchable()

                    ->sortable()

                    ->weight('bold'),

                Tables\Columns\TextColumn::make(
                    'nik'
                )

                    ->label('NIK')

                    ->searchable()

                    ->copyable()

                    ->toggleable(),

                Tables\Columns\TextColumn::make(
                    'jenis_kelamin'
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
                    'tanggal_lahir'
                )

                    ->label('Umur')

                    ->formatStateUsing(
                        function ($state) {

                            if (! $state) {
                                return '-';
                            }

                            $tanggalLahir =
                                Carbon::parse($state);

                            $bulan =
                                floor(
                                    $tanggalLahir
                                        ->diffInMonths(now())
                                );

                            return
                                $bulan.
                                ' bulan';
                        }
                    )

                    ->sortable(),

                Tables\Columns\TextColumn::make(
                    'orangTua.nama_lengkap'
                )

                    ->label(
                        'Orang Tua'
                    )

                    ->searchable()

                    ->toggleable(),

                Tables\Columns\IconColumn::make(
                    'aktif'
                )

                    ->label('Aktif')

                    ->boolean(),

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
                    'posyandu'
                )

                    ->relationship(
                        'posyandu',
                        'nama_posyandu'
                    )

                    ->searchable()

                    ->preload(),

                Tables\Filters\TernaryFilter::make(
                    'aktif'
                )

                    ->label(
                        'Status Aktif'
                    ),

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
                            'templates/template_anak.xlsx'
                        )

                    )

                    ->openUrlInNewTab(),

                Action::make('import')

                    ->label(
                        'Import Excel'
                    )

                    ->icon(
                        'heroicon-o-arrow-up-tray'
                    )

                    ->visible(

                        auth()
                            ->user()
                            ->hasAnyRole([

                                'super_admin',

                                'admin_dinkes',

                                'admin_instansi',

                                'petugas_posyandu',

                            ])

                    )

                    ->form([

                        Forms\Components\Select::make(
                            'posyandu_id'
                        )

                            ->label('Posyandu')

                            ->options(function () {

                                $user =
                                    auth()->user();

                                if (

                                    $user->hasAnyRole([

                                        'super_admin',

                                        'admin_dinkes',

                                    ])

                                ) {

                                    return Posyandu::query()

                                        ->orderBy(
                                            'nama_posyandu'
                                        )

                                        ->pluck(
                                            'nama_posyandu',
                                            'id'
                                        );
                                }

                                if (

                                    $user->hasRole(
                                        'admin_instansi'
                                    )

                                ) {

                                    return Posyandu::query()

                                        ->where(
                                            'instansi_id',
                                            $user->instansi_id
                                        )

                                        ->orderBy(
                                            'nama_posyandu'
                                        )

                                        ->pluck(
                                            'nama_posyandu',
                                            'id'
                                        );
                                }

                                return Posyandu::query()

                                    ->where(
                                        'id',
                                        $user->posyandu_id
                                    )

                                    ->pluck(
                                        'nama_posyandu',
                                        'id'
                                    );
                            })

                            ->default(
                                auth()->user()->posyandu_id
                            )

                            ->disabled(

                                auth()
                                    ->user()
                                    ->hasRole(
                                        'petugas_posyandu'
                                    )

                            )

                            ->searchable()

                            ->preload()

                            ->required(),

                        Forms\Components\FileUpload::make(
                            'file'
                        )

                            ->label(
                                'File Excel'
                            )

                            ->disk('local')

                            ->directory('imports')

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

                    ->action(function ($data) {
                        try {
                            $posyandu =
                                Posyandu::find(
                                    $data['posyandu_id']
                                );

                            if (! $posyandu) {
                                Notification::make()
                                    ->title('Posyandu tidak ditemukan.')
                                    ->danger()
                                    ->send();

                                return;
                            }

                            $relativeFile = $data['file'];
                            if (\Illuminate\Support\Facades\Storage::disk('local')->exists($relativeFile)) {
                                $file = \Illuminate\Support\Facades\Storage::disk('local')->path($relativeFile);
                            } elseif (file_exists(storage_path('app/private/' . $relativeFile))) {
                                $file = storage_path('app/private/' . $relativeFile);
                            } else {
                                $file = storage_path('app/' . $relativeFile);
                            }

                            Excel::import(
                                new ChildrenImport(
                                    $posyandu->instansi_id,
                                    $posyandu->id
                                ),
                                $file
                            );

                            Notification::make()
                                ->title('Import berhasil')
                                ->success()
                                ->send();
                        } catch (\Illuminate\Validation\ValidationException $e) {
                            $failures = collect($e->errors())->flatten()->implode(', ');
                            Notification::make()
                                ->title('Gagal Impor Data')
                                ->body('Validasi gagal: ' . $failures)
                                ->danger()
                                ->persistent()
                                ->send();
                        } catch (\Throwable $e) {
                            $msg = $e->getMessage();
                            if (str_contains($msg, 'Duplicate entry') || str_contains($msg, 'unique')) {
                                $msg = 'Terdapat duplikasi data (NIK/Anak) yang sudah terdaftar di sistem.';
                            }
                            Notification::make()
                                ->title('Gagal Impor Data')
                                ->body($msg)
                                ->danger()
                                ->persistent()
                                ->send();
                        }
                    }),

                Action::make('export')

                    ->label(
                        'Export Excel'
                    )

                    ->icon(
                        'heroicon-o-document-arrow-down'
                    )

                    ->color('success')

                    ->action(function () {

                        return Excel::download(

                            new ChildrenExport,

                            'children.xlsx'

                        );
                    }),

                CreateAction::make(),

            ])

            ->recordActions([
                \Filament\Actions\ViewAction::make(),
                EditAction::make(),

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
