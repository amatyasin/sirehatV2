<?php

namespace App\Imports;

use App\Models\Instansi;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\Posyandu;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PosyanduWilayahImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows): void
    {
        $instansiId = Instansi::first()?->id;

        if (! $instansiId) {
            $instansi = Instansi::create([
                'nama_instansi' => 'Default Posyandu',
            ]);
            $instansiId = $instansi->id;
        }

        foreach ($rows as $row) {
            if (
                empty($row['kecamatan']) ||
                empty($row['kelurahan']) ||
                empty($row['posyandu']) ||
                empty($row['puskesmas'])
            ) {
                continue;
            }

            $namaKecamatan = trim($row['kecamatan']);
            $namaKelurahan = trim($row['kelurahan']);
            $namaPosyandu = trim($row['posyandu']);
            $namaPuskesmas = trim($row['puskesmas']);
            $alamat = trim($row['cakupan_posyandu'] ?? '');

            $kecamatan = Kecamatan::firstOrCreate([
                'nama_kecamatan' => $namaKecamatan,
            ]);

            $kelurahan = Kelurahan::firstOrCreate([
                'kecamatan_id' => $kecamatan->id,
                'nama_kelurahan' => $namaKelurahan,
            ]);

            $instansi = Instansi::firstOrCreate(
                [
                    'nama_instansi' => $namaPuskesmas,
                ],
                [
                    'status' => true,
                ]
            );

            Posyandu::updateOrCreate(
                [
                    'nama_posyandu' => $namaPosyandu,
                    'kelurahan_id' => $kelurahan->id,
                ],
                [
                    'instansi_id' => $instansi->id,
                    'alamat' => $alamat,
                    'penanggung_jawab' => null,
                    'aktif' => true,
                ]
            );
        }
    }
}
