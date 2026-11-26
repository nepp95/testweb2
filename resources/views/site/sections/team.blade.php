<section class="section {{ $section->backgroundClass() }}" id="{{ $section->anchor }}">
    <div class="container">
        @include('site.partials.heading')
        <ul class="cards">
            @foreach ($section->itemsInGroup('persoon') as $person)
                <li class="card">
                    <h3>{{ $person->title }}</h3>
                    @if (filled($person->field('role')))
                        <p class="card-role">{{ $person->field('role') }}</p>
                    @endif
                    @if (filled($person->field('body')))
                        <p>{{ $person->field('body') }}</p>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
</section>
