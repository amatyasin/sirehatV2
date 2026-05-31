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
            'alamat'
        )->get();
    }

    public function headings(): array
    {
        return [
            'Nama Lengkap',
            'NISN',
            'Jenis Kelamin',
            'Tanggal Lahir',
            'Alamat',
        ];
    }
}
