<?php

namespace Tests\Feature;

use App\Exports\PemeriksaanBulananPosyanduExport;
use App\Models\Child;
use App\Models\OrangTua;
use App\Models\Posyandu;
use App\Models\PosyanduMonthlyExamination;
use App\Models\PosyanduMonthlyParticipant;
use App\Models\User;
use App\Services\Posyandu\PosyanduAnthropometryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PosyanduMonthlyExaminationTest extends TestCase
{
    use RefreshDatabase;

    public function test_anthropometry_service_calculations(): void
    {
        $service = new PosyanduAnthropometryService();

        // BMI
        $this->assertEquals(14.61, $service->calculateBMI(12.5, 92.5));
        $this->assertEquals('Normal', $service->determineBMICategory(15.0));

        // Stunting
        $this->assertEquals('Normal', $service->determineStuntingStatus(24, 85.0));
        $this->assertEquals('Pendek', $service->determineStuntingStatus(24, 80.0));
        $this->assertEquals('Sangat Pendek', $service->determineStuntingStatus(24, 75.0));

        // Head circumference
        $this->assertEquals('Normal', $service->determineHeadCircumferenceResult(48.0));
        $this->assertEquals('Mikrosefali', $service->determineHeadCircumferenceResult(40.0));
        $this->assertEquals('Makrosefali', $service->determineHeadCircumferenceResult(54.0));

        // TB screening
        $this->assertEquals('Tidak Terindikasi', $service->determineTBScreening('T', 'T', 'T', 'T'));
        $this->assertEquals('Terindikasi', $service->determineTBScreening('Y', 'T', 'T', 'T'));
    }

    public function test_create_monthly_examination_and_auto_generate_participants(): void
    {
        Role::create(['name' => 'super_admin']);
        $user = User::factory()->create();
        $user->assignRole('super_admin');
        $this->actingAs($user);

        $instansi = \App\Models\Instansi::create([
            'nama_instansi' => 'Puskesmas Flamboyan',
            'jenis' => 'puskesmas',
        ]);

        $posyandu = Posyandu::create([
            'instansi_id' => $instansi->id,
            'nama_posyandu' => 'Posyandu Mawar',
            'aktif' => true,
        ]);

        $ortu = OrangTua::create([
            'nama_lengkap' => 'Siti Aminah',
            'nik' => '3201019001000001',
        ]);

        $child1 = Child::create([
            'instansi_id' => $instansi->id,
            'posyandu_id' => $posyandu->id,
            'orang_tua_id' => $ortu->id,
            'nama_lengkap' => 'Adibah Zahirah',
            'nik' => '3201012608230001',
            'jenis_kelamin' => 'P',
            'tanggal_lahir' => '2023-06-30',
            'aktif' => true,
        ]);

        $examination = PosyanduMonthlyExamination::create([
            'posyandu_id' => $posyandu->id,
            'examination_date' => '2026-08-28',
            'month' => 8,
            'year' => 2026,
            'status' => 'ongoing',
        ]);

        $this->assertDatabaseHas('posyandu_monthly_examinations', [
            'id' => $examination->id,
            'posyandu_id' => $posyandu->id,
            'month' => 8,
            'year' => 2026,
        ]);

        // Auto-generate participant
        PosyanduMonthlyParticipant::create([
            'posyandu_monthly_examination_id' => $examination->id,
            'child_id' => $child1->id,
            'orang_tua_id' => $ortu->id,
            'attendance' => true,
            'weight' => 12.5,
            'height' => 92.5,
            'bmi' => 14.61,
            'bmi_category' => 'Normal',
            'stunting_status' => 'Normal',
            'head_circumference' => 48.0,
            'head_circumference_result' => 'Normal',
            'exclusive_breastfeeding' => 'Y',
            'mp_asi' => 'Y',
            'tb_cough' => 'T',
            'tb_fever' => 'T',
            'tb_weight_problem' => 'T',
            'tb_close_contact' => 'Y',
            'tb_screening_result' => 'Terindikasi',
            'examination_status' => 'Perlu Tindak Lanjut',
        ]);

        $this->assertDatabaseHas('posyandu_monthly_participants', [
            'posyandu_monthly_examination_id' => $examination->id,
            'child_id' => $child1->id,
            'tb_screening_result' => 'Terindikasi',
            'examination_status' => 'Perlu Tindak Lanjut',
        ]);
    }

    public function test_export_structure_and_title(): void
    {
        $export = new PemeriksaanBulananPosyanduExport();

        $headings = $export->headings();
        $this->assertCount(4, $headings); // 4 rows of multi-level header
        $this->assertCount(21, $headings[3]); // 21 subheader columns

        $this->assertEquals('BB (Kg)', $headings[3][8]);
        $this->assertEquals('TB (Cm)', $headings[3][9]);
        $this->assertEquals('LK (Cm)', $headings[3][10]);
        $this->assertEquals('Status BB/U', $headings[3][11]);
        $this->assertEquals('Status TB/U', $headings[3][12]);
        $this->assertEquals('Kontak Erat Pasien TBC', $headings[3][20]);

        $this->assertEquals('Rekap Pemeriksaan Posyandu', $export->title());
    }
}
