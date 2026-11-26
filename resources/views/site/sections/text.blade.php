@use('App\Support\Markdown')
<section class="section {{ $section->backgroundClass() }}" id="{{ $section->anchor }}">
    <div class="container narrow">
        @include('site.partials.heading')
        <div class="rich-text">{{ Markdown::block($section->field('body')) }}</div>
    </div>
</section>
