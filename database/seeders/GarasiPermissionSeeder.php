<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GarasiPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'garasi.view',
            'garasi.activity.view',
            'garasi.activity.create',
            'garasi.activity.update',
            'garasi.activity.delete',
            'garasi.participant.view',
            'garasi.participant.create',
            'garasi.participant.update',
            'garasi.screening.view',
            'garasi.screening.create',
            'garasi.screening.update',
            'garasi.education.view',
            'garasi.education.create',
            'garasi.education.update',
            'garasi.referral.view',
            'garasi.referral.create',
            'garasi.referral.update',
            'garasi.report.view',
            'garasi.report.export',
        ];

        foreach ($permissions as $permission) {
            \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $permission]);
        }

        $superAdmin = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'super_admin']);
        $superAdmin->givePermissionTo($permissions);

        $adminDinkes = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin_dinkes']);
        $adminDinkes->givePermissionTo([
            'garasi.view',
            'garasi.activity.view',
            'garasi.participant.view',
            'garasi.screening.view',
            'garasi.education.view',
            'garasi.referral.view',
            'garasi.report.view',
            'garasi.report.export',
        ]);

        $adminKecamatan = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin_kecamatan']);
        $adminKecamatan->givePermissionTo([
            'garasi.view',
            'garasi.activity.view',
            'garasi.participant.view',
            'garasi.screening.view',
            'garasi.education.view',
            'garasi.referral.view',
            'garasi.report.view',
            'garasi.report.export',
        ]);

        $adminPuskesmas = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin_instansi']);
        $adminPuskesmas->givePermissionTo([
            'garasi.view',
            'garasi.activity.view',
            'garasi.participant.view',
            'garasi.screening.view',
            'garasi.education.view',
            'garasi.referral.view',
            'garasi.report.view',
            'garasi.report.export',
        ]);

        $petugasPosyandu = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'petugas_posyandu']);
        $petugasPosyandu->givePermissionTo([
            'garasi.view',
            'garasi.activity.view',
            'garasi.activity.create',
            'garasi.activity.update',
            'garasi.activity.delete',
            'garasi.participant.view',
            'garasi.participant.create',
            'garasi.participant.update',
            'garasi.screening.view',
            'garasi.screening.create',
            'garasi.screening.update',
            'garasi.education.view',
            'garasi.education.create',
            'garasi.education.update',
            'garasi.referral.view',
            'garasi.referral.create',
            'garasi.referral.update',
        ]);
    }
}
