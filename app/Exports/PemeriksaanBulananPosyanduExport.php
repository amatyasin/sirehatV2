<?php

namespace App\Exports;

use App\Models\PosyanduMonthlyParticipant;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PemeriksaanBulananPosyanduExport implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithChunkReading,
    ShouldAutoSize,
    WithStyles,
    WithTitle
{
    protected ?int $examinationId;

    protected array $filters;

    protected ?array $recordIds;

    protected int $rowNumber = 1;

    protected string $posyanduName = 'SEMUA POSYANDU';

    protected string $periodLabel = '-';

    public function chunkSize(): int
    {
        return 500;
    }

    public function __construct(?int $examinationId = null, array $filters = [], ?array $recordIds = null)
    {
        $this->examinationId = $examinationId;
        $this->filters = $filters;
        $this->recordIds = $recordIds;

        if ($examinationId) {
            $exam = \App\Models\PosyanduMonthlyExamination::with('posyandu')->find($examinationId);
            if ($exam) {
                $this->posyanduName = $exam->posyandu?->nama_posyandu ?? 'POSYANDU';
                $this->periodLabel = sprintf('%02d/%d', $exam->month, $exam->year);
            }
        } elseif (! empty($filters['posyandu_id'])) {
            $posyandu = \App\Models\Posyandu::find($filters['posyandu_id']);
            if ($posyandu) {
                $this->posyanduName = $posyandu->nama_posyandu;
            }
        }
    }

    public function query(): Builder
    {
        $user = auth()->user();

        $query = PosyanduMonthlyParticipant::query()
            ->with([
                'examination',
                'examination.posyandu',
                'child',
                'child.orangTua',
                'orangTua',
            ])
            ->orderBy('created_at', 'desc');

        if ($this->examinationId !== null) {
            $query->where('posyandu_monthly_examination_id', $this->examinationId);
        }

        if ($this->recordIds !== null) {
            $query->whereIn('id', $this->recordIds);
        }

        if ($user) {
            if ($user->hasRole('admin_instansi')) {
                $query->whereHas('examination.posyandu', fn ($q) => $q->where('instansi_id', $user->instansi_id));
            } elseif ($user->hasRole('petugas_posyandu')) {
                $query->whereHas('examination', fn ($q) => $q->where('posyandu_id', $user->posyandu_id));
            } elseif ($user->hasRole('admin_kecamatan')) {
                $query->whereHas('examination.posyandu.kelurahan', fn ($q) => $q->where('kecamatan_id', $user->kecamatan_id));
            } elseif (! $user->hasAnyRole(['super_admin', 'admin_dinkes'])) {
                $query->whereRaw('1 = 0');
            }
        }

        if (! empty($this->filters['posyandu_id'])) {
            $query->whereHas('examination', fn ($q) => $q->where('posyandu_id', $this->filters['posyandu_id']));
        }

        if (! empty($this->filters['month'])) {
            $query->whereHas('examination', fn ($q) => $q->where('month', $this->filters['month']));
        }

        if (! empty($this->filters['year'])) {
            $query->whereHas('examination', fn ($q) => $q->where('year', $this->filters['year']));
        }

        if (! empty($this->filters['stunting_status'])) {
            $query->where('stunting_status', $this->filters['stunting_status']);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            ['REKAPITULASI HASIL PENGUKURAN, STATUS GIZI & SKRINING TBC'],
            ['POSYANDU '.strtoupper($this->posyanduName).' - PERIODE '.$this->periodLabel],
            [
                'No',
                'Nama Balita',
                'Umur',
                'JK',
                'Tanggal Lahir',
                'Tanggal Periksa',
                'Nama Orang Tua',
                'Alamat / RT',
                'HASIL UKUR',
                '',
                '',
                'STATUS GIZI (Z-SCORE)',
                '',
                '',
                '',
                'LAYANAN KESEHATAN',
                '',
                'SKRINING GEJALA TBC',
                '',
                '',
                '',
            ],
            [
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                'BB (Kg)',
                'TB (Cm)',
                'LK (Cm)',
                'Status BB/U',
                'Status TB/U',
                'Status LK/U',
                'Status Gizi Utama (BB/TB)',
                'ASI Eksklusif',
                'MP ASI',
                'Batuk Terus',
                'Demam ≥ 2 Minggu',
                'BB Turun 2 Bln',
                'Kontak Erat Pasien TBC',
            ],
        ];
    }

    public function map($record): array
    {
        $safe = fn ($v) => ($v !== null && $v !== '') ? $v : '-';

        $child = $record->child;
        $ortu = $record->orangTua ?? $child?->orangTua;
        $exam = $record->examination;
        $examDate = $exam?->examination_date ?? $record->created_at;

        $ageMonths = $child?->tanggal_lahir ? $child->tanggal_lahir->diffInMonths($examDate) : '-';

        return [
            $this->rowNumber++,
            $safe($child?->nama_lengkap),
            $ageMonths !== '-' ? $ageMonths.' Bln' : '-',
            $safe($child?->jenis_kelamin),
            $child?->tanggal_lahir ? $child->tanggal_lahir->format('d/m/Y') : '-',
            $examDate ? \Carbon\Carbon::parse($examDate)->format('d/m/Y') : '-',
            $safe($ortu?->nama_lengkap),
            $safe($child?->alamat),

            $safe($record->weight),
            $safe($record->height),
            $safe($record->head_circumference),

            $safe($record->bmi_category),
            $safe($record->stunting_status),
            $safe($record->head_circumference_result),
            $safe($record->bmi_category),

            $safe($record->exclusive_breastfeeding),
            $safe($record->mp_asi),

            $safe($record->tb_cough),
            $safe($record->tb_fever),
            $safe($record->tb_weight_problem),
            $safe($record->tb_close_contact),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        // ── Merge Header Cells ────────────────────────────────────────────────
        $sheet->mergeCells('A1:U1');
        $sheet->mergeCells('A2:U2');

        // Merged Header Identitas
        $sheet->mergeCells('A3:A4');
        $sheet->mergeCells('B3:B4');
        $sheet->mergeCells('C3:C4');
        $sheet->mergeCells('D3:D4');
        $sheet->mergeCells('E3:E4');
        $sheet->mergeCells('F3:F4');
        $sheet->mergeCells('G3:G4');
        $sheet->mergeCells('H3:H4');

        // Merged Header Grouping
        $sheet->mergeCells('I3:K3'); // HASIL UKUR
        $sheet->mergeCells('L3:O3'); // STATUS GIZI
        $sheet->mergeCells('P3:Q3'); // LAYANAN KESEHATAN
        $sheet->mergeCells('R3:U3'); // SKRINING TBC

        // ── Alignments & Formatting ──────────────────────────────────────────
        $sheet->getStyle('A1:U2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:U4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:U4')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A3:U4')->getAlignment()->setWrapText(true);

        $sheet->freezePane('I5');

        // Header Styling
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 10],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1E3A5F']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ];

        $sheet->getStyle('A3:U4')->applyFromArray($headerStyle);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(11);

        return [];
    }

    public function title(): string
    {
        return 'Rekap Pemeriksaan Posyandu';
    }
}
