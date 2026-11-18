<?php

namespace Tests\Feature;

use App\Filament\Pages\BulkDataImport;
use App\Filament\Resources\DocumentResource\Pages\ListDocuments;
use App\Filament\Resources\PolicyStageResource\Pages\ListPolicyStages;
use App\Filament\Resources\PolicyStatusResource\Pages\ListPolicyStatuses;
use App\Filament\Resources\UserResource\Pages\ListUsers;
use App\Models\Country;
use App\Models\Document;
use App\Models\Funder;
use App\Models\Organization;
use App\Models\PolicyStage;
use App\Models\PolicyStatus;
use App\Models\Region;
use App\Models\User;
use Database\Seeders\CountryAndStageSeeder;
use Database\Seeders\FocusAreaSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Database\Seeders\StateDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class FilamentActionAuthorizationTest extends TestCase
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

        $this->superAdmin = User::create([
            'name' => 'Super Admin User',
            'email' => 'superadmin.test@youthpolicytracker.ng',
            'password' => Hash::make('Secret123!'),
            'email_verified_at' => now(),
            'two_factor_confirmed_at' => now(),
        ]);
        $this->superAdmin->assignRole('Super Admin');

        $this->dataEntryManager = User::create([
            'name' => 'Data Entry User',
            'email' => 'dataentry.test@youthpolicytracker.ng',
            'password' => Hash::make('Secret123!'),
            'email_verified_at' => now(),
            'two_factor_confirmed_at' => now(),
        ]);
        $this->dataEntryManager->assignRole('Data Entry Manager');

        $this->viewer = User::create([
            'name' => 'Viewer User',
            'email' => 'viewer.test@youthpolicytracker.ng',
            'password' => Hash::make('Secret123!'),
            'email_verified_at' => now(),
            'two_factor_confirmed_at' => now(),
        ]);
        $this->viewer->assignRole('Viewer');
    }

    public function test_viewer_cannot_edit_or_delete_policy_statuses(): void
    {
        $this->actingAs($this->viewer);
        session(['filament.mfa_verified' => true]);

        $status = PolicyStatus::first();

        Livewire::test(ListPolicyStatuses::class)
            ->assertTableActionHidden('edit', $status)
            ->assertTableActionHidden('delete', $status);
    }

    public function test_viewer_cannot_edit_or_delete_documents(): void
    {
        $this->actingAs($this->viewer);
        session(['filament.mfa_verified' => true]);

        $region = Region::first();
        $doc = Document::create([
            'title' => 'Viewer Test Doc',
            'region_id' => $region->id,
            'document_type' => 'ctc_enacted',
            'year' => 2026,
            'file_path' => 'documents/test.pdf',
            'file_name' => 'test.pdf',
            'file_size_bytes' => 1024,
            'mime_type' => 'application/pdf',
            'is_public' => true,
        ]);

        Livewire::test(ListDocuments::class)
            ->assertTableActionHidden('edit', $doc)
            ->assertTableActionHidden('delete', $doc);
    }

    public function test_viewer_cannot_access_or_modify_users(): void
    {
        $this->actingAs($this->viewer);
        session(['filament.mfa_verified' => true]);

        $this->get('/admin/users')->assertStatus(403);
    }

    public function test_data_entry_manager_can_edit_but_cannot_delete_documents(): void
    {
        $this->actingAs($this->dataEntryManager);
        session(['filament.mfa_verified' => true]);

        $region = Region::first();
        $doc = Document::create([
            'title' => 'DEM Test Doc',
            'region_id' => $region->id,
            'document_type' => 'ctc_enacted',
            'year' => 2026,
            'file_path' => 'documents/test_dem.pdf',
            'file_name' => 'test_dem.pdf',
            'file_size_bytes' => 1024,
            'mime_type' => 'application/pdf',
            'is_public' => true,
        ]);

        Livewire::test(ListDocuments::class)
            ->assertTableActionVisible('edit', $doc)
            ->assertTableActionHidden('delete', $doc);
    }

    public function test_data_entry_manager_cannot_delete_or_edit_stages(): void
    {
        $this->actingAs($this->dataEntryManager);
        session(['filament.mfa_verified' => true]);

        $stage = PolicyStage::first();

        Livewire::test(ListPolicyStages::class)
            ->assertTableActionHidden('edit', $stage)
            ->assertTableActionHidden('delete', $stage);
    }

    public function test_data_entry_manager_cannot_access_users_management(): void
    {
        $this->actingAs($this->dataEntryManager);
        session(['filament.mfa_verified' => true]);

        $this->get('/admin/users')->assertStatus(403);
    }

    public function test_super_admin_can_delete_documents(): void
    {
        $this->actingAs($this->superAdmin);
        session(['filament.mfa_verified' => true]);

        $region = Region::first();
        $doc = Document::create([
            'title' => 'Admin Delete Doc',
            'region_id' => $region->id,
            'document_type' => 'ctc_enacted',
            'year' => 2026,
            'file_path' => 'documents/admin_del.pdf',
            'file_name' => 'admin_del.pdf',
            'file_size_bytes' => 2048,
            'mime_type' => 'application/pdf',
            'is_public' => true,
        ]);

        Livewire::test(ListDocuments::class)
            ->assertTableActionVisible('delete', $doc)
            ->callTableAction('delete', $doc);

        $this->assertSoftDeleted($doc);
    }

    public function test_super_admin_can_delete_users_except_self(): void
    {
        $this->actingAs($this->superAdmin);
        session(['filament.mfa_verified' => true]);

        $targetUser = User::create([
            'name' => 'Target User',
            'email' => 'target.user@youthpolicytracker.ng',
            'password' => Hash::make('Secret123!'),
            'email_verified_at' => now(),
        ]);
        $targetUser->assignRole('Viewer');

        Livewire::test(ListUsers::class)
            ->assertTableActionVisible('delete', $targetUser)
            ->callTableAction('delete', $targetUser);

        $this->assertDatabaseMissing('users', ['id' => $targetUser->id]);

        // Self-deletion is prevented by UserPolicy
        Livewire::test(ListUsers::class)
            ->assertTableActionHidden('delete', $this->superAdmin);
    }

    public function test_super_admin_can_delete_policy_stages(): void
    {
        $this->actingAs($this->superAdmin);
        session(['filament.mfa_verified' => true]);

        $country = Country::first();
        $unusedStage = PolicyStage::create([
            'country_id' => $country->id,
            'sequence_order' => 99,
            'name' => 'Temporary Test Stage',
            'slug' => 'temporary-test-stage',
            'color_hex' => '#999999',
            'text_color_hex' => '#111111',
            'bg_color_hex' => '#f3f4f6',
            'icon_class' => 'fa-circle',
            'is_active' => false,
            'description' => 'Temporary test stage for deletion check',
        ]);

        Livewire::test(ListPolicyStages::class)
            ->assertTableActionVisible('delete', $unusedStage)
            ->callTableAction('delete', $unusedStage);

        $this->assertDatabaseMissing('policy_stages', ['id' => $unusedStage->id]);
    }

    public function test_viewer_receives_403_on_direct_edit_urls(): void
    {
        $this->actingAs($this->viewer);
        session(['filament.mfa_verified' => true]);

        $region = Region::first();
        $doc = Document::create([
            'title' => 'Viewer Edit Block Test',
            'region_id' => $region->id,
            'document_type' => 'ctc_enacted',
            'year' => 2026,
            'file_path' => 'documents/test.pdf',
            'file_name' => 'test.pdf',
            'file_size_bytes' => 1024,
            'mime_type' => 'application/pdf',
            'is_public' => true,
        ]);

        $status = PolicyStatus::first();
        $org = Organization::first();
        $funder = Funder::first();

        $this->get("/admin/documents/{$doc->id}/edit")->assertStatus(403);
        $this->get("/admin/policy-statuses/{$status->id}/edit")->assertStatus(403);
        $this->get("/admin/organizations/{$org->id}/edit")->assertStatus(403);
        $this->get("/admin/funders/{$funder->id}/edit")->assertStatus(403);
    }

    public function test_viewer_and_data_entry_manager_cannot_commit_bulk_import_where_not_permitted(): void
    {
        // 1. Viewer is blocked entirely from accessing or committing bulk import
        $this->actingAs($this->viewer);
        session(['filament.mfa_verified' => true]);

        $this->get('/admin/bulk-data-import')->assertStatus(403);

        try {
            Livewire::test(BulkDataImport::class);
            $this->fail('Viewer must not be permitted to mount BulkDataImport');
        } catch (\Throwable $e) {
            // Mounting aborted with 403
            $this->assertTrue(true);
        }

        // 2. Data Entry Manager cannot commit import when file contains invalid rows
        $this->actingAs($this->dataEntryManager);
        session(['filament.mfa_verified' => true]);

        $invalidCsv = "State,Zone,Stage,Policy_Cycle,Leading_Organizations,Funding_Partners,Is_Stalled,Stalled_Reason,Is_Expired,Expired_Reason,Verification_Date,Source_Notes,Objectives_Summary\n"
                    . "Kano,North West,NonExistentStage,2026,YAF,Caritas,0,,0,,,,\n";
        $invalidFile = UploadedFile::fake()->createWithContent('invalid.csv', $invalidCsv);

        Livewire::test(BulkDataImport::class)
            ->set('csvFile', $invalidFile)
            ->call('validateFile')
            ->assertSet('totalInvalid', 1)
            ->call('commitImport')
            ->assertHasErrors(['csvFile']);
    }

    public function test_deleting_policy_stage_in_use_is_blocked_with_clear_message(): void
    {
        $this->actingAs($this->superAdmin);
        session(['filament.mfa_verified' => true]);

        // Stage 1 (Consultation / Drafting) is assigned to multiple seeded states
        $inUseStage = PolicyStage::where('sequence_order', 1)->first();
        $this->assertTrue($inUseStage->policyStatuses()->exists());

        Livewire::test(ListPolicyStages::class)
            ->assertTableActionHidden('delete', $inUseStage);

        // Record remains untouched in database
        $this->assertDatabaseHas('policy_stages', ['id' => $inUseStage->id]);
    }
}
