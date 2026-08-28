<?php

namespace Tests\Feature;

use App\Exports\UkgmPemeriksaanExport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UkgmPemeriksaanExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_ukgm_export_headings_and_title(): void
    {
        $export = new UkgmPemeriksaanExport();

        $headings = $export->headings();
        $this->assertContains('Puskesmas', $headings);
        $this->assertContains('Posyandu', $headings);
        $this->assertContains('Nama Anak', $headings);
        $this->assertContains('Gigi Berlubang (Karies)', $headings);
        $this->assertContains('Skor def-t', $headings);
        $this->assertContains('Skor DMF-T', $headings);

        $this->assertEquals('Pemeriksaan UKGM', $export->title());
    }

    public function test_ukgm_export_query_for_super_admin(): void
    {
        \Spatie\Permission\Models\Role::create(['name' => 'super_admin']);

        $user = User::factory()->create();
        $user->assignRole('super_admin');
        $this->actingAs($user);

        $export = new UkgmPemeriksaanExport();
        $query = $export->query();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Builder::class, $query);
    }
}
