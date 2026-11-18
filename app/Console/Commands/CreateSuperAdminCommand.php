<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class CreateSuperAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tracker:make-super-admin 
                            {--name= : The full name of the administrator}
                            {--email= : The email address of the administrator}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Super Admin user with prompted credentials and no default password';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Youth Policy Tracker: Super Admin Provisioning');
        $this->line('--------------------------------------------------');

        $name = $this->option('name') ?: $this->ask('Full Name');
        while (empty($name)) {
            $this->error('Full name is required.');
            $name = $this->ask('Full Name');
        }

        $email = $this->option('email') ?: $this->ask('Email Address');
        $validator = Validator::make(['email' => $email], [
            'email' => ['required', 'email', 'unique:users,email'],
        ]);

        while ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            $email = $this->ask('Email Address');
            $validator = Validator::make(['email' => $email], [
                'email' => ['required', 'email', 'unique:users,email'],
            ]);
        }

        $password = $this->secret('Enter Password (min 8 characters)');
        while (strlen($password) < 8) {
            $this->error('Password must be at least 8 characters long.');
            $password = $this->secret('Enter Password (min 8 characters)');
        }

        $passwordConfirm = $this->secret('Confirm Password');
        while ($password !== $passwordConfirm) {
            $this->error('Passwords do not match. Please re-enter.');
            $password = $this->secret('Enter Password (min 8 characters)');
            $passwordConfirm = $this->secret('Confirm Password');
        }

        // Ensure Super Admin role exists
        $role = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'email_verified_at' => now(),
        ]);

        $user->syncRoles([$role]);

        $this->newLine();
        $this->info("Super Admin [{$user->name}] successfully created with email: {$user->email}");
        $this->line('Role [Super Admin] assigned with full administrative governance.');

        return Command::SUCCESS;
    }
}
