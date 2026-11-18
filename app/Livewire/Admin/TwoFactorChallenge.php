<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Services\TotpService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;

#[Layout('components.layouts.app')]
class TwoFactorChallenge extends Component
{
    public bool $isSetup = false;
    public string $secret = '';
    public string $qrUri = '';
    public bool $usingRecoveryCode = false;

    #[Rule('nullable|string')]
    public string $code = '';

    #[Rule('nullable|string')]
    public string $recovery_code = '';

    public function mount(TotpService $totpService): void
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user) {
            $this->redirectRoute('filament.admin.auth.login');
            return;
        }

        // If already verified in this session, redirect to admin
        if (session()->get('filament.mfa_verified') === true) {
            $this->redirect('/admin');
            return;
        }

        if (!$user->hasEnabledTwoFactor()) {
            $this->isSetup = true;
            if (empty($user->two_factor_secret)) {
                $user->two_factor_secret = $totpService->generateSecret();
                $user->save();
            }
            $this->secret = $user->two_factor_secret;
            $this->qrUri = $totpService->getQrCodeUri(
                'Youth Policy Tracker',
                $user->email,
                $this->secret
            );
        } else {
            $this->isSetup = false;
        }
    }

    public function verify(TotpService $totpService): void
    {
        /** @var User $user */
        $user = Auth::user();
        $throttleKey = '2fa-verify:' . $user->id;

        // Rate limiting: 5 attempts per 60 seconds
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->addError(
                $this->usingRecoveryCode ? 'recovery_code' : 'code',
                "Too many verification attempts. Please wait {$seconds} seconds before trying again."
            );
            return;
        }

        if ($this->usingRecoveryCode) {
            $this->validate([
                'recovery_code' => ['required', 'string'],
            ]);

            $recoveryCodes = $user->two_factor_recovery_codes ?? [];
            $matchedIndex = $totpService->verifyRecoveryCode($this->recovery_code, $recoveryCodes);

            if ($matchedIndex !== false) {
                RateLimiter::clear($throttleKey);

                // Consume single-use hashed recovery code
                unset($recoveryCodes[$matchedIndex]);
                $user->two_factor_recovery_codes = array_values($recoveryCodes);
                $user->save();

                // Regenerate session ID to prevent session fixation attacks
                session()->regenerate();
                session()->put('filament.mfa_verified', true);
                $this->redirectIntended('/admin');
                return;
            }

            RateLimiter::hit($throttleKey, 60);
            $this->addError('recovery_code', 'The provided emergency recovery code is invalid.');
            return;
        }

        $this->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $secret = $this->isSetup ? $this->secret : $user->two_factor_secret;

        // Replay prevention: verify the code has not already been used within its time window
        $replayKey = 'totp-used:' . $user->id . ':' . $this->code;
        if (Cache::has($replayKey)) {
            RateLimiter::hit($throttleKey, 60);
            $this->addError('code', 'This authentication code has already been used. Please wait for the next code.');
            return;
        }

        if (!$totpService->verifyCode($secret, $this->code)) {
            RateLimiter::hit($throttleKey, 60);
            $this->addError('code', 'The provided 6-digit authentication code is invalid or has expired.');
            return;
        }

        // Lock code against reuse for 120 seconds
        Cache::put($replayKey, true, 120);
        RateLimiter::clear($throttleKey);

        if ($this->isSetup) {
            $user->two_factor_confirmed_at = now();
            $rawCodes = $totpService->generateRecoveryCodes();
            $user->two_factor_recovery_codes = array_map(fn ($c) => $totpService->hashRecoveryCode($c), $rawCodes);
            $user->save();
        }

        // Regenerate session ID upon successful 2FA elevation
        session()->regenerate();
        session()->put('filament.mfa_verified', true);
        $this->redirectIntended('/admin');
    }

    public function toggleRecoveryMode(): void
    {
        $this->usingRecoveryCode = !$this->usingRecoveryCode;
        $this->reset(['code', 'recovery_code']);
        $this->resetErrorBag();
    }

    public function logout(): void
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        $this->redirectRoute('filament.admin.auth.login');
    }

    public function render()
    {
        return view('livewire.admin.two-factor-challenge');
    }
}
