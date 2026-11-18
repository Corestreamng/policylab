<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Services\TotpService;
use Illuminate\Support\Facades\Auth;
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

        if ($this->usingRecoveryCode) {
            $this->validate([
                'recovery_code' => ['required', 'string'],
            ]);

            $recoveryCodes = $user->two_factor_recovery_codes ?? [];
            $inputCode = strtoupper(trim($this->recovery_code));

            $foundIndex = array_search($inputCode, $recoveryCodes, true);
            if ($foundIndex !== false) {
                // Consume the recovery code
                unset($recoveryCodes[$foundIndex]);
                $user->two_factor_recovery_codes = array_values($recoveryCodes);
                $user->save();

                session()->put('filament.mfa_verified', true);
                $this->redirectIntended('/admin');
                return;
            }

            $this->addError('recovery_code', 'The provided emergency recovery code is invalid.');
            return;
        }

        $this->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $secret = $this->isSetup ? $this->secret : $user->two_factor_secret;

        if (!$totpService->verifyCode($secret, $this->code)) {
            $this->addError('code', 'The provided 6-digit authentication code is invalid or has expired.');
            return;
        }

        if ($this->isSetup) {
            $user->two_factor_confirmed_at = now();
            $user->two_factor_recovery_codes = $totpService->generateRecoveryCodes();
            $user->save();
        }

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
