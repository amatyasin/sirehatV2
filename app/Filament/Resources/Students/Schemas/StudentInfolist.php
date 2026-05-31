<?php

namespace App\Filament\Resources\Students\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;

class StudentInfolist
{
    public static function configure(
        Schema $infolist
    ): Schema {
        return $infolist
            ->schema([
                Section::make('Identitas Siswa')
                    ->description('Data diri dan informasi akademik siswa')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextEntry::make('nama_lengkap')
                                    ->label('Nama Lengkap')
                                    ->weight('bold')
                                    ->color('primary'),
                                TextEntry::make('nik')
                                    ->label('NIK')
                                    ->placeholder('-'),
                                TextEntry::make('nisn')
                                    ->label('NISN'),
                                TextEntry::make('jenis_kelamin')
                                    ->label('Jenis Kelamin')
                                    ->badge()
                                    ->color(fn ($state) => $state === 'L' ? 'primary' : 'danger')
                                    ->formatStateUsing(fn ($state) => $state === 'L' ? 'Laki-laki' : 'Perempuan'),
                                TextEntry::make('tempat_lahir')
                                    ->label('Tempat Lahir'),
                                TextEntry::make('tanggal_lahir')
                                    ->label('Tanggal Lahir')
                                    ->date(),
                                TextEntry::make('school.nama_sekolah')
                                    ->label('Sekolah'),
                                TextEntry::make('activeClassHistory.schoolClass.nama_kelas')
                                    ->label('Kelas')
                                    ->badge()
                                    ->color('primary')
                                    ->placeholder('-'),
                                TextEntry::make('activeClassHistory.academicYear.nama')
                                    ->label('Tahun Ajaran')
                                    ->badge()
                                    ->color('success')
                                    ->placeholder('-'),
                                TextEntry::make('activeClassHistory.semester')
                                    ->label('Semester')
                                    ->badge()
                                    ->color('warning')
                                    ->placeholder('-'),
                                TextEntry::make('aktif')
                                    ->label('Status Aktif')
                                    ->badge()
                                    ->color(fn ($state) => $state ? 'success' : 'gray')
                                    ->formatStateUsing(fn ($state) => $state ? 'Aktif' : 'Tidak Aktif'),
                            ]),
                        TextEntry::make('alamat')
                            ->label('Alamat')
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->columnSpanFull(),

                Tabs::make('Riwayat Pemeriksaan Kesehatan')
                    ->tabs([
                        Tabs\Tab::make('Pemeriksaan Gigi')
                            ->icon('heroicon-o-face-smile')
                            ->schema([
                                RepeatableEntry::make('pemeriksaanGigis')
                                    ->label('')
                                    ->placeholder('Belum ada riwayat pemeriksaan gigi')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                TextEntry::make('tanggal_pemeriksaan')
                                                    ->label('Tanggal Pemeriksaan')
                                                    ->date()
                                                    ->weight('bold'),
                                                TextEntry::make('dirujuk_ke_fasyankes')
                                                    ->label('Rujukan')
                                                    ->badge()
                                                    ->color(fn ($state) => $state === 'Y' ? 'danger' : 'success')
                                                    ->formatStateUsing(fn ($state) => $state === 'Y' ? 'Rujuk' : 'Tidak Rujuk'),
                                                TextEntry::make('keterangan_rujukan')
                                                    ->label('Keterangan Rujukan')
                                                    ->placeholder('-'),
                                            ]),
                                        Section::make('Kondisi Mulut & Gigi')
                                            ->schema([
                                                Grid::make(4)
                                                    ->schema([
                                                        TextEntry::make('celah_bibir_langit')->label('Celah Bibir')->badge()->color(fn ($state) => $state === 'Y' ? 'danger' : 'success')->formatStateUsing(fn ($state) => $state === 'Y' ? 'Ya' : 'Tidak'),
                                                        TextEntry::make('luka_sudut_mulut')->label('Luka Sudut Mulut')->badge()->color(fn ($state) => $state === 'Y' ? 'danger' : 'success')->formatStateUsing(fn ($state) => $state === 'Y' ? 'Ya' : 'Tidak'),
                                                        TextEntry::make('sariawan')->label('Sariawan')->badge()->color(fn ($state) => $state === 'Y' ? 'danger' : 'success')->formatStateUsing(fn ($state) => $state === 'Y' ? 'Ya' : 'Tidak'),
                                                        TextEntry::make('lidah_kotor')->label('Lidah Kotor')->badge()->color(fn ($state) => $state === 'Y' ? 'danger' : 'success')->formatStateUsing(fn ($state) => $state === 'Y' ? 'Ya' : 'Tidak'),
                                                        TextEntry::make('gigi_berlubang')->label('Gigi Berlubang')->badge()->color(fn ($state) => $state === 'Y' ? 'danger' : 'success')->formatStateUsing(fn ($state) => $state === 'Y' ? 'Ya' : 'Tidak'),
                                                        TextEntry::make('jumlah_gigi_berlubang')->label('Jumlah Lubang')->placeholder('-'),
                                                        TextEntry::make('gusi_berdarah')->label('Gusi Berdarah')->badge()->color(fn ($state) => $state === 'Y' ? 'danger' : 'success')->formatStateUsing(fn ($state) => $state === 'Y' ? 'Ya' : 'Tidak'),
                                                        TextEntry::make('gusi_bengkak')->label('Gusi Bengkak')->badge()->color(fn ($state) => $state === 'Y' ? 'danger' : 'success')->formatStateUsing(fn ($state) => $state === 'Y' ? 'Ya' : 'Tidak'),
                                                        TextEntry::make('gigi_kotor_plak')->label('Gigi Kotor (Plak)')->badge()->color(fn ($state) => $state === 'Y' ? 'danger' : 'success')->formatStateUsing(fn ($state) => $state === 'Y' ? 'Ya' : 'Tidak'),
                                                        TextEntry::make('karang_gigi')->label('Karang Gigi')->badge()->color(fn ($state) => $state === 'Y' ? 'danger' : 'success')->formatStateUsing(fn ($state) => $state === 'Y' ? 'Ya' : 'Tidak'),
                                                        TextEntry::make('susunan_gigi_tidak_teratur')->label('Gigi Tidak Teratur')->badge()->color(fn ($state) => $state === 'Y' ? 'danger' : 'success')->formatStateUsing(fn ($state) => $state === 'Y' ? 'Ya' : 'Tidak'),
                                                    ]),
                                            ])->compact()->collapsible(),
                                        Section::make('Pemeriksaan Tambahan / Alat Bantu')
                                            ->schema([
                                                Grid::make(5)
                                                    ->schema([
                                                        TextEntry::make('penglihatan_loupe')->label('Loupe')->badge()->color(fn ($state) => $state === 'Y' ? 'primary' : 'gray')->formatStateUsing(fn ($state) => $state === 'Y' ? 'Ya' : 'Tidak'),
                                                        TextEntry::make('pendengaran')->label('Alat Dengar')->badge()->color(fn ($state) => $state === 'Y' ? 'primary' : 'gray')->formatStateUsing(fn ($state) => $state === 'Y' ? 'Ya' : 'Tidak'),
                                                        TextEntry::make('kursi_roda')->label('Kursi Roda')->badge()->color(fn ($state) => $state === 'Y' ? 'primary' : 'gray')->formatStateUsing(fn ($state) => $state === 'Y' ? 'Ya' : 'Tidak'),
                                                        TextEntry::make('tongkat_kruk')->label('Tongkat/Kruk')->badge()->color(fn ($state) => $state === 'Y' ? 'primary' : 'gray')->formatStateUsing(fn ($state) => $state === 'Y' ? 'Ya' : 'Tidak'),
                                                        TextEntry::make('kaki_tangan_mata_protese')->label('Protese')->badge()->color(fn ($state) => $state === 'Y' ? 'primary' : 'gray')->formatStateUsing(fn ($state) => $state === 'Y' ? 'Ya' : 'Tidak'),
                                                    ]),
                                            ])->compact()->collapsible(),
                                    ]),
                            ]),

                        Tabs\Tab::make('Pemeriksaan Mata')
                            ->icon('heroicon-o-eye')
                            ->schema([
                                RepeatableEntry::make('pemeriksaanMatas')
                                    ->label('')
                                    ->placeholder('Belum ada riwayat pemeriksaan mata')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                TextEntry::make('tanggal_pemeriksaan')
                                                    ->label('Tanggal Pemeriksaan')
                                                    ->date()
                                                    ->weight('bold'),
                                                TextEntry::make('dirujuk_ke_fasyankes')
                                                    ->label('Rujukan')
                                                    ->badge()
                                                    ->color(fn ($state) => $state === 'Y' ? 'danger' : 'success')
                                                    ->formatStateUsing(fn ($state) => $state === 'Y' ? 'Rujuk' : 'Tidak Rujuk'),
                                                TextEntry::make('keterangan_rujukan')
                                                    ->label('Keterangan Rujukan')
                                                    ->placeholder('-'),
                                            ]),
                                        Section::make('Fungsi Penglihatan')
                                            ->schema([
                                                Grid::make(4)
                                                    ->schema([
                                                        TextEntry::make('visus_kanan')->label('Visus Kanan')->placeholder('-'),
                                                        TextEntry::make('visus_kiri')->label('Visus Kiri')->placeholder('-'),
                                                        TextEntry::make('pakai_kacamata')->label('Pakai Kacamata')->badge()->color(fn ($state) => $state === 'Y' ? 'primary' : 'gray')->formatStateUsing(fn ($state) => $state === 'Y' ? 'Ya' : 'Tidak'),
                                                        TextEntry::make('buta_warna')->label('Buta Warna')->badge()->color(fn ($state) => $state === 'Y' ? 'danger' : 'success')->formatStateUsing(fn ($state) => $state === 'Y' ? 'Ya' : 'Tidak'),
                                                    ]),
                                            ])->compact()->collapsible(),
                                        Section::make('Gejala & Kondisi Fisik Mata')
                                            ->schema([
                                                Grid::make(6)
                                                    ->schema([
                                                        TextEntry::make('mata_merah')->label('Mata Merah')->badge()->color(fn ($state) => $state === 'Y' ? 'danger' : 'success')->formatStateUsing(fn ($state) => $state === 'Y' ? 'Ya' : 'Tidak'),
                                                        TextEntry::make('mata_berair')->label('Mata Berair')->badge()->color(fn ($state) => $state === 'Y' ? 'danger' : 'success')->formatStateUsing(fn ($state) => $state === 'Y' ? 'Ya' : 'Tidak'),
                                                        TextEntry::make('nyeri_mata')->label('Nyeri Mata')->badge()->color(fn ($state) => $state === 'Y' ? 'danger' : 'success')->formatStateUsing(fn ($state) => $state === 'Y' ? 'Ya' : 'Tidak'),
                                                        TextEntry::make('gatal_mata')->label('Gatal Mata')->badge()->color(fn ($state) => $state === 'Y' ? 'danger' : 'success')->formatStateUsing(fn ($state) => $state === 'Y' ? 'Ya' : 'Tidak'),
                                                        TextEntry::make('mata_bengkak')->label('Mata Bengkak')->badge()->color(fn ($state) => $state === 'Y' ? 'danger' : 'success')->formatStateUsing(fn ($state) => $state === 'Y' ? 'Ya' : 'Tidak'),
                                                        TextEntry::make('mata_belekan')->label('Mata Belekan')->badge()->color(fn ($state) => $state === 'Y' ? 'danger' : 'success')->formatStateUsing(fn ($state) => $state === 'Y' ? 'Ya' : 'Tidak'),
                                                    ]),
                                            ])->compact()->collapsible(),
                                    ]),
                            ]),

                        Tabs\Tab::make('Pemeriksaan Umum')
                            ->icon('heroicon-o-clipboard-document-check')
                            ->schema([
                                RepeatableEntry::make('pemeriksaanUmums')
                                    ->label('')
                                    ->placeholder('Belum ada riwayat pemeriksaan umum')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                TextEntry::make('tanggal_pemeriksaan')
                                                    ->label('Tanggal Pemeriksaan')
                                                    ->date()
                                                    ->weight('bold'),
                                                TextEntry::make('dirujuk_ke_fasyankes')
                                                    ->label('Rujukan')
                                                    ->badge()
                                                    ->color(fn ($state) => $state === 'Y' ? 'danger' : 'success')
                                                    ->formatStateUsing(fn ($state) => $state === 'Y' ? 'Rujuk' : 'Tidak Rujuk'),
                                                TextEntry::make('keterangan_rujukan')
                                                    ->label('Keterangan Rujukan')
                                                    ->placeholder('-'),
                                            ]),
                                        Section::make('Tanda-Tanda Vital')
                                            ->schema([
                                                Grid::make(4)
                                                    ->schema([
                                                        TextEntry::make('tekanan_darah')->label('Tekanan Darah (mmHg)')->placeholder('-'),
                                                        TextEntry::make('denyut_nadi')->label('Denyut Nadi (bpm)')->placeholder('-'),
                                                        TextEntry::make('frekuensi_pernapasan')->label('Laju Napas (x/mnt)')->placeholder('-'),
                                                        TextEntry::make('suhu')->label('Suhu Tubuh (°C)')->placeholder('-'),
                                                    ]),
                                            ])->compact()->collapsible(),
                                        Section::make('Kondisi Rambut, Kuku, Telinga & Kebiasaan')
                                            ->schema([
                                                Grid::make(4)
                                                    ->schema([
                                                        TextEntry::make('keadaan_rambut')->label('Keadaan Rambut')->placeholder('-'),
                                                        TextEntry::make('kondisi_kuku')->label('Kondisi Kuku')->placeholder('-'),
                                                        TextEntry::make('telinga_luar')->label('Telinga Luar')->placeholder('-'),
                                                        TextEntry::make('sarapan')->label('Sarapan Pagi')->badge()->color(fn ($state) => $state === 'Y' ? 'success' : 'danger')->formatStateUsing(fn ($state) => $state === 'Y' ? 'Ya' : 'Tidak'),
                                                    ]),
                                            ])->compact()->collapsible(),
                                        Section::make('Kondisi Kulit & Gejala Klinis')
                                            ->schema([
                                                Grid::make(4)
                                                    ->schema([
                                                        TextEntry::make('bercak_keputihan')->label('Bercak Keputihan')->badge()->color(fn ($state) => $state === 'Y' ? 'danger' : 'success')->formatStateUsing(fn ($state) => $state === 'Y' ? 'Ya' : 'Tidak'),
                                                        TextEntry::make('bercak_putih_mati_rasa')->label('Bercak Putih Mati Rasa')->badge()->color(fn ($state) => $state === 'Y' ? 'danger' : 'success')->formatStateUsing(fn ($state) => $state === 'Y' ? 'Ya' : 'Tidak'),
                                                        TextEntry::make('kulit_bersisik')->label('Kulit Bersisik')->badge()->color(fn ($state) => $state === 'Y' ? 'danger' : 'success')->formatStateUsing(fn ($state) => $state === 'Y' ? 'Ya' : 'Tidak'),
                                                        TextEntry::make('kulit_ada_memar')->label('Kulit Memar')->badge()->color(fn ($state) => $state === 'Y' ? 'danger' : 'success')->formatStateUsing(fn ($state) => $state === 'Y' ? 'Ya' : 'Tidak'),
                                                        TextEntry::make('kulit_ada_luka_sayatan')->label('Luka Sayatan')->badge()->color(fn ($state) => $state === 'Y' ? 'danger' : 'success')->formatStateUsing(fn ($state) => $state === 'Y' ? 'Ya' : 'Tidak'),
                                                        TextEntry::make('kulit_ada_luka_koreng')->label('Luka Koreng')->badge()->color(fn ($state) => $state === 'Y' ? 'danger' : 'success')->formatStateUsing(fn ($state) => $state === 'Y' ? 'Ya' : 'Tidak'),
                                                        TextEntry::make('luka_koreng_sukar_sembuh')->label('Luka Koreng Sukar Sembuh')->badge()->color(fn ($state) => $state === 'Y' ? 'danger' : 'success')->formatStateUsing(fn ($state) => $state === 'Y' ? 'Ya' : 'Tidak'),
                                                        TextEntry::make('bekas_suntikan')->label('Bekas Suntikan')->badge()->color(fn ($state) => $state === 'Y' ? 'danger' : 'success')->formatStateUsing(fn ($state) => $state === 'Y' ? 'Ya' : 'Tidak'),
                                                    ]),
                                            ])->compact()->collapsible(),
                                        Section::make('Risiko Merokok')
                                            ->schema([
                                                Grid::make(5)
                                                    ->schema([
                                                        TextEntry::make('risiko_merokok')->label('Risiko Merokok')->placeholder('-'),
                                                        TextEntry::make('merokok_setahun')->label('Merokok Setahun')->placeholder('-'),
                                                        TextEntry::make('jenis_rokok')->label('Jenis Rokok')->placeholder('-'),
                                                        TextEntry::make('jumlah_rokok')->label('Jumlah Batang/Hari')->placeholder('-'),
                                                        TextEntry::make('lama_merokok')->label('Lama Merokok (thn)')->placeholder('-'),
                                                    ]),
                                            ])->compact()->collapsible(),
                                    ]),
                            ]),

                        Tabs\Tab::make('Pemeriksaan Gizi')
                            ->icon('heroicon-o-chart-bar')
                            ->schema([
                                RepeatableEntry::make('pemeriksaanGizis')
                                    ->label('')
                                    ->placeholder('Belum ada riwayat pemeriksaan gizi')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                TextEntry::make('tanggal_pemeriksaan')
                                                    ->label('Tanggal Pemeriksaan')
                                                    ->date()
                                                    ->weight('bold'),
                                                TextEntry::make('dirujuk_ke_fasyankes')
                                                    ->label('Rujukan')
                                                    ->badge()
                                                    ->color(fn ($state) => $state === 'Y' ? 'danger' : 'success')
                                                    ->formatStateUsing(fn ($state) => $state === 'Y' ? 'Rujuk' : 'Tidak Rujuk'),
                                                TextEntry::make('keterangan_rujukan')
                                                    ->label('Keterangan Rujukan')
                                                    ->placeholder('-'),
                                            ]),
                                        Section::make('Antropometri & Status Gizi')
                                            ->schema([
                                                Grid::make(4)
                                                    ->schema([
                                                        TextEntry::make('berat_badan')->label('Berat Badan (kg)')->placeholder('-'),
                                                        TextEntry::make('tinggi_badan')->label('Tinggi Badan (cm)')->placeholder('-'),
                                                        TextEntry::make('imt')->label('Indeks Massa Tubuh (IMT)')->placeholder('-'),
                                                        TextEntry::make('status_gizi')->label('Status Gizi')->placeholder('-'),
                                                        TextEntry::make('lingkar_lengan')->label('Lingkar Lengan (cm)')->placeholder('-'),
                                                        TextEntry::make('lingkar_perut')->label('Lingkar Perut (cm)')->placeholder('-'),
                                                        TextEntry::make('tanda_klinis_anemia')->label('Klinis Anemia')->badge()->color(fn ($state) => $state === 'Y' ? 'danger' : 'success')->formatStateUsing(fn ($state) => $state === 'Y' ? 'Ya' : 'Tidak'),
                                                    ]),
                                            ])->compact()->collapsible(),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
