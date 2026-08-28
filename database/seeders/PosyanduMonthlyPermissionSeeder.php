<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PosyanduMonthlyPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'posyandu_monthly.view',
            'posyandu_monthly.create',
            'posyandu_monthly.update',
            'posyandu_monthly.delete',
            'posyandu_monthly.export',
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);
        }

        $roles = Role::all();
        foreach ($roles as $role) {
            $role->givePermissionTo($permissions);
        }
    }
}
