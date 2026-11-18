<?php

namespace Tests\Feature;

use App\Livewire\Admin\TwoFactorChallenge;
use App\Livewire\PolicyTracker;
use App\Models\User;
use App\Services\TotpService;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;
use Tests\TestCase;

class FilamentTwoFactorAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected TotpService $totpService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
        $this->totpService = new TotpService();

        $this->admin = User::create([
            'name' => '2FA Test Admin',
            'email' => 'admin.2fa@youthpolicytracker.ng',
            'password' => Hash::make('Secret123!'),
            'email_verified_at' => now(),
        ]);
        $this->admin->assignRole('Super Admin');
    }

    public function test_unauthenticated_request_to_two_factor_page_redirects_to_login(): void
    {
        $response = $this->get('/admin/two-factor-challenge');
        $response->assertRedirect('/admin/login');
    }

    public function test_authenticated_admin_without_2fa_session_is_redirected_to_challenge(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get('/admin');
        $response->assertRedirect(route('filament.admin.auth.two-factor'));
    }

    public function test_first_time_user_can_setup_2fa_with_valid_totp_code(): void
    {
        $this->actingAs($this->admin);

        $component = Livewire::test(TwoFactorChallenge::class)
            ->assertSet('isSetup', true);

        $secret = $component->get('secret');
        $this->assertNotEmpty($secret);
        $validCode = $this->totpService->getCode($secret);

        $component->set('code', $validCode)
            ->call('verify')
            ->assertHasNoErrors()
            ->assertRedirect('/admin');

        $this->admin->refresh();
        $this->assertNotNull($this->admin->two_factor_confirmed_at);
        $this->assertCount(8, $this->admin->two_factor_recovery_codes);
        $this->assertTrue(session('filament.mfa_verified'));
    }

    public function test_two_factor_secret_is_encrypted_at_rest_in_database(): void
    {
        $plainSecret = 'JBSWY3DPEHPK3PXP';
        $this->admin->two_factor_secret = $plainSecret;
        $this->admin->save();

        // Query raw database column to verify it is NOT stored in plaintext
        $rawRecord = DB::table('users')->where('id', $this->admin->id)->first();
        $this->assertNotEquals($plainSecret, $rawRecord->two_factor_secret);

        // When retrieved via Eloquent model, decrypted automatically
        $this->admin->refresh();
        $this->assertEquals($plainSecret, $this->admin->two_factor_secret);
    }

    public function test_recovery_codes_are_hashed_and_strictly_single_use(): void
    {
        $rawCodes = $this->totpService->generateRecoveryCodes(1);
        $plainCode = $rawCodes[0];
        // Assert recovery code format is 16 chars (4 groups of 4 separated by hyphens)
        $this->assertEquals(19, strlen($plainCode));
        $this->assertEquals(16, strlen(str_replace('-', '', $plainCode)));

        $hashedCode = $this->totpService->hashRecoveryCode($plainCode);

        // Verify the hash is HMAC-SHA256 keyed with app.key
        $expectedHmac = hash_hmac('sha256', strtoupper(str_replace('-', '', $plainCode)), (string) config('app.key'));
        $this->assertEquals($expectedHmac, $hashedCode);

        $this->admin->update([
            'two_factor_secret' => $this->totpService->generateSecret(),
            'two_factor_confirmed_at' => now(),
            'two_factor_recovery_codes' => [$hashedCode],
        ]);

        // Raw database column contains the HMAC hash, not the plaintext code
        $rawRecord = DB::table('users')->where('id', $this->admin->id)->first();
        $this->assertStringNotContainsString($plainCode, $rawRecord->two_factor_recovery_codes);

        $this->actingAs($this->admin);

        // First use: successfully verifies and consumes the code
        Livewire::test(TwoFactorChallenge::class)
            ->call('toggleRecoveryMode')
            ->set('recovery_code', $plainCode)
            ->call('verify')
            ->assertHasNoErrors()
            ->assertRedirect('/admin');

        $this->admin->refresh();
        $this->assertEmpty($this->admin->two_factor_recovery_codes);

        // Second use: immediately fails because code was single-use
        session()->forget('filament.mfa_verified');
        Livewire::test(TwoFactorChallenge::class)
            ->call('toggleRecoveryMode')
            ->set('recovery_code', $plainCode)
            ->call('verify')
            ->assertHasErrors(['recovery_code']);
    }

    public function test_ip_rate_limiting_and_escalating_lockout_on_2fa_challenge(): void
    {
        $this->actingAs($this->admin);
        $userThrottleKey = '2fa-verify:' . $this->admin->id;
        $ipThrottleKey = '2fa-ip:' . request()->ip();
        $lockoutKey = '2fa-lockout-until:' . $this->admin->id;
        $failuresKey = '2fa-failures:' . $this->admin->id;

        RateLimiter::clear($userThrottleKey);
        RateLimiter::clear($ipThrottleKey);
        Cache::forget($lockoutKey);
        Cache::forget($failuresKey);

        $component = Livewire::test(TwoFactorChallenge::class);

        // Fail 3 times triggers the first escalating lockout tier (60s)
        for ($i = 0; $i < 3; $i++) {
            $component->set('code', '000000')->call('verify');
        }

        $this->assertTrue(Cache::has($lockoutKey));

        // Attempting to submit while locked out shows lockout message
        $component->set('code', '000000')
            ->call('verify')
            ->assertSee('Account temporarily locked');
    }

    public function test_crafted_livewire_payloads_cannot_bypass_2fa(): void
    {
        $this->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class, \Illuminate\Foundation\Http\Middleware\PreventRequestForgery::class]);
        $this->actingAs($this->admin);

        // 1. Crafted payload with administrative Filament resource component
        $response1 = $this->post('/livewire/update', [
            'components' => [
                [
                    'snapshot' => json_encode(['memo' => ['name' => 'filament.resources.documents.pages.list-documents']]),
                    'updates' => [],
                ],
            ],
        ]);
        $response1->assertStatus(403);

        // 2. Crafted payload targeting arbitrary admin namespace
        $response2 = $this->post('/livewire/update', [
            'components' => [
                [
                    'snapshot' => json_encode(['memo' => ['name' => 'admin.manage-users']]),
                    'updates' => [],
                ],
            ],
        ]);
        $response2->assertStatus(403);
    }

    public function test_public_livewire_components_still_work_for_guests_and_unverified_admins(): void
    {
        // 1. Guests can interact with public PolicyTracker component
        auth()->logout();
        Livewire::test(PolicyTracker::class)
            ->set('search', 'Kano')
            ->assertSee('Kano');

        // 2. Unverified admins browsing the public site can also interact with public PolicyTracker
        $this->actingAs($this->admin);
        session()->forget('filament.mfa_verified');

        Livewire::test(PolicyTracker::class)
            ->set('search', 'Kano')
            ->assertSee('Kano');
    }

    public function test_rate_limiting_enforced_on_challenge_and_recovery(): void
    {
        $this->actingAs($this->admin);
        $throttleKey = '2fa-verify:' . $this->admin->id;
        RateLimiter::clear($throttleKey);

        $component = Livewire::test(TwoFactorChallenge::class);

        // Trigger 5 failed attempts
        for ($i = 0; $i < 5; $i++) {
            $component->set('code', '000000')->call('verify');
        }

        // 4th and subsequent attempts trigger escalating lockout
        $component->set('code', '000000')
            ->call('verify')
            ->assertSee('Account temporarily locked');
    }

    public function test_totp_replay_prevention_within_time_window(): void
    {
        $secret = $this->totpService->generateSecret();
        $this->admin->update([
            'two_factor_secret' => $secret,
            'two_factor_confirmed_at' => now(),
            'two_factor_recovery_codes' => [$this->totpService->hashRecoveryCode('RECOVERY12')],
        ]);

        $this->actingAs($this->admin);
        $validCode = $this->totpService->getCode($secret);

        // First attempt with valid code succeeds
        Livewire::test(TwoFactorChallenge::class)
            ->set('code', $validCode)
            ->call('verify')
            ->assertHasNoErrors();

        // Simulate new session attempting to replay the exact same code in the time window
        session()->forget('filament.mfa_verified');

        Livewire::test(TwoFactorChallenge::class)
            ->set('code', $validCode)
            ->call('verify')
            ->assertHasErrors(['code'])
            ->assertSee('already been used');
    }

    public function test_session_id_regenerated_after_successful_2fa(): void
    {
        $secret = $this->totpService->generateSecret();
        $this->admin->update([
            'two_factor_secret' => $secret,
            'two_factor_confirmed_at' => now(),
            'two_factor_recovery_codes' => [$this->totpService->hashRecoveryCode('RECOVERY12')],
        ]);

        $this->actingAs($this->admin);
        $validCode = $this->totpService->getCode($secret);

        $initialSessionId = session()->getId();

        Livewire::test(TwoFactorChallenge::class)
            ->set('code', $validCode)
            ->call('verify');

        $this->assertNotEquals($initialSessionId, session()->getId());
        $this->assertTrue(session('filament.mfa_verified'));
    }

    public function test_middleware_blocks_livewire_administrative_updates_without_2fa(): void
    {
        $this->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class, \Illuminate\Foundation\Http\Middleware\PreventRequestForgery::class]);
        $this->actingAs($this->admin);

        // Attempting an administrative Livewire update without 2FA verified returns 403
        $response = $this->post('/livewire/update', [
            'components' => [
                [
                    'snapshot' => json_encode(['memo' => ['name' => 'filament.resources.documents.pages.list-documents']]),
                    'updates' => [],
                ],
            ],
        ]);

        $response->assertStatus(403);
    }

    public function test_bundled_public_and_administrative_livewire_request_is_blocked(): void
    {
        $this->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class, \Illuminate\Foundation\Http\Middleware\PreventRequestForgery::class]);
        $this->actingAs($this->admin);

        // Attempt to bundle an allowlisted public component with an unauthorized administrative component
        $response = $this->post('/livewire/update', [
            'components' => [
                [
                    'snapshot' => json_encode(['memo' => ['name' => 'policy-tracker']]),
                    'updates' => [],
                ],
                [
                    'snapshot' => json_encode(['memo' => ['name' => 'filament.resources.documents.pages.list-documents']]),
                    'updates' => [],
                ],
            ],
        ]);

        $response->assertStatus(403);
    }

    public function test_artisan_tracker_reset_2fa_clears_user_credentials_and_lockouts(): void
    {
        $this->admin->update([
            'two_factor_secret' => $this->totpService->generateSecret(),
            'two_factor_confirmed_at' => now(),
            'two_factor_recovery_codes' => [$this->totpService->hashRecoveryCode('RECOVERY12')],
        ]);

        Cache::put('2fa-failures:' . $this->admin->id, 5);
        Cache::put('2fa-lockout-until:' . $this->admin->id, time() + 300);

        $this->artisan('tracker:reset-2fa', [
            'email' => $this->admin->email,
            '--force' => true,
        ])->assertExitCode(0);

        $this->admin->refresh();
        $this->assertNull($this->admin->two_factor_secret);
        $this->assertNull($this->admin->two_factor_confirmed_at);
        $this->assertNull($this->admin->two_factor_recovery_codes);
        $this->assertFalse(Cache::has('2fa-failures:' . $this->admin->id));
        $this->assertFalse(Cache::has('2fa-lockout-until:' . $this->admin->id));
    }

    public function test_admin_can_sign_out_from_two_factor_challenge(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(TwoFactorChallenge::class)
            ->call('logout')
            ->assertRedirect('/admin/login');

        $this->assertFalse(auth()->check());
    }
}
