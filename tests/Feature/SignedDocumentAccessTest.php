<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\Region;
use Database\Seeders\CountryAndStageSeeder;
use Database\Seeders\FocusAreaSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Database\Seeders\StateDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class SignedDocumentAccessTest extends TestCase
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

    public function test_valid_signed_url_allows_download_and_increments_counter(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('documents/test.pdf', '%PDF-1.4 test document content');

        $region = Region::first();
        $doc = Document::create([
            'title' => 'Official Test Gazette',
            'region_id' => $region->id,
            'document_type' => 'ctc_enacted',
            'year' => 2026,
            'file_path' => 'documents/test.pdf',
            'file_name' => 'test.pdf',
            'file_size_bytes' => 1024,
            'mime_type' => 'application/pdf',
            'is_public' => true,
            'download_count' => 0,
        ]);

        $signedUrl = URL::temporarySignedRoute(
            'documents.download',
            now()->addMinutes(15),
            ['document' => $doc->uuid]
        );

        $response = $this->get($signedUrl);
        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition', 'attachment; filename=test.pdf');

        $this->assertEquals(1, $doc->fresh()->download_count);
    }

    public function test_unsigned_or_tampered_url_is_forbidden(): void
    {
        $region = Region::first();
        $doc = Document::create([
            'title' => 'Unsigned Gazette Test',
            'region_id' => $region->id,
            'document_type' => 'ctc_enacted',
            'year' => 2026,
            'file_path' => 'documents/test.pdf',
            'file_name' => 'test.pdf',
            'file_size_bytes' => 1024,
            'mime_type' => 'application/pdf',
            'is_public' => true,
        ]);

        // Attempting to access without valid signature
        $unsignedUrl = "/documents/{$doc->uuid}/download";
        $response = $this->get($unsignedUrl);
        $response->assertStatus(403);
    }
}
