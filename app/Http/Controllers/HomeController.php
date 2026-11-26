<?php

namespace App\Http\Controllers;

use App\Enums\SectionType;
use App\Models\Section;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Show the single page with all visible sections.
     */
    public function __invoke(): View
    {
        $sections = Section::query()->visible()->ordered()->with('items')->get();
        $contactSection = $sections->firstWhere('type', SectionType::Contact);

        return view('site.home', [
            'sections' => $sections,
            'anchors' => $sections->pluck('anchor', 'id'),
            'contactSection' => $contactSection,
            'subjects' => $contactSection?->itemsInGroup(SectionType::CONTACT_SUBJECT_GROUP)->pluck('title', 'id') ?? collect(),
        ]);
    }
}
