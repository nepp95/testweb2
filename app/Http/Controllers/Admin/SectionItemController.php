<?php

namespace App\Http\Controllers\Admin;

use App\Enums\SectionType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveSectionItemRequest;
use App\Models\Section;
use App\Models\SectionItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SectionItemController extends Controller
{
    /**
     * Show the form for a new item in one of the section's groups.
     */
    public function create(Request $request, Section $section): View
    {
        $group = (string) $request->query('group');

        abort_unless(array_key_exists($group, $section->type->itemGroups()), 404);

        return view('admin.items.form', [
            'section' => $section,
            'item' => new SectionItem(['group' => $group]),
            'group' => $group,
            'subjectOptions' => $this->subjectOptions(),
        ]);
    }

    /**
     * Store a new item at the end of its group.
     */
    public function store(SaveSectionItemRequest $request, Section $section): RedirectResponse
    {
        $section->items()->create([
            ...$request->itemAttributes(),
            'group' => $request->group(),
        ]);

        return to_route('admin.sections.edit', $section)->with('notice', 'Het onderdeel is toegevoegd.');
    }

    /**
     * Show the form for editing an item.
     */
    public function edit(Section $section, SectionItem $item): View
    {
        return view('admin.items.form', [
            'section' => $section,
            'item' => $item,
            'group' => $item->group,
            'subjectOptions' => $this->subjectOptions(),
        ]);
    }

    /**
     * Update an item.
     */
    public function update(SaveSectionItemRequest $request, Section $section, SectionItem $item): RedirectResponse
    {
        $item->update($request->itemAttributes());

        return to_route('admin.sections.edit', $section)->with('notice', 'Het onderdeel is opgeslagen.');
    }

    /**
     * Move an item one place up or down within its group.
     */
    public function move(Section $section, SectionItem $item, string $direction): RedirectResponse
    {
        $item->move($direction);

        return to_route('admin.sections.edit', $section);
    }

    /**
     * Delete an item.
     */
    public function destroy(Section $section, SectionItem $item): RedirectResponse
    {
        $item->delete();

        return to_route('admin.sections.edit', $section)->with('notice', 'Het onderdeel is verwijderd.');
    }

    /**
     * Get the contact form subjects a button can preselect.
     *
     * @return array<int, string>
     */
    private function subjectOptions(): array
    {
        return SectionItem::query()
            ->where('group', SectionType::CONTACT_SUBJECT_GROUP)
            ->whereRelation('section', 'type', SectionType::Contact)
            ->orderBy('position')
            ->pluck('title', 'id')
            ->all();
    }
}
