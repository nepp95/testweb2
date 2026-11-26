@use('App\Support\Markdown')
@php($quotes = $section->itemsInGroup('citaat'))
<section class="section {{ $section->backgroundClass() }}" id="{{ $section->anchor }}">
    <div @class(['container', 'two-col' => $quotes->isNotEmpty()])>
        <div>
            @include('site.partials.heading')
            <div class="rich-text">{{ Markdown::block($section->field('body')) }}</div>
        </div>
        @if ($quotes->isNotEmpty())
            <div>
                @if (filled($section->field('aside_title')))
                    <p class="quotes-title">{{ $section->field('aside_title') }}</p>
                @endif
                <ul class="quotes">
                    @foreach ($quotes as $quote)
                        <li>“{{ $quote->title }}”</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</section>
