<?php

namespace App\Enums;

use App\Support\FormField;

enum SectionType: string
{
    case Hero = 'hero';
    case About = 'about';
    case Cards = 'cards';
    case Steps = 'steps';
    case Support = 'support';
    case Faq = 'faq';
    case Team = 'team';
    case Contact = 'contact';
    case Text = 'text';

    /**
     * The item group of the contact section that holds the form subjects.
     */
    public const string CONTACT_SUBJECT_GROUP = 'onderwerp';

    /**
     * Get the label shown in the admin.
     */
    public function label(): string
    {
        return match ($this) {
            self::Hero => 'Openingsblok',
            self::About => 'Tekst met citaten',
            self::Cards => 'Kaarten met iconen',
            self::Steps => 'Stappen met wel/niet-lijst',
            self::Support => 'Help mee (donker blok)',
            self::Faq => 'Veelgestelde vragen',
            self::Team => 'Team / bestuur',
            self::Contact => 'Contact en formulier',
            self::Text => 'Tekstblok',
        };
    }

    /**
     * Determine whether the page may contain at most one section of this type.
     */
    public function isSingleton(): bool
    {
        return in_array($this, [self::Hero, self::Contact], true);
    }

    /**
     * Get the type-specific fields, stored in the section's content.
     *
     * @return list<FormField>
     */
    public function fields(): array
    {
        $eyebrow = new FormField('eyebrow', 'Kleine kop boven de titel');
        $intro = new FormField('intro', 'Inleiding', FieldType::Textarea);

        return match ($this) {
            self::Hero => [
                $eyebrow,
                new FormField('title_accent', 'Tweede deel van de titel', help: 'Komt op een eigen regel, in de accentkleur.'),
                new FormField('lead', 'Inleiding', FieldType::Textarea),
                new FormField('primary_label', 'Hoofdknop: tekst'),
                new FormField('primary_target', 'Hoofdknop: gaat naar', FieldType::SectionLink),
                new FormField('secondary_label', 'Tweede knop: tekst'),
                new FormField('secondary_target', 'Tweede knop: gaat naar', FieldType::SectionLink),
            ],
            self::About => [
                $eyebrow,
                new FormField('body', 'Tekst', FieldType::Markdown, required: true),
                new FormField('aside_title', 'Titel boven de citaten', default: 'Herken je dit?'),
                self::backgroundField('wit'),
            ],
            self::Cards => [$eyebrow, $intro, self::backgroundField('zand')],
            self::Steps => [
                $eyebrow,
                $intro,
                new FormField('do_title', 'Titel van de wel-lijst', default: 'Wat we wel doen'),
                new FormField('dont_title', 'Titel van de niet-lijst', default: 'Wat we niet doen'),
                self::backgroundField('wit'),
            ],
            self::Support => [$eyebrow, $intro],
            self::Faq, self::Team => [$eyebrow, $intro, self::backgroundField('wit')],
            self::Contact => [
                $eyebrow,
                new FormField('body', 'Tekst', FieldType::Markdown),
                self::backgroundField('zand'),
            ],
            self::Text => [
                $eyebrow,
                new FormField('body', 'Tekst', FieldType::Markdown, required: true),
                self::backgroundField('wit'),
            ],
        };
    }

    /**
     * Get the item groups of this type as group key => label.
     *
     * @return array<string, string>
     */
    public function itemGroups(): array
    {
        return match ($this) {
            self::Hero => ['kenmerk' => 'Kenmerken onder de knoppen'],
            self::About => ['citaat' => 'Citaten'],
            self::Cards, self::Support => ['kaart' => 'Kaarten'],
            self::Steps => ['stap' => 'Stappen', 'wel' => 'Wel-lijst', 'niet' => 'Niet-lijst'],
            self::Faq => ['vraag' => 'Vragen'],
            self::Team => ['persoon' => 'Personen'],
            self::Contact => [self::CONTACT_SUBJECT_GROUP => 'Onderwerpen in het formulier'],
            self::Text => [],
        };
    }

    /**
     * Get the fields of an item in the given group. The "title" field is stored in its own column.
     *
     * @return list<FormField>
     */
    public function itemFields(string $group): array
    {
        return match ($this) {
            self::Hero => [new FormField('title', 'Kenmerk', required: true)],
            self::About => [new FormField('title', 'Citaat', required: true)],
            self::Cards => [
                new FormField('title', 'Titel', required: true),
                new FormField('body', 'Tekst', FieldType::Textarea),
                new FormField('icon', 'Icoon', FieldType::Select, options: CardIcon::options()),
            ],
            self::Steps => $group === 'stap'
                ? [new FormField('title', 'Titel', required: true), new FormField('body', 'Tekst', FieldType::Textarea)]
                : [new FormField('title', 'Tekst', required: true)],
            self::Support => [
                new FormField('title', 'Titel', required: true),
                new FormField('body', 'Tekst', FieldType::Textarea),
                new FormField('button_label', 'Knoptekst', help: 'De knop gaat naar het contactformulier.'),
                new FormField('button_subject', 'Onderwerp dat de knop vooraf kiest', FieldType::ContactSubject),
                new FormField('show_iban', 'Toon het IBAN-nummer op deze kaart', FieldType::Checkbox),
            ],
            self::Faq => [
                new FormField('title', 'Vraag', required: true),
                new FormField('body', 'Antwoord', FieldType::Markdown, required: true),
            ],
            self::Team => [
                new FormField('title', 'Naam', required: true),
                new FormField('role', 'Functie'),
                new FormField('body', 'Korte toelichting', FieldType::Textarea),
            ],
            self::Contact => [new FormField('title', 'Onderwerp', required: true)],
            self::Text => [],
        };
    }

    /**
     * Get the select options for all section types.
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $type) => [$type->value => $type->label()])->all();
    }

    /**
     * Build the background colour field.
     */
    private static function backgroundField(string $default): FormField
    {
        return new FormField('background', 'Achtergrond', FieldType::Select, required: true, options: [
            'wit' => 'Wit',
            'zand' => 'Zand',
        ], default: $default);
    }
}
