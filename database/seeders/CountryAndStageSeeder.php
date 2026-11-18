<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\PolicyStage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CountryAndStageSeeder extends Seeder
{
    public function run(): void
    {
        $country = Country::firstOrCreate(
            ['iso_code' => 'NG'],
            [
                'name' => 'Nigeria',
                'currency' => 'NGN',
                'phone_code' => '+234',
                'is_active' => true,
            ]
        );

        $stages = [
            [
                'sequence_order' => 0,
                'name' => 'No Verifiable Data',
                'color_hex' => '#64748B',
                'text_color_hex' => '#334155',
                'bg_color_hex' => '#F1F5F9',
                'icon_class' => 'fa-circle-question',
                'description' => 'No publicly verifiable documentation or formal gazette available for this state.',
            ],
            [
                'sequence_order' => 1,
                'name' => 'Developing',
                'color_hex' => '#E11D48',
                'text_color_hex' => '#BE123C',
                'bg_color_hex' => '#FFE4E6',
                'icon_class' => 'fa-file-pen',
                'description' => 'Drafting or policy formulation process is initiated by government or youth coalitions.',
            ],
            [
                'sequence_order' => 2,
                'name' => 'Consultation',
                'color_hex' => '#D97706',
                'text_color_hex' => '#92400E',
                'bg_color_hex' => '#FEF3C7',
                'icon_class' => 'fa-comments',
                'description' => 'Broad stakeholder engagements and youth dialogue sessions conducted across constituencies.',
            ],
            [
                'sequence_order' => 3,
                'name' => 'Co-Creating',
                'color_hex' => '#7C3AED',
                'text_color_hex' => '#6D28D9',
                'bg_color_hex' => '#EDE9FE',
                'icon_class' => 'fa-users-gear',
                'description' => 'Collaborative drafting between ministries, departments, agencies, and youth civil society organisations.',
            ],
            [
                'sequence_order' => 4,
                'name' => 'Validating',
                'color_hex' => '#2563EB',
                'text_color_hex' => '#1D4ED8',
                'bg_color_hex' => '#DBEAFE',
                'icon_class' => 'fa-clipboard-check',
                'description' => 'Draft policy is undergoing formal technical reviews and state validation workshops.',
            ],
            [
                'sequence_order' => 5,
                'name' => 'Validated',
                'color_hex' => '#0D9488',
                'text_color_hex' => '#0F766E',
                'bg_color_hex' => '#CCFBF1',
                'icon_class' => 'fa-stamp',
                'description' => 'Policy draft formally endorsed by stakeholders and submitted to state executive council.',
            ],
            [
                'sequence_order' => 6,
                'name' => 'Signed',
                'color_hex' => '#EA580C',
                'text_color_hex' => '#C2410C',
                'bg_color_hex' => '#FFEDD5',
                'icon_class' => 'fa-file-signature',
                'description' => 'Officially signed into law, enacted or gazetted by the State Executive Governor.',
            ],
            [
                'sequence_order' => 7,
                'name' => 'Implementing',
                'color_hex' => '#059669',
                'text_color_hex' => '#047857',
                'bg_color_hex' => '#D1FAE5',
                'icon_class' => 'fa-rocket',
                'description' => 'Active implementation framework and budgetary allocations operational across local government areas.',
            ],
        ];

        foreach ($stages as $stageData) {
            PolicyStage::updateOrCreate(
                [
                    'country_id' => $country->id,
                    'sequence_order' => $stageData['sequence_order'],
                ],
                [
                    'name' => $stageData['name'],
                    'slug' => Str::slug($stageData['name']),
                    'color_hex' => $stageData['color_hex'],
                    'text_color_hex' => $stageData['text_color_hex'],
                    'bg_color_hex' => $stageData['bg_color_hex'],
                    'icon_class' => $stageData['icon_class'],
                    'description' => $stageData['description'],
                    'is_active' => true,
                ]
            );
        }
    }
}
