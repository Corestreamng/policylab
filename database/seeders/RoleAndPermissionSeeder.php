<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'view_any_policy_status',
            'view_policy_status',
            'create_policy_status',
            'update_policy_status',
            'delete_policy_status',
            'view_any_document',
            'view_document',
            'create_document',
            'update_document',
            'delete_document',
            'view_any_organization',
            'create_organization',
            'update_organization',
            'delete_organization',
            'view_any_funder',
            'create_funder',
            'update_funder',
            'delete_funder',
            'import_data',
            'export_data',
            'manage_users',
            'manage_roles',
            'view_audit_logs',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $superAdminRole->syncPermissions(Permission::all());

        $dataEntryRole = Role::firstOrCreate(['name' => 'Data Entry Manager', 'guard_name' => 'web']);
        $dataEntryRole->syncPermissions([
            'view_any_policy_status',
            'view_policy_status',
            'create_policy_status',
            'update_policy_status',
            'view_any_document',
            'view_document',
            'create_document',
            'update_document',
            'view_any_organization',
            'create_organization',
            'update_organization',
            'view_any_funder',
            'create_funder',
            'update_funder',
            'import_data',
            'export_data',
        ]);

        $viewerRole = Role::firstOrCreate(['name' => 'Viewer', 'guard_name' => 'web']);
        $viewerRole->syncPermissions([
            'view_any_policy_status',
            'view_policy_status',
            'view_any_document',
            'view_document',
            'view_any_organization',
            'view_any_funder',
            'export_data',
        ]);

        // Demo users seeded strictly in local environment only
        if (app()->environment('local')) {
            $admin = User::firstOrCreate(
                ['email' => 'superadmin@youthpolicytracker.ng'],
                [
                    'name' => 'GMI Super Admin (Local Dev)',
                    'password' => Hash::make('Password123!'),
                    'email_verified_at' => now(),
                ]
            );
            $admin->syncRoles(['Super Admin']);

            $editor = User::firstOrCreate(
                ['email' => 'editor@youthpolicytracker.ng'],
                [
                    'name' => 'Data Entry Manager (Local Dev)',
                    'password' => Hash::make('Password123!'),
                    'email_verified_at' => now(),
                ]
            );
            $editor->syncRoles(['Data Entry Manager']);

            $viewer = User::firstOrCreate(
                ['email' => 'viewer@youthpolicytracker.ng'],
                [
                    'name' => 'Viewer Reviewer (Local Dev)',
                    'password' => Hash::make('Password123!'),
                    'email_verified_at' => now(),
                ]
            );
            $viewer->syncRoles(['Viewer']);
        }
    }
}
