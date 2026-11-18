<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Funder;
use App\Models\Organization;
use App\Models\PolicyStage;
use App\Models\PolicyStatus;
use App\Models\Region;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class StateDataSeeder extends Seeder
{
    public function run(): void
    {
        $country = Country::where('iso_code', 'NG')->firstOrFail();
        $stages = PolicyStage::where('country_id', $country->id)->get()->keyBy('name');

        // Canonical 37 entries
        $states = [
            // North Central
            [
                'name' => 'Benue',
                'zone' => 'North Central',
                'iso_code' => 'NG-BE',
                'grid_col' => 4,
                'grid_row' => 3,
                'stage' => 'Consultation',
                'orgs' => ['Benue Youth Forum', 'National Youth Council of Nigeria (NYCN)'],
                'funders' => [],
                'year' => '2026',
                'stalled' => false,
                'expired' => false,
            ],
            [
                'name' => 'FCT (Abuja)',
                'zone' => 'North Central',
                'iso_code' => 'NG-FC',
                'grid_col' => 2,
                'grid_row' => 3,
                'stage' => 'Validated',
                'orgs' => ['YouthHubAfrica'],
                'funders' => ['ActionAid'],
                'year' => '2024',
                'stalled' => false,
                'expired' => false,
            ],
            [
                'name' => 'Kogi',
                'zone' => 'North Central',
                'iso_code' => 'NG-KO',
                'grid_col' => 2,
                'grid_row' => 4,
                'stage' => 'No Verifiable Data',
                'orgs' => [],
                'funders' => [],
                'year' => null,
                'stalled' => false,
                'expired' => false,
            ],
            [
                'name' => 'Kwara',
                'zone' => 'North Central',
                'iso_code' => 'NG-KW',
                'grid_col' => 0,
                'grid_row' => 3,
                'stage' => 'Developing',
                'orgs' => ['United Nations Population Fund (UNFPA)'],
                'funders' => ['UNFPA'],
                'year' => '2025-2026',
                'stalled' => false,
                'expired' => false,
            ],
            [
                'name' => 'Nasarawa',
                'zone' => 'North Central',
                'iso_code' => 'NG-NA',
                'grid_col' => 3,
                'grid_row' => 3,
                'stage' => 'Validating',
                'orgs' => ['Connecting Villages Initiatives'],
                'funders' => ['Digital Democracy Initiatives'],
                'year' => '2025',
                'stalled' => false,
                'expired' => false,
            ],
            [
                'name' => 'Niger',
                'zone' => 'North Central',
                'iso_code' => 'NG-NI',
                'grid_col' => 1,
                'grid_row' => 2,
                'stage' => 'Validating',
                'orgs' => ['Glowing Minds Initiatives (GMI)'],
                'funders' => ['Nigeria Youth Futures Fund (NYFF)'],
                'year' => null,
                'stalled' => false,
                'expired' => false,
            ],
            [
                'name' => 'Plateau',
                'zone' => 'North Central',
                'iso_code' => 'NG-PL',
                'grid_col' => 3,
                'grid_row' => 2,
                'stage' => 'No Verifiable Data',
                'orgs' => [],
                'funders' => [],
                'year' => null,
                'stalled' => false,
                'expired' => false,
            ],

            // North East
            [
                'name' => 'Adamawa',
                'zone' => 'North East',
                'iso_code' => 'NG-AD',
                'grid_col' => 5,
                'grid_row' => 1,
                'stage' => 'Validated',
                'orgs' => ['Adamawa State Planning Commission', 'Ministry of Youth and Sports Development'],
                'funders' => ['UNFPA', 'European Union (via Spotlight Initiative/Sara Initiative)'],
                'year' => '2025',
                'stalled' => false,
                'expired' => false,
            ],
            [
                'name' => 'Bauchi',
                'zone' => 'North East',
                'iso_code' => 'NG-BA',
                'grid_col' => 3,
                'grid_row' => 1,
                'stage' => 'Co-Creating',
                'orgs' => ['Network for Youth Sustainable Initiatives (NGYouthSDGs)'],
                'funders' => [],
                'year' => null,
                'stalled' => false,
                'expired' => false,
            ],
            [
                'name' => 'Borno',
                'zone' => 'North East',
                'iso_code' => 'NG-BO',
                'grid_col' => 5,
                'grid_row' => 0,
                'stage' => 'No Verifiable Data',
                'orgs' => [],
                'funders' => [],
                'year' => null,
                'stalled' => false,
                'expired' => false,
            ],
            [
                'name' => 'Gombe',
                'zone' => 'North East',
                'iso_code' => 'NG-GO',
                'grid_col' => 4,
                'grid_row' => 1,
                'stage' => 'Implementing',
                'orgs' => ['Foundation for Community Empowerment Initiatives (FOCEI)'],
                'funders' => ['Nigeria Youth Futures Fund (NYFF)', 'LEAP Africa', 'Ford Foundation', 'MacArthur Foundation'],
                'year' => '2026',
                'stalled' => false,
                'expired' => false,
            ],
            [
                'name' => 'Taraba',
                'zone' => 'North East',
                'iso_code' => 'NG-TA',
                'grid_col' => 5,
                'grid_row' => 2,
                'stage' => 'No Verifiable Data',
                'orgs' => [],
                'funders' => [],
                'year' => null,
                'stalled' => false,
                'expired' => false,
            ],
            [
                'name' => 'Yobe',
                'zone' => 'North East',
                'iso_code' => 'NG-YO',
                'grid_col' => 4,
                'grid_row' => 0,
                'stage' => 'No Verifiable Data',
                'orgs' => [],
                'funders' => [],
                'year' => null,
                'stalled' => false,
                'expired' => false,
            ],

            // North West
            [
                'name' => 'Jigawa',
                'zone' => 'North West',
                'iso_code' => 'NG-JI',
                'grid_col' => 3,
                'grid_row' => 0,
                'stage' => 'No Verifiable Data',
                'orgs' => [],
                'funders' => [],
                'year' => null,
                'stalled' => false,
                'expired' => false,
            ],
            [
                'name' => 'Kaduna',
                'zone' => 'North West',
                'iso_code' => 'NG-KD',
                'grid_col' => 2,
                'grid_row' => 2,
                'stage' => 'Validated',
                'orgs' => ['Lafiya Programme (UK Aid)'],
                'funders' => ['Civic Impact for Sustainable Development Foundation (CISDEF)'],
                'year' => '2026',
                'stalled' => false,
                'expired' => false,
            ],
            [
                'name' => 'Kano',
                'zone' => 'North West',
                'iso_code' => 'NG-KN',
                'grid_col' => 2,
                'grid_row' => 1,
                'stage' => 'Validated',
                'orgs' => ['Youth Alive Foundation (YAF)'],
                'funders' => ['Civil Society Organizations (CSOs)', 'Caritas'],
                'year' => '2023-2027',
                'stalled' => false,
                'expired' => false,
            ],
            [
                'name' => 'Katsina',
                'zone' => 'North West',
                'iso_code' => 'NG-KT',
                'grid_col' => 2,
                'grid_row' => 0,
                'stage' => 'Developing',
                'orgs' => ['Bridge Connect Africa Initiative (BCAI)'],
                'funders' => ['Nigeria Youth Futures Fund (NYFF)'],
                'year' => '2026',
                'stalled' => false,
                'expired' => false,
            ],
            [
                'name' => 'Kebbi',
                'zone' => 'North West',
                'iso_code' => 'NG-KE',
                'grid_col' => 0,
                'grid_row' => 1,
                'stage' => 'Developing',
                'orgs' => ['Bridge Connect Africa Initiative (BCAI)'],
                'funders' => ['Nigeria Youth Futures Fund (NYFF)'],
                'year' => '2026',
                'stalled' => false,
                'expired' => false,
            ],
            [
                'name' => 'Sokoto',
                'zone' => 'North West',
                'iso_code' => 'NG-SO',
                'grid_col' => 1,
                'grid_row' => 0,
                'stage' => 'Implementing',
                'orgs' => ['Bridge Connect Africa Initiative (BCAI)'],
                'funders' => ['Nigeria Youth Futures Fund (NYFF)'],
                'year' => '2026',
                'stalled' => false,
                'expired' => false,
            ],
            [
                'name' => 'Zamfara',
                'zone' => 'North West',
                'iso_code' => 'NG-ZA',
                'grid_col' => 1,
                'grid_row' => 1,
                'stage' => 'Consultation',
                'orgs' => ['Bridge Connect Africa Initiative (BCAI)'],
                'funders' => ['Nigeria Youth Futures Fund (NYFF)'],
                'year' => '2026',
                'stalled' => false,
                'expired' => false,
            ],

            // South East
            [
                'name' => 'Abia',
                'zone' => 'South East',
                'iso_code' => 'NG-AB',
                'grid_col' => 4,
                'grid_row' => 5,
                'stage' => 'Validating',
                'orgs' => ['Youth Emancipation and Rehabilitation Foundation (YERF)'],
                'funders' => ['USAID', 'Youth Alive Foundation (YAF)'],
                'year' => '2022-2023',
                'stalled' => false,
                'expired' => false,
            ],
            [
                'name' => 'Anambra',
                'zone' => 'South East',
                'iso_code' => 'NG-AN',
                'grid_col' => 3,
                'grid_row' => 5,
                'stage' => 'No Verifiable Data',
                'orgs' => [],
                'funders' => [],
                'year' => null,
                'stalled' => false,
                'expired' => false,
            ],
            [
                'name' => 'Ebonyi',
                'zone' => 'South East',
                'iso_code' => 'NG-EB',
                'grid_col' => 4,
                'grid_row' => 4,
                'stage' => 'No Verifiable Data',
                'orgs' => [],
                'funders' => [],
                'year' => null,
                'stalled' => false,
                'expired' => false,
            ],
            [
                'name' => 'Enugu',
                'zone' => 'South East',
                'iso_code' => 'NG-EN',
                'grid_col' => 3,
                'grid_row' => 4,
                'stage' => 'Implementing',
                'orgs' => ['South Saharan Social Development Organisation (SSDO)'],
                'funders' => ['ActionAid'],
                'year' => '2025',
                'stalled' => false,
                'expired' => false,
            ],
            [
                'name' => 'Imo',
                'zone' => 'South East',
                'iso_code' => 'NG-IM',
                'grid_col' => 3,
                'grid_row' => 6,
                'stage' => 'Developing',
                'orgs' => ['Youth Awake for Better Society'],
                'funders' => ['Nigeria Youth Futures Fund (NYFF)'],
                'year' => '2026',
                'stalled' => false,
                'expired' => false,
            ],

            // South South
            [
                'name' => 'Akwa Ibom',
                'zone' => 'South South',
                'iso_code' => 'NG-AK',
                'grid_col' => 4,
                'grid_row' => 6,
                'stage' => 'Validated',
                'orgs' => ['Clement Isong Foundation (CIF)', 'Youth Pride'],
                'funders' => ['ActionAid'],
                'year' => '2025',
                'stalled' => false,
                'expired' => false,
            ],
            [
                'name' => 'Bayelsa',
                'zone' => 'South South',
                'iso_code' => 'NG-BY',
                'grid_col' => 1,
                'grid_row' => 6,
                'stage' => 'Validated',
                'orgs' => ['Bayelsa State Ministry of Youth Development'],
                'funders' => ['Partnership Initiatives in the Niger Delta (PIND)', 'Bayelsa State Ministry of Youth Development'],
                'year' => '2025',
                'stalled' => false,
                'expired' => false,
            ],
            [
                'name' => 'Cross River',
                'zone' => 'South South',
                'iso_code' => 'NG-CR',
                'grid_col' => 5,
                'grid_row' => 5,
                'stage' => 'No Verifiable Data',
                'orgs' => [],
                'funders' => [],
                'year' => null,
                'stalled' => false,
                'expired' => false,
            ],
            [
                'name' => 'Delta',
                'zone' => 'South South',
                'iso_code' => 'NG-DE',
                'grid_col' => 2,
                'grid_row' => 6,
                'stage' => 'No Verifiable Data',
                'orgs' => [],
                'funders' => [],
                'year' => null,
                'stalled' => false,
                'expired' => false,
            ],
            [
                'name' => 'Edo',
                'zone' => 'South South',
                'iso_code' => 'NG-ED',
                'grid_col' => 2,
                'grid_row' => 5,
                'stage' => 'Developing',
                'orgs' => ['Edo State Ministry of Youth and Humanitarian Affairs'],
                'funders' => ['GIZ'],
                'year' => '2022',
                'stalled' => true,
                'expired' => false,
                'stalled_reason' => 'Process paused pending transition and reconstitution of ministry technical advisory committee.',
            ],
            [
                'name' => 'Rivers',
                'zone' => 'South South',
                'iso_code' => 'NG-RI',
                'grid_col' => 3,
                'grid_row' => 6,
                'stage' => 'Implementing',
                'orgs' => ['Youth Alive Foundation (YAF)'],
                'funders' => ['Civil Society Organizations (CSOs)'],
                'year' => '2020-2022',
                'stalled' => false,
                'expired' => true,
                'expired_reason' => 'Policy implementation cycle (2020-2022) elapsed. Awaiting legislative renewal and new domestication framework.',
            ],

            // South West
            [
                'name' => 'Ekiti',
                'zone' => 'South West',
                'iso_code' => 'NG-EK',
                'grid_col' => 2,
                'grid_row' => 4,
                'stage' => 'Implementing',
                'orgs' => ['Ekiti State Ministry of Youth (EKSMY)'],
                'funders' => ['Ekiti State Ministry of Youth (EKSMY)'],
                'year' => '2024',
                'stalled' => false,
                'expired' => false,
            ],
            [
                'name' => 'Lagos',
                'zone' => 'South West',
                'iso_code' => 'NG-LA',
                'grid_col' => 0,
                'grid_row' => 6,
                'stage' => 'Implementing',
                'orgs' => ['Ministry of Youth and Social Development (MYSD)', 'Youth Alive Foundation (YAF)'],
                'funders' => ['LEAP Africa', 'Nigeria Youth Futures Fund (NYFF)'],
                'year' => '2024',
                'stalled' => false,
                'expired' => false,
            ],
            [
                'name' => 'Ogun',
                'zone' => 'South West',
                'iso_code' => 'NG-OG',
                'grid_col' => 0,
                'grid_row' => 5,
                'stage' => 'Developing',
                'orgs' => ['Ogun Youth Agenda'],
                'funders' => ['Nigeria Youth Futures Fund (NYFF)', 'LEAP Africa'],
                'year' => null,
                'stalled' => false,
                'expired' => false,
            ],
            [
                'name' => 'Ondo',
                'zone' => 'South West',
                'iso_code' => 'NG-ON',
                'grid_col' => 1,
                'grid_row' => 5,
                'stage' => 'Developing',
                'orgs' => ['PROMAD Foundation'],
                'funders' => ['European Union under Women and Youth Democratic Engagement (WYDE), with European Partnership for Democracy (EPD)'],
                'year' => '2025',
                'stalled' => false,
                'expired' => false,
            ],
            [
                'name' => 'Osun',
                'zone' => 'South West',
                'iso_code' => 'NG-OS',
                'grid_col' => 1,
                'grid_row' => 4,
                'stage' => 'Signed',
                'orgs' => ['Social Enterprise and Agricultural Development Initiative (SEAD Initiative)'],
                'funders' => [],
                'year' => '2026',
                'stalled' => false,
                'expired' => false,
            ],
            [
                'name' => 'Oyo',
                'zone' => 'South West',
                'iso_code' => 'NG-OY',
                'grid_col' => 0,
                'grid_row' => 4,
                'stage' => 'No Verifiable Data',
                'orgs' => [],
                'funders' => [],
                'year' => null,
                'stalled' => false,
                'expired' => false,
            ],
        ];

        foreach ($states as $stateData) {
            $region = Region::updateOrCreate(
                [
                    'country_id' => $country->id,
                    'name' => $stateData['name'],
                ],
                [
                    'slug' => Str::slug($stateData['name']),
                    'iso_code' => $stateData['iso_code'],
                    'zone' => $stateData['zone'],
                    'grid_col' => $stateData['grid_col'],
                    'grid_row' => $stateData['grid_row'],
                ]
            );

            // Determine stage model
            $stageName = $stateData['stage'];
            $stageModel = $stages[$stageName] ?? $stages['No Verifiable Data'];

            // Policy status
            PolicyStatus::updateOrCreate(
                ['region_id' => $region->id],
                [
                    'policy_stage_id' => $stageModel->id,
                    'is_stalled' => $stateData['stalled'],
                    'is_expired' => $stateData['expired'],
                    'stalled_reason' => $stateData['stalled_reason'] ?? null,
                    'expired_reason' => $stateData['expired_reason'] ?? null,
                    'year_commenced' => $stateData['year'],
                    'data_verified_at' => null, // Explicitly no fake verification dates
                    'source_notes' => null,     // Explicitly no fake source notes
                    'objectives_summary' => null, // Explicitly no fake summaries
                ]
            );

            // Connect Organizations
            $orgIds = [];
            foreach ($stateData['orgs'] as $orgName) {
                $orgName = trim($orgName);
                if (empty($orgName) || in_array($orgName, ['-', 'Information not available', 'Not specified'])) {
                    continue;
                }

                // Determine acronym and type
                $acronym = null;
                if (preg_match('/\(([^)]+)\)/', $orgName, $m)) {
                    $acronym = $m[1];
                }

                $type = 'cso';
                if (str_contains(strtolower($orgName), 'ministry') || str_contains(strtolower($orgName), 'commission')) {
                    $type = 'government';
                } elseif (str_contains(strtolower($orgName), 'united nations') || str_contains(strtolower($orgName), 'uk aid')) {
                    $type = 'international';
                }

                $org = Organization::firstOrCreate(
                    ['name' => $orgName],
                    [
                        'slug' => Str::slug($orgName),
                        'acronym' => $acronym,
                        'type' => $type,
                    ]
                );

                $region->organizations()->syncWithoutDetaching([
                    $org->id => ['role' => 'lead'],
                ]);
            }

            // Connect Funders
            foreach ($stateData['funders'] as $funderName) {
                $funderName = trim($funderName);
                if (empty($funderName) || in_array($funderName, ['-', 'Information not available', 'Not specified'])) {
                    continue;
                }

                $acronym = null;
                if (preg_match('/\(([^)]+)\)/', $funderName, $m)) {
                    $acronym = $m[1];
                }

                $type = 'philanthropic';
                if (str_contains(strtolower($funderName), 'ministry') || str_contains(strtolower($funderName), 'government')) {
                    $type = 'government';
                } elseif (str_contains(strtolower($funderName), 'european union') || str_contains(strtolower($funderName), 'usaid') || str_contains(strtolower($funderName), 'unfpa') || str_contains(strtolower($funderName), 'giz')) {
                    $type = 'bilateral';
                }

                $funder = Funder::firstOrCreate(
                    ['name' => $funderName],
                    [
                        'slug' => Str::slug($funderName),
                        'acronym' => $acronym,
                        'type' => $type,
                    ]
                );

                $region->funders()->syncWithoutDetaching([
                    $funder->id => ['grant_title' => null],
                ]);
            }
        }
    }
}
