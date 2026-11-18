<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\CountryAndStageSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolePermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
        $this->seed(CountryAndStageSeeder::class);
    }

    public function test_unauthenticated_user_redirected_from_admin(): void
    {
        $response = $this->get('/admin');
        $response->assertRedirect('/admin/login');
    }

    public function test_super_admin_can_access_admin_panel(): void
    {
        $admin = User::where('email', 'superadmin@youthpolicytracker.ng')->first();
        $this->actingAs($admin);

        $response = $this->get('/admin');
        $response->assertStatus(200);

        $responseUsers = $this->get('/admin/users');
        $responseUsers->assertStatus(200);
    }

    public function test_viewer_role_can_view_dashboard_but_has_restricted_access(): void
    {
        $viewer = User::where('email', 'viewer@youthpolicytracker.ng')->first();
        $this->actingAs($viewer);

        $response = $this->get('/admin');
        $response->assertStatus(200);

        $responseStatuses = $this->get('/admin/policy-statuses');
        $responseStatuses->assertStatus(200);

        // Viewer cannot access users resource
        $responseUsers = $this->get('/admin/users');
        $responseUsers->assertStatus(403);
    }
}
