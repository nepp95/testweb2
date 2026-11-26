<?php

namespace Tests\Feature\Admin;

use App\Enums\CardIcon;
use App\Enums\SectionType;
use App\Models\Section;
use App\Models\SectionItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SectionItemControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_adds_an_item_with_its_content_to_the_group(): void
    {
        $section = Section::factory()->ofType(SectionType::Cards)->create();

        $this->actingAs(User::factory()->create())
            ->post(route('admin.sections.items.store', $section), [
                'group' => 'kaart',
                'title' => 'Wonen',
                'content' => ['body' => 'Hulp bij wonen.', 'icon' => CardIcon::Housing->value],
            ])
            ->assertRedirect(route('admin.sections.edit', $section));

        $item = $section->items()->sole();
        $this->assertSame('Wonen', $item->title);
        $this->assertSame(['body' => 'Hulp bij wonen.', 'icon' => 'wonen'], $item->content);
    }

    public function test_store_rejects_a_group_the_section_type_does_not_have(): void
    {
        $section = Section::factory()->ofType(SectionType::Cards)->create();

        $this->actingAs(User::factory()->create())
            ->post(route('admin.sections.items.store', $section), ['group' => 'vraag', 'title' => 'Vraag'])
            ->assertSessionHasErrors('group');

        $this->assertSame(0, $section->items()->count());
    }

    public function test_support_button_may_only_preselect_a_contact_form_subject(): void
    {
        $support = Section::factory()->ofType(SectionType::Support)->create();
        $contact = Section::factory()->ofType(SectionType::Contact)->create();
        $subject = SectionItem::factory()->for($contact)->create(['group' => SectionType::CONTACT_SUBJECT_GROUP]);
        $otherItem = SectionItem::factory()->for($support)->create();
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('admin.sections.items.store', $support), [
                'group' => 'kaart',
                'title' => 'Word vrijwilliger',
                'content' => ['button_subject' => (string) $otherItem->id, 'show_iban' => '0'],
            ])
            ->assertSessionHasErrors('content.button_subject');

        $this->actingAs($user)
            ->post(route('admin.sections.items.store', $support), [
                'group' => 'kaart',
                'title' => 'Word vrijwilliger',
                'content' => ['button_subject' => (string) $subject->id, 'show_iban' => '1'],
            ])
            ->assertSessionHasNoErrors();

        $card = $support->items()->where('title', 'Word vrijwilliger')->sole();
        $this->assertSame($subject->id, $card->field('button_subject'));
        $this->assertTrue($card->field('show_iban'));
    }

    public function test_item_of_another_section_returns_404(): void
    {
        $section = Section::factory()->create();
        $foreignItem = SectionItem::factory()->create();

        $this->actingAs(User::factory()->create())
            ->get(route('admin.sections.items.edit', [$section, $foreignItem]))
            ->assertNotFound();
    }

    public function test_update_keeps_the_group_of_the_item(): void
    {
        $item = SectionItem::factory()->for(Section::factory()->ofType(SectionType::Steps))->create(['group' => 'wel']);

        $this->actingAs(User::factory()->create())
            ->put(route('admin.sections.items.update', [$item->section, $item]), ['group' => 'stap', 'title' => 'Luisteren'])
            ->assertSessionHasNoErrors();

        $item->refresh();
        $this->assertSame('Luisteren', $item->title);
        $this->assertSame('wel', $item->group);
    }

    public function test_move_only_reorders_items_within_the_same_group(): void
    {
        $section = Section::factory()->ofType(SectionType::Steps)->create();
        $step = SectionItem::factory()->for($section)->create(['group' => 'stap']);
        $first = SectionItem::factory()->for($section)->create(['group' => 'wel']);
        $second = SectionItem::factory()->for($section)->create(['group' => 'wel']);

        $user = User::factory()->create();
        $this->actingAs($user)->patch(route('admin.sections.items.move', [$section, $first, 'up']));
        $this->assertSame(1, $first->refresh()->position);

        $this->actingAs($user)->patch(route('admin.sections.items.move', [$section, $second, 'up']));
        $this->assertSame([$second->id, $first->id], $section->itemsInGroup('wel')->pluck('id')->all());
        $this->assertSame(1, $step->refresh()->position);
    }

    public function test_destroy_deletes_the_item(): void
    {
        $item = SectionItem::factory()->create();

        $this->actingAs(User::factory()->create())
            ->delete(route('admin.sections.items.destroy', [$item->section, $item]))
            ->assertRedirect(route('admin.sections.edit', $item->section));

        $this->assertModelMissing($item);
    }
}
