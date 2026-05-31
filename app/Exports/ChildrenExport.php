<?php

namespace App\Exports;

use App\Models\Child;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ChildrenExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $user = auth()->user();

        $query = Child::query();

        if (! $user->hasRole('super_admin')) {
            $query->where('instansi_id', $user->instansi_id);
        }

        return $query->select(
            'nama_anak',
            'nik',
            'jenis_kelamin',
            'tanggal_lahir',
            'nama_orang_tua',
            'alamat'
        )->get();
    }

    public function headings(): array
    {
        return [
            'Nama Anak',
            'NIK',
            'Jenis Kelamin',
            'Tanggal Lahir',
            'Nama Orang Tua',
            'Alamat',
        ];
    }
}
