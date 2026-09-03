<?php

namespace App\Filament\Resources\PosyanduMonthlyExaminations\Pages;

use App\Filament\Resources\PosyanduMonthlyExaminations\PosyanduMonthlyExaminationResource;
use App\Models\Child;
use App\Models\PosyanduMonthlyParticipant;
use App\Services\Posyandu\PosyanduAnthropometryService;
use Filament\Forms;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class ManagePosyanduMonthlyParticipants extends ManageRelatedRecords
{
    protected static string $resource = PosyanduMonthlyExaminationResource::class;

    protected static string $relationship = 'participants';

    protected static \BackedEnum|string|null $navigationIcon = Heroicon::OutlinedUsers;

    public static function getNavigationLabel(): string
    {
        return 'Kelola Peserta';
    }

    public function form(Schema $schema): Schema
    {
        $anthroService = app(PosyanduAnthropometryService::class);

        return $schema
            ->columns(1)
            ->schema([
                // SECTION 1: IDENTITAS ANAK
                Section::make('Identitas Anak')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Forms\Components\Select::make('child_id')
                            ->label('Anak')
                            ->options(function () {
                                $examination = $this->getOwnerRecord();

                                return Child::where('posyandu_id', $examination->posyandu_id)
                                    ->pluck('nama_lengkap', 'id');
                            })
                            ->required()
                            ->reactive()
                            ->disabled(fn (string $operation): bool => $operation === 'edit')
                            ->afterStateHydrated(function ($state, Set $set) {
                                if (! $state) {
                                    return;
                                }
                                $child = Child::with(['posyandu', 'orangTua'])->find($state);
                                if (! $child) {
                                    return;
                                }

                                $examination = $this->getOwnerRecord();
                                $examDate = $examination->examination_date?->format('Y-m-d') ?? now()->format('Y-m-d');
                                $ageMonths = app(PosyanduAnthropometryService::class)->calculateAgeMonths($child->tanggal_lahir?->format('Y-m-d'), $examDate);

                                $set('nik', $child->nik);
                                $set('jenis_kelamin', $child->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan');
                                $set('tanggal_lahir', $child->tanggal_lahir?->format('d M Y'));
                                $set('nama_orang_tua', $child->orangTua?->nama_lengkap);
                                $set('posyandu_name', $child->posyandu?->nama_posyandu);
                                $set('umur_display', $ageMonths.' bulan ('.floor($ageMonths / 12).' thn '.($ageMonths % 12).' bln)');
                                $set('orang_tua_id', $child->orang_tua_id);
                            })
                            ->afterStateUpdated(function ($state, Set $set) {
                                if (! $state) {
                                    return;
                                }
                                $child = Child::with(['posyandu', 'orangTua'])->find($state);
                                if (! $child) {
                                    return;
                                }

                                $examination = $this->getOwnerRecord();
                                $examDate = $examination->examination_date?->format('Y-m-d') ?? now()->format('Y-m-d');
                                $ageMonths = app(PosyanduAnthropometryService::class)->calculateAgeMonths($child->tanggal_lahir?->format('Y-m-d'), $examDate);

                                $set('nik', $child->nik);
                                $set('jenis_kelamin', $child->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan');
                                $set('tanggal_lahir', $child->tanggal_lahir?->format('d M Y'));
                                $set('nama_orang_tua', $child->orangTua?->nama_lengkap);
                                $set('posyandu_name', $child->posyandu?->nama_posyandu);
                                $set('umur_display', $ageMonths.' bulan ('.floor($ageMonths / 12).' thn '.($ageMonths % 12).' bln)');
                                $set('orang_tua_id', $child->orang_tua_id);
                            }),
                        Forms\Components\Hidden::make('orang_tua_id'),
                        Forms\Components\TextInput::make('nik')->label('NIK')->disabled(),
                        Forms\Components\TextInput::make('jenis_kelamin')->label('Jenis Kelamin')->disabled(),
                        Forms\Components\TextInput::make('tanggal_lahir')->label('Tanggal Lahir')->disabled(),
                        Forms\Components\TextInput::make('nama_orang_tua')->label('Nama Orang Tua / Ibu')->disabled(),
                        Forms\Components\TextInput::make('posyandu_name')->label('Posyandu')->disabled(),
                        Forms\Components\TextInput::make('umur_display')->label('Umur Saat Pemeriksaan')->disabled(),
                        Forms\Components\Toggle::make('attendance')
                            ->label('Kehadiran Anak (Hadir)')
                            ->default(true)
                            ->reactive()
                            ->columnSpanFull(),
                    ])->columns(['default' => 1, 'sm' => 2, 'md' => 3]),

                // SECTION 2: PEMERIKSAAN ANTROPOMETRI
                Section::make('Pemeriksaan Antropometri')
                    ->icon('heroicon-o-scale')
                    ->visible(fn ($get) => (bool) $get('attendance'))
                    ->schema([
                        Forms\Components\TextInput::make('weight')
                            ->label('Berat Badan (kg) *')
                            ->numeric()
                            ->minValue(0)
                            ->required(fn ($get) => (bool) $get('attendance'))
                            ->suffix('kg')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set) use ($anthroService) {
                                self::updateAnthropometry($get, $set, $anthroService);
                            }),

                        Forms\Components\TextInput::make('height')
                            ->label('Tinggi Badan (cm) *')
                            ->numeric()
                            ->minValue(0)
                            ->required(fn ($get) => (bool) $get('attendance'))
                            ->suffix('cm')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set) use ($anthroService) {
                                self::updateAnthropometry($get, $set, $anthroService);
                            }),

                        Forms\Components\TextInput::make('bmi')
                            ->label('IMT (BB/TB²)')
                            ->readOnly()
                            ->dehydrated()
                            ->suffix('kg/m²'),

                        Forms\Components\TextInput::make('bmi_category')
                            ->label('Kategori IMT')
                            ->readOnly()
                            ->dehydrated(),

                        Forms\Components\TextInput::make('stunting_status')
                            ->label('TB/U (Stunting)')
                            ->readOnly()
                            ->dehydrated()
                            ->helperText('Status stunting dihitung otomatis dari standar WHO'),

                        Forms\Components\TextInput::make('head_circumference')
                            ->label('Lingkar Kepala (cm) *')
                            ->numeric()
                            ->minValue(0)
                            ->required(fn ($get) => (bool) $get('attendance'))
                            ->suffix('cm')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set) use ($anthroService) {
                                $lk = (float) $get('head_circumference');
                                if ($lk > 0) {
                                    $set('head_circumference_result', $anthroService->determineHeadCircumferenceResult($lk));
                                }
                            }),

                        Forms\Components\TextInput::make('head_circumference_result')
                            ->label('Status Lingkar Kepala (LK/U)')
                            ->readOnly()
                            ->dehydrated(),
                    ])->columns(2),

                // SECTION 3: SKRINING KESEHATAN (ASI & MP ASI)
                Section::make('I. Skrining Kesehatan')
                    ->icon('heroicon-o-heart')
                    ->visible(fn ($get) => (bool) $get('attendance'))
                    ->schema([
                        Forms\Components\Radio::make('exclusive_breastfeeding')
                            ->label('Apakah anak mendapatkan ASI Eksklusif?')
                            ->options([
                                'Y' => 'Ya (Y)',
                                'T' => 'Tidak (T)',
                            ])
                            ->default('Y')
                            ->inline(),

                        Forms\Components\Radio::make('mp_asi')
                            ->label('Apakah pemberian MP ASI sesuai umur dan komposisi?')
                            ->options([
                                'Y' => 'Ya (Y)',
                                'T' => 'Tidak (T)',
                            ])
                            ->default('Y')
                            ->inline(),
                    ])->columns(2),

                // SECTION 4: SKRINING GEJALA TBC
                Section::make('II. Skrining Gejala TBC')
                    ->icon('heroicon-o-shield-exclamation')
                    ->visible(fn ($get) => (bool) $get('attendance'))
                    ->schema([
                        Forms\Components\Radio::make('tb_cough')
                            ->label('1. Batuk terus menerus?')
                            ->options(['Y' => 'Ya (Y)', 'T' => 'Tidak (T)'])
                            ->default('T')
                            ->inline()
                            ->reactive()
                            ->afterStateUpdated(fn (Get $get, Set $set) => self::updateTbResult($get, $set, $anthroService)),

                        Forms\Components\Radio::make('tb_fever')
                            ->label('2. Demam lebih dari atau sama dengan 2 minggu?')
                            ->options(['Y' => 'Ya (Y)', 'T' => 'Tidak (T)'])
                            ->default('T')
                            ->inline()
                            ->reactive()
                            ->afterStateUpdated(fn (Get $get, Set $set) => self::updateTbResult($get, $set, $anthroService)),

                        Forms\Components\Radio::make('tb_weight_problem')
                            ->label('3. BB tidak naik/turun dalam 2 bulan berturut-turut?')
                            ->options(['Y' => 'Ya (Y)', 'T' => 'Tidak (T)'])
                            ->default('T')
                            ->inline()
                            ->reactive()
                            ->afterStateUpdated(fn (Get $get, Set $set) => self::updateTbResult($get, $set, $anthroService)),

                        Forms\Components\Radio::make('tb_close_contact')
                            ->label('4. Kontak erat pasien TBC?')
                            ->options(['Y' => 'Ya (Y)', 'T' => 'Tidak (T)'])
                            ->default('T')
                            ->inline()
                            ->reactive()
                            ->afterStateUpdated(fn (Get $get, Set $set) => self::updateTbResult($get, $set, $anthroService)),

                        Forms\Components\TextInput::make('tb_screening_result')
                            ->label('Hasil Skrining TBC')
                            ->readOnly()
                            ->dehydrated()
                            ->default('Tidak Terindikasi')
                            ->columnSpanFull(),
                    ])->columns(2),

                // SECTION 5: HASIL PEMERIKSAAN OTOMATIS & TINDAK LANJUT
                Section::make('III. Hasil & Tindak Lanjut')
                    ->icon('heroicon-o-check-badge')
                    ->visible(fn ($get) => (bool) $get('attendance'))
                    ->schema([
                        Forms\Components\Select::make('examination_status')
                            ->label('Status Pemeriksaan')
                            ->options([
                                'Belum Diperiksa' => 'Belum Diperiksa',
                                'Sudah Diperiksa' => 'Sudah Diperiksa',
                                'Perlu Tindak Lanjut' => 'Perlu Tindak Lanjut',
                                'Dirujuk' => 'Dirujuk',
                                'Selesai' => 'Selesai',
                            ])
                            ->default('Sudah Diperiksa')
                            ->required(),

                        Forms\Components\Textarea::make('follow_up_recommendation')
                            ->label('Rekomendasi / Saran Tindak Lanjut')
                            ->placeholder('Contoh: Perlu pemantauan pertumbuhan / Perlu pemeriksaan lanjutan TBC di Puskesmas')
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan Pemeriksaan')
                            ->columnSpanFull(),
                    ])->columns(1),

                // SECTION 6: HISTORI PEMERIKSAAN BULANAN ANAK
                Section::make('Histori Pemeriksaan Bulanan Anak')
                    ->icon('heroicon-o-clock')
                    ->schema([
                        Forms\Components\Placeholder::make('history_table')
                            ->label('')
                            ->content(function (PosyanduMonthlyParticipant $record) {
                                $childId = $record->child_id;
                                if (! $childId) {
                                    return 'Pilih anak untuk melihat riwayat.';
                                }

                                $histories = PosyanduMonthlyParticipant::with('examination')
                                    ->where('child_id', $childId)
                                    ->where('id', '!=', $record->id)
                                    ->orderBy('created_at', 'desc')
                                    ->get();

                                if ($histories->isEmpty()) {
                                    return new HtmlString('<div class="text-sm text-gray-500 py-2">Belum ada riwayat pemeriksaan bulan sebelumnya.</div>');
                                }

                                $html = '<div class="overflow-x-auto"><table class="w-full text-xs text-left border border-gray-200 dark:border-gray-700 divide-y divide-gray-200">
                                    <thead class="bg-gray-100 dark:bg-gray-800 font-semibold">
                                        <tr>
                                            <th class="p-2">Bulan/Tsn</th>
                                            <th class="p-2">BB (kg)</th>
                                            <th class="p-2">TB (cm)</th>
                                            <th class="p-2">IMT</th>
                                            <th class="p-2">TB/U (Stunting)</th>
                                            <th class="p-2">LK (cm)</th>
                                            <th class="p-2">Skrining TBC</th>
                                            <th class="p-2">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">';

                                foreach ($histories as $h) {
                                    $exam = $h->examination;
                                    $period = $exam ? sprintf('%02d/%d', $exam->month, $exam->year) : $h->created_at->format('m/Y');
                                    $html .= sprintf(
                                        '<tr>
                                            <td class="p-2 font-medium">%s</td>
                                            <td class="p-2">%s</td>
                                            <td class="p-2">%s</td>
                                            <td class="p-2">%s</td>
                                            <td class="p-2"><span class="px-1.5 py-0.5 rounded text-white bg-%s">%s</span></td>
                                            <td class="p-2">%s</td>
                                            <td class="p-2">%s</td>
                                            <td class="p-2 font-semibold">%s</td>
                                        </tr>',
                                        $period,
                                        $h->weight ?? '-',
                                        $h->height ?? '-',
                                        $h->bmi ?? '-',
                                        in_array($h->stunting_status, ['Pendek', 'Sangat Pendek']) ? 'red-500' : 'emerald-600',
                                        $h->stunting_status ?? '-',
                                        $h->head_circumference ?? '-',
                                        $h->tb_screening_result ?? '-',
                                        $h->examination_status
                                    );
                                }
                                $html .= '</tbody></table></div>';

                                return new HtmlString($html);
                            }),
                    ])
                    ->hidden(fn (string $operation): bool => $operation === 'create'),
            ]);
    }

    protected static function updateAnthropometry(Get $get, Set $set, PosyanduAnthropometryService $service): void
    {
        $bb = (float) $get('weight');
        $tb = (float) $get('height');
        $childId = $get('child_id');

        if ($bb > 0 && $tb > 0) {
            $bmi = $service->calculateBMI($bb, $tb);
            $bmiCat = $service->determineBMICategory($bmi);

            $set('bmi', $bmi);
            $set('bmi_category', $bmiCat);

            if ($childId) {
                $child = Child::find($childId);
                if ($child) {
                    $ageMonths = $service->calculateAgeMonths($child->tanggal_lahir?->format('Y-m-d'));
                    $stunting = $service->determineStuntingStatus($ageMonths, $tb);
                    $set('stunting_status', $stunting);
                }
            }
        }
    }

    protected static function updateTbResult(Get $get, Set $set, PosyanduAnthropometryService $service): void
    {
        $result = $service->determineTBScreening(
            (string) $get('tb_cough'),
            (string) $get('tb_fever'),
            (string) $get('tb_weight_problem'),
            (string) $get('tb_close_contact')
        );

        $set('tb_screening_result', $result);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('child.nama_lengkap')
            ->columns([
                Tables\Columns\TextColumn::make('child.nama_lengkap')
                    ->label('Nama Anak')
                    ->searchable(['nama_lengkap', 'nik'])
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('child.tanggal_lahir')
                    ->label('Umur')
                    ->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->age.' thn' : '-'),

                Tables\Columns\TextColumn::make('orangTua.nama_lengkap')
                    ->label('Ibu / Orang Tua')
                    ->searchable(),

                Tables\Columns\IconColumn::make('attendance')
                    ->label('Kehadiran')
                    ->boolean(),

                Tables\Columns\TextColumn::make('stunting_status')
                    ->label('TB/U (Stunting)')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'Sangat Pendek' => 'danger',
                        'Pendek' => 'warning',
                        'Normal' => 'success',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('tb_screening_result')
                    ->label('Skrining TBC')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'Terindikasi' => 'danger',
                        'Tidak Terindikasi' => 'success',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('examination_status')
                    ->label('Status Peserta')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'Belum Diperiksa' => 'gray',
                        'Sudah Diperiksa' => 'success',
                        'Perlu Tindak Lanjut' => 'warning',
                        'Dirujuk' => 'danger',
                        'Selesai' => 'primary',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('attendance')
                    ->label('Kehadiran')
                    ->placeholder('Semua')
                    ->trueLabel('Hadir')
                    ->falseLabel('Tidak Hadir'),

                Tables\Filters\SelectFilter::make('stunting_status')
                    ->label('Status Stunting')
                    ->options([
                        'Normal' => 'Normal',
                        'Pendek' => 'Pendek',
                        'Sangat Pendek' => 'Sangat Pendek',
                    ]),

                Tables\Filters\SelectFilter::make('examination_status')
                    ->label('Status Peserta')
                    ->options([
                        'Belum Diperiksa' => 'Belum Diperiksa',
                        'Sudah Diperiksa' => 'Sudah Diperiksa',
                        'Perlu Tindak Lanjut' => 'Perlu Tindak Lanjut',
                        'Dirujuk' => 'Dirujuk',
                        'Selesai' => 'Selesai',
                    ]),
            ])
            ->headerActions([
                \Filament\Actions\Action::make('export_excel')
                    ->label('Export Excel')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(function () {
                        $examination = $this->getOwnerRecord();
                        $posyanduName = $examination->posyandu ? \Illuminate\Support\Str::slug($examination->posyandu->nama_posyandu) : 'posyandu';
                        $filename = sprintf('rekap_pemeriksaan_posyandu_%s_%02d%d.xlsx', $posyanduName, $examination->month, $examination->year);

                        return \Maatwebsite\Excel\Facades\Excel::download(
                            new \App\Exports\PemeriksaanBulananPosyanduExport($examination->id),
                            $filename
                        );
                    }),

                \Filament\Actions\Action::make('generate_participants')
                    ->label(fn () => $this->getOwnerRecord()->participants()->count() > 0 ? 'Generate Ulang Peserta' : 'Generate Peserta')
                    ->icon('heroicon-o-sparkles')
                    ->color(fn () => $this->getOwnerRecord()->participants()->count() > 0 ? 'gray' : 'primary')
                    ->requiresConfirmation()
                    ->action(function () {
                        $examination = $this->getOwnerRecord();
                        $children = Child::where('posyandu_id', $examination->posyandu_id)->get();

                        foreach ($children as $child) {
                            PosyanduMonthlyParticipant::insertOrIgnore([
                                'posyandu_monthly_examination_id' => $examination->id,
                                'child_id' => $child->id,
                                'orang_tua_id' => $child->orang_tua_id,
                                'attendance' => false,
                                'examination_status' => 'Belum Diperiksa',
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }

                        \Filament\Notifications\Notification::make()
                            ->title('Peserta pemeriksaan bulanan berhasil digenerate!')
                            ->success()
                            ->send();
                    }),

                \Filament\Actions\CreateAction::make()
                    ->label('Tambah Manual')
                    ->icon('heroicon-o-plus'),
            ])
            ->actions([
                \Filament\Actions\EditAction::make()
                    ->label('Periksa')
                    ->icon('heroicon-o-pencil-square'),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\BulkAction::make('export_selected')
                        ->label('Export Selected')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('success')
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $recordIds = $records->pluck('id')->toArray();
                            $filename = 'rekap_pemeriksaan_posyandu_selected_'.now()->format('Ymd_His').'.xlsx';

                            return \Maatwebsite\Excel\Facades\Excel::download(
                                new \App\Exports\PemeriksaanBulananPosyanduExport(null, [], $recordIds),
                                $filename
                            );
                        }),
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
