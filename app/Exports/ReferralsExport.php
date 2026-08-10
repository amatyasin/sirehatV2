<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ReferralsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $referrals;
    protected $rowNumber = 0;

    public function __construct($referrals)
    {
        $this->referrals = $referrals;
    }

    /**
     * Return collection of referrals.
     */
    public function collection()
    {
        return $this->referrals;
    }

    /**
     * Table headers.
     */
    public function headings(): array
    {
        return [
            'No',
            'Nama Siswa',
            'NIK',
            'NISN',
            'Sekolah',
            'Kelas',
            'Jenis Pemeriksaan',
            'Alasan Rujukan',
            'Status Rujukan',
            'Tanggal Pemeriksaan',
            'Tanggal Rujukan',
            'Catatan Tindak Lanjut',
        ];
    }

    /**
     * Map each row of referral data.
     */
    public function map($referral): array
    {
        $this->rowNumber++;
        $studentHistory = $referral->studentClassHistory;
        
        return [
            $this->rowNumber,
            $studentHistory?->student?->nama_lengkap ?? '-',
            $studentHistory?->student?->nik ? "'" . $studentHistory->student->nik : '-', // Prefix single quote to prevent scientific notation in Excel
            $studentHistory?->student?->nisn ? "'" . $studentHistory->student->nisn : '-',
            $studentHistory?->school?->nama_sekolah ?? '-',
            $studentHistory?->schoolClass?->nama_kelas ?? '-',
            $referral->jenis_pemeriksaan,
            $referral->alasan_rujukan,
            $referral->status_rujukan,
            $referral->tanggal_pemeriksaan?->toDateString() ?? '-',
            $referral->tanggal_rujukan?->toDateString() ?? '-',
            $referral->catatan_tindak_lanjut ?? '-',
        ];
    }
}
