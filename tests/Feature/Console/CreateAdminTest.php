<?php

namespace Tests\Feature\Console;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CreateAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_the_admin_account(): void
    {
        $this->artisan('admin:create')
            ->expectsQuestion('Naam', 'Beheerder')
            ->expectsQuestion('E-mailadres', 'Beheer@Example.test')
            ->expectsQuestion('Wachtwoord', 'een-lang-wachtwoord')
            ->expectsQuestion('Wachtwoord nogmaals', 'een-lang-wachtwoord')
            ->assertSuccessful();

        $user = User::query()->sole();
        $this->assertSame('beheer@example.test', $user->email);
        $this->assertTrue(Hash::check('een-lang-wachtwoord', $user->password));
    }

    public function test_refuses_when_an_account_already_exists(): void
    {
        User::factory()->create();

        $this->artisan('admin:create')->assertFailed();

        $this->assertSame(1, User::query()->count());
    }

    public function test_refuses_when_the_passwords_differ(): void
    {
        $this->artisan('admin:create')
            ->expectsQuestion('Naam', 'Beheerder')
            ->expectsQuestion('E-mailadres', 'beheer@example.test')
            ->expectsQuestion('Wachtwoord', 'een-lang-wachtwoord')
            ->expectsQuestion('Wachtwoord nogmaals', 'iets-anders-ingetypt')
            ->assertFailed();

        $this->assertSame(0, User::query()->count());
    }
}
