<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Validation\Factory as ValidationFactory;
use Illuminate\Validation\Rules\Password;

class CreateAdminUser extends Command
{
    /**
     * @var string
     */
    protected $signature = 'app:create-admin';

    /**
     * @var string
     */
    protected $description = 'Creates a new admin user';

    public function handle(
        ValidationFactory $validatorFactory
    ): int {
        $name = $this->ask('Full Name?');
        $email = $this->ask('Email?');
        $password = $this->secret('Password?');
        $confirmPassword = $this->secret('Confirm Password?');

        $validator = $validatorFactory->make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $confirmPassword,
        ], [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        if ($validator->fails()) {
            $this->error('Admin user not created. See error messages below:');
            foreach ($validator->errors()->all() as $error) {
                $this->line($error);
            }

            return self::FAILURE;
        }

        User::query()->forceCreate([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'email_verified_at' => now(),
            'is_admin' => true,
        ]);

        $this->info('Admin user created successfully.');

        return self::SUCCESS;
    }
}
