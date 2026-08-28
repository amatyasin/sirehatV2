<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\AcademicYear;
use App\Models\StudentClassHistory;
use App\Models\PemeriksaanGizi;
use App\Models\Referral;
use App\Models\ReferralStatusHistory;
use App\Services\Referral\ReferralService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReferralApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles
        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
    }

    /**
     * Test that unauthenticated users are blocked.
     */
    public function test_unauthenticated_user_cannot_access_referrals_endpoints()
    {
        $this->getJson('/api/referrals')->assertStatus(401);
        $this->getJson('/api/referrals/options')->assertStatus(401);
        $this->getJson('/api/referrals/1')->assertStatus(401);
        $this->putJson('/api/referrals/1/status', ['status_rujukan' => 'Selesai'])->assertStatus(401);
    }

    /**
     * Test that authenticated users can fetch filter options.
     */
    public function test_authenticated_user_can_access_endpoints()
    {
        $user = User::factory()->create();
        $user->assignRole('super_admin');

        $response = $this->actingAs($user)->getJson('/api/referrals/options');
        $response->assertStatus(200)
            ->assertJsonStructure(['schools', 'classes', 'kecamatans', 'kelurahans', 'user']);
    }

    /**
     * Test that abnormal health checkups automatically trigger referrals,
     * and their status changes are properly logged.
     */
    public function test_pemeriksaan_gizi_abnormal_triggers_referral_sync()
    {
        $instansi = \App\Models\Instansi::first() ?? \App\Models\Instansi::create([
            'nama_instansi' => 'Puskesmas Test',
            'alamat' => 'Jl. Test',
            'telepon' => '123456',
            'status' => true,
        ]);

        $school = School::create([
            'instansi_id' => $instansi->id,
            'nama_sekolah' => 'SD Test',
            'npsn' => '12345678',
        ]);
        
        $class = SchoolClass::create([
            'school_id' => $school->id,
            'nama_kelas' => '1A',
            'urutan' => 1,
        ]);

        $academicYear = AcademicYear::create([
            'nama' => '2026/2027',
            'aktif' => true,
        ]);

        $student = Student::create([
            'instansi_id' => $instansi->id,
            'school_id' => $school->id,
            'nama_lengkap' => 'John Doe',
            'nik' => '1234567890123456',
            'nisn' => '1234567890',
            'jenis_kelamin' => 'L',
            'tanggal_lahir' => '2018-05-10',
            'aktif' => true,
        ]);

        $history = StudentClassHistory::create([
            'student_id' => $student->id,
            'school_id' => $school->id,
            'school_class_id' => $class->id,
            'academic_year_id' => $academicYear->id,
            'semester' => 'Ganjil',
            'aktif' => true,
        ]);

        // Create abnormal PemeriksaanGizi (Sangat Kurus)
        $pemeriksaan = PemeriksaanGizi::create([
            'student_class_history_id' => $history->id,
            'tanggal_pemeriksaan' => '2026-06-23',
            'berat_badan' => 15.5,
            'tinggi_badan' => 110,
            'imt' => 12.8,
            'status_gizi' => 'Sangat Kurus',
            'dirujuk_ke_fasyankes' => 'N',
        ]);

        // Invoke ReferralService sync
        $service = app(ReferralService::class);
        $referral = $service->syncReferral($pemeriksaan);

        $this->assertNotNull($referral);
        $this->assertEquals('Belum Dirujuk', $referral->status_rujukan);
        $this->assertEquals('Status Gizi Sangat Kurus', $referral->alasan_rujukan);

        // Try updating status
        $user = User::factory()->create();
        $user->assignRole('super_admin');

        $response = $this->actingAs($user)->putJson("/api/referrals/{$referral->id}/status", [
            'status_rujukan' => 'Sudah Dirujuk',
            'catatan' => 'Siswa sudah diantar ke Puskesmas'
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('referrals', [
            'id' => $referral->id,
            'status_rujukan' => 'Sudah Dirujuk',
            'catatan_tindak_lanjut' => 'Siswa sudah diantar ke Puskesmas'
        ]);

        $this->assertDatabaseHas('referral_status_histories', [
            'referral_id' => $referral->id,
            'status_lama' => 'Belum Dirujuk',
            'status_baru' => 'Sudah Dirujuk',
            'user_id' => $user->id,
            'catatan' => 'Siswa sudah diantar ke Puskesmas'
        ]);
    }
}
