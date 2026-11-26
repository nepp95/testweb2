<?php

namespace Tests\Feature;

use App\Enums\SettingKey;
use App\Support\SiteSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrivacyControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_renders_the_saved_privacy_statement(): void
    {
        app(SiteSettings::class)->update([SettingKey::PrivacyStatement->value => "## Bewaartermijn\n\nWe bewaren gegevens twee jaar."]);

        $this->get(route('privacy'))
            ->assertOk()
            ->assertSee('<h2>Bewaartermijn</h2>', false)
            ->assertSee('We bewaren gegevens twee jaar.');
    }

    public function test_falls_back_to_the_concept_statement(): void
    {
        $this->get(route('privacy'))->assertOk()->assertSee('Welke gegevens we gebruiken');
    }
}
