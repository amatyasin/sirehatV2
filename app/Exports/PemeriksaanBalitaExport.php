<?php

namespace App\Exports;

use App\Models\PemeriksaanBalita;
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

class PemeriksaanBalitaExport implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithChunkReading,
    ShouldAutoSize,
    WithStyles,
    WithTitle
{
    protected array $filters;

    protected ?array $recordIds;

    public function chunkSize(): int
    {
        return 500;
    }

    public function __construct(array $filters = [], ?array $recordIds = null)
    {
        $this->filters = $filters;
        $this->recordIds = $recordIds;
    }

    public function query(): Builder
    {
        $user = auth()->user();

        $query = PemeriksaanBalita::query()
            ->with([
                'child',
                'child.posyandu',
                'child.posyandu.instansi',
                'child.orangTua',
                'posyandu',
            ])
            ->orderBy('tanggal_pemeriksaan', 'desc');

        if ($this->recordIds !== null) {
            $query->whereIn('id', $this->recordIds);
        }

        if ($user) {
            if ($user->hasRole('admin_instansi')) {
                $query->whereHas('child.posyandu', fn ($q) => $q->where('instansi_id', $user->instansi_id));
            } elseif ($user->hasRole('petugas_posyandu')) {
                $query->whereHas('child', fn ($q) => $q->where('posyandu_id', $user->posyandu_id));
            } elseif (! $user->hasAnyRole(['super_admin', 'admin_dinkes'])) {
                $query->whereRaw('1 = 0');
            }
        }

        if (! empty($this->filters['posyandu'])) {
            $query->whereHas('child', fn ($q) => $q->where('posyandu_id', $this->filters['posyandu']));
        }

        if (! empty($this->filters['jenis_kelamin'])) {
            $query->whereHas('child', fn ($q) => $q->where('jenis_kelamin', $this->filters['jenis_kelamin']));
        }

        if (! empty($this->filters['status_stunting'])) {
            $query->where('status_stunting', $this->filters['status_stunting']);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            // ── Identitas Anak & Posyandu ──────────────────────────────────────
            'Puskesmas',
            'Posyandu',
            'Nama Anak',
            'NIK Anak',
            'Jenis Kelamin',
            'Tanggal Lahir',
            'Umur (Bulan)',
            'Umur saat Pemeriksaan',
            'Nama Orang Tua',
            'NIK Orang Tua',
            'No HP Orang Tua',
            'Alamat',

            // ── Pengukuran & Z-Score ──────────────────────────────────────────
            'Tanggal Pemeriksaan',
            'Berat Badan (kg)',
            'Tinggi Badan (cm)',
            'Lingkar Kepala (cm)',
            'Lingkar Lengan (cm)',
            'IMT',
            'Z-Score BB/U',
            'Z-Score TB/U',
            'Z-Score BB/TB',
            'Z-Score IMT/U',
            'Z-Score Lingkar Kepala',

            // ── Status Gizi & Stunting ─────────────────────────────────────────
            'Status BB/U',
            'Status TB/U',
            'Status BB/TB',
            'Status IMT/U',
            'Status Lingkar Kepala',
            'Status Stunting',

            // ── Skrining & Tumbuh Kembang ──────────────────────────────────────
            'Disabilitas',
            'Riwayat Diabetes Orang Tua',
            'Makan Banyak Makanan Manis',
            'Makan Pagi Sudah Banyak',
            'Mengalami Penurunan BB',
            'Riwayat Kencing Manis',
            'Indikasi GPPH',
            'Hasil GPPH',
            'Indikasi KMPE',
            'Hasil KMPE',
            'Hasil KPSP',
            'Hasil Perilaku',
            'Hasil Tes Daya Dengar',
            'Hasil Tes Daya Lihat',
            'Pemeriksaan Mata',
            'Serumen Impaksi',
            'Infeksi Telinga',
            'Jumlah Gigi Karies',

            // ── Skrining TB & Penyakit Tropis ──────────────────────────────────
            'TB Batuk',
            'TB BB Turun',
            'TB Demam',
            'TB Lesu',
            'TB Kelenjar',
            'TB Rontgen',
            'TB Kontak',
            'TB Metode',
            'Hasil Pemeriksaan TB',
            'Hasil Frambusia',
            'Hasil Kusta',
            'Hasil Skabies',

            // ── Rujukan & Catatan ─────────────────────────────────────────────
            'Dirujuk ke Fasyankes',
            'Keterangan Rujukan',
            'Catatan',
        ];
    }

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

        $fmtDate = function ($v, string $format = 'd/m/Y') {
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

        $child = $record->child;
        $ortu = $child?->orangTua;
        $posyandu = $child?->posyandu ?? $record->posyandu;
        $instansi = $posyandu?->instansi;

        $genderRaw = strtoupper(trim((string) ($child?->jenis_kelamin ?? '')));
        $jenisKelamin = match ($genderRaw) {
            'L', 'LAKI-LAKI' => 'Laki-laki',
            'P', 'PEREMPUAN' => 'Perempuan',
            default => '-',
        };

        $nikChild = $child?->nik ? "'".trim((string) $child->nik) : '-';
        $nikOrtu = $ortu?->nik ? "'".trim((string) $ortu->nik) : '-';

        return [
            // ── Identitas Anak & Posyandu ──────────────────────────────────────
            $safe($instansi?->nama_instansi),
            $safe($posyandu?->nama_posyandu),
            $safe($child?->nama_lengkap),
            $nikChild,
            $jenisKelamin,
            $fmtDate($child?->tanggal_lahir),
            $safe($record->umur_bulan),
            $safe($record->umur_saat_pemeriksaan),
            $safe($ortu?->nama_lengkap),
            $nikOrtu,
            $safe($ortu?->no_hp),
            $safe($child?->alamat),

            // ── Pengukuran & Z-Score ──────────────────────────────────────────
            $fmtDate($record->tanggal_pemeriksaan),
            $safe($record->berat_badan),
            $safe($record->tinggi_badan),
            $safe($record->lingkar_kepala),
            $safe($record->lingkar_lengan),
            $safe($record->imt),
            $safe($record->zscore_bb_u),
            $safe($record->zscore_tb_u),
            $safe($record->zscore_bb_tb),
            $safe($record->zscore_imt_u),
            $safe($record->zscore_lingkar_kepala),

            // ── Status Gizi & Stunting ─────────────────────────────────────────
            $safe($record->status_bb_u),
            $safe($record->status_tb_u),
            $safe($record->status_bb_tb),
            $safe($record->status_imt_u),
            $safe($record->status_lingkar_kepala),
            $safe($record->status_stunting),

            // ── Skrining & Tumbuh Kembang ──────────────────────────────────────
            $yn($record->disabilitas),
            $yn($record->riwayat_diabetes_orangtua),
            $yn($record->makan_banyak_makanan_manis),
            $yn($record->makan_pagi_sudah_banyak),
            $yn($record->mengalami_penurunan_berat_badan),
            $yn($record->riwayat_kencing_manis),
            $yn($record->indikasi_gpph),
            $safe($record->hasil_gpph),
            $yn($record->indikasi_kmpe),
            $safe($record->hasil_kmpe),
            $safe($record->hasil_kpsp),
            $safe($record->hasil_perilaku),
            $safe($record->hasil_tes_daya_dengar),
            $safe($record->hasil_pemeriksaan_tes_daya_lihat),
            $safe($record->pemeriksaan_mata),
            $safe($record->serumen_impaksi),
            $safe($record->infeksi_telinga),
            $safe($record->jumlah_gigi_karies),

            // ── Skrining TB & Penyakit Tropis ──────────────────────────────────
            $safe($record->tb_batuk),
            $yn($record->tb_bb_turun),
            $yn($record->tb_demam),
            $yn($record->tb_lesu),
            $yn($record->tb_kelenjar),
            $yn($record->tb_rontgen),
            $safe($record->tb_kontak),
            $safe($record->tb_metode),
            $safe($record->hasil_pemeriksaan_tb),
            $safe($record->hasil_frambusia),
            $safe($record->hasil_kusta),
            $safe($record->hasil_skabies),

            // ── Rujukan & Catatan ─────────────────────────────────────────────
            $yn($record->dirujuk_ke_fasyankes),
            $safe($record->keterangan_rujukan),
            $safe($record->catatan),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->getStyle('D:D')->getNumberFormat()->setFormatCode('@');
        $sheet->getStyle('J:J')->getNumberFormat()->setFormatCode('@');
        $sheet->freezePane('D2');

        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 10],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1E3A5F']],
                'alignment' => ['horizontal' => 'center', 'wrapText' => true],
            ],
        ];
    }

    public function title(): string
    {
        return 'Pemeriksaan Balita & Apras';
    }
}
