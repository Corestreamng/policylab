<?php

namespace Tests\Feature;

use App\Models\Region;
use Database\Seeders\CountryAndStageSeeder;
use Database\Seeders\StateDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SvgMapIntegrityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(CountryAndStageSeeder::class);
        $this->seed(StateDataSeeder::class);
    }

    public function test_svg_file_exists_in_public_maps(): void
    {
        $this->assertFileExists(public_path('maps/states.svg'));
    }

    public function test_all_37_slugs_in_svg_match_regions_table_exactly(): void
    {
        $svgContent = file_get_contents(public_path('maps/states.svg'));
        
        // Extract all state-{slug} IDs from the SVG paths
        preg_match_all('/id="state-([^"]+)"/', $svgContent, $matches);
        $svgSlugs = $matches[1];

        // Fetch all region slugs from the database
        $dbSlugs = Region::pluck('slug')->toArray();

        // 1. Assert exactly 37 state paths exist in SVG
        $this->assertCount(37, $svgSlugs, 'SVG must contain exactly 37 state boundary paths.');

        // 2. Assert exactly 37 regions exist in database
        $this->assertCount(37, $dbSlugs, 'Database must contain exactly 37 regions.');

        // 3. Assert zero differences between SVG and DB slugs
        $inSvgNotDb = array_diff($svgSlugs, $dbSlugs);
        $inDbNotSvg = array_diff($dbSlugs, $svgSlugs);

        $this->assertEmpty($inSvgNotDb, 'The following slugs are in SVG but missing in database: ' . implode(', ', $inSvgNotDb));
        $this->assertEmpty($inDbNotSvg, 'The following slugs are in database but missing in SVG: ' . implode(', ', $inDbNotSvg));

        // 4. Assert sorted arrays are identical
        sort($svgSlugs);
        sort($dbSlugs);
        $this->assertSame($dbSlugs, $svgSlugs, 'The set of 37 slugs in SVG and database must match exactly.');
    }

    public function test_svg_has_viewbox_and_data_attributes(): void
    {
        $svgContent = file_get_contents(public_path('maps/states.svg'));

        $this->assertStringContainsString('viewBox="0 0 800 650"', $svgContent);
        $this->assertStringContainsString('data-name="Lagos"', $svgContent);
        $this->assertStringContainsString('data-iso="NG-LA"', $svgContent);
        $this->assertStringContainsString('data-zone="South West"', $svgContent);
    }
}
