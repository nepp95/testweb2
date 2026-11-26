<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

#[Signature('admin:create')]
#[Description('Maak het (enige) beheerdersaccount aan')]
class CreateAdmin extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (User::query()->exists()) {
            $this->components->error('Er bestaat al een beheerdersaccount. Er kan maar één account zijn.');

            return self::FAILURE;
        }

        $name = text(
            label: 'Naam',
            required: true,
            validate: ['name' => ['required', 'string', 'max:255']],
        );

        $email = text(
            label: 'E-mailadres',
            required: true,
            validate: ['email' => ['required', 'string', 'email', 'max:255']],
        );

        $password = password(
            label: 'Wachtwoord',
            required: true,
            validate: ['password' => ['required', 'string', Password::defaults()]],
            hint: 'Minimaal 12 tekens.',
        );

        $confirmation = password(label: 'Wachtwoord nogmaals', required: true);

        $validator = Validator::make(
            ['password' => $password, 'password_confirmation' => $confirmation],
            ['password' => ['confirmed']],
        );

        if ($validator->fails()) {
            $this->components->error('De wachtwoorden komen niet overeen.');

            return self::FAILURE;
        }

        User::query()->create([
            'name' => $name,
            'email' => strtolower($email),
            'password' => $password,
        ]);

        $this->components->info('Het beheerdersaccount is aangemaakt. Log in via /admin/login.');

        return self::SUCCESS;
    }
}
