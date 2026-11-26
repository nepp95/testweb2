<?php

namespace Tests\Feature\Admin;

use App\Enums\SectionType;
use App\Models\Section;
use App\Models\SectionItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SectionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_the_login_page(): void
    {
        $this->get(route('admin.sections.index'))->assertRedirect(route('login'));
        $this->post(route('admin.sections.store'), [])->assertRedirect(route('login'));
    }

    public function test_index_lists_sections_and_is_not_indexed_by_search_engines(): void
    {
        Section::factory()->create(['title' => 'Over ons']);

        $this->actingAs(User::factory()->create())
            ->get(route('admin.sections.index'))
            ->assertOk()
            ->assertSee('Over ons')
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow');
    }

    public function test_store_creates_a_section_with_its_content_at_the_end(): void
    {
        Section::factory()->create();

        $this->actingAs(User::factory()->create())
            ->post(route('admin.sections.store'), [
                'type' => SectionType::Faq->value,
                'title' => 'Goed om te weten',
                'anchor' => '',
                'nav_label' => 'Vragen',
                'is_visible' => '1',
                'content' => ['eyebrow' => 'Veelgestelde vragen', 'intro' => '', 'background' => 'zand'],
            ])
            ->assertSessionHasNoErrors();

        $section = Section::query()->where('type', SectionType::Faq)->sole();

        $this->assertSame('goed-om-te-weten', $section->anchor);
        $this->assertSame(2, $section->position);
        $this->assertSame(['eyebrow' => 'Veelgestelde vragen', 'intro' => null, 'background' => 'zand'], $section->content);
    }

    public function test_store_rejects_a_second_section_of_a_single_use_type(): void
    {
        Section::factory()->ofType(SectionType::Contact)->create();

        $this->actingAs(User::factory()->create())
            ->post(route('admin.sections.store'), [
                'type' => SectionType::Contact->value,
                'title' => 'Nog een contactblok',
                'content' => ['background' => 'wit'],
            ])
            ->assertSessionHasErrors('type');

        $this->assertSame(1, Section::query()->where('type', SectionType::Contact)->count());
    }

    public function test_store_rejects_reserved_and_duplicate_anchors(): void
    {
        Section::factory()->create(['anchor' => 'over-ons']);
        $user = User::factory()->create();

        foreach (['main', 'Over ons'] as $anchor) {
            $this->actingAs($user)
                ->post(route('admin.sections.store'), [
                    'type' => SectionType::Text->value,
                    'title' => 'Tekst',
                    'anchor' => $anchor,
                    'content' => ['body' => 'Tekst', 'background' => 'wit'],
                ])
                ->assertSessionHasErrors('anchor');
        }
    }

    public function test_store_requires_the_fields_of_the_chosen_type(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('admin.sections.store'), [
                'type' => SectionType::Text->value,
                'title' => 'Tekst',
                'content' => ['background' => 'paars'],
            ])
            ->assertSessionHasErrors(['content.body', 'content.background']);
    }

    public function test_update_saves_the_section_and_keeps_its_anchor_when_unchanged(): void
    {
        $section = Section::factory()->ofType(SectionType::Hero)->create(['anchor' => 'welkom']);
        $target = Section::factory()->create();

        $this->actingAs(User::factory()->create())
            ->put(route('admin.sections.update', $section), [
                'title' => 'Nieuwe titel',
                'anchor' => 'welkom',
                'is_visible' => '0',
                'content' => ['primary_label' => 'Ik zoek hulp', 'primary_target' => (string) $target->id],
            ])
            ->assertRedirect(route('admin.sections.edit', $section));

        $section->refresh();
        $this->assertSame('Nieuwe titel', $section->title);
        $this->assertFalse($section->is_visible);
        $this->assertSame($target->id, $section->field('primary_target'));
    }

    public function test_move_swaps_the_section_with_its_neighbour(): void
    {
        $first = Section::factory()->create();
        $second = Section::factory()->create();

        $this->actingAs(User::factory()->create())
            ->patch(route('admin.sections.move', [$second, 'up']))
            ->assertRedirect(route('admin.sections.index'));

        $this->assertSame([$second->id, $first->id], Section::query()->ordered()->pluck('id')->all());
    }

    public function test_toggle_visibility_hides_and_shows_the_section(): void
    {
        $section = Section::factory()->create();
        $user = User::factory()->create();

        $this->actingAs($user)->patch(route('admin.sections.visibility', $section));
        $this->assertFalse($section->refresh()->is_visible);

        $this->actingAs($user)->patch(route('admin.sections.visibility', $section));
        $this->assertTrue($section->refresh()->is_visible);
    }

    public function test_destroy_deletes_the_section_and_its_items(): void
    {
        $item = SectionItem::factory()->create();

        $this->actingAs(User::factory()->create())
            ->delete(route('admin.sections.destroy', $item->section))
            ->assertRedirect(route('admin.sections.index'));

        $this->assertModelMissing($item->section);
        $this->assertModelMissing($item);
    }
}
