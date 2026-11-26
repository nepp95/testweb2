<?php

namespace Tests\Feature\Admin;

use App\Enums\SettingKey;
use App\Models\User;
use App\Support\SiteSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_edit_shows_the_placeholder_defaults(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('admin.settings.edit'))
            ->assertOk()
            ->assertSee('Stichting [naam]')
            ->assertSee('NL00 BANK 0000 0000 00');
    }

    public function test_update_saves_values_and_empty_fields_stay_empty(): void
    {
        $this->actingAs(User::factory()->create())
            ->put(route('admin.settings.update'), [
                SettingKey::Name->value => 'Stichting De Brug',
                SettingKey::WhatsApp->value => '',
                SettingKey::Phone->value => '+31 20 123 4567',
            ])
            ->assertRedirect(route('admin.settings.edit'));

        $settings = app(SiteSettings::class);
        $this->assertSame('Stichting De Brug', $settings->get(SettingKey::Name));
        $this->assertSame('', $settings->get(SettingKey::WhatsApp));
        $this->assertSame('tel:+31201234567', $settings->phoneHref());
    }

    public function test_update_rejects_an_invalid_form_recipient(): void
    {
        $this->actingAs(User::factory()->create())
            ->put(route('admin.settings.update'), [SettingKey::FormRecipient->value => 'geen-adres'])
            ->assertSessionHasErrors(SettingKey::FormRecipient->value);
    }

    public function test_guest_cannot_update_settings(): void
    {
        $this->put(route('admin.settings.update'), [SettingKey::Name->value => 'Gehackt'])
            ->assertRedirect(route('login'));

        $this->assertSame('Stichting [naam]', app(SiteSettings::class)->get(SettingKey::Name));
    }
}
