<x-layouts.site :nav-sections="$sections">
    @foreach ($sections as $section)
        @include('site.sections.'.$section->type->value, ['section' => $section])
    @endforeach
</x-layouts.site>
