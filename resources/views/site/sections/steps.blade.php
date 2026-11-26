@php
    $steps = $section->itemsInGroup('stap');
    $doItems = $section->itemsInGroup('wel');
    $dontItems = $section->itemsInGroup('niet');
@endphp
<section class="section {{ $section->backgroundClass() }}" id="{{ $section->anchor }}">
    <div class="container">
        @include('site.partials.heading')
        @if ($steps->isNotEmpty())
            <ol class="steps">
                @foreach ($steps as $step)
                    <li class="step">
                        <span class="step-num" aria-hidden="true">{{ $loop->iteration }}</span>
                        <h3>{{ $step->title }}</h3>
                        @if (filled($step->field('body')))
                            <p>{{ $step->field('body') }}</p>
                        @endif
                    </li>
                @endforeach
            </ol>
        @endif

        @if ($doItems->isNotEmpty() || $dontItems->isNotEmpty())
            <div class="do-dont">
                @if ($doItems->isNotEmpty())
                    <div class="do">
                        <h3>{{ $section->field('do_title') }}</h3>
                        <ul>
                            @foreach ($doItems as $item)
                                <li>{{ $item->title }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if ($dontItems->isNotEmpty())
                    <div class="dont">
                        <h3>{{ $section->field('dont_title') }}</h3>
                        <ul>
                            @foreach ($dontItems as $item)
                                <li>{{ $item->title }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        @endif
    </div>
</section>
