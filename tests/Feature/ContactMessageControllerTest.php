<?php

namespace Tests\Feature;

use App\Enums\SectionType;
use App\Enums\SettingKey;
use App\Mail\ContactMessage;
use App\Models\Section;
use App\Models\SectionItem;
use App\Support\SiteSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactMessageControllerTest extends TestCase
{
    use RefreshDatabase;

    private Section $contactSection;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();

        $this->contactSection = Section::factory()->ofType(SectionType::Contact)->create();
        SectionItem::factory()->for($this->contactSection)->create(['group' => SectionType::CONTACT_SUBJECT_GROUP, 'title' => 'Ik zoek hulp voor mezelf']);

        app(SiteSettings::class)->update([SettingKey::Email->value => 'info@stichting.test']);
    }

    public function test_valid_message_is_mailed_to_the_public_address(): void
    {
        $this->post(route('contact.store'), $this->validPayload())
            ->assertRedirect(route('home').'#contact-form')
            ->assertSessionHas('contact-status', 'sent');

        Mail::assertSent(ContactMessage::class, function (ContactMessage $mail): bool {
            return $mail->hasTo('info@stichting.test')
                && $mail->hasReplyTo('anna@example.test')
                && $mail->onderwerp === 'Ik zoek hulp voor mezelf'
                && $mail->bericht === 'Ik loop vast met mijn toeslag.';
        });
    }

    public function test_message_goes_to_the_form_recipient_when_one_is_set(): void
    {
        app(SiteSettings::class)->update([SettingKey::FormRecipient->value => 'aanmeldingen@stichting.test']);

        $this->post(route('contact.store'), $this->validPayload());

        Mail::assertSent(ContactMessage::class, fn (ContactMessage $mail) => $mail->hasTo('aanmeldingen@stichting.test'));
    }

    public function test_phone_number_as_contact_does_not_set_a_reply_to(): void
    {
        $this->post(route('contact.store'), $this->validPayload(['contact' => '06 12345678']));

        Mail::assertSent(ContactMessage::class, fn (ContactMessage $mail) => $mail->replyTo === []);
    }

    public function test_rejects_missing_required_fields_with_dutch_messages(): void
    {
        $this->post(route('contact.store'), ['onderwerp' => 'Ik zoek hulp voor mezelf'])
            ->assertRedirect(route('home').'#contact-form')
            ->assertSessionHasErrors([
                'naam' => 'Vul een naam in, zodat we weten hoe we je kunnen aanspreken.',
                'contact' => 'Vul een telefoonnummer of e-mailadres in.',
                'privacy' => 'Vink dit aan, anders mogen we geen contact met je opnemen.',
            ]);

        Mail::assertNothingSent();
    }

    public function test_rejects_a_subject_that_is_not_offered(): void
    {
        $this->post(route('contact.store'), $this->validPayload(['onderwerp' => 'Verzonnen onderwerp']))
            ->assertSessionHasErrors('onderwerp');

        Mail::assertNothingSent();
    }

    public function test_filled_honeypot_pretends_success_without_sending(): void
    {
        $this->post(route('contact.store'), $this->validPayload(['website' => 'https://spam.test']))
            ->assertSessionHas('contact-status', 'sent');

        Mail::assertNothingSent();
    }

    public function test_reports_failure_when_no_valid_recipient_is_configured(): void
    {
        app(SiteSettings::class)->update([SettingKey::Email->value => '[email]']);

        $this->post(route('contact.store'), $this->validPayload())
            ->assertSessionHasErrors('form')
            ->assertSessionHasInput('naam', 'Anna');

        Mail::assertNothingSent();
    }

    public function test_returns_404_while_the_contact_section_is_hidden(): void
    {
        $this->contactSection->update(['is_visible' => false]);

        $this->post(route('contact.store'), $this->validPayload())->assertNotFound();

        Mail::assertNothingSent();
    }

    public function test_limits_messages_per_visitor(): void
    {
        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $this->post(route('contact.store'), $this->validPayload())->assertSessionHas('contact-status', 'sent');
        }

        $this->post(route('contact.store'), $this->validPayload())
            ->assertRedirect(route('home').'#contact-form')
            ->assertSessionHasErrors('form');

        Mail::assertSentCount(5);
    }

    public function test_mail_contains_the_submitted_details(): void
    {
        $mail = new ContactMessage(naam: 'Anna & Co', contact: '06 12345678', onderwerp: 'Iets anders', bericht: 'Hallo <daar>');

        $mail->assertSeeInText('Naam: Anna & Co');
        $mail->assertSeeInText('Bereikbaar via: 06 12345678');
        $mail->assertSeeInText('Hallo <daar>');
        $mail->assertHasSubject('Contactformulier: Iets anders — Anna & Co');
    }

    /**
     * @param  array<string, string>  $overrides
     * @return array<string, string>
     */
    private function validPayload(array $overrides = []): array
    {
        return [
            'onderwerp' => 'Ik zoek hulp voor mezelf',
            'naam' => 'Anna',
            'contact' => 'anna@example.test',
            'bericht' => 'Ik loop vast met mijn toeslag.',
            'privacy' => '1',
            ...$overrides,
        ];
    }
}
