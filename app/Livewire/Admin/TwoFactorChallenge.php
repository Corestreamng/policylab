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
        $userThrottleKey = '2fa-verify:' . $user->id;
        $ipThrottleKey = '2fa-ip:' . request()->ip();
        $lockoutKey = '2fa-lockout-until:' . $user->id;
        $failuresKey = '2fa-failures:' . $user->id;

        // 1. Check escalating lockout status
        $lockoutUntil = Cache::get($lockoutKey);
        if ($lockoutUntil && time() < $lockoutUntil) {
            $remaining = $lockoutUntil - time();
            $this->addError(
                $this->usingRecoveryCode ? 'recovery_code' : 'code',
                "Account temporarily locked due to multiple failed verification attempts. Please wait {$remaining} seconds before retrying."
            );
            return;
        }

        // 2. IP-based rate limiting (maximum 10 attempts per minute per IP)
        if (RateLimiter::tooManyAttempts($ipThrottleKey, 10)) {
            $seconds = RateLimiter::availableIn($ipThrottleKey);
            $this->addError(
                $this->usingRecoveryCode ? 'recovery_code' : 'code',
                "Too many verification attempts from this IP address. Please wait {$seconds} seconds."
            );
            return;
        }

        // 3. User-based rate limiting (maximum 5 attempts per 60 seconds)
        if (RateLimiter::tooManyAttempts($userThrottleKey, 5)) {
            $seconds = RateLimiter::availableIn($userThrottleKey);
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
                // Clear rate limiters and failure lockouts upon success
                RateLimiter::clear($userThrottleKey);
                RateLimiter::clear($ipThrottleKey);
                Cache::forget($lockoutKey);
                Cache::forget($failuresKey);

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

            $this->handleFailedAttempt($userThrottleKey, $ipThrottleKey, $failuresKey, $lockoutKey);
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
            $this->handleFailedAttempt($userThrottleKey, $ipThrottleKey, $failuresKey, $lockoutKey);
            $this->addError('code', 'This authentication code has already been used. Please wait for the next code.');
            return;
        }

        if (!$totpService->verifyCode($secret, $this->code)) {
            $this->handleFailedAttempt($userThrottleKey, $ipThrottleKey, $failuresKey, $lockoutKey);
            $this->addError('code', 'The provided 6-digit authentication code is invalid or has expired.');
            return;
        }

        // Lock code against reuse for 120 seconds
        Cache::put($replayKey, true, 120);

        // Clear rate limiters and failure lockouts on success
        RateLimiter::clear($userThrottleKey);
        RateLimiter::clear($ipThrottleKey);
        Cache::forget($lockoutKey);
        Cache::forget($failuresKey);

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

    /**
     * Increment failure metrics and apply escalating lockout windows.
     */
    protected function handleFailedAttempt(string $userThrottleKey, string $ipThrottleKey, string $failuresKey, string $lockoutKey): void
    {
        RateLimiter::hit($userThrottleKey, 60);
        RateLimiter::hit($ipThrottleKey, 60);

        $failures = (int) Cache::increment($failuresKey);
        Cache::put($failuresKey, $failures, 1800); // Retain count for 30 minutes

        // Escalating lockout schedule:
        // 3 consecutive failures  -> 60 second lockout
        // 5 consecutive failures  -> 300 second (5 min) lockout
        // 10 consecutive failures -> 900 second (15 min) lockout
        if ($failures >= 10) {
            Cache::put($lockoutKey, time() + 900, 900);
        } elseif ($failures >= 5) {
            Cache::put($lockoutKey, time() + 300, 300);
        } elseif ($failures >= 3) {
            Cache::put($lockoutKey, time() + 60, 60);
        }
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
