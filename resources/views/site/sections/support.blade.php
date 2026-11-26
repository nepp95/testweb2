@use('App\Enums\SettingKey')
<section class="section section-dark" id="{{ $section->anchor }}">
    <div class="container">
        @include('site.partials.heading')
        <ul class="cards cards-3">
            @foreach ($section->itemsInGroup('kaart') as $card)
                <li class="card card-on-dark">
                    <h3>{{ $card->title }}</h3>
                    @if (filled($card->field('body')))
                        <p>{{ $card->field('body') }}</p>
                    @endif
                    @if ($card->field('show_iban') && filled($settings->get(SettingKey::Iban)))
                        <p class="iban">IBAN: <span>{{ $settings->get(SettingKey::Iban) }}</span><br>t.n.v. <span>{{ $settings->get(SettingKey::Name) }}</span></p>
                    @endif
                    @if (filled($card->field('button_label')) && $contactSection)
                        <a class="btn btn-light" href="#{{ $contactSection->anchor }}" @if ($subject = $subjects[$card->field('button_subject')] ?? null) data-onderwerp="{{ $subject }}" @endif>{{ $card->field('button_label') }}</a>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
</section>
