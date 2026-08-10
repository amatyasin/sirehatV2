<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StudentsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $user = auth()->user();

        $query = Student::query();

        if (! $user->hasRole('super_admin')) {
            $query->where('instansi_id', $user->instansi_id);
        }

        return $query->select(
            'nama_lengkap',
            'nisn',
            'jenis_kelamin',
            'tanggal_lahir',
            'alamat',
            'nik',
            'tempat_lahir',
            'nama_orang_tua',
            'nik_orang_tua',
            'no_hp_orang_tua'
        )->get();
    }

    public function headings(): array
    {
        return [
            'Nama Lengkap',
            'NISN',
            'Jenis Kelamin (L/P)',
            'Tanggal Lahir',
            'Alamat',
            'NIK',
            'Tempat Lahir',
            'Nama Orang Tua / Wali',
            'NIK Orang Tua / Wali',
            'No HP Orang Tua / Wali',
        ];
    }
}
