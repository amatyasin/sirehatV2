<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\School;
use App\Models\Instansi;
use App\Models\Posyandu;
use App\Models\Kecamatan;
use App\Models\Kelurahan;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | ROLE SEEDER
        |--------------------------------------------------------------------------
        */

        $this->call([
            RoleSeeder::class,
        ]);

        /*
        |--------------------------------------------------------------------------
        | KECAMATAN
        |--------------------------------------------------------------------------
        */

        $samarindaUlu = Kecamatan::create([
            'nama_kecamatan' => 'Samarinda Ulu',
        ]);

        $sungaiKunjang = Kecamatan::create([
            'nama_kecamatan' => 'Sungai Kunjang',
        ]);

        /*
        |--------------------------------------------------------------------------
        | KELURAHAN
        |--------------------------------------------------------------------------
        */

        $sidodadi = Kelurahan::create([
            'kecamatan_id'   => $samarindaUlu->id,
            'nama_kelurahan' => 'Sidodadi',
        ]);

        $airHitam = Kelurahan::create([
            'kecamatan_id'   => $samarindaUlu->id,
            'nama_kelurahan' => 'Air Hitam',
        ]);

        $karangAnyar = Kelurahan::create([
            'kecamatan_id'   => $sungaiKunjang->id,
            'nama_kelurahan' => 'Karang Anyar',
        ]);

        /*
        |--------------------------------------------------------------------------
        | PUSKESMAS / INSTANSI
        |--------------------------------------------------------------------------
        */

       $puskesmasSidodadi = Instansi::create([

    'kecamatan_id' => $samarindaUlu->id,

    'nama_instansi' => 'Puskesmas Sidodadi',

    'alamat' => 'Jl. Sidodadi',

    'telepon' => '0541123456',

    'status' => true,

]);

        $puskesmasAirHitam = Instansi::create([

    'kecamatan_id' => $sungaiKunjang->id,

    'nama_instansi' => 'Puskesmas Air Hitam',

    'alamat' => 'Jl. Air Hitam',

    'telepon' => '0541456789',

    'status' => true,

]);

        /*
        |--------------------------------------------------------------------------
        | SEKOLAH
        |--------------------------------------------------------------------------
        */

        $sdn001 = School::create([
            'instansi_id' => $puskesmasSidodadi->id,
            'nama_sekolah' => 'SDN 001 Samarinda',
            'npsn' => '12345678',
            'alamat' => 'Jl. Pendidikan 1',
        ]);

        $smpn005 = School::create([
            'instansi_id' => $puskesmasSidodadi->id,
            'nama_sekolah' => 'SMPN 005 Samarinda',
            'npsn' => '23456789',
            'alamat' => 'Jl. Pendidikan 2',
        ]);

        $sdn010 = School::create([
            'instansi_id' => $puskesmasAirHitam->id,
            'nama_sekolah' => 'SDN 010 Samarinda',
            'npsn' => '34567890',
            'alamat' => 'Jl. Pendidikan 3',
        ]);

        /*
        |--------------------------------------------------------------------------
        | POSYANDU
        |--------------------------------------------------------------------------
        */

        $melati = Posyandu::create([
            'instansi_id' => $puskesmasSidodadi->id,
            'kelurahan_id' => $sidodadi->id,
            'nama_posyandu' => 'Posyandu Melati',
            'alamat' => 'Jl. Mawar RT 01',
            'penanggung_jawab' => 'Siti Aminah',
            'no_wa' => '081234567890',
            'rt' => '01',
            'rw' => '02',
            'kode_pos' => '75123',
            'aktif' => true,
        ]);

        $anggrek = Posyandu::create([
            'instansi_id' => $puskesmasSidodadi->id,
            'kelurahan_id' => $airHitam->id,
            'nama_posyandu' => 'Posyandu Anggrek',
            'alamat' => 'Jl. Anggrek RT 03',
            'penanggung_jawab' => 'Nurhayati',
            'no_wa' => '081298765432',
            'rt' => '03',
            'rw' => '01',
            'kode_pos' => '75124',
            'aktif' => true,
        ]);

        $kenanga = Posyandu::create([
            'instansi_id' => $puskesmasAirHitam->id,
            'kelurahan_id' => $karangAnyar->id,
            'nama_posyandu' => 'Posyandu Kenanga',
            'alamat' => 'Jl. Kenanga RT 05',
            'penanggung_jawab' => 'Dewi Sartika',
            'no_wa' => '081277788899',
            'rt' => '05',
            'rw' => '04',
            'kode_pos' => '75125',
            'aktif' => true,
        ]);

        /*
        |--------------------------------------------------------------------------
        | SUPER ADMIN
        |--------------------------------------------------------------------------
        */

        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@test.id',
            'password' => bcrypt('password'),
        ]);

        $superAdmin->assignRole(
            'super_admin'
        );

        /*
        |--------------------------------------------------------------------------
        | ADMIN DINKES
        |--------------------------------------------------------------------------
        */

        $dinkes = User::create([
            'name' => 'Admin Dinkes',
            'email' => 'dinkes@test.id',
            'password' => bcrypt('password'),
        ]);

        $dinkes->assignRole(
            'admin_dinkes'
        );

        /*
        |--------------------------------------------------------------------------
        | ADMIN KECAMATAN
        |--------------------------------------------------------------------------
        */

        $adminKecamatan = User::create([
            'name' => 'Admin Kecamatan',
            'email' => 'kecamatan@test.id',
            'password' => bcrypt('password'),
            'kecamatan_id' => $samarindaUlu->id,
        ]);

        $adminKecamatan->assignRole(
            'admin_kecamatan'
        );

        /*
        |--------------------------------------------------------------------------
        | ADMIN PUSKESMAS
        |--------------------------------------------------------------------------
        */

        $adminPuskesmas = User::create([
            'name' => 'Admin Puskesmas',
            'email' => 'puskesmas@test.id',
            'password' => bcrypt('password'),
            'instansi_id' => $puskesmasSidodadi->id,
        ]);

        $adminPuskesmas->assignRole(
            'admin_instansi'
        );

        /*
        |--------------------------------------------------------------------------
        | ADMIN SEKOLAH
        |--------------------------------------------------------------------------
        */

        $adminSekolah = User::create([
            'name' => 'Admin Sekolah',
            'email' => 'sekolah@test.id',
            'password' => bcrypt('password'),
            'instansi_id' => $puskesmasSidodadi->id,
            'school_id' => $sdn001->id,
        ]);

        $adminSekolah->assignRole(
            'admin_sekolah'
        );

        /*
        |--------------------------------------------------------------------------
        | PETUGAS POSYANDU
        |--------------------------------------------------------------------------
        */

        $petugasPosyandu = User::create([
            'name' => 'Petugas Posyandu',
            'email' => 'posyandu@test.id',
            'password' => bcrypt('password'),
            'instansi_id' => $puskesmasSidodadi->id,
            'posyandu_id' => $melati->id,
        ]);

        $petugasPosyandu->assignRole(
            'petugas_posyandu'
        );
    }
}