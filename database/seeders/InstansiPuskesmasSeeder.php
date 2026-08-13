<?php

namespace Database\Seeders;

use App\Models\Instansi;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InstansiPuskesmasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dataPuskesmas = [
            'Palaran' => [
                ['nama' => 'Puskesmas Palaran', 'kelurahan' => 'Rawa Makmur'],
                ['nama' => 'Puskesmas Bantuas', 'kelurahan' => 'Bantuas'],
                ['nama' => 'Puskesmas Bukuan', 'kelurahan' => 'Bukuan']
            ],
            'Samarinda Ilir' => [
                ['nama' => 'Puskesmas Sidomulyo', 'kelurahan' => 'Sidomulyo']
            ],
            'Samarinda Kota' => [
                ['nama' => 'Puskesmas Samarinda Kota', 'kelurahan' => 'Bugis']
            ],
            'Samarinda Seberang' => [
                ['nama' => 'Puskesmas Kampung Baqa', 'kelurahan' => 'Baqa'],
                ['nama' => 'Puskesmas Mangkupalas', 'kelurahan' => 'Mesjid']
            ],
            'Samarinda Ulu' => [
                ['nama' => 'Puskesmas Air Putih', 'kelurahan' => 'Air Putih'],
                ['nama' => 'Puskesmas Juanda', 'kelurahan' => 'Air Hitam'],
                ['nama' => 'Puskesmas Pasundan', 'kelurahan' => 'Jawa'],
                ['nama' => 'Puskesmas Segiri', 'kelurahan' => 'Sidodadi']
            ],
            'Samarinda Utara' => [
                ['nama' => 'Puskesmas Lempake', 'kelurahan' => 'Lempake'],
                ['nama' => 'Puskesmas Sempaja', 'kelurahan' => 'Sempaja Utara'],
                ['nama' => 'Puskesmas Bengkuring', 'kelurahan' => 'Sempaja Timur'],
                ['nama' => 'Puskesmas Sungai Siring', 'kelurahan' => 'Tanah Merah']
            ],
            'Sambutan' => [
                ['nama' => 'Puskesmas Makroman', 'kelurahan' => 'Makroman'],
                ['nama' => 'Puskesmas Sambutan', 'kelurahan' => 'Sambutan'],
                ['nama' => 'Puskesmas Sungai Kapih', 'kelurahan' => 'Sungai Kapih']
            ],
            'Sungai Kunjang' => [
                ['nama' => 'Puskesmas Karang Asam', 'kelurahan' => 'Karang Asam Ilir'],
                ['nama' => 'Puskesmas Loa Bakung', 'kelurahan' => 'Loa Bakung'],
                ['nama' => 'Puskesmas Lok Bahu', 'kelurahan' => 'Lok Bahu'],
                ['nama' => 'Puskesmas Wonorejo', 'kelurahan' => 'Teluk Lerong Ulu']
            ],
            'Sungai Pinang' => [
                ['nama' => 'Puskesmas Remaja', 'kelurahan' => 'Temindung Permai'],
                ['nama' => 'Puskesmas Temindung', 'kelurahan' => 'Sungai Pinang Dalam']
            ],
            'Loa Janan Ilir' => [
                ['nama' => 'Puskesmas Harapan Baru', 'kelurahan' => 'Harapan Baru'],
                ['nama' => 'Puskesmas Trauma Center', 'kelurahan' => 'Sengkotek']
            ],
        ];

        DB::transaction(function () use ($dataPuskesmas) {
            foreach ($dataPuskesmas as $namaKecamatan => $listPuskesmas) {
                // Ambil ID Kecamatan untuk akurasi pencarian Kelurahan
                $kecamatan = Kecamatan::where('nama_kecamatan', $namaKecamatan)->first();

                foreach ($listPuskesmas as $item) {
                    /*
                     * CATATAN PENTING:
                     * Berdasarkan struktur database Anda saat ini, tabel 'instansis' TIDAK memiliki
                     * kolom 'kecamatan_id' maupun 'kelurahan_id'.
                     * Sebaliknya, tabel 'kelurahans'-lah yang memiliki 'instansi_id'.
                     * (Satu puskesmas bisa membawahi banyak kelurahan, makanya FK ada di kelurahan).
                     */

                    // 1. Buat atau update Puskesmas
                    $instansi = Instansi::updateOrCreate(
                        ['nama_instansi' => $item['nama']],
                        [
                            'status' => true,
                            // Dummy alamat otomatis
                            'alamat' => 'Jl. ' . $item['kelurahan'] . ', Kec. ' . $namaKecamatan,
                        ]
                    );

                    // 2. Hubungkan Puskesmas ke Kelurahannya (update instansi_id di tabel kelurahans)
                    if ($kecamatan) {
                        Kelurahan::where('nama_kelurahan', $item['kelurahan'])
                            ->where('kecamatan_id', $kecamatan->id)
                            ->update(['instansi_id' => $instansi->id]);
                    }
                }
            }
        });
    }
}
