@use('App\Enums\SettingKey')
@use('App\Support\Markdown')
@php
    $fieldErrors = [
        'naam' => 'Vul een naam in, zodat we weten hoe we je kunnen aanspreken.',
        'contact' => 'Vul een telefoonnummer of e-mailadres in.',
        'privacy' => 'Vink dit aan, anders mogen we geen contact met je opnemen.',
    ];
@endphp
<section class="section {{ $section->backgroundClass() }}" id="{{ $section->anchor }}">
    <div class="container two-col contact-grid">
        <div>
            @include('site.partials.heading')
            <div class="rich-text">{{ Markdown::block($section->field('body')) }}</div>
            <ul class="contact-list">
                @if (filled($settings->get(SettingKey::Phone)))
                    <li>
                        <span class="contact-label">Bellen</span>
                        @if ($settings->phoneHref())
                            <a href="{{ $settings->phoneHref() }}">{{ $settings->get(SettingKey::Phone) }}</a>
                        @else
                            <span>{{ $settings->get(SettingKey::Phone) }}</span>
                        @endif
                        @if (filled($settings->get(SettingKey::PhoneHours)))
                            <span class="contact-note">{{ $settings->get(SettingKey::PhoneHours) }}</span>
                        @endif
                    </li>
                @endif
                @if (filled($settings->get(SettingKey::WhatsApp)))
                    <li>
                        <span class="contact-label">WhatsApp</span>
                        @if ($settings->whatsAppHref())
                            <a href="{{ $settings->whatsAppHref() }}">{{ $settings->get(SettingKey::WhatsApp) }}</a>
                        @else
                            <span>{{ $settings->get(SettingKey::WhatsApp) }}</span>
                        @endif
                    </li>
                @endif
                @if (filled($settings->get(SettingKey::Email)))
                    <li>
                        <span class="contact-label">E-mail</span>
                        @if ($settings->publicEmail())
                            <a href="mailto:{{ $settings->publicEmail() }}">{{ $settings->publicEmail() }}</a>
                        @else
                            <span>{{ $settings->get(SettingKey::Email) }}</span>
                        @endif
                    </li>
                @endif
                @if (filled($settings->get(SettingKey::Address)))
                    <li>
                        <span class="contact-label">Langskomen</span>
                        <span>{{ collect([$settings->get(SettingKey::VisitNote), $settings->get(SettingKey::Address)])->filter()->implode(' — ') }}</span>
                    </li>
                @endif
            </ul>
        </div>

        <form class="contact-form" id="contact-form" method="POST" action="{{ route('contact.store') }}" novalidate>
            @csrf

            @if (session('contact-status') === 'sent')
                <p class="form-success" role="status">Bedankt, je bericht is verstuurd. We nemen zo snel mogelijk contact met je op.</p>
            @endif
            @error('form')
                <p class="form-alert" role="alert">{{ $message }}</p>
            @enderror

            @if ($subjects->isNotEmpty())
                <div class="field">
                    <label for="f-onderwerp">Waar gaat het over?</label>
                    <select id="f-onderwerp" name="onderwerp" @error('onderwerp') aria-invalid="true" aria-describedby="f-onderwerp-error" @enderror>
                        @foreach ($subjects as $subject)
                            <option @selected(old('onderwerp') === $subject)>{{ $subject }}</option>
                        @endforeach
                    </select>
                    @error('onderwerp')
                        <p class="field-error" id="f-onderwerp-error">{{ $message }}</p>
                    @enderror
                </div>
            @endif

            <div class="field">
                <label for="f-naam">Je naam <span class="optional">(mag ook alleen je voornaam)</span></label>
                <input id="f-naam" name="naam" type="text" autocomplete="given-name" maxlength="100" required value="{{ old('naam') }}" data-error="f-naam-error" @error('naam') aria-invalid="true" aria-describedby="f-naam-error" @enderror>
                <p class="field-error" id="f-naam-error" @error('naam') @else hidden @enderror>{{ $errors->first('naam') ?: $fieldErrors['naam'] }}</p>
            </div>

            <div class="field">
                <label for="f-contact">Hoe kunnen we je bereiken?</label>
                <input id="f-contact" name="contact" type="text" placeholder="Telefoonnummer of e-mailadres" maxlength="255" required value="{{ old('contact') }}" data-error="f-contact-error" @error('contact') aria-invalid="true" aria-describedby="f-contact-error" @enderror>
                <p class="field-error" id="f-contact-error" @error('contact') @else hidden @enderror>{{ $errors->first('contact') ?: $fieldErrors['contact'] }}</p>
            </div>

            <div class="field">
                <label for="f-bericht">Wat speelt er? <span class="optional">(niet verplicht)</span></label>
                <textarea id="f-bericht" name="bericht" rows="5" maxlength="5000" @error('bericht') aria-invalid="true" aria-describedby="f-bericht-error" @enderror>{{ old('bericht') }}</textarea>
                @error('bericht')
                    <p class="field-error" id="f-bericht-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="field-trap" aria-hidden="true">
                <label for="f-website">Laat dit veld leeg</label>
                <input id="f-website" name="website" type="text" tabindex="-1" autocomplete="off">
            </div>

            <div class="field field-check">
                <input id="f-privacy" name="privacy" type="checkbox" value="1" required @checked(old('privacy')) data-error="f-privacy-error" @error('privacy') aria-invalid="true" aria-describedby="f-privacy-error" @enderror>
                <label for="f-privacy">Ik ga akkoord dat jullie mijn gegevens gebruiken om contact met mij op te nemen. (<a href="{{ route('privacy') }}">privacyverklaring</a>)</label>
            </div>
            <p class="field-error" id="f-privacy-error" @error('privacy') @else hidden @enderror>{{ $errors->first('privacy') ?: $fieldErrors['privacy'] }}</p>

            <button class="btn btn-primary" type="submit">Verstuur</button>
        </form>
    </div>
</section>
