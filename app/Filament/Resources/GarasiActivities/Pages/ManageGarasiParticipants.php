<?php

namespace App\Filament\Resources\GarasiActivities\Pages;

use App\Filament\Resources\GarasiActivities\GarasiActivityResource;
use App\Models\Child;
use App\Models\GarasiParticipant;
use Filament\Actions;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class ManageGarasiParticipants extends ManageRelatedRecords
{
    protected static string $resource = GarasiActivityResource::class;

    protected static string $relationship = 'participants';

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-users';

    public static function getNavigationLabel(): string
    {
        return 'Kelola Peserta';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->schema([
                \Filament\Schemas\Components\Section::make('Informasi Anak')
                    ->schema([
                        Forms\Components\Placeholder::make('child_info')
                            ->label('')
                            ->content(function (GarasiParticipant $record) {
                                $child = $record->child;
                                if (!$child) return '-';
                                
                                $age = \Carbon\Carbon::parse($child->tanggal_lahir)->age;
                                return new HtmlString("
                                    <strong>Nama:</strong> {$child->nama_lengkap}<br>
                                    <strong>NIK:</strong> {$child->nik}<br>
                                    <strong>Umur:</strong> {$age} tahun<br>
                                    <strong>Ibu:</strong> " . ($child->orangTua ? $child->orangTua->nama_lengkap : '-') . "
                                ");
                            }),
                    ])
                    ->hidden(fn (string $operation): bool => $operation === 'create'),

                \Filament\Schemas\Components\Section::make('Tahap 1 — Kehadiran')
                    ->schema([
                        Forms\Components\Select::make('child_id')
                            ->label('Anak')
                            ->options(function () {
                                $activity = $this->getOwnerRecord();
                                return Child::where('posyandu_id', $activity->posyandu_id)
                                    ->pluck('nama_lengkap', 'id');
                            })
                            ->required()
                            ->reactive()
                            ->disabled(fn (string $operation): bool => $operation === 'edit')
                            ->afterStateUpdated(function (callable $set, $state) {
                                if ($state) {
                                    $child = Child::find($state);
                                    if ($child) {
                                        $set('orang_tua_id', $child->orang_tua_id);
                                    }
                                }
                            }),
                        Forms\Components\Hidden::make('orang_tua_id'),
                        Forms\Components\Toggle::make('attendance')
                            ->label('Kehadiran Anak (Hadir)')
                            ->default(false)
                            ->reactive(),
                        Forms\Components\Toggle::make('mother_accompanied')
                            ->label('Ibu Mendampingi')
                            ->default(false)
                            ->visible(fn (callable $get) => $get('attendance')),
                    ])->columns(['default' => 1, 'sm' => 2]),

                \Filament\Schemas\Components\Section::make('Tahap 2 — Praktik Menyikat Gigi')
                    ->schema([
                        Forms\Components\Select::make('toothbrushing_practice')
                            ->label('Kemampuan Anak')
                            ->options([
                                'mandiri' => 'Mandiri',
                                'dengan_bantuan' => 'Dengan Bantuan',
                                'belum_mampu' => 'Belum Mampu',
                            ]),
                        Forms\Components\Select::make('brushing_frequency')
                            ->label('Frekuensi Menyikat')
                            ->options([
                                'tidak_rutin' => 'Tidak Rutin',
                                '1_kali' => '1 Kali/Hari',
                                '2_kali' => '2 Kali/Hari',
                                'lebih_2_kali' => '> 2 Kali/Hari',
                            ]),
                        Forms\Components\Select::make('use_toothpaste')
                            ->label('Menggunakan Pasta Gigi')
                            ->options([
                                'ya' => 'Ya',
                                'tidak' => 'Tidak',
                                'tidak_diketahui' => 'Tidak Diketahui',
                            ]),
                        Forms\Components\Toggle::make('brushing_before_bed')
                            ->label('Menyikat Sebelum Tidur'),
                    ])
                    ->columns(['default' => 1, 'sm' => 2])
                    ->visible(fn (callable $get) => $get('attendance')),

                \Filament\Schemas\Components\Section::make('Tahap 3 — Edukasi Ibu')
                    ->relationship('education')
                    ->schema([
                        Forms\Components\Toggle::make('brushing_education')->label('Cara Menyikat Gigi'),
                        Forms\Components\Toggle::make('brushing_frequency_education')->label('Waktu Menyikat Gigi'),
                        Forms\Components\Toggle::make('fluoride_education')->label('Penggunaan Pasta Gigi'),
                        Forms\Components\Toggle::make('sugar_education')->label('Pembatasan Makanan Manis'),
                        Forms\Components\Toggle::make('dental_checkup_education')->label('Pemeriksaan Gigi secara berkala'),
                        Forms\Components\Toggle::make('home_care_education')->label('Perawatan Gigi di Rumah'),
                        Forms\Components\Textarea::make('notes')->label('Catatan Edukasi')->columnSpanFull(),
                    ])
                    ->columns(['default' => 1, 'sm' => 2])
                    ->visible(fn (callable $get) => $get('attendance')),

                \Filament\Schemas\Components\Section::make('Tahap 4 — Skrining Gigi dan Mulut')
                    ->relationship('screening')
                    ->schema([
                        \Filament\Schemas\Components\Fieldset::make('Keluhan')
                            ->schema([
                                Forms\Components\Toggle::make('toothache')->label('Sakit Gigi')->reactive(),
                                Forms\Components\Toggle::make('sensitive_teeth')->label('Gigi Ngilu/Sensitif'),
                                Forms\Components\Toggle::make('bleeding_gums')->label('Gusi Berdarah'),
                                Forms\Components\Toggle::make('swollen_gums')->label('Gusi Bengkak'),
                                Forms\Components\Toggle::make('bad_breath')->label('Bau Mulut'),
                                Forms\Components\Toggle::make('mouth_sores')->label('Sariawan'),
                                Forms\Components\Toggle::make('chewing_difficulty')->label('Sulit Mengunyah'),
                            ])->columns(['default' => 1, 'sm' => 2, 'md' => 3]),
                        \Filament\Schemas\Components\Fieldset::make('Observasi')
                            ->schema([
                                Forms\Components\Select::make('oral_hygiene')
                                    ->label('Kebersihan Mulut')
                                    ->options(['baik' => 'Baik', 'sedang' => 'Sedang', 'buruk' => 'Buruk']),
                                Forms\Components\Toggle::make('plaque')->label('Plak'),
                                Forms\Components\Toggle::make('cavities')->label('Indikasi Gigi Berlubang'),
                                Forms\Components\Toggle::make('broken_teeth')->label('Gigi Patah'),
                                Forms\Components\Toggle::make('red_gums')->label('Gusi Merah'),
                                Forms\Components\Toggle::make('swollen_gums_observed')->label('Gusi Bengkak'),
                                Forms\Components\TextInput::make('other_findings')->label('Temuan Lain'),
                            ])->columns(['default' => 1, 'sm' => 2, 'md' => 3]),
                        Forms\Components\Select::make('risk_level')
                            ->label('Tingkat Risiko (Hasil)')
                            ->options([
                                'rendah' => '🟢 Risiko Rendah',
                                'pemantauan' => '🟡 Perlu Pemantauan',
                                'lanjutan' => '🟠 Perlu Pemeriksaan Lanjutan',
                                'rujukan' => '🔴 Perlu Rujukan',
                            ])
                            ->required()
                            ->reactive(),
                        Forms\Components\Textarea::make('recommendation')->label('Rekomendasi / Saran')->columnSpanFull(),
                    ])
                    ->visible(fn (callable $get) => $get('attendance')),

                \Filament\Schemas\Components\Section::make('Tahap 5 — Rujukan & Tindak Lanjut')
                    ->relationship('referral')
                    ->schema([
                        Forms\Components\DatePicker::make('referral_date')->label('Tanggal Rujukan')->default(now()),
                        Forms\Components\TextInput::make('destination')->label('Tujuan Rujukan'),
                        Forms\Components\Textarea::make('reason')->label('Alasan Rujukan')->columnSpanFull(),
                        Forms\Components\Select::make('status')
                            ->label('Status Tindak Lanjut')
                            ->options([
                                'pending' => 'Belum Ditindaklanjuti', 
                                'referred' => 'Sudah Dirujuk',
                                'in_progress' => 'Dalam Proses',
                                'completed' => 'Selesai', 
                            ])
                            ->default('pending'),
                        Forms\Components\DatePicker::make('follow_up_date')->label('Tanggal Tindak Lanjut / Selesai'),
                        Forms\Components\Textarea::make('follow_up_result')->label('Hasil Tindak Lanjut / Catatan')->columnSpanFull(),
                    ])
                    ->columns(['default' => 1, 'sm' => 2])
                    ->visible(fn (callable $get) => $get('attendance') && $get('screening.risk_level') === 'rujukan'),
                    
                \Filament\Schemas\Components\Section::make('Riwayat GARASI Anak')
                    ->schema([
                        Forms\Components\Placeholder::make('history')
                            ->label('')
                            ->content(function (GarasiParticipant $record) {
                                $child = $record->child;
                                if (!$child) return '-';
                                
                                $histories = GarasiParticipant::where('child_id', $child->id)
                                    ->where('id', '!=', $record->id)
                                    ->orderBy('created_at', 'desc')
                                    ->get();
                                    
                                if ($histories->isEmpty()) {
                                    return 'Belum ada riwayat kegiatan lain.';
                                }
                                
                                $html = '<ul>';
                                foreach($histories as $history) {
                                    $date = $history->activity->activity_date->format('d M Y');
                                    $posyandu = $history->activity->posyandu->nama_posyandu;
                                    $status = $history->attendance ? 'Hadir' : 'Tidak Hadir';
                                    $risk = $history->screening ? $history->screening->risk_level : '-';
                                    
                                    $html .= "<li><strong>{$date}</strong> di {$posyandu} - Status: {$status} | Risiko: {$risk}</li>";
                                }
                                $html .= '</ul>';
                                return new HtmlString($html);
                            }),
                    ])
                    ->hidden(fn (string $operation): bool => $operation === 'create'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('child.nama_lengkap')
            ->columns([
                Tables\Columns\TextColumn::make('child.nama_lengkap')
                    ->label('Nama Anak')
                    ->searchable(['nama_lengkap', 'nik'])
                    ->sortable(),
                Tables\Columns\TextColumn::make('child.tanggal_lahir')
                    ->label('Umur')
                    ->formatStateUsing(fn ($state) => \Carbon\Carbon::parse($state)->age . ' thn'),
                Tables\Columns\TextColumn::make('child.orangTua.nama_lengkap')
                    ->label('Ibu')
                    ->searchable(),
                Tables\Columns\IconColumn::make('attendance')
                    ->label('Kehadiran')
                    ->boolean(),
                Tables\Columns\IconColumn::make('mother_accompanied')
                    ->label('Ibu Mendampingi')
                    ->boolean(),
                Tables\Columns\TextColumn::make('screening.risk_level')
                    ->label('Risiko')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'rendah' => 'success',
                        'pemantauan' => 'warning',
                        'lanjutan' => 'warning',
                        'rujukan' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->getStateUsing(function (GarasiParticipant $record) {
                        if (!$record->attendance) return 'Tidak Hadir';
                        if (!$record->screening) return 'Belum Diperiksa';
                        if ($record->screening->risk_level === 'rujukan') {
                            return $record->referral && $record->referral->status === 'completed' ? 'Selesai (Dirujuk)' : 'Menunggu Tindak Lanjut';
                        }
                        return 'Selesai';
                    })
                    ->badge()
                    ->color(function ($state) {
                        if ($state === 'Tidak Hadir') return 'gray';
                        if ($state === 'Belum Diperiksa') return 'warning';
                        if ($state === 'Selesai') return 'success';
                        return 'info';
                    }),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('attendance')
                    ->label('Kehadiran')
                    ->placeholder('Semua')
                    ->trueLabel('Hadir')
                    ->falseLabel('Tidak Hadir'),
                Tables\Filters\SelectFilter::make('screening.risk_level')
                    ->label('Tingkat Risiko')
                    ->options([
                        'rendah' => 'Risiko Rendah',
                        'pemantauan' => 'Perlu Pemantauan',
                        'lanjutan' => 'Perlu Pemeriksaan Lanjutan',
                        'rujukan' => 'Perlu Rujukan',
                    ]),
            ])
            ->headerActions([
                \Filament\Actions\Action::make('generate_participants')
                    ->label(fn () => $this->getOwnerRecord()->participants()->count() > 0 ? 'Generate Ulang Peserta' : 'Generate Peserta')
                    ->icon('heroicon-o-sparkles')
                    ->color(fn () => $this->getOwnerRecord()->participants()->count() > 0 ? 'gray' : 'primary')
                    ->requiresConfirmation()
                    ->action(function () {
                        $activity = $this->getOwnerRecord();
                        $children = Child::where('posyandu_id', $activity->posyandu_id)->get();

                        foreach ($children as $child) {
                            GarasiParticipant::insertOrIgnore([
                                'garasi_activity_id' => $activity->id,
                                'child_id' => $child->id,
                                'orang_tua_id' => $child->orang_tua_id,
                                'attendance' => false,
                                'mother_accompanied' => false,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }

                        \Filament\Notifications\Notification::make()
                            ->title('Peserta berhasil digenerate!')
                            ->success()
                            ->send();
                    }),
                \Filament\Actions\CreateAction::make()
                    ->label('Tambah Manual')
                    ->icon('heroicon-o-plus'),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
