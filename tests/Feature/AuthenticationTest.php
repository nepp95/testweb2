<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider;
use PragmaRX\Google2FA\Google2FA;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_and_passkey_routes_do_not_exist(): void
    {
        $this->get('/register')->assertNotFound();
        $this->get('/admin/register')->assertNotFound();
        $this->get('/admin/passkeys/login/options')->assertNotFound();
    }

    public function test_login_page_is_served_under_the_admin_prefix(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Inloggen')
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow');

        $this->assertSame(url('/admin/login'), route('login'));
    }

    public function test_admin_logs_in_and_lands_on_the_admin_home(): void
    {
        $user = User::factory()->create();

        $this->post(route('login.store'), ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect('/admin');

        $this->assertAuthenticatedAs($user);
    }

    public function test_wrong_password_shows_a_dutch_error(): void
    {
        $user = User::factory()->create();

        $this->post(route('login.store'), ['email' => $user->email, 'password' => 'fout'])
            ->assertSessionHasErrors(['email' => 'Deze combinatie van e-mailadres en wachtwoord is niet bij ons bekend.']);

        $this->assertGuest();
    }

    public function test_login_is_throttled_after_five_failed_attempts(): void
    {
        $user = User::factory()->create();

        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $this->post(route('login.store'), ['email' => $user->email, 'password' => 'fout']);
        }

        $this->post(route('login.store'), ['email' => $user->email, 'password' => 'password'])
            ->assertTooManyRequests();

        $this->assertGuest();
    }

    public function test_admin_with_two_factor_enabled_must_pass_the_challenge(): void
    {
        $user = User::factory()->create();
        $provider = app(TwoFactorAuthenticationProvider::class);
        $secret = $provider->generateSecretKey();
        $user->forceFill([
            'two_factor_secret' => encrypt($secret),
            'two_factor_recovery_codes' => encrypt(json_encode(['herstelcode-1'])),
            'two_factor_confirmed_at' => now(),
        ])->save();

        $this->post(route('login.store'), ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect(route('two-factor.login'));
        $this->assertGuest();

        $this->post(route('two-factor.login.store'), ['code' => '000000'])
            ->assertSessionHasErrors('code');
        $this->assertGuest();

        $this->post(route('two-factor.login.store'), ['code' => app(Google2FA::class)->getCurrentOtp($secret)])
            ->assertRedirect('/admin');
        $this->assertAuthenticatedAs($user);
    }

    public function test_two_factor_settings_require_a_recently_confirmed_password(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('admin.account.two-factor'))
            ->assertRedirect(route('password.confirm'));
    }

    public function test_two_factor_settings_show_after_password_confirmation(): void
    {
        $this->actingAs(User::factory()->create())
            ->withSession(['auth.password_confirmed_at' => time()])
            ->get(route('admin.account.two-factor'))
            ->assertOk()
            ->assertSee('Tweestapsverificatie aanzetten');
    }

    public function test_profile_update_requires_the_current_password(): void
    {
        $user = User::factory()->create(['email' => 'oud@example.test']);

        $this->actingAs($user)
            ->put(route('user-profile-information.update'), ['name' => 'Beheer', 'email' => 'nieuw@example.test'])
            ->assertSessionHasErrorsIn('updateProfileInformation', 'current_password');
        $this->assertSame('oud@example.test', $user->refresh()->email);

        $this->actingAs($user)
            ->put(route('user-profile-information.update'), ['name' => 'Beheer', 'email' => 'nieuw@example.test', 'current_password' => 'password'])
            ->assertSessionHasNoErrors();
        $this->assertSame('nieuw@example.test', $user->refresh()->email);
    }
}
