<?php

namespace Tests\Feature;

use App\Filament\Pages\BulkDataImport;
use App\Models\PolicyStatus;
use App\Models\Region;
use App\Models\User;
use Database\Seeders\CountryAndStageSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Database\Seeders\StateDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use Tests\TestCase;

class BulkImportValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
        $this->seed(CountryAndStageSeeder::class);
        $this->seed(StateDataSeeder::class);
    }

    public function test_csv_import_validation_identifies_invalid_stages(): void
    {
        $admin = User::where('email', 'superadmin@youthpolicytracker.ng')->first();
        $this->actingAs($admin);

        $csvContent = "State,Zone,Stage,Policy_Cycle,Leading_Organizations,Funding_Partners,Is_Stalled,Stalled_Reason,Is_Expired,Expired_Reason,Verification_Date,Source_Notes,Objectives_Summary\n"
                    . "Kano,North West,NonExistentStage,2026,YAF,Caritas,0,,0,,,,\n";

        $file = UploadedFile::fake()->createWithContent('invalid_import.csv', $csvContent);

        Livewire::test(BulkDataImport::class)
            ->set('csvFile', $file)
            ->call('validateFile')
            ->assertSet('totalInvalid', 1)
            ->assertSet('totalValid', 0);
    }

    public function test_valid_csv_commits_successfully(): void
    {
        $admin = User::where('email', 'superadmin@youthpolicytracker.ng')->first();
        $this->actingAs($admin);

        $csvContent = "State,Zone,Stage,Policy_Cycle,Leading_Organizations,Funding_Partners,Is_Stalled,Stalled_Reason,Is_Expired,Expired_Reason,Verification_Date,Source_Notes,Objectives_Summary\n"
                    . "Kogi,North Central,Developing,2026,Kogi Youth Assembly,NYFF,0,,0,,2026-11-01,State Circular,Focus on vocational skills\n";

        $file = UploadedFile::fake()->createWithContent('valid_import.csv', $csvContent);

        Livewire::test(BulkDataImport::class)
            ->set('csvFile', $file)
            ->call('validateFile')
            ->assertSet('totalValid', 1)
            ->call('commitImport');

        $kogi = Region::where('name', 'Kogi')->first();
        $this->assertNotNull($kogi);
        $this->assertEquals('Developing', $kogi->policyStatus->stage->name);
        $this->assertEquals('2026-11-01', $kogi->policyStatus->data_verified_at->toDateString());
    }
}
