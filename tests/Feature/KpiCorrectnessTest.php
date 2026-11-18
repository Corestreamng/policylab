<?php

namespace Tests\Feature;

use App\Models\Funder;
use App\Models\Organization;
use App\Models\PolicyStage;
use App\Models\PolicyStatus;
use App\Models\Region;
use Database\Seeders\CountryAndStageSeeder;
use Database\Seeders\FocusAreaSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Database\Seeders\StateDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KpiCorrectnessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
        $this->seed(CountryAndStageSeeder::class);
        $this->seed(FocusAreaSeeder::class);
        $this->seed(StateDataSeeder::class);
    }

    public function test_total_states_count_is_exactly_37(): void
    {
        $this->assertEquals(37, Region::count());
    }

    public function test_stage_distribution_matches_real_seed_data_exactly(): void
    {
        $countsByOrder = [];
        $stages = PolicyStage::orderBy('sequence_order')->get();

        foreach ($stages as $stage) {
            $countsByOrder[$stage->sequence_order] = PolicyStatus::where('policy_stage_id', $stage->id)->count();
        }

        // Real expected values from the client brief
        $this->assertEquals(11, $countsByOrder[0], 'Stage 0 (No Verifiable Data) must equal 11');
        $this->assertEquals(7, $countsByOrder[1], 'Stage 1 (Developing) must equal 7');
        $this->assertEquals(2, $countsByOrder[2], 'Stage 2 (Consultation) must equal 2');
        $this->assertEquals(1, $countsByOrder[3], 'Stage 3 (Co-Creating) must equal 1');
        $this->assertEquals(3, $countsByOrder[4], 'Stage 4 (Validating) must equal 3');
        $this->assertEquals(6, $countsByOrder[5], 'Stage 5 (Validated) must equal 6');
        $this->assertEquals(1, $countsByOrder[6], 'Stage 6 (Signed/Enacted) must equal 1');
        $this->assertEquals(6, $countsByOrder[7], 'Stage 7 (Implementing) must equal 6');
    }

    public function test_stalled_and_expired_flags_match_specification(): void
    {
        $stalledStates = Region::whereHas('policyStatus', fn($q) => $q->where('is_stalled', true))->get();
        $this->assertCount(1, $stalledStates);
        $this->assertEquals('Edo', $stalledStates->first()->name);

        $expiredStates = Region::whereHas('policyStatus', fn($q) => $q->where('is_expired', true))->get();
        $this->assertCount(1, $expiredStates);
        $this->assertEquals('Rivers', $expiredStates->first()->name);
    }

    public function test_deduplicated_stakeholders_counts(): void
    {
        $this->assertEquals(25, Organization::count(), 'Deduplicated organisations count must be 25');
        $this->assertEquals(18, Funder::count(), 'Deduplicated funding partners count must be 18');
    }
}
