<?php

namespace Tests\Feature;

use Database\Seeders\CountryAndStageSeeder;
use Database\Seeders\FocusAreaSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Database\Seeders\StateDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiV1Test extends TestCase
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

    public function test_api_v1_states_returns_all_37_states(): void
    {
        $response = $this->getJson('/api/v1/states');
        $response->assertStatus(200);
        $response->assertJsonPath('meta.total_states', 37);
        $response->assertJsonStructure([
            'meta' => ['total_states', 'version', 'dataset_license', 'publisher', 'timestamp'],
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'slug',
                    'iso_code',
                    'zone',
                    'current_stage',
                    'is_stalled',
                    'is_expired',
                    'leading_organizations',
                    'funding_partners',
                ]
            ]
        ]);
    }

    public function test_api_v1_single_state_returns_dossier(): void
    {
        $response = $this->getJson('/api/v1/states/lagos');
        $response->assertStatus(200);
        $response->assertJsonPath('data.name', 'Lagos');
        $response->assertJsonPath('data.status.stage_name', 'Implementing');
        $response->assertJsonPath('data.status.stage_number', 7);
    }

    public function test_api_v1_kpis_endpoint_returns_real_figures(): void
    {
        $response = $this->getJson('/api/v1/kpis');
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'total_states' => 37,
                'signed' => 1,
                'implementing' => 6,
                'co_created_validating_validated' => 10,
                'active_organizations' => 25,
                'active_funders' => 18,
                'no_verifiable_data' => 11,
                'stalled' => 1,
                'expired' => 1,
            ]
        ]);
    }

    public function test_api_v1_export_csv_streams_file(): void
    {
        $response = $this->get('/api/v1/export/csv');
        $response->assertStatus(200);
        $this->assertStringContainsString('text/csv', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('youth_policy_tracker_nigeria', $response->headers->get('Content-Disposition'));
    }

    public function test_browser_navigation_to_api_states_redirects_to_interactive_docs(): void
    {
        $response = $this->withHeaders([
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'sec-fetch-dest' => 'document',
        ])->get('/api/v1/states');

        $response->assertRedirect('/api/docs?endpoint=states');
    }

    public function test_browser_navigation_with_format_json_returns_raw_json(): void
    {
        $response = $this->withHeaders([
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'sec-fetch-dest' => 'document',
        ])->get('/api/v1/states?format=json');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/json');
        $response->assertJsonPath('meta.total_states', 37);
    }

    public function test_browser_navigation_to_single_state_redirects_to_docs_with_state(): void
    {
        $response = $this->withHeaders([
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'sec-fetch-dest' => 'document',
        ])->get('/api/v1/states/kano');

        $response->assertRedirect('/api/docs?endpoint=states-slug&state=kano');
    }

    public function test_api_documentation_page_loads_with_explorer_and_endpoints(): void
    {
        $response = $this->get('/api/docs');
        $response->assertStatus(200);
        $response->assertSee('Public REST API &amp; Developer Hub', false);
        $response->assertSee('/api/v1/states');
        $response->assertSee('/api/v1/states/{slug}');
        $response->assertSee('/api/v1/stages');
        $response->assertSee('/api/v1/kpis');
        $response->assertSee('/api/v1/export/csv');
        $response->assertSee('Glowing Minds Initiatives (GMI)');
    }

    public function test_api_shortcut_url_routes_to_docs(): void
    {
        $response = $this->get('/api');
        $response->assertStatus(200);
        $response->assertSee('Public REST API &amp; Developer Hub', false);
    }
}
