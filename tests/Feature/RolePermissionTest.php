<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\Funder;
use App\Models\Organization;
use App\Models\PolicyStatus;
use App\Models\Region;
use App\Models\User;
use Database\Seeders\CountryAndStageSeeder;
use Database\Seeders\FocusAreaSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Database\Seeders\StateDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RolePermissionTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $dataEntryManager;
    protected User $viewer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
        $this->seed(CountryAndStageSeeder::class);
        $this->seed(FocusAreaSeeder::class);
        $this->seed(StateDataSeeder::class);

        // Explicitly create test users with respective roles
        $this->superAdmin = User::create([
            'name' => 'Super Administrator',
            'email' => 'admin-test@youthpolicytracker.ng',
            'password' => Hash::make('Secret123!'),
            'email_verified_at' => now(),
        ]);
        $this->superAdmin->assignRole('Super Admin');

        $this->dataEntryManager = User::create([
            'name' => 'Data Entry Officer',
            'email' => 'editor-test@youthpolicytracker.ng',
            'password' => Hash::make('Secret123!'),
            'email_verified_at' => now(),
        ]);
        $this->dataEntryManager->assignRole('Data Entry Manager');

        $this->viewer = User::create([
            'name' => 'Auditor Viewer',
            'email' => 'viewer-test@youthpolicytracker.ng',
            'password' => Hash::make('Secret123!'),
            'email_verified_at' => now(),
        ]);
        $this->viewer->assignRole('Viewer');
    }

    public function test_unauthenticated_guests_are_redirected_to_login(): void
    {
        $restrictedUrls = [
            '/admin',
            '/admin/policy-statuses',
            '/admin/documents',
            '/admin/organizations',
            '/admin/funders',
            '/admin/users',
            '/admin/audit-logs',
            '/admin/bulk-data-import',
        ];

        foreach ($restrictedUrls as $url) {
            $response = $this->get($url);
            $response->assertRedirect('/admin/login');
        }
    }

    public function test_super_admin_has_unrestricted_access_to_all_resources(): void
    {
        $this->actingAs($this->superAdmin);

        $allowedUrls = [
            '/admin',
            '/admin/policy-statuses',
            '/admin/policy-statuses/create',
            '/admin/documents',
            '/admin/documents/create',
            '/admin/organizations',
            '/admin/organizations/create',
            '/admin/funders',
            '/admin/funders/create',
            '/admin/users',
            '/admin/users/create',
            '/admin/audit-logs',
            '/admin/bulk-data-import',
        ];

        foreach ($allowedUrls as $url) {
            $response = $this->get($url);
            $response->assertStatus(200, "Super Admin should have 200 access to {$url}");
        }
    }

    public function test_data_entry_manager_allowed_actions(): void
    {
        $this->actingAs($this->dataEntryManager);

        $allowedUrls = [
            '/admin',
            '/admin/policy-statuses',
            '/admin/policy-statuses/create',
            '/admin/documents',
            '/admin/documents/create',
            '/admin/organizations',
            '/admin/organizations/create',
            '/admin/funders',
            '/admin/funders/create',
            '/admin/bulk-data-import',
        ];

        foreach ($allowedUrls as $url) {
            $response = $this->get($url);
            $response->assertStatus(200, "Data Entry Manager should have 200 access to {$url}");
        }
    }

    public function test_data_entry_manager_denied_security_and_user_governance(): void
    {
        $this->actingAs($this->dataEntryManager);

        $deniedUrls = [
            '/admin/users',
            '/admin/users/create',
            '/admin/audit-logs',
        ];

        foreach ($deniedUrls as $url) {
            $response = $this->get($url);
            $response->assertStatus(403, "Data Entry Manager must be forbidden (403) from {$url}");
        }
    }

    public function test_viewer_allowed_read_only_access(): void
    {
        $this->actingAs($this->viewer);

        $allowedReadUrls = [
            '/admin',
            '/admin/policy-statuses',
            '/admin/documents',
            '/admin/organizations',
            '/admin/funders',
        ];

        foreach ($allowedReadUrls as $url) {
            $response = $this->get($url);
            $response->assertStatus(200, "Viewer should have read-only access (200) to {$url}");
        }
    }

    public function test_viewer_denied_mutation_and_management_actions(): void
    {
        $this->actingAs($this->viewer);

        $deniedActionUrls = [
            '/admin/policy-statuses/create',
            '/admin/documents/create',
            '/admin/organizations/create',
            '/admin/funders/create',
            '/admin/users',
            '/admin/users/create',
            '/admin/audit-logs',
        ];

        foreach ($deniedActionUrls as $url) {
            $response = $this->get($url);
            $response->assertStatus(403, "Viewer must be forbidden (403) from mutation route {$url}");
        }
    }
}
