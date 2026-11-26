@php
    $buttons = collect([
        ['label' => $section->field('primary_label'), 'target' => $anchors[$section->field('primary_target')] ?? null, 'class' => 'btn btn-primary'],
        ['label' => $section->field('secondary_label'), 'target' => $anchors[$section->field('secondary_target')] ?? null, 'class' => 'btn btn-ghost'],
    ])->filter(fn (array $button) => filled($button['label']) && filled($button['target']));
    $features = $section->itemsInGroup('kenmerk');
@endphp
<section class="hero" id="{{ $section->anchor }}">
    <div class="container hero-inner">
        <div class="hero-text">
            @if (filled($section->field('eyebrow')))
                <p class="eyebrow">{{ $section->field('eyebrow') }}</p>
            @endif
            <h1>
                {{ $section->title }}
                @if (filled($section->field('title_accent')))
                    <span class="accent">{{ $section->field('title_accent') }}</span>
                @endif
            </h1>
            @if (filled($section->field('lead')))
                <p class="lead">{{ $section->field('lead') }}</p>
            @endif
            @if ($buttons->isNotEmpty())
                <div class="hero-actions">
                    @foreach ($buttons as $button)
                        <a class="{{ $button['class'] }}" href="#{{ $button['target'] }}">{{ $button['label'] }}</a>
                    @endforeach
                </div>
            @endif
            @if ($features->isNotEmpty())
                <ul class="trust-list" aria-label="Wat je van ons kunt verwachten">
                    @foreach ($features as $feature)
                        <li>{{ $feature->title }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
        <div class="hero-art" aria-hidden="true">
            <svg viewBox="0 0 480 400" class="hero-illustration">
                <!-- water -->
                <path class="ill-water" d="M0 300 Q 60 285 120 300 T 240 300 T 360 300 T 480 300 V400 H0Z"/>
                <path class="ill-water-2" d="M0 330 Q 60 318 120 330 T 240 330 T 360 330 T 480 330 V400 H0Z"/>
                <!-- quay (wal) -->
                <rect class="ill-quay" x="0" y="230" width="150" height="170" rx="4"/>
                <rect class="ill-quay-top" x="0" y="222" width="158" height="16" rx="4"/>
                <!-- ship (schip) -->
                <path class="ill-ship" d="M285 250 H470 L445 320 H310 Z"/>
                <rect class="ill-cabin" x="330" y="200" width="80" height="50" rx="6"/>
                <rect class="ill-window" x="345" y="214" width="18" height="16" rx="3"/>
                <rect class="ill-window" x="377" y="214" width="18" height="16" rx="3"/>
                <!-- bridge (plank) -->
                <path class="ill-bridge" d="M150 226 L300 246" />
                <path class="ill-rail" d="M158 200 L292 220" />
                <path class="ill-post" d="M170 228 V202 M210 233 V207 M250 239 V213 M285 244 V218"/>
                <!-- two figures -->
                <circle class="ill-person-a" cx="200" cy="168" r="13"/>
                <path class="ill-person-a" d="M188 186 h24 l4 42 h-32z"/>
                <circle class="ill-person-b" cx="238" cy="174" r="13"/>
                <path class="ill-person-b" d="M226 192 h24 l4 42 h-32z"/>
                <path class="ill-hand" d="M212 200 Q 220 196 228 204"/>
                <!-- sun -->
                <circle class="ill-sun" cx="90" cy="90" r="38"/>
              </svg>
        </div>
    </div>
</section>
