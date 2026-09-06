<?php

namespace App\Filament\Resources\PosyanduMonthlyExaminations;

use App\Filament\Resources\PosyanduMonthlyExaminations\Pages\CreatePosyanduMonthlyExamination;
use App\Filament\Resources\PosyanduMonthlyExaminations\Pages\EditPosyanduMonthlyExamination;
use App\Filament\Resources\PosyanduMonthlyExaminations\Pages\ListPosyanduMonthlyExaminations;
use App\Filament\Resources\PosyanduMonthlyExaminations\Pages\ManagePosyanduMonthlyParticipants;
use App\Models\PosyanduMonthlyExamination;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class PosyanduMonthlyExaminationResource extends Resource
{
    protected static ?string $model = PosyanduMonthlyExamination::class;

    protected static string|UnitEnum|null $navigationGroup = 'Posyandu';

    protected static ?string $navigationLabel = 'Pemeriksaan Bulanan';

    protected static ?string $modelLabel = 'Pemeriksaan Bulanan';

    protected static ?string $pluralModelLabel = 'Pemeriksaan Bulanan';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?int $navigationSort = 4;

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user && ($user->hasAnyRole(['super_admin', 'admin_dinkes', 'admin_instansi', 'admin_kecamatan', 'petugas_posyandu'])
            || $user->can('posyandu_monthly.view'));
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['posyandu', 'posyandu.instansi']);
        $user = auth()->user();

        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->hasRole('super_admin') || $user->hasRole('admin_dinkes')) {
            return $query;
        }

        if ($user->hasRole('admin_kecamatan')) {
            return $query->whereHas('posyandu.kelurahan', fn ($q) => $q->where('kecamatan_id', $user->kecamatan_id));
        }

        if ($user->hasRole('admin_instansi')) {
            return $query->whereHas('posyandu', fn ($q) => $q->where('instansi_id', $user->instansi_id));
        }

        if ($user->hasRole('petugas_posyandu')) {
            return $query->where('posyandu_id', $user->posyandu_id);
        }

        return $query->whereRaw('1 = 0');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identitas Sesi Pemeriksaan Bulanan')
                    ->schema([
                        \Filament\Forms\Components\Select::make('posyandu_id')
                            ->label('Posyandu')
                            ->options(function () {
                                $user = auth()->user();
                                $query = \App\Models\Posyandu::query()->with(['instansi', 'kelurahan']);

                                if ($user->hasRole('admin_instansi')) {
                                    $query->where('instansi_id', $user->instansi_id);
                                }

                                if ($user->hasRole('petugas_posyandu')) {
                                    $query->where('id', $user->posyandu_id);
                                }

                                return $query
                                    ->orderBy('nama_posyandu')
                                    ->get()
                                    ->mapWithKeys(function ($item) {
                                        $puskesmas = $item->instansi?->nama_instansi;
                                        $kelurahan = $item->kelurahan?->nama_kelurahan;

                                        if ($puskesmas && $kelurahan) {
                                            $label = "{$item->nama_posyandu} - {$puskesmas} (Kel. {$kelurahan})";
                                        } elseif ($puskesmas) {
                                            $label = "{$item->nama_posyandu} - {$puskesmas}";
                                        } elseif ($kelurahan) {
                                            $label = "{$item->nama_posyandu} - Kel. {$kelurahan}";
                                        } else {
                                            $label = $item->nama_posyandu;
                                        }

                                        return [$item->id => $label];
                                    });
                            })
                            ->default(fn () => auth()->user()?->posyandu_id)
                            ->disabled(fn () => auth()->user()?->hasRole('petugas_posyandu'))
                            ->dehydrated()
                            ->searchable()
                            ->required(),

                        \Filament\Forms\Components\DatePicker::make('examination_date')
                            ->label('Tanggal Pemeriksaan')
                            ->default(now())
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, $state) {
                                if ($state) {
                                    $date = \Carbon\Carbon::parse($state);
                                    $set('month', $date->month);
                                    $set('year', $date->year);
                                }
                            })
                            ->required(),

                        \Filament\Forms\Components\Select::make('month')
                            ->label('Bulan')
                            ->options([
                                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                            ])
                            ->default(now()->month)
                            ->required(),

                        \Filament\Forms\Components\TextInput::make('year')
                            ->label('Tahun')
                            ->numeric()
                            ->default(now()->year)
                            ->required(),

                        \Filament\Forms\Components\TextInput::make('location')
                            ->label('Lokasi')
                            ->placeholder('Posyandu / Gedung Paud / Balai Desa')
                            ->columnSpanFull(),

                        \Filament\Forms\Components\Select::make('status')
                            ->label('Status Sesi')
                            ->options([
                                'scheduled' => 'Terjadwal',
                                'ongoing' => 'Berlangsung',
                                'completed' => 'Selesai',
                                'cancelled' => 'Dibatalkan',
                            ])
                            ->default('scheduled')
                            ->required(),

                        \Filament\Forms\Components\Textarea::make('notes')
                            ->label('Catatan')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('posyandu.nama_posyandu')
                    ->label('Posyandu')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('examination_date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('month')
                    ->label('Bulan / Tahun')
                    ->formatStateUsing(fn ($record) => sprintf('%02d / %d', $record->month, $record->year))
                    ->sortable(),

                Tables\Columns\TextColumn::make('location')
                    ->label('Lokasi')
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'scheduled' => 'gray',
                        'ongoing' => 'warning',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'scheduled' => 'Terjadwal',
                        'ongoing' => 'Berlangsung',
                        'completed' => 'Selesai',
                        'cancelled' => 'Dibatalkan',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('participants_count')
                    ->label('Peserta')
                    ->counts('participants')
                    ->badge()
                    ->color('info'),
            ])
            ->defaultSort('examination_date', 'desc')
            ->headerActions([
                \Filament\Actions\Action::make('export_rekap')
                    ->label('Export Data Pemeriksaan')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->form([
                        \Filament\Forms\Components\Select::make('posyandu_id')
                            ->label('Posyandu')
                            ->options(function () {
                                $user = auth()->user();
                                $query = \App\Models\Posyandu::query()->with(['instansi', 'kelurahan']);

                                if ($user->hasRole('admin_instansi')) {
                                    $query->where('instansi_id', $user->instansi_id);
                                }

                                if ($user->hasRole('petugas_posyandu')) {
                                    $query->where('id', $user->posyandu_id);
                                }

                                return $query
                                    ->orderBy('nama_posyandu')
                                    ->get()
                                    ->mapWithKeys(function ($item) {
                                        $puskesmas = $item->instansi?->nama_instansi;
                                        $kelurahan = $item->kelurahan?->nama_kelurahan;

                                        if ($puskesmas && $kelurahan) {
                                            $label = "{$item->nama_posyandu} - {$puskesmas} (Kel. {$kelurahan})";
                                        } elseif ($puskesmas) {
                                            $label = "{$item->nama_posyandu} - {$puskesmas}";
                                        } elseif ($kelurahan) {
                                            $label = "{$item->nama_posyandu} - Kel. {$kelurahan}";
                                        } else {
                                            $label = $item->nama_posyandu;
                                        }

                                        return [$item->id => $label];
                                    });
                            })
                            ->searchable()
                            ->placeholder('Semua Posyandu')
                            ->nullable(),

                        \Filament\Forms\Components\Select::make('month')
                            ->label('Bulan')
                            ->options([
                                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                            ])
                            ->placeholder('Semua Bulan')
                            ->nullable(),

                        \Filament\Forms\Components\TextInput::make('year')
                            ->label('Tahun')
                            ->numeric()
                            ->placeholder('Semua Tahun')
                            ->nullable(),
                    ])
                    ->action(function (array $data) {
                        $filters = array_filter($data, fn ($v) => $v !== null && $v !== '');
                        $filename = 'rekap_pemeriksaan_posyandu_'.now()->format('Ymd_His').'.xlsx';

                        return \Maatwebsite\Excel\Facades\Excel::download(
                            new \App\Exports\PemeriksaanBulananPosyanduExport(null, $filters),
                            $filename
                        );
                    }),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\Action::make('peserta')
                    ->label('Kelola Peserta')
                    ->icon('heroicon-o-users')
                    ->color('success')
                    ->url(fn (PosyanduMonthlyExamination $record): string => static::getUrl('participants', ['record' => $record])),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->hasAnyRole(['super_admin', 'admin_dinkes'])),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPosyanduMonthlyExaminations::route('/'),
            'create' => CreatePosyanduMonthlyExamination::route('/create'),
            'edit' => EditPosyanduMonthlyExamination::route('/{record}/edit'),
            'participants' => ManagePosyanduMonthlyParticipants::route('/{record}/participants'),
        ];
    }
}
