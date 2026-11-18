<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;

class ResetTwoFactorCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tracker:reset-2fa 
                            {email : The email address of the administrator whose 2FA to reset}
                            {--force : Force the operation without confirmation prompt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear an administrative user 2FA secret, recovery codes, and lockout counters (Server access only)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = trim($this->argument('email'));

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("Administrative user not found with email: {$email}");
            return 1;
        }

        if (!$this->option('force') && $this->input->isInteractive()) {
            $confirmed = $this->confirm("Are you sure you want to reset 2FA for '{$user->name}' ({$email})?", false);
            if (!$confirmed) {
                $this->info('2FA reset cancelled.');
                return 0;
            }
        }

        // 1. Clear 2FA credentials
        $user->two_factor_secret = null;
        $user->two_factor_recovery_codes = null;
        $user->two_factor_confirmed_at = null;
        $user->save();

        // 2. Clear rate limiters and failure lockouts
        Cache::forget('2fa-failures:' . $user->id);
        Cache::forget('2fa-lockout-until:' . $user->id);
        RateLimiter::clear('2fa-verify:' . $user->id);

        $this->info("SUCCESS: Two-factor authentication has been reset for '{$user->name}' ({$email}).");
        $this->line("All lockout counters have been cleared. The user will be required to configure a new authenticator app upon next login.");

        return 0;
    }
}
