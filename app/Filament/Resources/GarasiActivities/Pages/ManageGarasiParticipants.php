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
                // READONLY INFO & TAHAP 1 — KEHADIRAN
                \Filament\Schemas\Components\Section::make('Informasi Anak')
                    ->schema([
                        Forms\Components\Placeholder::make('child_info')
                            ->label('')
                            ->content(function (GarasiParticipant $record) {
                                $child = $record->child;
                                if (!$child) return '-';
                                
                                $age = \Carbon\Carbon::parse($child->tanggal_lahir)->age;
                                $posyandu = $child->posyandu ? $child->posyandu->nama_posyandu : '-';
                                return new HtmlString("
                                    <div class='grid grid-cols-2 md:grid-cols-3 gap-2 text-sm'>
                                        <div><strong>Nama:</strong> {$child->nama_lengkap}</div>
                                        <div><strong>NIK:</strong> {$child->nik}</div>
                                        <div><strong>Tanggal Lahir:</strong> " . \Carbon\Carbon::parse($child->tanggal_lahir)->format('d M Y') . "</div>
                                        <div><strong>Umur:</strong> {$age} tahun</div>
                                        <div><strong>Ibu:</strong> " . ($child->orangTua ? $child->orangTua->nama_lengkap : '-') . "</div>
                                        <div><strong>Posyandu:</strong> {$posyandu}</div>
                                    </div>
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
                            ->label('Ibu Mendampingi Kegiatan')
                            ->default(false)
                            ->visible(fn (callable $get) => $get('attendance')),
                        Forms\Components\Toggle::make('mother_accompanied_brushing')
                            ->label('Ibu Mendampingi Anak Menyikat Gigi')
                            ->default(false)
                            ->visible(fn (callable $get) => $get('attendance')),
                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan Kehadiran')
                            ->columnSpanFull(),
                    ])->columns(['default' => 1, 'sm' => 2]),

                // TAHAP 2 — PRAKTIK MENYIKAT GIGI
                \Filament\Schemas\Components\Section::make('Tahap 2 — Praktik Menyikat Gigi')
                    ->relationship('brushingPractice')
                    ->schema([
                        Forms\Components\Toggle::make('together_brushing')
                            ->label('Anak Menyikat Gigi Bersama'),
                        Forms\Components\Select::make('practice_ability')
                            ->label('Kemampuan Menyikat Gigi')
                            ->options([
                                'belum_mampu' => 'Belum Mampu',
                                'bantuan_ibu' => 'Dengan Bantuan Ibu',
                                'arahan_petugas' => 'Dengan Arahan Petugas',
                                'mandiri' => 'Mandiri',
                            ]),
                        Forms\Components\Select::make('brushing_frequency')
                            ->label('Frekuensi Menyikat Gigi')
                            ->options([
                                'tidak_rutin' => 'Tidak Rutin',
                                '1_kali' => '1x Sehari',
                                '2_kali' => '2x Sehari',
                                'lebih_2_kali' => '> 2x Sehari',
                            ]),
                        Forms\Components\Toggle::make('brushing_before_bed')
                            ->label('Menyikat Gigi Sebelum Tidur'),
                        Forms\Components\Select::make('mother_accompaniment_frequency')
                            ->label('Pendampingan Ibu Saat Menyikat Gigi')
                            ->options([
                                'selalu' => 'Selalu',
                                'kadang_kadang' => 'Kadang-kadang',
                                'tidak_pernah' => 'Tidak Pernah',
                            ]),
                        Forms\Components\Select::make('use_toothpaste')
                            ->label('Menggunakan Pasta Gigi')
                            ->options([
                                'ya' => 'Ya',
                                'tidak' => 'Tidak',
                                'tidak_diketahui' => 'Tidak Diketahui',
                            ])
                            ->reactive(),
                        Forms\Components\TextInput::make('toothpaste_brand')
                            ->label('Merek Pasta Gigi (Opsional)')
                            ->visible(fn (callable $get) => $get('use_toothpaste') === 'ya'),
                        Forms\Components\Select::make('tool_used')
                            ->label('Alat yang Digunakan')
                            ->options([
                                'sikat_manual' => 'Sikat Gigi Manual',
                                'sikat_elektrik' => 'Sikat Gigi Elektrik',
                                'benang_gigi' => 'Benang Gigi',
                                'lainnya' => 'Lainnya',
                            ])
                            ->reactive(),
                        Forms\Components\TextInput::make('tool_other_description')
                            ->label('Sebutkan Alat')
                            ->visible(fn (callable $get) => $get('tool_used') === 'lainnya'),
                    ])
                    ->columns(['default' => 1, 'sm' => 2])
                    ->visible(fn (callable $get) => $get('attendance')),

                // TAHAP 3 — EDUKASI
                \Filament\Schemas\Components\Section::make('Tahap 3 — Edukasi Ibu')
                    ->relationship('education')
                    ->schema([
                        Forms\Components\Toggle::make('brushing_education')->label('Cara Menyikat Gigi'),
                        Forms\Components\Toggle::make('brushing_frequency_education')->label('Waktu Menyikat Gigi'),
                        Forms\Components\Toggle::make('fluoride_education')->label('Penggunaan Pasta Gigi'),
                        Forms\Components\Toggle::make('child_toothbrush_selection')->label('Pemilihan Sikat Gigi Anak'),
                        Forms\Components\Toggle::make('mother_toothbrush_selection')->label('Pemilihan Sikat Gigi Ibu'),
                        Forms\Components\Toggle::make('sugar_education')->label('Pembatasan Makanan/Minuman Manis'),
                        Forms\Components\Toggle::make('dental_checkup_education')->label('Pemeriksaan Gigi secara Berkala'),
                        Forms\Components\Toggle::make('home_care_education')->label('Perawatan Gigi di Rumah'),
                        Forms\Components\Textarea::make('notes')->label('Catatan Edukasi')->columnSpanFull(),
                    ])
                    ->columns(['default' => 1, 'sm' => 2])
                    ->visible(fn (callable $get) => $get('attendance')),

                // TAHAP 4 — SKRINING GIGI & MULUT, INDEKS & ODONTOGRAM
                \Filament\Schemas\Components\Section::make('Tahap 4 — Skrining Gigi & Mulut')
                    ->schema([
                        \Filament\Schemas\Components\Fieldset::make('Keluhan')
                            ->relationship('screening')
                            ->schema([
                                Forms\Components\Toggle::make('toothache')->label('Sakit Gigi'),
                                Forms\Components\Toggle::make('sensitive_teeth')->label('Gigi Ngilu/Sensitif'),
                                Forms\Components\Toggle::make('bleeding_gums')->label('Gusi Berdarah'),
                                Forms\Components\Toggle::make('swollen_gums')->label('Gusi Bengkak'),
                                Forms\Components\Toggle::make('bad_breath')->label('Bau Mulut'),
                                Forms\Components\Toggle::make('mouth_sores')->label('Sariawan'),
                                Forms\Components\Toggle::make('chewing_difficulty')->label('Sulit Mengunyah'),
                                Forms\Components\Toggle::make('complaint_other')->label('Lainnya')->reactive(),
                                Forms\Components\TextInput::make('complaint_other_description')
                                    ->label('Keluhan Lainnya')
                                    ->visible(fn (callable $get) => $get('complaint_other')),
                            ])->columns(['default' => 1, 'sm' => 2, 'md' => 3]),

                        \Filament\Schemas\Components\Fieldset::make('Temuan Pemeriksaan')
                            ->relationship('screening')
                            ->schema([
                                Forms\Components\Select::make('oral_hygiene')
                                    ->label('Kebersihan Mulut')
                                    ->options(['baik' => 'Baik', 'sedang' => 'Sedang', 'buruk' => 'Buruk']),
                                Forms\Components\Toggle::make('tartar')->label('Karang Gigi'),
                                Forms\Components\Toggle::make('cavities')->label('Gigi Berlubang')->reactive(),
                                Forms\Components\Toggle::make('broken_teeth')->label('Gigi Patah'),
                                Forms\Components\Toggle::make('plaque')->label('Plak'),
                                Forms\Components\Toggle::make('red_gums')->label('Gusi Merah'),
                                Forms\Components\Toggle::make('swollen_gums_observed')->label('Gusi Bengkak'),
                                Forms\Components\Toggle::make('poor_oral_hygiene')->label('Kebersihan Mulut Kurang'),
                                Forms\Components\Toggle::make('finding_other')->label('Lainnya')->reactive(),
                                Forms\Components\TextInput::make('finding_other_description')
                                    ->label('Keterangan Tambahan')
                                    ->visible(fn (callable $get) => $get('finding_other')),
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
                            ])->columns(['default' => 1, 'sm' => 2, 'md' => 3]),

                        \Filament\Schemas\Components\Fieldset::make('Indeks Gigi (DMF-T & def-t Otomatis)')
                            ->relationship('dentalIndex')
                            ->schema([
                                Forms\Components\Select::make('dentition_type')
                                    ->label('Kondisi Gigi Anak')
                                    ->options([
                                        'sulung' => 'Gigi Sulung (def-t)',
                                        'permanen' => 'Gigi Permanen (DMF-T)',
                                        'mixed' => 'Mixed Dentition (Keduanya)',
                                    ])
                                    ->default('mixed')
                                    ->reactive(),

                                // def-t (Gigi Sulung)
                                Forms\Components\TextInput::make('decay_prim_d')
                                    ->label('decayed (d)')
                                    ->numeric()->default(0)->reactive()
                                    ->afterStateUpdated(function (callable $set, callable $get) {
                                        $d = (int) $get('decay_prim_d');
                                        $e = (int) $get('extracted_prim_e');
                                        $f = (int) $get('filled_prim_f');
                                        $set('deft_score', $d + $e + $f);
                                    })
                                    ->visible(fn (callable $get) => in_array($get('dentition_type'), ['sulung', 'mixed'])),
                                Forms\Components\TextInput::make('extracted_prim_e')
                                    ->label('extracted (e)')
                                    ->numeric()->default(0)->reactive()
                                    ->afterStateUpdated(function (callable $set, callable $get) {
                                        $d = (int) $get('decay_prim_d');
                                        $e = (int) $get('extracted_prim_e');
                                        $f = (int) $get('filled_prim_f');
                                        $set('deft_score', $d + $e + $f);
                                    })
                                    ->visible(fn (callable $get) => in_array($get('dentition_type'), ['sulung', 'mixed'])),
                                Forms\Components\TextInput::make('filled_prim_f')
                                    ->label('filled (f)')
                                    ->numeric()->default(0)->reactive()
                                    ->afterStateUpdated(function (callable $set, callable $get) {
                                        $d = (int) $get('decay_prim_d');
                                        $e = (int) $get('extracted_prim_e');
                                        $f = (int) $get('filled_prim_f');
                                        $set('deft_score', $d + $e + $f);
                                    })
                                    ->visible(fn (callable $get) => in_array($get('dentition_type'), ['sulung', 'mixed'])),
                                Forms\Components\TextInput::make('deft_score')
                                    ->label('Skor def-t (Otomatis)')
                                    ->readOnly()
                                    ->default(0)
                                    ->visible(fn (callable $get) => in_array($get('dentition_type'), ['sulung', 'mixed'])),

                                // DMF-T (Gigi Permanen)
                                Forms\Components\TextInput::make('decay_perm_D')
                                    ->label('Decay (D)')
                                    ->numeric()->default(0)->reactive()
                                    ->afterStateUpdated(function (callable $set, callable $get) {
                                        $D = (int) $get('decay_perm_D');
                                        $M = (int) $get('missing_perm_M');
                                        $F = (int) $get('filling_perm_F');
                                        $set('dmft_score', $D + $M + $F);
                                    })
                                    ->visible(fn (callable $get) => in_array($get('dentition_type'), ['permanen', 'mixed'])),
                                Forms\Components\TextInput::make('missing_perm_M')
                                    ->label('Missing (M)')
                                    ->numeric()->default(0)->reactive()
                                    ->afterStateUpdated(function (callable $set, callable $get) {
                                        $D = (int) $get('decay_perm_D');
                                        $M = (int) $get('missing_perm_M');
                                        $F = (int) $get('filling_perm_F');
                                        $set('dmft_score', $D + $M + $F);
                                    })
                                    ->visible(fn (callable $get) => in_array($get('dentition_type'), ['permanen', 'mixed'])),
                                Forms\Components\TextInput::make('filling_perm_F')
                                    ->label('Filling (F)')
                                    ->numeric()->default(0)->reactive()
                                    ->afterStateUpdated(function (callable $set, callable $get) {
                                        $D = (int) $get('decay_perm_D');
                                        $M = (int) $get('missing_perm_M');
                                        $F = (int) $get('filling_perm_F');
                                        $set('dmft_score', $D + $M + $F);
                                    })
                                    ->visible(fn (callable $get) => in_array($get('dentition_type'), ['permanen', 'mixed'])),
                                Forms\Components\TextInput::make('dmft_score')
                                    ->label('Skor DMF-T (Otomatis)')
                                    ->readOnly()
                                    ->default(0)
                                    ->visible(fn (callable $get) => in_array($get('dentition_type'), ['permanen', 'mixed'])),
                            ])->columns(['default' => 1, 'sm' => 2, 'md' => 4]),

                        \Filament\Schemas\Components\Fieldset::make('Odontogram / Peta Gigi (Opsional)')
                            ->schema([
                                Forms\Components\Repeater::make('dentalFindings')
                                    ->relationship('dentalFindings')
                                    ->schema([
                                        Forms\Components\TextInput::make('tooth_number')
                                            ->label('Nomor Gigi (cth: 51, 61, 11)')
                                            ->required(),
                                        Forms\Components\Select::make('condition')
                                            ->label('Kondisi')
                                            ->options([
                                                'normal' => 'Normal',
                                                'decay' => 'Decay / Berlubang',
                                                'filling' => 'Filling / Tambalan',
                                                'missing' => 'Missing / Hilang',
                                                'broken' => 'Gigi Patah',
                                                'other' => 'Lainnya',
                                            ])
                                            ->required(),
                                        Forms\Components\TextInput::make('notes')->label('Catatan Gigi'),
                                    ])
                                    ->columns(['default' => 1, 'sm' => 3])
                                    ->addActionLabel('Tambah Temuan Gigi')
                                    ->collapsible()
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->visible(fn (callable $get) => $get('attendance')),

                // TAHAP 5 — TINDAKAN / TREATMENT
                \Filament\Schemas\Components\Section::make('Tahap 5 — Tindakan / Treatment')
                    ->relationship('treatment')
                    ->schema([
                        Forms\Components\Placeholder::make('cavities_warning')
                            ->label('')
                            ->content(new HtmlString('<div class="p-3 bg-amber-500/10 border border-amber-500/30 text-amber-600 dark:text-amber-400 rounded-lg text-sm font-semibold">⚠️ WARNING: Anak memiliki temuan gigi berlubang tetapi belum ada tindakan/tindak lanjut yang dipilih.</div>'))
                            ->visible(function (callable $get) {
                                $cavities = $get('../../screening.cavities');
                                $filling = $get('filling');
                                $extraction = $get('extraction');
                                $observation = $get('observation');
                                $scaling = $get('scaling');
                                $rootCanal = $get('root_canal');
                                $prosthesis = $get('prosthesis');
                                $other = $get('treatment_other');

                                return $cavities && !($filling || $extraction || $observation || $scaling || $rootCanal || $prosthesis || $other);
                            })
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('education')->label('Edukasi'),
                        Forms\Components\Toggle::make('observation')->label('Observasi'),
                        Forms\Components\Toggle::make('filling')->label('Tumpatan (Penambalan)')->reactive(),
                        Forms\Components\Toggle::make('extraction')->label('Ekstraksi (Pencabutan)')->reactive(),
                        Forms\Components\Toggle::make('scaling')->label('Scaling (Pembersihan Karang)'),
                        Forms\Components\Toggle::make('root_canal')->label('Perawatan Saluran Akar'),
                        Forms\Components\Toggle::make('prosthesis')->label('Protesa (Gigi Tiruan)'),
                        Forms\Components\Toggle::make('treatment_other')->label('Lainnya')->reactive(),
                        Forms\Components\TextInput::make('treatment_other_description')
                            ->label('Sebutkan Tindakan')
                            ->visible(fn (callable $get) => $get('treatment_other')),
                        Forms\Components\Textarea::make('notes')->label('Catatan Tindakan')->columnSpanFull(),
                    ])
                    ->columns(['default' => 1, 'sm' => 2])
                    ->visible(fn (callable $get) => $get('attendance')),

                // TAHAP 6 — RUJUKAN
                \Filament\Schemas\Components\Section::make('Tahap 6 — Rujukan')
                    ->relationship('referral')
                    ->schema([
                        Forms\Components\Toggle::make('referral_needed')
                            ->label('Perlu Rujukan?')
                            ->default(false)
                            ->reactive(),

                        Forms\Components\DatePicker::make('referral_date')
                            ->label('Tanggal Rujukan')
                            ->default(now())
                            ->required(fn (callable $get) => $get('referral_needed'))
                            ->visible(fn (callable $get) => $get('referral_needed')),
                        Forms\Components\Select::make('reason')
                            ->label('Alasan Rujukan')
                            ->options([
                                'karies_luas' => 'Karies Luas',
                                'gigi_patah' => 'Gigi Patah',
                                'perawatan_lanjutan' => 'Perlu Perawatan Lanjutan',
                                'kelainan_jaringan_lunak' => 'Kelainan Jaringan Lunak',
                                'Lainnya' => 'Lainnya',
                            ])
                            ->reactive()
                            ->required(fn (callable $get) => $get('referral_needed'))
                            ->visible(fn (callable $get) => $get('referral_needed')),
                        Forms\Components\TextInput::make('reason_other')
                            ->label('Alasan Tambahan')
                            ->visible(fn (callable $get) => $get('referral_needed') && $get('reason') === 'Lainnya'),
                        Forms\Components\Select::make('destination')
                            ->label('Tujuan Rujukan')
                            ->options([
                                'Dokter Gigi' => 'Dokter Gigi',
                                'Puskesmas' => 'Puskesmas',
                                'Klinik Gigi' => 'Klinik Gigi',
                                'Rumah Sakit' => 'Rumah Sakit',
                                'Lainnya' => 'Lainnya',
                            ])
                            ->reactive()
                            ->required(fn (callable $get) => $get('referral_needed'))
                            ->visible(fn (callable $get) => $get('referral_needed')),
                        Forms\Components\TextInput::make('destination_other')
                            ->label('Tujuan Tambahan')
                            ->visible(fn (callable $get) => $get('referral_needed') && $get('destination') === 'Lainnya'),
                        Forms\Components\CheckboxList::make('recommended_actions')
                            ->label('Tindakan yang Direkomendasikan')
                            ->options([
                                'pemeriksaan_dokter_gigi' => 'Pemeriksaan Dokter Gigi',
                                'tumpatan' => 'Tumpatan',
                                'ekstraksi' => 'Ekstraksi',
                                'scaling' => 'Scaling',
                                'perawatan_saluran_akar' => 'Perawatan Saluran Akar',
                                'pemeriksaan_lanjutan' => 'Pemeriksaan Lanjutan',
                                'lainnya' => 'Lainnya',
                            ])
                            ->required(fn (callable $get) => $get('referral_needed'))
                            ->visible(fn (callable $get) => $get('referral_needed'))
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('notes')->label('Catatan Rujukan')
                            ->visible(fn (callable $get) => $get('referral_needed'))
                            ->columnSpanFull(),
                    ])
                    ->columns(['default' => 1, 'sm' => 2])
                    ->visible(fn (callable $get) => $get('attendance')),

                // TAHAP 7 — JADWAL FOLLOW-UP
                \Filament\Schemas\Components\Section::make('Tahap 7 — Jadwal Follow-up')
                    ->schema([
                        Forms\Components\DatePicker::make('follow_up_scheduled_date')
                            ->label('Tanggal Jadwal Follow-up')
                            ->placeholder('Pilih tanggal jika perlu follow-up bulan berikutnya'),
                    ])
                    ->visible(fn (callable $get) => $get('attendance')),

                // RIWAYAT UKGM ANAK
                \Filament\Schemas\Components\Section::make('Riwayat UKGM Anak')
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
                                
                                $html = '<ul class="divide-y divide-gray-200 dark:divide-gray-700">';
                                foreach($histories as $history) {
                                    $date = $history->activity ? $history->activity->activity_date->format('d M Y') : '-';
                                    $posyandu = $history->activity && $history->activity->posyandu ? $history->activity->posyandu->nama_posyandu : '-';
                                    $status = $history->attendance ? 'Hadir' : 'Tidak Hadir';
                                    $risk = $history->screening ? $history->screening->risk_level : '-';
                                    $dmft = $history->dentalIndex ? "DMF-T: {$history->dentalIndex->dmft_score}" : '';
                                    
                                    $html .= "<li class='py-2'><strong>{$date}</strong> di {$posyandu} - Status: <strong>{$status}</strong> | Risiko: <strong>{$risk}</strong> {$dmft}</li>";
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
                    ->color(fn ($state): string => match ((string)$state) {
                        'rendah' => 'success',
                        'pemantauan' => 'warning',
                        'lanjutan' => 'warning',
                        'rujukan' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status Peserta')
                    ->getStateUsing(function (GarasiParticipant $record) {
                        if (!$record->attendance) return 'Tidak Hadir';
                        if ($record->referral && $record->referral->referral_needed) {
                            return 'Dirujuk';
                        }
                        if ($record->follow_up_scheduled_date) {
                            return 'Jadwal Follow-up';
                        }
                        if ($record->screening) return 'Selesai';
                        return 'Belum Diperiksa';
                    })
                    ->badge()
                    ->color(function ($state) {
                        if ($state === 'Tidak Hadir') return 'gray';
                        if ($state === 'Belum Diperiksa') return 'warning';
                        if ($state === 'Dirujuk') return 'danger';
                        if ($state === 'Jadwal Follow-up') return 'info';
                        return 'success';
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
                                'status' => 'pending',
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
