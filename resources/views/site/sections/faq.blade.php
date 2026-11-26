@use('App\Support\Markdown')
<section class="section {{ $section->backgroundClass() }}" id="{{ $section->anchor }}">
    <div class="container narrow">
        @include('site.partials.heading')
        <div class="faq">
            @foreach ($section->itemsInGroup('vraag') as $question)
                <details>
                    <summary>{{ $question->title }}</summary>
                    <div class="rich-text">{{ Markdown::block($question->field('body')) }}</div>
                </details>
            @endforeach
        </div>
    </div>
</section>
