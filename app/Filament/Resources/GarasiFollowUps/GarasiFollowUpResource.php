<?php

namespace App\Filament\Resources\GarasiFollowUps;

use App\Filament\Resources\GarasiFollowUps\Pages\CreateGarasiFollowUp;
use App\Filament\Resources\GarasiFollowUps\Pages\EditGarasiFollowUp;
use App\Filament\Resources\GarasiFollowUps\Pages\ListGarasiFollowUps;
use App\Models\GarasiFollowUp;
use App\Models\GarasiParticipant;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class GarasiFollowUpResource extends Resource
{
    protected static ?string $model = GarasiFollowUp::class;

    protected static \UnitEnum|string|null $navigationGroup = 'UKGM (Upaya Kesehatan Gigi Masyarakat)';

    protected static ?string $navigationLabel = 'Follow-up & Evaluasi';

    protected static ?string $modelLabel = 'Follow-up UKGM';

    protected static ?string $pluralModelLabel = 'Follow-up UKGM';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-path-rounded-square';

    public static function canAccess(): bool
    {
        return auth()->user()->hasAnyRole(['super_admin', 'admin_dinkes', 'admin_instansi', 'admin_kecamatan', 'petugas_posyandu'])
            && auth()->user()->can('garasi.follow-up');
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user->hasRole('super_admin') || $user->hasRole('admin_dinkes')) {
            return $query;
        }

        if ($user->hasRole('admin_kecamatan')) {
            return $query->whereHas('participant.activity.posyandu.kelurahan', fn ($q) => $q->where('kecamatan_id', $user->kecamatan_id));
        }

        if ($user->hasRole('admin_instansi')) {
            return $query->whereHas('participant.activity', fn ($q) => $q->where('instansi_id', $user->instansi_id));
        }

        if ($user->hasRole('petugas_posyandu')) {
            return $query->whereHas('participant.activity', fn ($q) => $q->where('posyandu_id', $user->posyandu_id));
        }

        return $query->whereRaw('1 = 0');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->schema([
                \Filament\Schemas\Components\Section::make('Pilih Peserta UKGM')
                    ->schema([
                        Forms\Components\Select::make('garasi_participant_id')
                            ->label('Peserta Kegiatan UKGM')
                            ->options(function () {
                                return GarasiParticipant::where('attendance', true)
                                    ->where(function ($q) {
                                        $q->whereNotNull('follow_up_scheduled_date')
                                          ->orWhereHas('referral', fn ($r) => $r->where('referral_needed', true));
                                    })
                                    ->with(['child', 'activity.posyandu'])
                                    ->get()
                                    ->mapWithKeys(function ($p) {
                                        $childName = $p->child ? $p->child->nama_lengkap : 'Anak #'.$p->child_id;
                                        $posyandu = $p->activity && $p->activity->posyandu ? $p->activity->posyandu->nama_posyandu : '-';
                                        $date = $p->activity ? $p->activity->activity_date->format('d M Y') : '-';
                                        return [$p->id => "{$childName} ({$posyandu} - {$date})"];
                                    });
                            })
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, $state) {
                                if ($state) {
                                    $set('previous_participant_id', $state);
                                }
                            }),
                        Forms\Components\Hidden::make('previous_participant_id'),

                        Forms\Components\Placeholder::make('previous_data_card')
                            ->label('Data Pemeriksaan Sebelumnya')
                            ->content(function (callable $get) {
                                $participantId = $get('garasi_participant_id');
                                if (!$participantId) {
                                    return new HtmlString('<div class="text-sm text-gray-500 italic">Pilih peserta di atas untuk melihat rekam medis sebelumnya.</div>');
                                }

                                $p = GarasiParticipant::with(['child.orangTua', 'activity.posyandu', 'brushingPractice', 'screening', 'dentalIndex', 'treatment', 'referral'])
                                    ->find($participantId);

                                if (!$p) return '-';

                                $childName = $p->child ? $p->child->nama_lengkap : '-';
                                $motherName = $p->child && $p->child->orangTua ? $p->child->orangTua->nama_lengkap : '-';
                                $actDate = $p->activity ? $p->activity->activity_date->format('d M Y') : '-';
                                $freq = $p->brushingPractice ? $p->brushingPractice->brushing_frequency : '-';
                                $acc = $p->brushingPractice ? $p->brushingPractice->mother_accompaniment_frequency : '-';
                                $dmft = $p->dentalIndex ? "DMF-T: {$p->dentalIndex->dmft_score} | def-t: {$p->dentalIndex->deft_score}" : '-';
                                $risk = $p->screening ? $p->screening->risk_level : '-';
                                $refStatus = $p->referral && $p->referral->referral_needed ? $p->referral->status : 'Tidak Ada Rujukan';

                                return new HtmlString("
                                    <div class='p-4 bg-gray-50 dark:bg-gray-800 rounded-lg text-sm border border-gray-200 dark:border-gray-700 grid grid-cols-1 md:grid-cols-2 gap-3'>
                                        <div><strong>Anak:</strong> {$childName}</div>
                                        <div><strong>Ibu:</strong> {$motherName}</div>
                                        <div><strong>Tanggal Kegiatan Lalu:</strong> {$actDate}</div>
                                        <div><strong>Frekuensi Sikat Gigi Lalu:</strong> {$freq}</div>
                                        <div><strong>Pendampingan Ibu Lalu:</strong> {$acc}</div>
                                        <div><strong>Indeks Gigi Lalu:</strong> {$dmft}</div>
                                        <div><strong>Tingkat Risiko Lalu:</strong> {$risk}</div>
                                        <div><strong>Status Rujukan Lalu:</strong> {$refStatus}</div>
                                    </div>
                                ");
                            })
                            ->columnSpanFull(),
                    ]),

                \Filament\Schemas\Components\Section::make('Evaluasi & Perubahan Perilaku Bulan Ini')
                    ->schema([
                        Forms\Components\DatePicker::make('follow_up_date')
                            ->label('Tanggal Evaluasi / Follow-up')
                            ->default(now())
                            ->required(),

                        Forms\Components\Select::make('behavior_change')
                            ->label('A. Perubahan Perilaku Menyikat Gigi')
                            ->options([
                                'ada_perubahan' => 'Ada Perubahan',
                                'tidak_ada_perubahan' => 'Tidak Ada Perubahan',
                            ])
                            ->required()
                            ->reactive(),

                        Forms\Components\Textarea::make('behavior_change_description')
                            ->label('Jelaskan Perubahan Perilaku')
                            ->visible(fn (callable $get) => $get('behavior_change') === 'ada_perubahan')
                            ->columnSpanFull(),

                        Forms\Components\Select::make('mother_accompaniment_change')
                            ->label('B. Perubahan Pendampingan Ibu')
                            ->options([
                                'meningkat' => 'Meningkat',
                                'tetap' => 'Tetap',
                                'menurun' => 'Menurun',
                            ])
                            ->required(),

                        Forms\Components\Select::make('dental_condition_change')
                            ->label('C. Kondisi Gigi Saat Follow-up')
                            ->options([
                                'membaik' => 'Membaik',
                                'tetap' => 'Tetap',
                                'memburuk' => 'Memburuk',
                            ])
                            ->required(),

                        Forms\Components\Select::make('referral_status')
                            ->label('D. Status Rujukan')
                            ->options([
                                'sudah_dilakukan' => 'Sudah Dilakukan',
                                'belum_dilakukan' => 'Belum Dilakukan',
                                'tidak_diperlukan' => 'Tidak Diperlukan Lagi',
                            ]),

                        Forms\Components\Textarea::make('notes')
                            ->label('E. Catatan Evaluasi Follow-up')
                            ->columnSpanFull(),
                    ])->columns(['default' => 1, 'sm' => 2]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('participant.child.nama_lengkap')
                    ->label('Nama Anak')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('follow_up_date')
                    ->label('Tanggal Follow-up')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('behavior_change')
                    ->label('Perubahan Perilaku')
                    ->badge()
                    ->color(fn ($state) => $state === 'ada_perubahan' ? 'success' : 'gray'),
                Tables\Columns\TextColumn::make('mother_accompaniment_change')
                    ->label('Pendampingan Ibu')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'meningkat' => 'success',
                        'tetap' => 'info',
                        'menurun' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('dental_condition_change')
                    ->label('Kondisi Gigi')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'membaik' => 'success',
                        'tetap' => 'warning',
                        'memburuk' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('referral_status')
                    ->label('Status Rujukan')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'sudah_dilakukan' => 'success',
                        'belum_dilakukan' => 'danger',
                        'tidak_diperlukan' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('evaluator.name')
                    ->label('Evaluator'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('behavior_change')
                    ->options([
                        'ada_perubahan' => 'Ada Perubahan',
                        'tidak_ada_perubahan' => 'Tidak Ada Perubahan',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGarasiFollowUps::route('/'),
            'create' => Pages\CreateGarasiFollowUp::route('/create'),
            'edit' => Pages\EditGarasiFollowUp::route('/{record}/edit'),
        ];
    }
}
