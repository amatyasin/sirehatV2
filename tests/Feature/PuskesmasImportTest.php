<?php

namespace Tests\Feature;

use App\Imports\ChildrenImport;
use App\Imports\StudentsImport;
use App\Models\AcademicYear;
use App\Models\Child;
use App\Models\Instansi;
use App\Models\Posyandu;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PuskesmasImportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'super_admin']);
        Role::create(['name' => 'admin_dinkes']);
        Role::create(['name' => 'admin_instansi']);
    }

    public function test_puskesmas_admin_can_import_children_in_their_jurisdiction(): void
    {
        $puskesmas = Instansi::create(['nama_instansi' => 'Puskesmas Mawar', 'jenis' => 'puskesmas']);
        $posyandu1 = Posyandu::create(['instansi_id' => $puskesmas->id, 'nama_posyandu' => 'Posyandu Mawar 1']);
        $posyandu2 = Posyandu::create(['instansi_id' => $puskesmas->id, 'nama_posyandu' => 'Posyandu Mawar 2']);

        $user = User::factory()->create(['instansi_id' => $puskesmas->id]);
        $user->assignRole('admin_instansi');
        $this->actingAs($user);

        // Import for Posyandu 1
        $rows1 = collect([
            [
                'nama_lengkap_anak' => 'Anak Posyandu 1',
                'nik_anak' => '3201010101010010',
                'jk' => 'L',
                'tanggal_lahir_anak' => '2023-01-01',
                'nama_lengkap_ortu' => 'Ortu 1',
            ],
        ]);
        (new ChildrenImport($puskesmas->id, $posyandu1->id))->collection($rows1);

        // Import for Posyandu 2
        $rows2 = collect([
            [
                'nama_lengkap_anak' => 'Anak Posyandu 2',
                'nik_anak' => '3201010101010020',
                'jk' => 'P',
                'tanggal_lahir_anak' => '2023-02-02',
                'nama_lengkap_ortu' => 'Ortu 2',
            ],
        ]);
        (new ChildrenImport($puskesmas->id, $posyandu2->id))->collection($rows2);

        $this->assertDatabaseHas('children', ['nama_lengkap' => 'Anak Posyandu 1', 'posyandu_id' => $posyandu1->id, 'instansi_id' => $puskesmas->id]);
        $this->assertDatabaseHas('children', ['nama_lengkap' => 'Anak Posyandu 2', 'posyandu_id' => $posyandu2->id, 'instansi_id' => $puskesmas->id]);
    }

    public function test_puskesmas_admin_can_import_students_in_their_jurisdiction(): void
    {
        $puskesmas = Instansi::create(['nama_instansi' => 'Puskesmas Mawar', 'jenis' => 'puskesmas']);
        $school1 = School::create(['instansi_id' => $puskesmas->id, 'nama_sekolah' => 'SD 1 Mawar']);
        $school2 = School::create(['instansi_id' => $puskesmas->id, 'nama_sekolah' => 'SD 2 Mawar']);

        $academicYear = AcademicYear::create(['nama' => '2026/2027', 'aktif' => true]);
        $class1 = SchoolClass::create(['school_id' => $school1->id, 'nama_kelas' => 'Kelas 1A', 'urutan' => 1]);
        $class2 = SchoolClass::create(['school_id' => $school2->id, 'nama_kelas' => 'Kelas 1B', 'urutan' => 1]);

        $user = User::factory()->create(['instansi_id' => $puskesmas->id]);
        $user->assignRole('admin_instansi');
        $this->actingAs($user);

        // Import for School 1
        $rows1 = collect([
            ['Nama', 'NISN', 'JK', 'Tanggal Lahir', 'Alamat', 'NIK', 'Tempat Lahir', 'Nama Ortu', 'NIK Ortu', 'No HP Ortu'],
            ['Siswa Sekolah 1', '1000000001', 'L', '2015-01-01', 'Alamat 1', '3201010101150001', 'Jakarta', 'Ortu 1', '3201010101150002', '08111111111'],
        ]);
        (new StudentsImport($puskesmas->id, $school1->id, $academicYear->id, '1', $class1->id))->collection($rows1);

        // Import for School 2
        $rows2 = collect([
            ['Nama', 'NISN', 'JK', 'Tanggal Lahir', 'Alamat', 'NIK', 'Tempat Lahir', 'Nama Ortu', 'NIK Ortu', 'No HP Ortu'],
            ['Siswa Sekolah 2', '1000000002', 'P', '2015-02-02', 'Alamat 2', '3201010202150001', 'Jakarta', 'Ortu 2', '3201010202150002', '08222222222'],
        ]);
        (new StudentsImport($puskesmas->id, $school2->id, $academicYear->id, '1', $class2->id))->collection($rows2);

        $this->assertDatabaseHas('students', ['nama_lengkap' => 'Siswa Sekolah 1', 'school_id' => $school1->id, 'instansi_id' => $puskesmas->id]);
        $this->assertDatabaseHas('students', ['nama_lengkap' => 'Siswa Sekolah 2', 'school_id' => $school2->id, 'instansi_id' => $puskesmas->id]);
    }
}
