<?php

namespace App\Exports;

use App\Models\StudentClassHistory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

/**
 * Versi Queue dari RekapPemeriksaanExport.
 *
 * Digunakan saat data > 2.000 baris agar proses tidak memblokir HTTP request.
 * File disimpan ke disk 'public', URL dikirim via notifikasi Filament ke user.
 *
 * Usage (di Filament Action):
 *   Excel::queue(new RekapPemeriksaanQueuedExport($filters), $filename, 'public')
 *       ->chain([new NotifyUserExportReady($user, $filename)]);
 */
class RekapPemeriksaanQueuedExport implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithChunkReading,
    ShouldAutoSize,
    WithStyles,
    WithTitle,
    ShouldQueue   // ← Makes Maatwebsite process this via Laravel Queue
{
    use Exportable;

    protected array $filters;
    protected int $userId;
    protected int $rowNumber = 0;

    /**
     * Setiap job queue memproses 500 baris.
     * Untuk server dengan RAM terbatas, turunkan ke 200.
     */
    public function chunkSize(): int
    {
        return 500;
    }

    public function __construct(array $filters = [], int $userId = 0)
    {
        $this->filters = $filters;
        $this->userId  = $userId;
    }

    public function query(): Builder
    {
        $user = \App\Models\User::find($this->userId);

        $dateDari = $this->filters['tanggal_pemeriksaan_dari'] ?? null;
        $dateSampai = $this->filters['tanggal_pemeriksaan_sampai'] ?? null;

        $filterRelation = function ($q) use ($dateDari, $dateSampai) {
            if ($dateDari) {
                $q->whereDate('tanggal_pemeriksaan', '>=', $dateDari);
            }
            if ($dateSampai) {
                $q->whereDate('tanggal_pemeriksaan', '<=', $dateSampai);
            }
        };

        $query = StudentClassHistory::query()
            ->with([
                'student:id,nama_lengkap,nisn,nik,jenis_kelamin,tempat_lahir,tanggal_lahir,alamat,nama_orang_tua,no_hp_orang_tua',
                'school:id,nama_sekolah',
                'schoolClass:id,nama_kelas',
                'academicYear:id,nama',
                'pemeriksaanUmum' => $filterRelation,
                'pemeriksaanGizi' => $filterRelation,
                'pemeriksaanGigi' => $filterRelation,
                'pemeriksaanMata' => $filterRelation,
                'pemeriksaanTelinga' => $filterRelation,
            ])
            ->where('aktif', true)
            ->orderBy('school_id')
            ->orderBy('school_class_id');

        if ($user) {
            if ($user->hasRole('admin_sekolah')) {
                $query->where('school_id', $user->school_id);
            } elseif ($user->hasRole('admin_instansi') || $user->hasRole('petugas_pemeriksaan')) {
                $query->whereHas('school', fn ($q) => $q->where('instansi_id', $user->instansi_id));
            } elseif (! $user->hasAnyRole(['super_admin', 'admin_dinkes'])) {
                $query->whereRaw('1 = 0');
            }
        }

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
        if (! empty($this->filters['tanggal_pemeriksaan_dari'])) {
            $date = $this->filters['tanggal_pemeriksaan_dari'];
            $query->where(function ($sub) use ($date) {
                $sub->whereHas('pemeriksaanUmum', fn ($p) => $p->whereDate('tanggal_pemeriksaan', '>=', $date))
                    ->orWhereHas('pemeriksaanGigi', fn ($p) => $p->whereDate('tanggal_pemeriksaan', '>=', $date))
                    ->orWhereHas('pemeriksaanGizi', fn ($p) => $p->whereDate('tanggal_pemeriksaan', '>=', $date))
                    ->orWhereHas('pemeriksaanMata', fn ($p) => $p->whereDate('tanggal_pemeriksaan', '>=', $date));
            });
        }
        if (! empty($this->filters['tanggal_pemeriksaan_sampai'])) {
            $date = $this->filters['tanggal_pemeriksaan_sampai'];
            $query->where(function ($sub) use ($date) {
                $sub->whereHas('pemeriksaanUmum', fn ($p) => $p->whereDate('tanggal_pemeriksaan', '<=', $date))
                    ->orWhereHas('pemeriksaanGigi', fn ($p) => $p->whereDate('tanggal_pemeriksaan', '<=', $date))
                    ->orWhereHas('pemeriksaanGizi', fn ($p) => $p->whereDate('tanggal_pemeriksaan', '<=', $date))
                    ->orWhereHas('pemeriksaanMata', fn ($p) => $p->whereDate('tanggal_pemeriksaan', '<=', $date));
            });
        }

        return $query;
    }

    public function headings(): array
    {
        return (new RekapPemeriksaanExport())->headings();
    }

    public function map($record): array
    {
        $this->rowNumber++;
        return (new RekapPemeriksaanExport($this->filters))->map($record);
    }

    public function styles(Worksheet $sheet): array
    {
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
