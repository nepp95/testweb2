<?php

namespace App\Enums;

enum CardIcon: string
{
    case Money = 'geld';
    case Housing = 'wonen';
    case Paperwork = 'papierwerk';
    case Health = 'zorg';
    case Work = 'werk';
    case People = 'mensen';

    /**
     * Get the label shown in the admin.
     */
    public function label(): string
    {
        return match ($this) {
            self::Money => 'Geld (portemonnee)',
            self::Housing => 'Wonen (huis)',
            self::Paperwork => 'Papierwerk (document)',
            self::Health => 'Zorg (hart)',
            self::Work => 'Werk (koffer)',
            self::People => 'Mensen (twee personen)',
        };
    }

    /**
     * Get the inner SVG markup for a 24x24 stroke icon.
     */
    public function paths(): string
    {
        return match ($this) {
            self::Money => '<path d="M4 7h16v12H4z"/><path d="M4 7l2-3h12l2 3"/><circle cx="12" cy="13" r="2.5"/>',
            self::Housing => '<path d="M3 11l9-7 9 7"/><path d="M5 10v10h14V10"/><path d="M10 20v-6h4v6"/>',
            self::Paperwork => '<path d="M6 3h9l4 4v14H6z"/><path d="M15 3v4h4"/><path d="M9 12h7M9 16h7"/>',
            self::Health => '<path d="M12 21s-7-4.5-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 11c0 5.5-7 10-7 10z"/>',
            self::Work => '<rect x="3" y="7" width="18" height="13" rx="2"/><path d="M9 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2"/><path d="M3 13h18"/>',
            self::People => '<circle cx="9" cy="8" r="3"/><circle cx="17" cy="9" r="2.5"/><path d="M3 20c0-3.3 2.7-6 6-6s6 2.7 6 6"/><path d="M15 14.5c.6-.3 1.3-.5 2-.5 2.2 0 4 1.8 4 4"/>',
        };
    }

    /**
     * Get all icons as select options.
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $icon) => [$icon->value => $icon->label()])->all();
    }
}
