<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = ['manage users', 'manage roles', 'create courses', 'edit own courses', 'publish own courses', 'view enrolled courses', 'view assigned courses', 'grade submissions', 'view courses', 'view reports'];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $superAdmin = Role::firstOrCreate(['name' => 'super-admin']);
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $instructor = Role::firstOrCreate(['name' => 'instructor']);
        $assistant = Role::firstOrCreate(['name' => 'assistant']);
        $auditor = Role::firstOrCreate(['name' => 'auditor']);
        $student = Role::firstOrCreate(['name' => 'student']);

        // Super Admin gets every permission listed above.
        $superAdmin->syncPermissions($permissions);

        // Admin gets the existing operational permissions, but not role management.
        $admin->syncPermissions(['manage users', 'create courses', 'edit own courses', 'publish own courses', 'view enrolled courses', 'view assigned courses', 'grade submissions', 'view courses', 'view reports']);

        $instructor->syncPermissions(['create courses', 'edit own courses', 'publish own courses']);

        $assistant->syncPermissions(['view assigned courses', 'grade submissions']);

        $auditor->syncPermissions(['view courses', 'view reports']);

        $student->syncPermissions(['view enrolled courses']);
    }
}
