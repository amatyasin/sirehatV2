<?php

namespace Tests\Feature;

use App\Exports\ChildrenExport;
use App\Models\Child;
use App\Models\Instansi;
use App\Models\Posyandu;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ChildrenTableRoleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'super_admin']);
        Role::create(['name' => 'admin_dinkes']);
        Role::create(['name' => 'admin_instansi']);
        Role::create(['name' => 'petugas_posyandu']);
    }

    public function test_petugas_posyandu_query_and_export_are_scoped_strictly_to_their_posyandu(): void
    {
        $puskesmas = Instansi::create(['nama_instansi' => 'Puskesmas Mawar', 'jenis' => 'puskesmas']);
        $posyanduA = Posyandu::create(['instansi_id' => $puskesmas->id, 'nama_posyandu' => 'Posyandu Mawar 1']);
        $posyanduB = Posyandu::create(['instansi_id' => $puskesmas->id, 'nama_posyandu' => 'Posyandu Mawar 2']);

        $childA = Child::create([
            'instansi_id' => $puskesmas->id,
            'posyandu_id' => $posyanduA->id,
            'nama_lengkap' => 'Anak Posyandu A',
            'nik' => '3201010101010001',
            'jenis_kelamin' => 'L',
            'aktif' => true,
        ]);

        $childB = Child::create([
            'instansi_id' => $puskesmas->id,
            'posyandu_id' => $posyanduB->id,
            'nama_lengkap' => 'Anak Posyandu B',
            'nik' => '3201010101010002',
            'jenis_kelamin' => 'P',
            'aktif' => true,
        ]);

        $userPosyanduA = User::factory()->create(['posyandu_id' => $posyanduA->id, 'instansi_id' => $puskesmas->id]);
        $userPosyanduA->assignRole('petugas_posyandu');

        $this->actingAs($userPosyanduA);

        // Check Eloquent Query Scoping
        $queryResult = \App\Filament\Resources\Children\ChildResource::getEloquentQuery()->get();
        $this->assertCount(1, $queryResult);
        $this->assertEquals($childA->id, $queryResult->first()->id);

        // Check Export Scoping
        $exportQuery = (new ChildrenExport)->query()->get();
        $this->assertCount(1, $exportQuery);
        $this->assertEquals($childA->id, $exportQuery->first()->id);
    }
}
