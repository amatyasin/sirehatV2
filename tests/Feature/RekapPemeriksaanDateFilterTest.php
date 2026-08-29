<?php

namespace Tests\Feature;

use App\Exports\RekapPemeriksaanExport;
use App\Models\AcademicYear;
use App\Models\Instansi;
use App\Models\PemeriksaanUmum;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentClassHistory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RekapPemeriksaanDateFilterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'super_admin']);
        Role::create(['name' => 'admin_dinkes']);
    }

    public function test_rekap_pemeriksaan_export_filters_by_examination_date_range(): void
    {
        $instansi = Instansi::create(['nama_instansi' => 'Puskesmas C', 'jenis' => 'puskesmas']);
        $school = School::create(['instansi_id' => $instansi->id, 'nama_sekolah' => 'SD Negeri 3']);
        $academicYear = AcademicYear::create(['nama' => '2026/2027', 'aktif' => true]);
        $schoolClass = SchoolClass::create(['school_id' => $school->id, 'nama_kelas' => 'Kelas 1C', 'urutan' => 1]);

        $student1 = Student::create([
            'instansi_id' => $instansi->id,
            'school_id' => $school->id,
            'nama_lengkap' => 'Siswa Januari',
            'nisn' => '1111111111',
            'jenis_kelamin' => 'L',
            'aktif' => true,
        ]);

        $history1 = StudentClassHistory::create([
            'student_id' => $student1->id,
            'school_id' => $school->id,
            'school_class_id' => $schoolClass->id,
            'academic_year_id' => $academicYear->id,
            'semester' => '1',
            'aktif' => true,
        ]);

        PemeriksaanUmum::create([
            'student_class_history_id' => $history1->id,
            'tanggal_pemeriksaan' => '2026-01-15',
            'suhu' => 36.5,
        ]);

        $student2 = Student::create([
            'instansi_id' => $instansi->id,
            'school_id' => $school->id,
            'nama_lengkap' => 'Siswa Agustus',
            'nisn' => '2222222222',
            'jenis_kelamin' => 'P',
            'aktif' => true,
        ]);

        $history2 = StudentClassHistory::create([
            'student_id' => $student2->id,
            'school_id' => $school->id,
            'school_class_id' => $schoolClass->id,
            'academic_year_id' => $academicYear->id,
            'semester' => '1',
            'aktif' => true,
        ]);

        PemeriksaanUmum::create([
            'student_class_history_id' => $history2->id,
            'tanggal_pemeriksaan' => '2026-08-20',
            'suhu' => 36.7,
        ]);

        $user = User::factory()->create();
        $user->assignRole('super_admin');
        $this->actingAs($user);

        // Export filtering for January only
        $exportJan = new RekapPemeriksaanExport([
            'tanggal_pemeriksaan_dari' => '2026-01-01',
            'tanggal_pemeriksaan_sampai' => '2026-01-31',
        ]);
        $resultsJan = $exportJan->query()->get();

        $this->assertCount(1, $resultsJan);
        $this->assertEquals($history1->id, $resultsJan->first()->id);
    }
}
