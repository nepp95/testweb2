@if (filled($section->field('eyebrow')))
    <p class="eyebrow">{{ $section->field('eyebrow') }}</p>
@endif
<h2>{{ $section->title }}</h2>
@if (filled($section->field('intro')))
    <p class="section-intro">{{ $section->field('intro') }}</p>
@endif
