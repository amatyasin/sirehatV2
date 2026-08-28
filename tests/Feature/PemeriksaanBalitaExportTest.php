<?php

namespace Tests\Feature;

use App\Exports\PemeriksaanBalitaExport;
use App\Models\Child;
use App\Models\PemeriksaanBalita;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class PemeriksaanBalitaExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_export_instantiates_and_returns_correct_headings_and_mapping(): void
    {
        $export = new PemeriksaanBalitaExport();

        $headings = $export->headings();
        $this->assertContains('Nama Anak', $headings);
        $this->assertContains('NIK Anak', $headings);
        $this->assertContains('Status Stunting', $headings);
        $this->assertContains('Dirujuk ke Fasyankes', $headings);

        $this->assertEquals('Pemeriksaan Balita & Apras', $export->title());
    }

    public function test_export_query_works_for_super_admin(): void
    {
        \Spatie\Permission\Models\Role::create(['name' => 'super_admin']);

        $user = User::factory()->create();
        $user->assignRole('super_admin');
        $this->actingAs($user);

        $export = new PemeriksaanBalitaExport();
        $query = $export->query();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Builder::class, $query);
    }
}
