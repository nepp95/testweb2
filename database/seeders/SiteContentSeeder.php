<?php

namespace Database\Seeders;

use App\Enums\CardIcon;
use App\Enums\SectionType;
use App\Models\Section;
use Illuminate\Database\Seeder;

/**
 * Fills an empty website with the sections and texts of the reference design.
 */
class SiteContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Section::query()->exists()) {
            $this->command?->warn('Er staan al secties in de database; de standaardinhoud is niet toegevoegd.');

            return;
        }

        $hero = $this->section(SectionType::Hero, 'Vastgelopen tussen regels, loketten en formulieren?', 'welkom', null, [
            'eyebrow' => 'Voor iedereen die tussen wal en schip valt',
            'title_accent' => 'Wij lopen met je mee.',
            'lead' => 'Soms past je situatie in geen enkel hokje. Je wordt van het ene loket naar het andere gestuurd en niemand voelt zich verantwoordelijk. Wij luisteren, zoeken uit wat er kan en blijven naast je staan tot er een oplossing is.',
            'primary_label' => 'Ik zoek hulp',
            'secondary_label' => 'Ik wil helpen',
        ], [
            'kenmerk' => ['Gratis', 'Vertrouwelijk', 'Zonder oordeel', 'Geen verwijsbrief nodig'],
        ]);

        $this->section(SectionType::About, 'Wat betekent ‘tussen wal en schip’?', 'over-ons', 'Over ons', [
            'eyebrow' => 'Over ons',
            'body' => implode("\n\n", [
                'Nederland heeft veel regelingen, loketten en hulpverleners. Toch zijn er mensen voor wie niets lijkt te passen. Ze voldoen nét niet aan de regels, hun probleem hoort bij niemand, of ze raken de weg kwijt in brieven en formulieren.',
                '**Stichting [naam]** is er voor hen. Onze vrijwilligers en professionals kennen de weg. We gaan mee naar afspraken, bellen met instanties, helpen met papierwerk en zoeken net zo lang tot er iemand is die wél kan helpen.',
                'We nemen het werk van andere organisaties niet over. We zijn de schakel ertussen.',
            ]),
            'aside_title' => 'Herken je dit?',
            'background' => 'wit',
        ], [
            'citaat' => [
                'Ik verdien net te veel voor toeslag, maar te weinig om rond te komen.',
                'Zonder adres krijg ik geen uitkering. Zonder uitkering krijg ik geen woning.',
                'De gemeente stuurt me naar de zorgverzekeraar, en die stuurt me terug.',
                'Ik ben net 18 en moet ineens alles zelf regelen.',
                'Ik durf de envelop niet meer open te maken.',
            ],
        ]);

        $this->section(SectionType::Cards, 'Geen probleem is te klein of te ingewikkeld', 'hulp', 'Waarmee we helpen', [
            'eyebrow' => 'Waarmee we helpen',
            'intro' => 'Weet je niet waar je moet beginnen? Dat hoeft ook niet. Vertel ons wat er speelt, dan zoeken we samen uit wat de eerste stap is.',
            'background' => 'zand',
        ], [
            'kaart' => [
                ['Geld en schulden', ['body' => 'Rekeningen die zich opstapelen, aanmaningen, een beslag. We helpen overzicht maken en brengen je in contact met schuldhulp.', 'icon' => CardIcon::Money->value]],
                ['Wonen', ['body' => 'Dreigende uitzetting, geen vast adres of een woning die niet meer veilig is. We zoeken mee naar opvang, een briefadres of een oplossing met de verhuurder.', 'icon' => CardIcon::Housing->value]],
                ['Instanties en papierwerk', ['body' => 'Brieven van de gemeente, Belastingdienst, UWV of DUO die je niet begrijpt. We lezen mee, helpen met bezwaar en bellen samen met je.', 'icon' => CardIcon::Paperwork->value]],
                ['Zorg en gezondheid', ['body' => 'Geen zorgverzekering, geen huisarts of zorg die niemand wil betalen. We zoeken uit waar je recht op hebt en wie dit kan regelen.', 'icon' => CardIcon::Health->value]],
                ['Werk en inkomen', ['body' => 'Een uitkering die stopt, geen inkomen tijdens het wachten of werk dat niet genoeg oplevert. We kijken welke regelingen er zijn.', 'icon' => CardIcon::Work->value]],
                ['Er alleen voor staan', ['body' => 'Soms is het grootste probleem dat er niemand is om mee te praten. We luisteren, denken mee en helpen je je netwerk weer op te bouwen.', 'icon' => CardIcon::People->value]],
            ],
        ]);

        $this->section(SectionType::Steps, 'In drie stappen', 'hoe-het-werkt', 'Hoe het werkt', [
            'eyebrow' => 'Hoe het werkt',
            'do_title' => 'Wat we wel doen',
            'dont_title' => 'Wat we niet doen',
            'background' => 'wit',
        ], [
            'stap' => [
                ['Je neemt contact op', ['body' => 'Bel, mail, app of vul het formulier in. Je hoeft niet alles al uit te kunnen leggen. Iemand anders mag ook namens jou contact opnemen.']],
                ['We maken kennis', ['body' => 'Binnen een paar werkdagen spreken we af. Bij jou thuis, bij ons of ergens anders waar jij je prettig voelt. We luisteren naar je hele verhaal.']],
                ['We lopen met je mee', ['body' => 'Je krijgt één vast aanspreekpunt. Samen maken we een plan en zetten we de stappen. We blijven betrokken tot het geregeld is.']],
            ],
            'wel' => [
                'Luisteren zonder oordeel',
                'Uitzoeken waar je recht op hebt',
                'Meegaan naar afspraken en gesprekken',
                'Instanties aanspreken en bij elkaar brengen',
                'Je doorverwijzen naar de juiste hulp, en controleren of het lukt',
            ],
            'niet' => [
                'Geld uitlenen of schulden betalen',
                'Medische of psychische behandeling geven',
                'Beslissingen nemen zonder jou',
                'Informatie delen zonder jouw toestemming',
            ],
        ]);

        $support = $this->section(SectionType::Support, 'Samen vangen we meer mensen op', 'help-mee', 'Help mee', [
            'eyebrow' => 'Help mee',
            'intro' => 'Wij bestaan dankzij mensen die hun tijd, kennis of geld willen geven. Doe je mee?',
        ]);

        $this->section(SectionType::Faq, 'Goed om te weten', 'vragen', 'Vragen', [
            'eyebrow' => 'Veelgestelde vragen',
            'background' => 'wit',
        ], [
            'vraag' => [
                ['Kost jullie hulp geld?', ['body' => 'Nee. Onze hulp is altijd gratis. We worden betaald uit giften en fondsen.']],
                ['Heb ik een verwijzing nodig?', ['body' => 'Nee. Je kunt zelf contact met ons opnemen. Een familielid, buurman of hulpverlener mag dat ook voor je doen, als jij dat goed vindt.']],
                ['Wat als ik al hulp krijg van een andere organisatie?', ['body' => 'Dat is geen probleem. Vaak helpt het juist als iemand het overzicht houdt. Wij werken graag samen met andere hulpverleners, maar alleen met jouw toestemming.']],
                ['Wat gebeurt er met mijn gegevens?', ['body' => 'We gaan zorgvuldig om met alles wat je ons vertelt. We vragen alleen wat we nodig hebben en delen niets zonder jouw toestemming. Lees meer in onze [privacyverklaring](/privacy).']],
                ['Ik spreek niet goed Nederlands. Kan ik toch hulp krijgen?', ['body' => 'Ja. Neem contact op in de taal die jij het fijnst vindt. We zoeken samen naar een manier om elkaar te begrijpen, bijvoorbeeld met een tolk.']],
                ['In welke regio zijn jullie actief?', ['body' => '[Werkgebied invullen, bijvoorbeeld: de gemeente … en omgeving.] Woon je ergens anders? Neem toch contact op, dan helpen we je op weg naar een organisatie bij jou in de buurt.']],
            ],
        ]);

        $this->section(SectionType::Team, 'Het bestuur', 'bestuur', null, [
            'eyebrow' => 'Wie we zijn',
            'background' => 'wit',
        ], [
            'persoon' => [
                ['[naam]', ['role' => 'Voorzitter']],
                ['[naam]', ['role' => 'Secretaris']],
                ['[naam]', ['role' => 'Penningmeester']],
            ],
        ], isVisible: false);

        $contact = $this->section(SectionType::Contact, 'Vertel ons wat er speelt', 'contact', 'Hulp vragen', [
            'eyebrow' => 'Hulp vragen of contact',
            'body' => 'Je hoeft het niet in één keer goed uit te leggen. Een paar zinnen is genoeg. We nemen binnen twee werkdagen contact met je op.',
            'background' => 'zand',
        ], [
            SectionType::CONTACT_SUBJECT_GROUP => [
                'Ik zoek hulp voor mezelf',
                'Ik zoek hulp voor iemand anders',
                'Ik wil vrijwilliger worden',
                'Ik wil iemand doorverwijzen',
                'Iets anders',
            ],
        ]);

        $subjectIds = $contact->items()->pluck('id', 'title');

        $this->items($support, 'kaart', [
            ['Word vrijwilliger', [
                'body' => 'Heb je een paar uur per week? Als maatje loop je mee met iemand die vastzit. Je krijgt een training en begeleiding. Kennis van regelingen, juridische zaken of zorg is extra welkom.',
                'button_label' => 'Meld je aan',
                'button_subject' => $subjectIds['Ik wil vrijwilliger worden'],
                'show_iban' => false,
            ]],
            ['Doneer', [
                'body' => 'Met jouw gift betalen we trainingen voor vrijwilligers, reiskosten naar afspraken en de kosten van onze bereikbaarheid. Elk bedrag helpt.',
                'show_iban' => true,
            ]],
            ['Verwijs door', [
                'body' => 'Werk je bij een gemeente, wijkteam, huisartsenpraktijk of andere organisatie? Loop je vast met een cliënt die nergens past? Neem contact op, dan kijken we samen wat we kunnen doen.',
                'button_label' => 'Neem contact op',
                'button_subject' => $subjectIds['Ik wil iemand doorverwijzen'],
                'show_iban' => false,
            ]],
        ]);

        $hero->update(['content' => [
            ...$hero->content,
            'primary_target' => $contact->id,
            'secondary_target' => $support->id,
        ]]);
    }

    /**
     * Create a section with its items.
     *
     * @param  array<string, mixed>  $content
     * @param  array<string, list<string|array{0: string, 1: array<string, mixed>}>>  $itemsByGroup
     */
    private function section(
        SectionType $type,
        string $title,
        string $anchor,
        ?string $navLabel,
        array $content,
        array $itemsByGroup = [],
        bool $isVisible = true,
    ): Section {
        $section = Section::query()->create([
            'type' => $type,
            'title' => $title,
            'anchor' => $anchor,
            'nav_label' => $navLabel,
            'is_visible' => $isVisible,
            'content' => $content,
        ]);

        foreach ($itemsByGroup as $group => $items) {
            $this->items($section, $group, $items);
        }

        return $section;
    }

    /**
     * Create items in one group of a section. Each item is a title, or a title with its content.
     *
     * @param  list<string|array{0: string, 1: array<string, mixed>}>  $items
     */
    private function items(Section $section, string $group, array $items): void
    {
        foreach ($items as $item) {
            [$title, $content] = is_array($item) ? $item : [$item, []];

            $section->items()->create(['group' => $group, 'title' => $title, 'content' => $content]);
        }
    }
}
