<?php

namespace Tests\Feature;

use App\Livewire\Admin\TwoFactorChallenge;
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
        $plainCode = '4F2A1B8C9D';
        $hashedCode = $this->totpService->hashRecoveryCode($plainCode);

        $this->admin->update([
            'two_factor_secret' => $this->totpService->generateSecret(),
            'two_factor_confirmed_at' => now(),
            'two_factor_recovery_codes' => [$hashedCode],
        ]);

        // Raw database column contains the SHA-256 hash, not the plaintext code
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

        // 6th attempt is throttled
        $component->set('code', '000000')
            ->call('verify')
            ->assertSee('Too many verification attempts');
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

    public function test_admin_can_sign_out_from_two_factor_challenge(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(TwoFactorChallenge::class)
            ->call('logout')
            ->assertRedirect('/admin/login');

        $this->assertFalse(auth()->check());
    }
}
