<?php

namespace Tests\Feature;

use App\Livewire\Admin\TwoFactorChallenge;
use App\Models\User;
use App\Services\TotpService;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
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

        // Accessing any admin URL before 2FA verification redirects to challenge
        $response = $this->get('/admin');
        $response->assertRedirect(route('filament.admin.auth.two-factor'));
    }

    public function test_first_time_user_can_setup_2fa_with_valid_totp_code(): void
    {
        $this->actingAs($this->admin);

        // Mount the challenge in setup mode
        $component = Livewire::test(TwoFactorChallenge::class)
            ->assertSet('isSetup', true);

        $secret = $component->get('secret');
        $this->assertNotEmpty($secret);
        $validCode = $this->totpService->getCode($secret);

        // Submitting valid code enables 2FA and grants session access
        $component->set('code', $validCode)
            ->call('verify')
            ->assertHasNoErrors()
            ->assertRedirect('/admin');

        $this->admin->refresh();
        $this->assertNotNull($this->admin->two_factor_confirmed_at);
        $this->assertCount(8, $this->admin->two_factor_recovery_codes);
        $this->assertTrue(session('filament.mfa_verified'));
    }

    public function test_invalid_totp_code_fails_validation(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(TwoFactorChallenge::class)
            ->set('code', '000000')
            ->call('verify')
            ->assertHasErrors(['code']);

        $this->assertFalse(session()->has('filament.mfa_verified'));
    }

    public function test_user_with_confirmed_2fa_can_verify_with_valid_totp_code(): void
    {
        $secret = $this->totpService->generateSecret();
        $this->admin->update([
            'two_factor_secret' => $secret,
            'two_factor_confirmed_at' => now(),
            'two_factor_recovery_codes' => ['TESTRECOVERY1', 'TESTRECOVERY2'],
        ]);

        $this->actingAs($this->admin);

        $validCode = $this->totpService->getCode($secret);

        Livewire::test(TwoFactorChallenge::class)
            ->assertSet('isSetup', false)
            ->set('code', $validCode)
            ->call('verify')
            ->assertHasNoErrors()
            ->assertRedirect('/admin');

        $this->assertTrue(session('filament.mfa_verified'));
    }

    public function test_user_can_authenticate_with_emergency_recovery_code(): void
    {
        $secret = $this->totpService->generateSecret();
        $recoveryCodes = ['CODE123456', 'CODE789012'];

        $this->admin->update([
            'two_factor_secret' => $secret,
            'two_factor_confirmed_at' => now(),
            'two_factor_recovery_codes' => $recoveryCodes,
        ]);

        $this->actingAs($this->admin);

        Livewire::test(TwoFactorChallenge::class)
            ->call('toggleRecoveryMode')
            ->assertSet('usingRecoveryCode', true)
            ->set('recovery_code', 'CODE123456')
            ->call('verify')
            ->assertHasNoErrors()
            ->assertRedirect('/admin');

        $this->admin->refresh();
        // The used code must be consumed
        $this->assertCount(1, $this->admin->two_factor_recovery_codes);
        $this->assertNotContains('CODE123456', $this->admin->two_factor_recovery_codes);
        $this->assertTrue(session('filament.mfa_verified'));
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
