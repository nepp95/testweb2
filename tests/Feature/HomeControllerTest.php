<?php

namespace Tests\Feature;

use App\Enums\SectionType;
use App\Enums\SettingKey;
use App\Models\Section;
use App\Models\SectionItem;
use App\Support\SiteSettings;
use Database\Seeders\SiteContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_renders_the_seeded_reference_content(): void
    {
        $this->seed(SiteContentSeeder::class);

        $this->get(route('home'))
            ->assertOk()
            ->assertSeeInOrder([
                'Vastgelopen tussen regels, loketten en formulieren?',
                'Herken je dit?',
                'Geen probleem is te klein of te ingewikkeld',
                'In drie stappen',
                'Samen vangen we meer mensen op',
                'Goed om te weten',
                'Vertel ons wat er speelt',
            ])
            ->assertSee('data-onderwerp="Ik wil vrijwilliger worden"', false)
            ->assertSee('href="#contact"', false)
            ->assertDontSee('Het bestuur');
    }

    public function test_renders_visible_sections_in_their_position_order(): void
    {
        $first = Section::factory()->create(['title' => 'Eerste sectie']);
        $second = Section::factory()->create(['title' => 'Tweede sectie']);
        $first->move('down');

        $this->get(route('home'))->assertSeeInOrder([$second->title, $first->title]);
    }

    public function test_does_not_render_hidden_sections(): void
    {
        Section::factory()->hidden()->create(['title' => 'Verborgen sectie']);

        $this->get(route('home'))->assertOk()->assertDontSee('Verborgen sectie');
    }

    public function test_lists_sections_with_a_menu_name_in_the_navigation(): void
    {
        Section::factory()->create(['anchor' => 'over-ons', 'nav_label' => 'Over ons']);
        Section::factory()->ofType(SectionType::Contact)->create(['anchor' => 'contact', 'nav_label' => 'Hulp vragen']);

        $this->get(route('home'))
            ->assertSee('<a href="#over-ons">Over ons</a>', false)
            ->assertSee('<a class="btn btn-small" href="#contact">Hulp vragen</a>', false);
    }

    public function test_strips_raw_html_and_unsafe_links_from_markdown(): void
    {
        Section::factory()->create(['content' => [
            'body' => "<script>alert('x')</script>\n\n[klik](javascript:alert(1)) <img src=x onerror=alert(1)> **vet**",
            'background' => 'wit',
        ]]);

        $this->get(route('home'))
            ->assertSee('<strong>vet</strong>', false)
            ->assertDontSee('<script>alert', false)
            ->assertDontSee('onerror', false)
            ->assertDontSee('javascript:', false);
    }

    public function test_escapes_plain_text_fields(): void
    {
        $section = Section::factory()->ofType(SectionType::Cards)->create();
        SectionItem::factory()->for($section)->create(['title' => '<b>Kaart</b>']);

        $this->get(route('home'))->assertSee('&lt;b&gt;Kaart&lt;/b&gt;', false);
    }

    public function test_shows_saved_foundation_details_instead_of_placeholders(): void
    {
        app(SiteSettings::class)->update([SettingKey::Name->value => 'Stichting De Brug']);

        $this->get(route('home'))
            ->assertSee('Stichting De Brug')
            ->assertDontSee('Stichting [naam]');
    }

    public function test_hides_the_crisis_bar_when_its_text_is_empty(): void
    {
        $this->get(route('home'))->assertSee('crisis-bar', false);

        app(SiteSettings::class)->update([SettingKey::CrisisBar->value => '']);

        $this->get(route('home'))->assertDontSee('crisis-bar', false);
    }
}
