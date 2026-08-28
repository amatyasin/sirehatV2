<?php

namespace Tests\Feature;

use App\Imports\ChildrenImport;
use App\Imports\StudentsImport;
use App\Models\Child;
use App\Models\Instansi;
use App\Models\Posyandu;
use App\Models\School;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ImportPermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'super_admin']);
        Role::create(['name' => 'admin_dinkes']);
        Role::create(['name' => 'admin_instansi']);
        Role::create(['name' => 'admin_sekolah']);
        Role::create(['name' => 'petugas_sekolah']);
        Role::create(['name' => 'petugas_posyandu']);
    }

    public function test_petugas_posyandu_can_access_child_resource(): void
    {
        $instansi = Instansi::create(['nama_instansi' => 'Puskesmas A', 'jenis' => 'puskesmas']);
        $posyandu = Posyandu::create(['instansi_id' => $instansi->id, 'nama_posyandu' => 'Posyandu Melati']);

        $user = User::factory()->create(['posyandu_id' => $posyandu->id]);
        $user->assignRole('petugas_posyandu');

        $this->actingAs($user);

        $this->assertTrue(\App\Filament\Resources\Children\ChildResource::canAccess());
        $this->assertTrue(\App\Filament\Resources\Children\ChildResource::canCreate());
    }

    public function test_petugas_sekolah_can_access_student_resource(): void
    {
        $instansi = Instansi::create(['nama_instansi' => 'Dinas Pendidikan', 'jenis' => 'dinas']);
        $school = School::create(['instansi_id' => $instansi->id, 'nama_sekolah' => 'SD Negeri 1']);

        $user = User::factory()->create(['school_id' => $school->id]);
        $user->assignRole('petugas_sekolah');

        $this->actingAs($user);

        $this->assertTrue(\App\Filament\Resources\Students\StudentResource::canAccess());
        $this->assertTrue(\App\Filament\Resources\Students\StudentResource::canCreate());
    }

    public function test_children_import_assigns_correct_posyandu_and_instansi(): void
    {
        $instansi = Instansi::create(['nama_instansi' => 'Puskesmas B', 'jenis' => 'puskesmas']);
        $posyandu = Posyandu::create(['instansi_id' => $instansi->id, 'nama_posyandu' => 'Posyandu Anggrek']);

        $rows = collect([
            [
                'nama_lengkap_anak' => 'Budi Santoso',
                'nik_anak' => '3201010101010001',
                'jk' => 'L',
                'tanggal_lahir_anak' => '2023-01-01',
                'nama_lengkap_ortu' => 'Ayah Budi',
                'nik_ortu' => '3201010101010002',
            ],
        ]);

        $import = new ChildrenImport($instansi->id, $posyandu->id);
        $import->collection($rows);

        $this->assertDatabaseHas('children', [
            'nama_lengkap' => 'Budi Santoso',
            'posyandu_id' => $posyandu->id,
            'instansi_id' => $instansi->id,
        ]);
    }

    public function test_students_import_assigns_correct_school_and_instansi(): void
    {
        $instansi = Instansi::create(['nama_instansi' => 'Dinas Pendidikan', 'jenis' => 'dinas']);
        $school = School::create(['instansi_id' => $instansi->id, 'nama_sekolah' => 'SD Negeri 2']);
        $academicYear = \App\Models\AcademicYear::create(['nama' => '2026/2027', 'aktif' => true]);
        $schoolClass = \App\Models\SchoolClass::create(['school_id' => $school->id, 'nama_kelas' => 'Kelas 1A', 'urutan' => 1]);

        $rows = collect([
            ['Nama', 'NISN', 'JK', 'Tanggal Lahir', 'Alamat', 'NIK', 'Tempat Lahir', 'Nama Ortu', 'NIK Ortu', 'No HP Ortu'],
            ['Siti Rahma', '1234567890', 'P', '2015-05-05', 'Jl. Merdeka', '3201010505150001', 'Jakarta', 'Ibu Siti', '3201010505150002', '08123456789'],
        ]);

        $import = new StudentsImport($instansi->id, $school->id, $academicYear->id, '1', $schoolClass->id);
        $import->collection($rows);

        $this->assertDatabaseHas('students', [
            'nama_lengkap' => 'Siti Rahma',
            'school_id' => $school->id,
            'instansi_id' => $instansi->id,
        ]);
    }
}
