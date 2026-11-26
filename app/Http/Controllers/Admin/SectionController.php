<?php

namespace App\Http\Controllers\Admin;

use App\Enums\SectionType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveSectionRequest;
use App\Models\Section;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SectionController extends Controller
{
    /**
     * List all sections in page order.
     */
    public function index(): View
    {
        $sections = Section::query()->ordered()->withCount('items')->get();
        $usedTypes = $sections->pluck('type');

        return view('admin.sections.index', [
            'sections' => $sections,
            'availableTypes' => collect(SectionType::cases())
                ->reject(fn (SectionType $type) => $type->isSingleton() && $usedTypes->contains($type))
                ->values(),
        ]);
    }

    /**
     * Show the form for a new section of the chosen type.
     */
    public function create(Request $request): View|RedirectResponse
    {
        $type = SectionType::tryFrom((string) $request->query('type'));

        if ($type === null) {
            return to_route('admin.sections.index')->withErrors(['type' => 'Kies een type sectie.']);
        }

        if ($type->isSingleton() && Section::query()->where('type', $type)->exists()) {
            return to_route('admin.sections.index')->withErrors(['type' => 'Er is al een sectie van dit type.']);
        }

        return view('admin.sections.create', [
            'type' => $type,
            'sectionOptions' => $this->sectionOptions(),
        ]);
    }

    /**
     * Store a new section at the end of the page.
     */
    public function store(SaveSectionRequest $request): RedirectResponse
    {
        $section = new Section($request->sectionAttributes());
        $section->type = $request->sectionType();
        $section->save();

        return to_route('admin.sections.edit', $section)->with('notice', 'De sectie is toegevoegd.');
    }

    /**
     * Show the form for editing a section and its items.
     */
    public function edit(Section $section): View
    {
        $section->load('items');

        return view('admin.sections.edit', [
            'section' => $section,
            'sectionOptions' => $this->sectionOptions(),
        ]);
    }

    /**
     * Update a section.
     */
    public function update(SaveSectionRequest $request, Section $section): RedirectResponse
    {
        $section->update($request->sectionAttributes());

        return to_route('admin.sections.edit', $section)->with('notice', 'De wijzigingen zijn opgeslagen.');
    }

    /**
     * Show or hide a section on the website.
     */
    public function toggleVisibility(Section $section): RedirectResponse
    {
        $section->update(['is_visible' => ! $section->is_visible]);

        return to_route('admin.sections.index')->with('notice', $section->is_visible
            ? "‘{$section->title}’ staat weer op de website."
            : "‘{$section->title}’ is verborgen.");
    }

    /**
     * Move a section one place up or down.
     */
    public function move(Section $section, string $direction): RedirectResponse
    {
        $section->move($direction);

        return to_route('admin.sections.index');
    }

    /**
     * Delete a section and its items.
     */
    public function destroy(Section $section): RedirectResponse
    {
        $section->delete();

        return to_route('admin.sections.index')->with('notice', "‘{$section->title}’ is verwijderd.");
    }

    /**
     * Get the sections a button can link to.
     *
     * @return array<int, string>
     */
    private function sectionOptions(): array
    {
        return Section::query()->ordered()->pluck('title', 'id')->all();
    }
}
