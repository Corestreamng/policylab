<?php

namespace Tests\Feature;

use App\Livewire\PolicyTracker;
use Database\Seeders\CountryAndStageSeeder;
use Database\Seeders\FocusAreaSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Database\Seeders\StateDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PublicTrackerFiltersTest extends TestCase
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

    public function test_tracker_page_loads_with_status_200(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('YOUTH POLICY TRACKER');
        $response->assertSee('Signed / Enacted');
    }

    public function test_filter_by_zone_filters_correct_states(): void
    {
        Livewire::test(PolicyTracker::class)
            ->set('selectedZone', 'North West')
            ->assertViewHas('filteredStates', function ($states) {
                $names = $states->pluck('name')->toArray();
                return count($names) === 7
                    && in_array('Kano', $names)
                    && in_array('Kaduna', $names)
                    && in_array('Sokoto', $names)
                    && !in_array('Lagos', $names);
            });
    }

    public function test_filter_by_stage_signed_returns_osun(): void
    {
        Livewire::test(PolicyTracker::class)
            ->set('selectedStage', '6')
            ->assertViewHas('filteredStates', function ($states) {
                $names = $states->pluck('name')->toArray();
                return count($names) === 1 && $names[0] === 'Osun';
            });
    }

    public function test_filter_by_stalled_returns_edo(): void
    {
        Livewire::test(PolicyTracker::class)
            ->set('stalledOnly', true)
            ->assertViewHas('filteredStates', function ($states) {
                $names = $states->pluck('name')->toArray();
                return count($names) === 1 && $names[0] === 'Edo';
            });
    }

    public function test_filter_by_expired_returns_rivers(): void
    {
        Livewire::test(PolicyTracker::class)
            ->set('expiredOnly', true)
            ->assertViewHas('filteredStates', function ($states) {
                $names = $states->pluck('name')->toArray();
                return count($names) === 1 && $names[0] === 'Rivers';
            });
    }

    public function test_free_text_search_for_partner(): void
    {
        Livewire::test(PolicyTracker::class)
            ->set('search', 'BCAI')
            ->assertViewHas('filteredStates', function ($states) {
                $names = $states->pluck('name')->toArray();
                return count($names) === 4
                    && in_array('Sokoto', $names)
                    && in_array('Katsina', $names)
                    && in_array('Kebbi', $names)
                    && in_array('Zamfara', $names);
            });
    }

    public function test_state_deep_link_opens_drawer(): void
    {
        $response = $this->get('/states/lagos');
        $response->assertStatus(200);
        $response->assertSee('Lagos State Policy Dossier');
        $response->assertSee('Ministry of Youth and Social Development (MYSD)');
    }
}
