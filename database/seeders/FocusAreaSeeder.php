<?php

namespace Database\Seeders;

use App\Models\FocusArea;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FocusAreaSeeder extends Seeder
{
    public function run(): void
    {
        $areas = [
            ['name' => 'Education & Digital Literacy', 'icon_class' => 'fa-graduation-cap', 'description' => 'Equipping young people with 21st century foundational skills and technology capability.'],
            ['name' => 'Employment & Enterprise', 'icon_class' => 'fa-briefcase', 'description' => 'Fostering youth entrepreneurship, access to capital, apprenticeships, and decent work.'],
            ['name' => 'Health & Wellbeing', 'icon_class' => 'fa-heart-pulse', 'description' => 'Expanding youth-friendly healthcare, mental wellbeing, sexual and reproductive health rights.'],
            ['name' => 'Civic Participation & Peace', 'icon_class' => 'fa-scale-balanced', 'description' => 'Enhancing youth inclusion in public decision-making, governance, and community security.'],
            ['name' => 'Climate Action & Environment', 'icon_class' => 'fa-leaf', 'description' => 'Engaging youth in sustainable agriculture, renewable energy, and ecological preservation.'],
            ['name' => 'Social Inclusion & Gender Equity', 'icon_class' => 'fa-hands-holding-child', 'description' => 'Targeting vulnerable youths, persons with disabilities, and young women.'],
        ];

        foreach ($areas as $area) {
            FocusArea::updateOrCreate(
                ['name' => $area['name']],
                [
                    'slug' => Str::slug($area['name']),
                    'icon_class' => $area['icon_class'],
                    'description' => $area['description'],
                ]
            );
        }
    }
}
