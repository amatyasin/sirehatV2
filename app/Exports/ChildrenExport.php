<?php

namespace App\Exports;

use App\Models\Child;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ChildrenExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function query(): Builder
    {
        $user = auth()->user();
        $query = Child::query()->with(['posyandu', 'posyandu.instansi', 'orangTua']);

        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->hasAnyRole(['super_admin', 'admin_dinkes'])) {
            return $query;
        }

        if ($user->hasRole('admin_kecamatan')) {
            return $query->whereHas('posyandu.kelurahan', fn ($q) => $q->where('kecamatan_id', $user->kecamatan_id));
        }

        if ($user->hasRole('admin_instansi')) {
            return $query->whereHas('posyandu', fn ($q) => $q->where('instansi_id', $user->instansi_id));
        }

        if ($user->hasRole('petugas_posyandu')) {
            return $query->where('posyandu_id', $user->posyandu_id);
        }

        return $query->whereRaw('1 = 0');
    }

    public function headings(): array
    {
        return [
            'Nama Anak',
            'NIK',
            'Jenis Kelamin',
            'Tanggal Lahir',
            'Umur (Bulan)',
            'Nama Orang Tua',
            'Alamat',
            'Posyandu',
            'Puskesmas',
        ];
    }

    public function map($child): array
    {
        $safe = fn ($v) => ($v !== null && $v !== '') ? $v : '-';
        $nik = $child->nik ? "'".trim((string) $child->nik) : '-';
        $jk = $child->jenis_kelamin === 'L' ? 'Laki-laki' : ($child->jenis_kelamin === 'P' ? 'Perempuan' : '-');
        $age = $child->tanggal_lahir ? $child->tanggal_lahir->diffInMonths(now()) : '-';

        return [
            $safe($child->nama_lengkap),
            $nik,
            $jk,
            $child->tanggal_lahir ? $child->tanggal_lahir->format('d/m/Y') : '-',
            $age,
            $safe($child->orangTua?->nama_lengkap),
            $safe($child->alamat),
            $safe($child->posyandu?->nama_posyandu),
            $safe($child->posyandu?->instansi?->nama_instansi),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->getStyle('B:B')->getNumberFormat()->setFormatCode('@');

        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 10],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1E3A5F']],
            ],
        ];
    }
}
