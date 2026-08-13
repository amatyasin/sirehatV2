<?php

namespace App\Exports;

use App\Models\StudentClassHistory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class RekapPemeriksaanExport implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithChunkReading,
    ShouldAutoSize,
    WithStyles,
    WithTitle
{
    protected array $filters;
    protected int $rowNumber = 0;

    /**
     * Chunk size: jumlah record per batch.
     * 500 adalah sweet-spot untuk 44 kolom + eager load relasi berat.
     * Turunkan ke 200 jika memory masih ketat.
     */
    public function chunkSize(): int
    {
        return 500;
    }

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    // =========================================================================
    // QUERY — satu JOIN besar ke semua tabel pemeriksaan via eager load
    // =========================================================================

    public function query(): Builder
    {
        $user = auth()->user();

        /*
         * STRATEGI: Query pivot dari StudentClassHistory (bukan dari
         * masing-masing tabel pemeriksaan) agar 1 baris = 1 siswa per
         * semester, lalu eager-load semua pemeriksaan sekaligus.
         *
         * Ini menghilangkan N+1 query sepenuhnya.
         */
        $query = StudentClassHistory::query()
            ->with([
                // Core identitas — hanya select kolom yang diperlukan
                'student:id,nama_lengkap,nisn,nik,jenis_kelamin,tempat_lahir,tanggal_lahir,alamat,nama_orang_tua,no_hp_orang_tua',
                'school:id,nama_sekolah',
                'schoolClass:id,nama_kelas',
                'academicYear:id,nama',

                // Pemeriksaan — HasOne (1 per semester, pakai unique constraint)
                'pemeriksaanUmum:student_class_history_id,tanggal_pemeriksaan,tekanan_darah,denyut_nadi,frekuensi_pernapasan,suhu,keadaan_rambut,kondisi_kuku,telinga_luar,sarapan,bercak_keputihan,bercak_putih_mati_rasa,kulit_bersisik,risiko_merokok,dirujuk_ke_fasyankes',
                'pemeriksaanGigi:student_class_history_id,tanggal_pemeriksaan,celah_bibir_langit,luka_sudut_mulut,sariawan,gigi_berlubang,jumlah_gigi_berlubang,gusi_berdarah,gusi_bengkak,gigi_kotor_plak,karang_gigi,susunan_gigi_tidak_teratur,dirujuk_ke_fasyankes',
                'pemeriksaanMata:student_class_history_id,tanggal_pemeriksaan,visus_kanan,visus_kiri,pakai_kacamata,buta_warna,mata_merah,mata_berair,nyeri_mata,dirujuk_ke_fasyankes',
                'pemeriksaanGizi:student_class_history_id,tanggal_pemeriksaan,berat_badan,tinggi_badan,imt,status_gizi,hemoglobin,status_anemia,gula_darah_sewaktu,status_gula,dirujuk_ke_fasyankes',

                // Telinga (HasOne)
                'pemeriksaanTelinga:student_class_history_id,tanggal_pemeriksaan,telinga_luar_kanan,telinga_luar_kiri,gangguan_pendengaran_kanan,gangguan_pendengaran_kiri,serumen_kanan,serumen_kiri,dirujuk_ke_fasyankes',
            ])
            ->where('aktif', true)
            ->orderBy('school_id')
            ->orderBy('school_class_id');

        // ── Role scoping ──────────────────────────────────────────────────────
        if ($user->hasRole('admin_sekolah')) {
            $query->where('school_id', $user->school_id);
        } elseif ($user->hasRole('admin_instansi') || $user->hasRole('petugas_pemeriksaan')) {
            $query->whereHas('school', fn ($q) => $q->where('instansi_id', $user->instansi_id));
        } elseif (! $user->hasAnyRole(['super_admin', 'admin_dinkes'])) {
            $query->whereRaw('1 = 0'); // no access
        }

        // ── Filters ───────────────────────────────────────────────────────────
        if (! empty($this->filters['school_id'])) {
            $query->where('school_id', $this->filters['school_id']);
        }

        if (! empty($this->filters['school_class_id'])) {
            $query->where('school_class_id', $this->filters['school_class_id']);
        }

        if (! empty($this->filters['academic_year_id'])) {
            $query->where('academic_year_id', $this->filters['academic_year_id']);
        }

        if (! empty($this->filters['semester'])) {
            $query->where('semester', $this->filters['semester']);
        }

        if (! empty($this->filters['jenis_kelamin'])) {
            $query->whereHas('student', fn ($q) => $q->where('jenis_kelamin', $this->filters['jenis_kelamin']));
        }

        // Filter: hanya yang sudah diperiksa salah satu jenis
        if (! empty($this->filters['hanya_sudah_diperiksa'])) {
            $query->where(function ($q) {
                $q->whereHas('pemeriksaanUmum')
                    ->orWhereHas('pemeriksaanGigi')
                    ->orWhereHas('pemeriksaanMata')
                    ->orWhereHas('pemeriksaanGizi')
                    ->orWhereHas('pemeriksaanTelinga');
            });
        }

        return $query;
    }

    // =========================================================================
    // HEADINGS — 44 kolom
    // =========================================================================

    public function headings(): array
    {
        return [
            // [A] Identitas
            'No',
            'Nama Siswa',
            'NISN',
            'NIK',
            'Jenis Kelamin',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Alamat',
            'Nama Orang Tua',
            'No HP Orang Tua',

            // [B] Akademik
            'Sekolah',
            'Kelas',
            'Tahun Ajaran',
            'Semester',

            // [C] Pemeriksaan Umum (11 kolom)
            'Tgl Periksa Umum',
            'Tekanan Darah',
            'Denyut Nadi',
            'Frekuensi Napas',
            'Suhu Tubuh (°C)',
            'Keadaan Rambut',
            'Kondisi Kuku',
            'Sarapan Pagi',
            'Bercak Keputihan',
            'Risiko Merokok',
            'Rujuk (Umum)',

            // [D] Pemeriksaan Gigi (7 kolom)
            'Tgl Periksa Gigi',
            'Celah Bibir/Langit',
            'Gigi Berlubang',
            'Jml Lubang',
            'Gusi Berdarah',
            'Karang Gigi',
            'Rujuk (Gigi)',

            // [E] Pemeriksaan Mata (7 kolom)
            'Tgl Periksa Mata',
            'Visus Kanan',
            'Visus Kiri',
            'Pakai Kacamata',
            'Buta Warna',
            'Mata Merah',
            'Rujuk (Mata)',

            // [F] Pemeriksaan Gizi (7 kolom)
            'Tgl Periksa Gizi',
            'Berat Badan (kg)',
            'Tinggi Badan (cm)',
            'IMT',
            'Status Gizi',
            'HB (g/dL)',
            'Status Anemia',
            'Rujuk (Gizi)',

            // [G] Pemeriksaan Telinga (4 kolom)
            'Tgl Periksa Telinga',
            'Gg Pendengaran Kanan',
            'Gg Pendengaran Kiri',
            'Rujuk (Telinga)',
        ];
    }

    // =========================================================================
    // MAPPING — transformasi tiap record menjadi baris Excel
    // =========================================================================

    public function map($record): array
    {
        $this->rowNumber++;

        $yn    = fn ($v) => $v === 'Y' ? 'Ya' : ($v === 'N' ? 'Tidak' : '-');
        $safe  = fn ($v) => $v ?? '-';
        $date  = fn ($v) => $v?->format('d/m/Y') ?? '-';

        $umum   = $record->pemeriksaanUmum;
        $gigi   = $record->pemeriksaanGigi;
        $mata   = $record->pemeriksaanMata;
        $gizi   = $record->pemeriksaanGizi;
        $telinga = $record->pemeriksaanTelinga;

        $siswa  = $record->student;

        return [
            // [A] Identitas
            $this->rowNumber,
            $safe($siswa?->nama_lengkap),
            $safe($siswa?->nisn),
            $safe($siswa?->nik),
            $siswa?->jenis_kelamin === 'L' ? 'Laki-laki' : ($siswa?->jenis_kelamin === 'P' ? 'Perempuan' : '-'),
            $safe($siswa?->tempat_lahir),
            $date($siswa?->tanggal_lahir),
            $safe($siswa?->alamat),
            $safe($siswa?->nama_orang_tua),
            $safe($siswa?->no_hp_orang_tua),

            // [B] Akademik
            $safe($record->school?->nama_sekolah),
            $safe($record->schoolClass?->nama_kelas),
            $safe($record->academicYear?->nama),
            $safe($record->semester),

            // [C] Pemeriksaan Umum
            $date($umum?->tanggal_pemeriksaan),
            $safe($umum?->tekanan_darah),
            $safe($umum?->denyut_nadi),
            $safe($umum?->frekuensi_pernapasan),
            $safe($umum?->suhu),
            $safe($umum?->keadaan_rambut),
            $safe($umum?->kondisi_kuku),
            $yn($umum?->sarapan),
            $yn($umum?->bercak_keputihan),
            $safe($umum?->risiko_merokok),
            $yn($umum?->dirujuk_ke_fasyankes),

            // [D] Pemeriksaan Gigi
            $date($gigi?->tanggal_pemeriksaan),
            $yn($gigi?->celah_bibir_langit),
            $yn($gigi?->gigi_berlubang),
            $safe($gigi?->jumlah_gigi_berlubang),
            $yn($gigi?->gusi_berdarah),
            $yn($gigi?->karang_gigi),
            $yn($gigi?->dirujuk_ke_fasyankes),

            // [E] Pemeriksaan Mata
            $date($mata?->tanggal_pemeriksaan),
            $safe($mata?->visus_kanan),
            $safe($mata?->visus_kiri),
            $yn($mata?->pakai_kacamata),
            $yn($mata?->buta_warna),
            $yn($mata?->mata_merah),
            $yn($mata?->dirujuk_ke_fasyankes),

            // [F] Pemeriksaan Gizi
            $date($gizi?->tanggal_pemeriksaan),
            $safe($gizi?->berat_badan),
            $safe($gizi?->tinggi_badan),
            $safe($gizi?->imt),
            $safe($gizi?->status_gizi),
            $safe($gizi?->hemoglobin),
            $safe($gizi?->status_anemia),
            $yn($gizi?->dirujuk_ke_fasyankes),

            // [G] Pemeriksaan Telinga
            $date($telinga?->tanggal_pemeriksaan),
            $safe($telinga?->gangguan_pendengaran_kanan),
            $safe($telinga?->gangguan_pendengaran_kiri),
            $yn($telinga?->dirujuk_ke_fasyankes),
        ];
    }

    // =========================================================================
    // STYLES — header row bold + freeze pane
    // =========================================================================

    public function styles(Worksheet $sheet): array
    {
        // Freeze baris header + 2 kolom pertama agar scroll tetap terlihat
        $sheet->freezePane('C2');

        return [
            // Row 1 = header: bold, background biru gelap, teks putih
            1 => [
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 10],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1E3A5F']],
                'alignment' => ['horizontal' => 'center', 'wrapText' => true],
            ],
        ];
    }

    public function title(): string
    {
        return 'Rekap Pemeriksaan Siswa';
    }
}
