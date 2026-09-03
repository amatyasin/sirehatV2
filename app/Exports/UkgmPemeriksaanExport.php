<?php

namespace App\Exports;

use App\Models\GarasiParticipant;
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

class UkgmPemeriksaanExport implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithChunkReading,
    ShouldAutoSize,
    WithStyles,
    WithTitle
{
    protected ?int $activityId;

    protected array $filters;

    protected ?array $recordIds;

    public function chunkSize(): int
    {
        return 500;
    }

    public function __construct(?int $activityId = null, array $filters = [], ?array $recordIds = null)
    {
        $this->activityId = $activityId;
        $this->filters = $filters;
        $this->recordIds = $recordIds;
    }

    public function query(): Builder
    {
        $user = auth()->user();

        $query = GarasiParticipant::query()
            ->with([
                'activity',
                'activity.posyandu',
                'activity.posyandu.instansi',
                'child',
                'child.orangTua',
                'orangTua',
                'brushingPractice',
                'education',
                'screening',
                'dentalIndex',
                'treatment',
                'referral',
            ])
            ->orderBy('created_at', 'desc');

        if ($this->activityId !== null) {
            $query->where('garasi_activity_id', $this->activityId);
        }

        if ($this->recordIds !== null) {
            $query->whereIn('id', $this->recordIds);
        }

        if ($user) {
            if ($user->hasRole('admin_instansi')) {
                $query->whereHas('activity', fn ($q) => $q->where('instansi_id', $user->instansi_id));
            } elseif ($user->hasRole('petugas_posyandu')) {
                $query->whereHas('activity', fn ($q) => $q->where('posyandu_id', $user->posyandu_id));
            } elseif ($user->hasRole('admin_kecamatan')) {
                $query->whereHas('activity.posyandu.kelurahan', fn ($q) => $q->where('kecamatan_id', $user->kecamatan_id));
            } elseif (! $user->hasAnyRole(['super_admin', 'admin_dinkes'])) {
                $query->whereRaw('1 = 0');
            }
        }

        if (isset($this->filters['attendance']) && $this->filters['attendance'] !== '' && $this->filters['attendance'] !== null) {
            $query->where('attendance', (bool) $this->filters['attendance']);
        }

        if (! empty($this->filters['risk_level'])) {
            $query->whereHas('screening', fn ($q) => $q->where('risk_level', $this->filters['risk_level']));
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            // ── Identitas & Posyandu ───────────────────────────────────────────
            'Puskesmas',
            'Posyandu',
            'Tanggal Kegiatan',
            'Lokasi Kegiatan',
            'Nama Anak',
            'NIK Anak',
            'Jenis Kelamin',
            'Tanggal Lahir',
            'Umur (Tahun)',
            'Nama Orang Tua / Ibu',
            'NIK Orang Tua',
            'No HP Orang Tua',

            // ── Tahap 1: Kehadiran ─────────────────────────────────────────────
            'Kehadiran Anak',
            'Ibu Mendampingi Kegiatan',
            'Ibu Mendampingi Sikat Gigi',
            'Catatan Kehadiran',

            // ── Tahap 2: Praktik Sikat Gigi ────────────────────────────────────
            'Menyikat Gigi Bersama',
            'Kemampuan Menyikat Gigi',
            'Frekuensi Menyikat Gigi',
            'Pendampingan Ibu',
            'Dosis Pasta Gigi',
            'Sikat Gigi yang Digunakan',

            // ── Tahap 3: Edukasi Ibu ───────────────────────────────────────────
            'Edukasi Cara Sikat Gigi',
            'Edukasi Waktu Menyikat',
            'Edukasi Fluoride / Pasta Gigi',
            'Edukasi Sikat Gigi Anak',
            'Edukasi Sikat Gigi Ibu',
            'Edukasi Pembatasan Manis',
            'Edukasi Pemeriksaan Berkala',
            'Edukasi Perawatan Rumah',
            'Catatan Edukasi',

            // ── Tahap 4: Skrining & Indeks Gigi ───────────────────────────────
            'Sakit Gigi',
            'Gigi Ngilu / Sensitif',
            'Gusi Berdarah',
            'Gusi Bengkak (Keluhan)',
            'Bau Mulut',
            'Sariawan',
            'Sulit Mengunyah',
            'Kebersihan Mulut',
            'Karang Gigi',
            'Gigi Berlubang (Karies)',
            'Gigi Patah',
            'Plak',
            'Gusi Merah',
            'Tingkat Risiko',
            'Rekomendasi / Saran',

            // ── Indeks Gigi (def-t & DMF-T) ──────────────────────────────────
            'Kondisi Gigi (Dentition)',
            'decayed (d)',
            'extracted (e)',
            'filled (f)',
            'Skor def-t',
            'Decay (D)',
            'Missing (M)',
            'Filling (F)',
            'Skor DMF-T',

            // ── Tahap 5: Tindakan / Treatment ────────────────────────────────
            'Tindakan Edukasi',
            'Dental Imunisasi Samarinda (DENISA)',
            'Catatan Tindakan',

            // ── Tahap 6 & 7: Rujukan & Follow-up ──────────────────────────────
            'Perlu Rujukan',
            'Tanggal Rujukan',
            'Alasan Rujukan',
            'Tujuan Rujukan',
            'Tindakan Direkomendasikan',
            'Catatan Rujukan',
            'Jadwal Follow-up',
        ];
    }

    public function map($record): array
    {
        $safe = fn ($v) => ($v !== null && $v !== '') ? $v : '-';

        $yn = function ($v) {
            if ($v === null || $v === '') {
                return '-';
            }
            if ($v === true || $v === 1 || $v === 'Y' || $v === '1') {
                return 'Ya';
            }
            if ($v === false || $v === 0 || $v === 'N' || $v === '0') {
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
        $ortu = $record->orangTua ?? $child?->orangTua;
        $activity = $record->activity;
        $posyandu = $activity?->posyandu ?? $child?->posyandu;
        $instansi = $posyandu?->instansi;

        $brushing = $record->brushingPractice;
        $education = $record->education;
        $screening = $record->screening;
        $dentalIndex = $record->dentalIndex;
        $treatment = $record->treatment;
        $referral = $record->referral;

        $genderRaw = strtoupper(trim((string) ($child?->jenis_kelamin ?? '')));
        $jenisKelamin = match ($genderRaw) {
            'L', 'LAKI-LAKI' => 'Laki-laki',
            'P', 'PEREMPUAN' => 'Perempuan',
            default => '-',
        };

        $nikChild = $child?->nik ? "'".trim((string) $child->nik) : '-';
        $nikOrtu = $ortu?->nik ? "'".trim((string) $ortu->nik) : '-';

        $riskLabel = match ($screening?->risk_level) {
            'rendah' => 'Risiko Rendah',
            'pemantauan' => 'Perlu Pemantauan',
            'rujukan' => 'Perlu Rujukan',
            default => $safe($screening?->risk_level),
        };

        $recommendedActionsStr = '-';
        if ($referral && ! empty($referral->recommended_actions)) {
            $actions = is_array($referral->recommended_actions)
                ? $referral->recommended_actions
                : json_decode($referral->recommended_actions, true);
            if (is_array($actions)) {
                $recommendedActionsStr = implode(', ', array_map(fn ($a) => ucfirst(str_replace('_', ' ', $a)), $actions));
            }
        }

        return [
            // ── Identitas & Posyandu ───────────────────────────────────────────
            $safe($instansi?->nama_instansi),
            $safe($posyandu?->nama_posyandu),
            $fmtDate($activity?->activity_date),
            $safe($activity?->location),
            $safe($child?->nama_lengkap),
            $nikChild,
            $jenisKelamin,
            $fmtDate($child?->tanggal_lahir),
            $child?->tanggal_lahir ? \Carbon\Carbon::parse($child->tanggal_lahir)->age : '-',
            $safe($ortu?->nama_lengkap),
            $nikOrtu,
            $safe($ortu?->no_hp),

            // ── Tahap 1: Kehadiran ─────────────────────────────────────────────
            $yn($record->attendance),
            $yn($record->mother_accompanied),
            $yn($record->mother_accompanied_brushing),
            $safe($record->notes),

            // ── Tahap 2: Praktik Sikat Gigi ────────────────────────────────────
            $yn($brushing?->together_brushing),
            $safe($brushing?->practice_ability),
            $safe($brushing?->brushing_frequency),
            $safe($brushing?->mother_accompaniment_frequency),
            match ($brushing?->use_toothpaste) {
                'sesuai' => 'Sesuai',
                'tidak_sesuai' => 'Tidak Sesuai',
                'ya' => 'Ya',
                'tidak' => 'Tidak',
                'tidak_diketahui' => 'Tidak Diketahui',
                default => $safe($brushing?->use_toothpaste),
            },
            $safe($brushing?->tool_used),

            // ── Tahap 3: Edukasi Ibu ───────────────────────────────────────────
            $yn($education?->brushing_education),
            $yn($education?->brushing_frequency_education),
            $yn($education?->fluoride_education),
            $yn($education?->child_toothbrush_selection),
            $yn($education?->mother_toothbrush_selection),
            $yn($education?->sugar_education),
            $yn($education?->dental_checkup_education),
            $yn($education?->home_care_education),
            $safe($education?->notes),

            // ── Tahap 4: Skrining & Indeks Gigi ───────────────────────────────
            $yn($screening?->toothache),
            $yn($screening?->sensitive_teeth),
            $yn($screening?->bleeding_gums),
            $yn($screening?->swollen_gums),
            $yn($screening?->bad_breath),
            $yn($screening?->mouth_sores),
            $yn($screening?->chewing_difficulty),
            $safe($screening?->oral_hygiene),
            $yn($screening?->tartar),
            $yn($screening?->cavities),
            $yn($screening?->broken_teeth),
            $yn($screening?->plaque),
            $yn($screening?->red_gums),
            $riskLabel,
            $safe($screening?->recommendation),

            // ── Indeks Gigi (def-t & DMF-T) ──────────────────────────────────
            $safe($dentalIndex?->dentition_type),
            $safe($dentalIndex?->decay_prim_d),
            $safe($dentalIndex?->extracted_prim_e),
            $safe($dentalIndex?->filled_prim_f),
            $safe($dentalIndex?->deft_score),
            $safe($dentalIndex?->decay_perm_D),
            $safe($dentalIndex?->missing_perm_M),
            $safe($dentalIndex?->filling_perm_F),
            $safe($dentalIndex?->dmft_score),

            // ── Tahap 5: Tindakan / Treatment ────────────────────────────────
            $yn($treatment?->education),
            $yn($treatment?->denisa),
            $safe($treatment?->notes),

            // ── Tahap 6 & 7: Rujukan & Follow-up ──────────────────────────────
            $yn($referral?->referral_needed),
            $fmtDate($referral?->referral_date),
            $safe($referral?->reason),
            $safe($referral?->destination),
            $recommendedActionsStr,
            $safe($referral?->notes),
            $fmtDate($record->follow_up_scheduled_date),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->getStyle('F:F')->getNumberFormat()->setFormatCode('@');
        $sheet->getStyle('K:K')->getNumberFormat()->setFormatCode('@');
        $sheet->freezePane('F2');

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
        return 'Pemeriksaan UKGM';
    }
}
