<?php

namespace Database\Seeders;

use App\Models\Kecamatan;
use App\Models\Kelurahan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WilayahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $wilayahSamarinda = [
            'Palaran' => [
                'Bantuas', 'Bukuan', 'Handil Bakti', 'Rawa Makmur', 'Simpang Pasir'
            ],
            'Samarinda Ilir' => [
                'Pelita', 'Selili', 'Sidomulyo', 'Sungai Dama', 'Sidodamai'
            ],
            'Samarinda Kota' => [
                'Bugis', 'Karang Mumus', 'Pasar Pagi', 'Pelabuhan', 'Sungai Pinang Luar'
            ],
            'Samarinda Seberang' => [
                'Baqa', 'Gunung Panjang', 'Mangkupalas', 'Mesjid', 'Sungai Keledang', 'Tenun'
            ],
            'Samarinda Ulu' => [
                'Air Hitam', 'Air Putih', 'Bukit Pinang', 'Dadi Mulya', 'Gunung Kelua', 'Jawa', 'Sidodadi', 'Teluk Lerong Ilir'
            ],
            'Samarinda Utara' => [
                'Budaya Pampang', 'Lempake', 'Sempaja Selatan', 'Sempaja Utara', 'Sempaja Timur', 'Sempaja Barat', 'Sungai Siring', 'Tanah Merah'
            ],
            'Sambutan' => [
                'Makroman', 'Pulau Atas', 'Sambutan', 'Sindang Sari', 'Sungai Kapih'
            ],
            'Sungai Kunjang' => [
                'Karang Anyar', 'Karang Asam Ilir', 'Karang Asam Ulu', 'Lok Bahu', 'Loa Bakung', 'Loa Buah', 'Teluk Lerong Ulu'
            ],
            'Sungai Pinang' => [
                'Bandara', 'Gunung Lingai', 'Mugirejo', 'Temindung Permai', 'Sungai Pinang Dalam'
            ],
            'Loa Janan Ilir' => [
                'Harapan Baru', 'Rapak Dalam', 'Simpang Tiga', 'Sengkotek', 'Tani Aman'
            ],
        ];

        DB::transaction(function () use ($wilayahSamarinda) {
            foreach ($wilayahSamarinda as $namaKecamatan => $kelurahans) {
                // 1. Buat atau ambil data Kecamatan
                $kecamatan = Kecamatan::firstOrCreate(
                    ['nama_kecamatan' => $namaKecamatan]
                );

                // 2. Buat data Kelurahan-kelurahan yang terkait
                foreach ($kelurahans as $namaKelurahan) {
                    Kelurahan::firstOrCreate(
                        [
                            'nama_kelurahan' => $namaKelurahan,
                            'kecamatan_id'   => $kecamatan->id,
                        ],
                        [
                            // Set default aktif jika kelurahan baru saja dibuat
                            'aktif' => true,
                        ]
                    );
                }
            }
        });
    }
}
