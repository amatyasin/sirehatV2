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

        $samarindaUlu = Kecamatan::firstOrCreate(['nama_kecamatan' => 'Kecamatan Wowo']);

        $sungaiKunjang = Kecamatan::firstOrCreate(['nama_kecamatan' => 'Kecamatan Hehe']);

        /*
        |--------------------------------------------------------------------------
        | PUSKESMAS / INSTANSI
        |--------------------------------------------------------------------------
        */

        $puskesmasSidodadi = Instansi::firstOrCreate(
            ['nama_instansi' => 'Puskesmas A'],
            [
                'alamat' => 'Jl. Kelurahan Mimi',
                'telepon' => '0541123456',
                'status' => true,
            ]
        );

        $puskesmasAirHitam = Instansi::firstOrCreate(
            ['nama_instansi' => 'Puskesmas B'],
            [
                'alamat' => 'Jl. Kelurahan Mumu',
                'telepon' => '0541456789',
                'status' => true,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | KELURAHAN
        |--------------------------------------------------------------------------
        */

        $sidodadi = Kelurahan::firstOrCreate(
            ['nama_kelurahan' => 'Kelurahan Mimi'],
            [
                'kecamatan_id'   => $samarindaUlu->id,
                'instansi_id'    => $puskesmasSidodadi->id,
            ]
        );

        $airHitam = Kelurahan::firstOrCreate(
            ['nama_kelurahan' => 'Kelurahan Mumu'],
            [
                'kecamatan_id'   => $samarindaUlu->id,
                'instansi_id'    => $puskesmasSidodadi->id,
            ]
        );

        $karangAnyar = Kelurahan::firstOrCreate(
            ['nama_kelurahan' => 'Kelurahan Haha'],
            [
                'kecamatan_id'   => $sungaiKunjang->id,
                'instansi_id'    => $puskesmasAirHitam->id,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | SEKOLAH
        |--------------------------------------------------------------------------
        */

        $sdn001 = School::firstOrCreate(
            ['npsn' => '12345678'],
            [
                'instansi_id' => $puskesmasSidodadi->id,
                'nama_sekolah' => 'SD Harvard',
                'alamat' => 'Jl. Pendidikan 1',
            ]
        );

        $smpn005 = School::firstOrCreate(
            ['npsn' => '23456789'],
            [
                'instansi_id' => $puskesmasSidodadi->id,
                'nama_sekolah' => 'SMP Gaul',
                'alamat' => 'Jl. Pendidikan 2',
            ]
        );

        $sdn010 = School::firstOrCreate(
            ['npsn' => '34567890'],
            [
                'instansi_id' => $puskesmasAirHitam->id,
                'nama_sekolah' => 'SD Stanford',
                'alamat' => 'Jl. Pendidikan 3',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | POSYANDU
        |--------------------------------------------------------------------------
        */

        $melati = Posyandu::firstOrCreate(
            ['nama_posyandu' => 'Posyandu Melati'],
            [
                'instansi_id' => $puskesmasSidodadi->id,
                'kelurahan_id' => $sidodadi->id,
                'alamat' => 'Jl. Mawar RT 01',
                'penanggung_jawab' => 'Siti Aminah',
                'no_wa' => '081234567890',
                'rt' => '01',
                'rw' => '02',
                'kode_pos' => '75123',
                'aktif' => true,
            ]
        );

        $anggrek = Posyandu::firstOrCreate(
            ['nama_posyandu' => 'Posyandu Anggrek'],
            [
                'instansi_id' => $puskesmasSidodadi->id,
                'kelurahan_id' => $airHitam->id,
                'alamat' => 'Jl. Anggrek RT 03',
                'penanggung_jawab' => 'Nurhayati',
                'no_wa' => '081298765432',
                'rt' => '03',
                'rw' => '01',
                'kode_pos' => '75124',
                'aktif' => true,
            ]
        );

        $kenanga = Posyandu::firstOrCreate(
            ['nama_posyandu' => 'Posyandu Kenanga'],
            [
                'instansi_id' => $puskesmasAirHitam->id,
                'kelurahan_id' => $karangAnyar->id,
                'alamat' => 'Jl. Kenanga RT 05',
                'penanggung_jawab' => 'Dewi Sartika',
                'no_wa' => '081277788899',
                'rt' => '05',
                'rw' => '04',
                'kode_pos' => '75125',
                'aktif' => true,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | SUPER ADMIN
        |--------------------------------------------------------------------------
        */

        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@test.id'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password'),
            ]
        );

        $superAdmin->assignRole('super_admin');

        /*
        |--------------------------------------------------------------------------
        | ADMIN DINKES
        |--------------------------------------------------------------------------
        */

        $dinkes = User::firstOrCreate(
            ['email' => 'dinkes@test.id'],
            [
                'name' => 'Admin Dinkes',
                'password' => bcrypt('password'),
            ]
        );

        $dinkes->assignRole('admin_dinkes');

        /*
        |--------------------------------------------------------------------------
        | ADMIN KECAMATAN
        |--------------------------------------------------------------------------
        */

        $adminKecamatan = User::firstOrCreate(
            ['email' => 'kecamatan@test.id'],
            [
                'name' => 'Admin Kecamatan',
                'password' => bcrypt('password'),
                'kecamatan_id' => $samarindaUlu->id,
            ]
        );

        $adminKecamatan->assignRole('admin_kecamatan');

        /*
        |--------------------------------------------------------------------------
        | ADMIN PUSKESMAS
        |--------------------------------------------------------------------------
        */

        $adminPuskesmas = User::firstOrCreate(
            ['email' => 'puskesmas@test.id'],
            [
                'name' => 'Admin Puskesmas',
                'password' => bcrypt('password'),
                'instansi_id' => $puskesmasSidodadi->id,
            ]
        );

        $adminPuskesmas->assignRole('admin_instansi');

        /*
        |--------------------------------------------------------------------------
        | ADMIN SEKOLAH
        |--------------------------------------------------------------------------
        */

        $adminSekolah = User::firstOrCreate(
            ['email' => 'sekolah@test.id'],
            [
                'name' => 'Admin Sekolah',
                'password' => bcrypt('password'),
                'instansi_id' => $puskesmasSidodadi->id,
                'school_id' => $sdn001->id,
            ]
        );

        $adminSekolah->assignRole('admin_sekolah');

        /*
        |--------------------------------------------------------------------------
        | PETUGAS POSYANDU
        |--------------------------------------------------------------------------
        */

        $petugasPosyandu = User::firstOrCreate(
            ['email' => 'posyandu@test.id'],
            [
                'name' => 'Petugas Posyandu',
                'password' => bcrypt('password'),
                'instansi_id' => $puskesmasSidodadi->id,
                'posyandu_id' => $melati->id,
            ]
        );

        $petugasPosyandu->assignRole('petugas_posyandu');
    }
}