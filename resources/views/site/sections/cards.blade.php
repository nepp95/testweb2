@use('App\Enums\CardIcon')
<section class="section {{ $section->backgroundClass() }}" id="{{ $section->anchor }}">
    <div class="container">
        @include('site.partials.heading')
        <ul class="cards">
            @foreach ($section->itemsInGroup('kaart') as $card)
                <li class="card">
                    @if ($icon = CardIcon::tryFrom((string) $card->field('icon')))
                        <svg class="card-icon" viewBox="0 0 24 24" aria-hidden="true">{!! $icon->paths() !!}</svg>
                    @endif
                    <h3>{{ $card->title }}</h3>
                    @if (filled($card->field('body')))
                        <p>{{ $card->field('body') }}</p>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
</section>
