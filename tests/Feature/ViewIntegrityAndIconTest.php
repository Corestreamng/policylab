<?php

namespace Tests\Feature;

use Tests\TestCase;

class ViewIntegrityAndIconTest extends TestCase
{
    /**
     * Assert that no emoji Unicode characters exist in any Blade template.
     */
    public function test_views_contain_no_emojis(): void
    {
        $emojiPattern = '/[\x{1F600}-\x{1F64F}]|[\x{1F300}-\x{1F5FF}]|[\x{1F680}-\x{1F6FF}]|[\x{1F1E0}-\x{1F1FF}]|[\x{2702}-\x{27B0}]|[\x{1F900}-\x{1F9FF}]|[\x{1FA70}-\x{1FAFF}]|[\x{2600}-\x{26FF}]/u';

        $viewsPath = resource_path('views');
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($viewsPath));

        $violations = [];

        foreach ($files as $file) {
            if ($file->isDir() || !str_ends_with($file->getFilename(), '.blade.php')) {
                continue;
            }

            $content = file_get_contents($file->getPathname());
            if (preg_match_all($emojiPattern, $content, $matches)) {
                $violations[$file->getPathname()] = $matches[0];
            }
        }

        $this->assertEmpty(
            $violations,
            'Emojis found in Blade templates: ' . json_encode($violations)
        );
    }

    /**
     * Assert that every icon class used across Blade views exists in Font Awesome Free.
     */
    public function test_views_use_only_font_awesome_free_icons(): void
    {
        $cssPath = public_path('vendor/fontawesome/css/all.min.css');
        $this->assertFileExists($cssPath, 'Font Awesome Free CSS must be installed.');

        $cssContent = file_get_contents($cssPath);
        preg_match_all('/\.fa-([a-zA-Z0-9-]+)/', $cssContent, $cssMatches);
        $allowedIcons = array_flip($cssMatches[1] ?? []);

        $utilityClasses = array_flip([
            'fw', 'spin', 'pulse', '2x', '3x', '4x', '5x', 'lg', 'xs', 'sm', '2xs', 'xl', '2xl',
            'stack', 'inverse', 'rotate-90', 'rotate-180', 'rotate-270', 'flip-horizontal', 'flip-vertical',
            'border', 'pull-left', 'pull-right', 'solid', 'regular', 'brands', 'classic'
        ]);

        $viewsPath = resource_path('views');
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($viewsPath));

        $invalidIcons = [];

        foreach ($files as $file) {
            if ($file->isDir() || !str_ends_with($file->getFilename(), '.blade.php')) {
                continue;
            }

            $content = file_get_contents($file->getPathname());

            // Match all fa- classes
            if (preg_match_all('/fa[srbld]?\s+fa-([a-zA-Z0-9-]+)/', $content, $matches)) {
                foreach ($matches[1] as $icon) {
                    if (isset($utilityClasses[$icon])) {
                        continue;
                    }

                    if (!isset($allowedIcons[$icon])) {
                        $invalidIcons[$file->getFilename()][] = $icon;
                    }
                }
            }
        }

        $this->assertEmpty(
            $invalidIcons,
            'Pro or non-Free Font Awesome icons found in views: ' . json_encode($invalidIcons)
        );
    }

    /**
     * Assert that language files exist and contain translation strings for all supported locales.
     */
    public function test_language_files_exist_for_all_supported_locales(): void
    {
        $locales = ['en', 'fr', 'ha', 'yo', 'ig', 'ar'];

        foreach ($locales as $locale) {
            $path = base_path("lang/{$locale}/tracker.php");
            $this->assertFileExists($path, "Language file for {$locale} must exist.");

            $translations = include $path;
            $this->assertIsArray($translations, "Language file for {$locale} must return an array.");
            $this->assertArrayHasKey('app_name', $translations);
            $this->assertArrayHasKey('country_name', $translations);
            $this->assertArrayHasKey('nav_map', $translations);
        }
    }

    /**
     * Assert that the favicon set, PWA icons, and manifest are present and valid.
     */
    public function test_favicon_set_and_manifest_exist(): void
    {
        $this->assertFileExists(public_path('favicon.ico'));
        $this->assertGreaterThan(100, filesize(public_path('favicon.ico')));

        $this->assertFileExists(public_path('favicon.svg'));
        $this->assertFileExists(public_path('apple-touch-icon.png'));
        $this->assertFileExists(public_path('icons/icon-192x192.png'));
        $this->assertFileExists(public_path('icons/icon-512x512.png'));

        $manifestPath = public_path('manifest.json');
        $this->assertFileExists($manifestPath);
        $manifest = json_decode(file_get_contents($manifestPath), true);
        $this->assertIsArray($manifest);
        $this->assertArrayHasKey('icons', $manifest);
        $this->assertStringContainsString('placeholder', strtolower($manifest['description']));
    }

    /**
     * Assert that switching locale updates session and sets application locale.
     */
    public function test_locale_switcher_updates_session(): void
    {
        $response = $this->get(route('locale.switch', 'fr'));
        $response->assertSessionHas('app_locale', 'fr');

        $response = $this->get(route('locale.switch', 'ar'));
        $response->assertSessionHas('app_locale', 'ar');
    }
}
