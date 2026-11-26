<?php

namespace App\Enums;

use App\Support\FormField;

enum SettingKey: string
{
    case Name = 'naam';
    case Tagline = 'ondertitel';
    case MetaDescription = 'meta_omschrijving';
    case CrisisBar = 'crisisbalk';
    case FooterText = 'footer_tekst';
    case Phone = 'telefoon';
    case PhoneHours = 'telefoon_bereikbaar';
    case WhatsApp = 'whatsapp';
    case Email = 'email';
    case Address = 'adres';
    case VisitNote = 'bezoek';
    case FormRecipient = 'formulier_ontvanger';
    case Kvk = 'kvk';
    case Rsin = 'rsin';
    case Anbi = 'anbi';
    case Iban = 'iban';
    case PrivacyStatement = 'privacyverklaring';

    /**
     * Get the label shown in the admin.
     */
    public function label(): string
    {
        return match ($this) {
            self::Name => 'Naam van de stichting',
            self::Tagline => 'Ondertitel',
            self::MetaDescription => 'Omschrijving voor zoekmachines',
            self::CrisisBar => 'Crisisbalk bovenaan',
            self::FooterText => 'Tekst in de footer',
            self::Phone => 'Telefoonnummer',
            self::PhoneHours => 'Telefonisch bereikbaar',
            self::WhatsApp => 'WhatsApp-nummer',
            self::Email => 'E-mailadres',
            self::Address => 'Adres',
            self::VisitNote => 'Opmerking bij langskomen',
            self::FormRecipient => 'Berichten van het contactformulier sturen naar',
            self::Kvk => 'KvK-nummer',
            self::Rsin => 'RSIN',
            self::Anbi => 'ANBI-status',
            self::Iban => 'IBAN',
            self::PrivacyStatement => 'Privacyverklaring',
        };
    }

    /**
     * Get the group heading the setting is listed under in the admin.
     */
    public function group(): string
    {
        return match ($this) {
            self::Name, self::Tagline, self::MetaDescription, self::CrisisBar, self::FooterText => 'Algemeen',
            self::Phone, self::PhoneHours, self::WhatsApp, self::Email, self::Address, self::VisitNote, self::FormRecipient => 'Contactgegevens',
            self::Kvk, self::Rsin, self::Anbi, self::Iban => 'Officiële gegevens',
            self::PrivacyStatement => 'Privacyverklaring',
        };
    }

    /**
     * Get the form field used to edit this setting.
     */
    public function field(): FormField
    {
        $type = match ($this) {
            self::MetaDescription => FieldType::Textarea,
            self::CrisisBar, self::PrivacyStatement => FieldType::Markdown,
            self::FormRecipient => FieldType::Email,
            default => FieldType::Text,
        };

        $help = match ($this) {
            self::Name => 'Wordt gebruikt in de kop, de footer, de paginatitel en bij het IBAN-nummer.',
            self::Tagline => 'Staat achter de naam in de titel van het browsertabblad.',
            self::CrisisBar => 'Laat leeg om de balk te verbergen.',
            self::Phone => 'Laat een veld leeg om het op de website te verbergen.',
            self::WhatsApp => 'Gebruik het internationale formaat, bijvoorbeeld +31 6 12345678. De link naar WhatsApp wordt daaruit gemaakt.',
            self::Email => 'Wordt getoond op de website. Alleen een geldig adres wordt een klikbare link.',
            self::FormRecipient => 'Niet zichtbaar op de website. Laat leeg om het e-mailadres hierboven te gebruiken.',
            default => null,
        };

        return new FormField($this->value, $this->label(), $type, help: $help);
    }

    /**
     * Get the value used until the admin saves the setting.
     */
    public function default(): string
    {
        return match ($this) {
            self::Name => 'Stichting [naam]',
            self::Tagline => 'hulp als je tussen wal en schip valt',
            self::MetaDescription => 'Vastgelopen tussen regels, loketten en formulieren? Wij zoeken samen met jou naar een oplossing. Gratis, vertrouwelijk en zonder oordeel.',
            self::CrisisBar => '**Direct gevaar?** Bel [112](tel:112). · Denk je aan zelfdoding? Bel [113](tel:113) of gratis [0800-0113](tel:08000113).',
            self::FooterText => 'Voor mensen die tussen wal en schip vallen.',
            self::Phone => '[telefoonnummer]',
            self::PhoneHours => 'maandag t/m vrijdag, 9.00–17.00 uur',
            self::WhatsApp => '[WhatsApp-nummer]',
            self::Email => '[email]',
            self::Address => '[adres]',
            self::VisitNote => 'Alleen op afspraak',
            self::FormRecipient => '',
            self::Kvk, self::Rsin => '[nummer]',
            self::Anbi => '[aangevraagd / ja]',
            self::Iban => 'NL00 BANK 0000 0000 00',
            self::PrivacyStatement => self::defaultPrivacyStatement(),
        };
    }

    /**
     * Get the concept privacy statement, based on the reference design.
     */
    private static function defaultPrivacyStatement(): string
    {
        return <<<'MARKDOWN'
            *Let op: dit is een concepttekst. Laat deze controleren voordat de website live gaat.*

            Stichting [naam] helpt mensen in kwetsbare situaties. Je vertrouwt ons vaak persoonlijke informatie toe. Daar gaan we zorgvuldig mee om, volgens de Algemene verordening gegevensbescherming (AVG).

            ## Welke gegevens we gebruiken

            - Je naam en contactgegevens (telefoonnummer of e-mailadres)
            - Wat je ons vertelt over je situatie, voor zover nodig om je te helpen
            - Afspraken die we samen maken

            ## Waarvoor we ze gebruiken

            Alleen om contact met je op te nemen en je te helpen. We gebruiken je gegevens niet voor reclame en verkopen ze nooit.

            ## Met wie we gegevens delen

            Alleen met organisaties waarmee we samen aan jouw situatie werken, en alleen als jij daar toestemming voor geeft. Uitzondering: als de wet ons verplicht, of bij acuut gevaar voor jou of anderen.

            ## Hoe lang we gegevens bewaren

            Niet langer dan nodig. Na afronding van de hulp bewaren we je gegevens maximaal [termijn invullen], daarna verwijderen we ze.

            ## Jouw rechten

            Je mag altijd vragen welke gegevens we van je hebben, en ze laten aanpassen of verwijderen. Mail of bel ons, dan regelen we dat. Ben je niet tevreden over hoe we met je gegevens omgaan? Dan kun je een klacht indienen bij de Autoriteit Persoonsgegevens.

            ## Deze website

            Berichten die je via het contactformulier stuurt, worden per e-mail naar ons verstuurd en niet op de website opgeslagen. De website plaatst alleen functionele cookies die nodig zijn om het formulier veilig te laten werken. We gebruiken geen cookies om je te volgen. Lettertypes worden vanaf deze website zelf geladen, niet via een externe dienst.

            ## Contact

            Stichting [naam]\
            [adres]\
            [email]\
            KvK: [nummer]
            MARKDOWN;
    }
}
