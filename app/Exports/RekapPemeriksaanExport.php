<?php

namespace App\Exports;

use App\Models\StudentClassHistory;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

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

    public function chunkSize(): int
    {
        return 500;
    }

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    // =========================================================================
    // QUERY — pivot StudentClassHistory dengan eager loading seluruh pemeriksaan
    // =========================================================================

    public function query(): Builder
    {
        $user = auth()->user();

        $query = StudentClassHistory::query()
            ->with([
                'student',
                'school:id,nama_sekolah',
                'schoolClass:id,nama_kelas',
                'academicYear:id,nama',
                'pemeriksaanUmum',
                'pemeriksaanGizi',
                'pemeriksaanGigi',
                'pemeriksaanMata',
            ])
            ->where('aktif', true)
            ->orderBy('school_id')
            ->orderBy('school_class_id');

        // ── Role Scoping ──────────────────────────────────────────────────────
        if ($user) {
            if ($user->hasRole('admin_sekolah')) {
                $query->where('school_id', $user->school_id);
            } elseif ($user->hasRole('admin_instansi') || $user->hasRole('petugas_pemeriksaan')) {
                $query->whereHas('school', fn ($q) => $q->where('instansi_id', $user->instansi_id));
            } elseif (! $user->hasAnyRole(['super_admin', 'admin_dinkes'])) {
                $query->whereRaw('1 = 0'); // no access
            }
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

        if (! empty($this->filters['hanya_sudah_diperiksa'])) {
            $query->where(function ($q) {
                $q->whereHas('pemeriksaanUmum')
                    ->orWhereHas('pemeriksaanGigi')
                    ->orWhereHas('pemeriksaanMata')
                    ->orWhereHas('pemeriksaanGizi');
            });
        }

        return $query;
    }

    // =========================================================================
    // HEADINGS — Total 71 Kolom (Persis seperti susunan export lama)
    // =========================================================================

    public function headings(): array
    {
        return [
            // ── A. Data Siswa (11 kolom) ─────────────────────────────────────
            'Nama Lengkap',
            'Jenis Kelamin',
            'Tanggal Lahir',
            'Tempat Lahir',
            'Nama Orang Tua',
            'NIK Orang Tua',
            'No HP Orang Tua',
            'NIK',
            'NISN',
            'Kelas',
            'Nama Sekolah',

            // ── B. Pemeriksaan Gizi (9 kolom) ────────────────────────────────
            'Waktu Pemeriksaan Gizi',
            'Berat Badan',
            'Tinggi Badan',
            'IMT',
            'Deskripsi IMT',
            'TB/U',
            'Tanda Klinis Anemia',
            'Dirujuk Gizi',
            'Keterangan Rujukan Gizi',

            // ── C. Pemeriksaan Umum (24 kolom) ───────────────────────────────
            'Waktu Pemeriksaan Umum',
            'Tekanan Darah',
            'Denyut Nadi',
            'Frekuensi Pernapasan',
            'Suhu',
            'Bising Jantung',
            'Bising Paru',
            'Keadaan Rambut',
            'Bercak Keputihan',
            'Bercak Putih Mati Rasa',
            'Kulit Bersisik',
            'Kulit Ada Memar',
            'Kulit Ada Luka Sayatan',
            'Kulit Ada Luka Koreng',
            'Luka Koreng Sukar Sembuh',
            'Bekas Suntikan',
            'Risiko Merokok',
            'Menstruasi',
            'Keputihan',
            'Telinga Luar',
            'Sarapan',
            'Kondisi Kuku',
            'Dirujuk Umum',
            'Keterangan Rujukan Umum',

            // ── D. Pemeriksaan Mata (7 kolom) ────────────────────────────────
            'Waktu Pemeriksaan Mata',
            'Visus Kanan',
            'Visus Kiri',
            'Berkacamata',
            'Buta Warna',
            'Dirujuk Mata',
            'Keterangan Rujukan Mata',

            // ── E. Pemeriksaan Gigi, Mulut & Alat Bantu (20 kolom) ───────────
            'Waktu Pemeriksaan Gigi',
            'Celah Bibir/Langit',
            'Luka Sudut Mulut',
            'Sariawan',
            'Lidah Kotor',
            'Luka Lain di Mulut',
            'Gigi Berlubang',
            'Jumlah Gigi Berlubang',
            'Gusi Berdarah',
            'Gusi Bengkak',
            'Gigi Kotor/Plak',
            'Karang Gigi',
            'Susunan Gigi Tidak Teratur',
            'Penglihatan Loupe',
            'Pendengaran',
            'Kursi Roda',
            'Tongkat/Kruk',
            'Kaki/Tangan/Mata Protese',
            'Dirujuk Gigi',
            'Keterangan Rujukan Gigi',
        ];
    }

    // =========================================================================
    // MAPPING — Transformasi data per baris
    // =========================================================================

    public function map($record): array
    {
        $safe = fn ($v) => ($v !== null && $v !== '') ? $v : '-';

        $yn = function ($v) {
            if ($v === null || $v === '') {
                return '-';
            }
            if ($v === 'Y' || $v === true || $v === 1) {
                return 'Ya';
            }
            if ($v === 'N' || $v === false || $v === 0) {
                return 'Tidak';
            }

            return (string) $v;
        };

        $fmtDate = function ($v, string $format = 'd/m/Y H:i') {
            if (empty($v)) {
                return '-';
            }
            if ($v instanceof \Carbon\CarbonInterface || $v instanceof \DateTimeInterface) {
                return $v->format($format);
            }

            try {
                return \Carbon\Carbon::parse($v)->format($format);
            } catch (\Throwable $e) {
                return (string) $v;
            }
        };

        $fmtTelingaLuar = function ($val) {
            if (empty($val)) {
                return '-';
            }

            return match (strtolower(trim((string) $val))) {
                'sehat'   => 'Sehat',
                'serumen' => 'Serumen',
                'infeksi' => 'Infeksi',
                default   => ucfirst((string) $val),
            };
        };

        $siswa   = $record->student;
        $gizi    = $record->pemeriksaanGizi;
        $umum    = $record->pemeriksaanUmum;
        $mata    = $record->pemeriksaanMata;
        $gigi    = $record->pemeriksaanGigi;

        // ── Data Siswa ───────────────────────────────────────────────────────
        $genderRaw = strtoupper(trim((string) ($siswa?->jenis_kelamin ?? '')));
        $jenisKelamin = match ($genderRaw) {
            'L', 'LAKI-LAKI' => 'Laki-laki',
            'P', 'PEREMPUAN' => 'Perempuan',
            default          => '-',
        };

        $nikOrtu = $siswa?->nik_orang_tua ? "'" . trim((string) $siswa->nik_orang_tua) : '-';
        $nik     = $siswa?->nik ? "'" . trim((string) $siswa->nik) : '-';
        $nisn    = $siswa?->nisn ? "'" . trim((string) $siswa->nisn) : '-';

        // ── Pemeriksaan Umum & Gender Screening ──────────────────────────────
        $isFemale   = in_array($genderRaw, ['P', 'PEREMPUAN'], true);
        $menstruasi = $isFemale ? $yn($umum?->sudah_menstruasi) : '-';
        $keputihan  = $isFemale ? $yn($umum?->mengalami_keputihan) : '-';

        // ── Rujukan Helper ────────────────────────────────────────────────────
        $dirujukGizi = $yn($gizi?->dirujuk_ke_fasyankes);
        $rujukanGizi = ($gizi?->dirujuk_ke_fasyankes === 'Y') ? $safe($gizi?->keterangan_rujukan) : '-';

        $dirujukUmum = $yn($umum?->dirujuk_ke_fasyankes);
        $rujukanUmum = ($umum?->dirujuk_ke_fasyankes === 'Y') ? $safe($umum?->keterangan_rujukan) : '-';

        $dirujukMata = $yn($mata?->dirujuk_ke_fasyankes);
        $rujukanMata = ($mata?->dirujuk_ke_fasyankes === 'Y') ? $safe($mata?->keterangan_rujukan) : '-';

        $dirujukGigi = $yn($gigi?->dirujuk_ke_fasyankes);
        $rujukanGigi = ($gigi?->dirujuk_ke_fasyankes === 'Y') ? $safe($gigi?->keterangan_rujukan) : '-';

        return [
            // ── A. DATA SISWA (11 kolom) ─────────────────────────────────────
            $safe($siswa?->nama_lengkap),
            $jenisKelamin,
            $siswa?->tanggal_lahir ? $siswa->tanggal_lahir->format('d/m/Y') : '-',
            $safe($siswa?->tempat_lahir),
            $safe($siswa?->nama_orang_tua),
            $nikOrtu,
            $safe($siswa?->no_hp_orang_tua),
            $nik,
            $nisn,
            $safe($record->schoolClass?->nama_kelas),
            $safe($record->school?->nama_sekolah),

            // ── B. PEMERIKSAAN GIZI (9 kolom) ────────────────────────────────
            $fmtDate($gizi?->tanggal_pemeriksaan ?? $gizi?->created_at),
            $safe($gizi?->berat_badan),
            $safe($gizi?->tinggi_badan),
            $safe($gizi?->imt),
            $safe($gizi?->deskripsi_imt ?? $gizi?->status_gizi),
            $safe($gizi?->tb_u),
            $yn($gizi?->tanda_klinis_anemia),
            $dirujukGizi,
            $rujukanGizi,

            // ── C. PEMERIKSAAN UMUM (24 kolom) ───────────────────────────────
            $fmtDate($umum?->tanggal_pemeriksaan ?? $umum?->created_at),
            $safe($umum?->tekanan_darah),
            $safe($umum?->denyut_nadi),
            $safe($umum?->frekuensi_pernapasan),
            $safe($umum?->suhu),
            $yn($umum?->bising_jantung),
            $yn($umum?->bising_paru),
            $safe($umum?->keadaan_rambut),
            $yn($umum?->bercak_keputihan),
            $yn($umum?->bercak_putih_mati_rasa),
            $yn($umum?->kulit_bersisik),
            $yn($umum?->kulit_ada_memar),
            $yn($umum?->kulit_ada_luka_sayatan),
            $yn($umum?->kulit_ada_luka_koreng),
            $yn($umum?->luka_koreng_sukar_sembuh),
            $yn($umum?->bekas_suntikan),
            $yn($umum?->risiko_merokok),
            $menstruasi,
            $keputihan,
            $fmtTelingaLuar($umum?->telinga_luar),
            $yn($umum?->sarapan),
            $safe($umum?->kondisi_kuku),
            $dirujukUmum,
            $rujukanUmum,

            // ── D. PEMERIKSAAN MATA (7 kolom) ────────────────────────────────
            $fmtDate($mata?->tanggal_pemeriksaan ?? $mata?->created_at),
            $safe($mata?->visus_kanan),
            $safe($mata?->visus_kiri),
            $yn($mata?->pakai_kacamata ?? $mata?->berkacamata),
            $yn($mata?->buta_warna),
            $dirujukMata,
            $rujukanMata,

            // ── E. PEMERIKSAAN GIGI, MULUT & ALAT BANTU (20 kolom) ───────────
            $fmtDate($gigi?->tanggal_pemeriksaan ?? $gigi?->created_at),
            $yn($gigi?->celah_bibir_langit),
            $yn($gigi?->luka_sudut_mulut),
            $yn($gigi?->sariawan),
            $yn($gigi?->lidah_kotor),
            $yn($gigi?->luka_lain_di_mulut),
            $yn($gigi?->gigi_berlubang),
            $safe($gigi?->jumlah_gigi_berlubang),
            $yn($gigi?->gusi_berdarah),
            $yn($gigi?->gusi_bengkak),
            $yn($gigi?->gigi_kotor_plak),
            $yn($gigi?->karang_gigi),
            $yn($gigi?->susunan_gigi_tidak_teratur),
            $yn($gigi?->penglihatan_loupe),
            $yn($gigi?->pendengaran),
            $yn($gigi?->kursi_roda),
            $yn($gigi?->tongkat_kruk),
            $yn($gigi?->kaki_tangan_mata_protese),
            $dirujukGigi,
            $rujukanGigi,
        ];
    }

    // =========================================================================
    // STYLES — Freeze pane, Header styling, dan Text Format untuk NIK / NISN
    // =========================================================================

    public function styles(Worksheet $sheet): array
    {
        // Explicitly set Text format (@) for NIK Ortu (F), NIK (H), and NISN (I)
        $sheet->getStyle('F:F')->getNumberFormat()->setFormatCode('@');
        $sheet->getStyle('H:H')->getNumberFormat()->setFormatCode('@');
        $sheet->getStyle('I:I')->getNumberFormat()->setFormatCode('@');

        // Freeze baris header + 2 kolom pertama
        $sheet->freezePane('C2');

        return [
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
