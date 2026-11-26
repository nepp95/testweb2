<x-layouts.admin title="Secties">
    <p class="mb-6 max-w-2xl text-stone-700">De website is één pagina. De secties hieronder staan in dezelfde volgorde als op de pagina. Een sectie met een naam in het menu krijgt een link in de kop van de website.</p>

    @error('type')
        <p class="mb-6 rounded-lg bg-red-50 px-4 py-3 font-bold text-red-800" role="alert">{{ $message }}</p>
    @enderror

    @if ($sections->isEmpty())
        <p class="mb-6 rounded-lg border border-dashed border-stone-300 bg-white px-4 py-6 text-center text-stone-600">Er zijn nog geen secties.</p>
    @else
        <ol class="mb-8 divide-y divide-stone-200 overflow-hidden rounded-xl border border-stone-200 bg-white">
            @foreach ($sections as $section)
                <li class="flex flex-wrap items-center gap-3 px-4 py-3">
                    <div class="flex gap-1">
                        <form method="POST" action="{{ route('admin.sections.move', [$section, 'up']) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="rounded border border-stone-300 px-2 py-1 text-sm hover:bg-sand-100 disabled:opacity-30" @disabled($loop->first) aria-label="‘{{ $section->title }}’ omhoog">↑</button>
                        </form>
                        <form method="POST" action="{{ route('admin.sections.move', [$section, 'down']) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="rounded border border-stone-300 px-2 py-1 text-sm hover:bg-sand-100 disabled:opacity-30" @disabled($loop->last) aria-label="‘{{ $section->title }}’ omlaag">↓</button>
                        </form>
                    </div>

                    <div class="min-w-0 flex-1">
                        <a href="{{ route('admin.sections.edit', $section) }}" class="font-bold text-brand-900 underline-offset-2 hover:underline">{{ $section->title }}</a>
                        <p class="text-sm text-stone-600">
                            {{ $section->type->label() }} · #{{ $section->anchor }}
                            @if ($section->nav_label)
                                · in menu als “{{ $section->nav_label }}”
                            @endif
                            @if ($section->items_count)
                                · {{ $section->items_count }} {{ $section->items_count === 1 ? 'onderdeel' : 'onderdelen' }}
                            @endif
                        </p>
                    </div>

                    @unless ($section->is_visible)
                        <span class="rounded-full bg-stone-200 px-2.5 py-0.5 text-xs font-bold text-stone-700">Verborgen</span>
                    @endunless

                    <div class="flex gap-2">
                        <a href="{{ route('admin.sections.edit', $section) }}" class="rounded-lg border border-stone-300 px-3 py-1.5 text-sm font-bold hover:bg-sand-100">Bewerken</a>
                        <form method="POST" action="{{ route('admin.sections.visibility', $section) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="rounded-lg border border-stone-300 px-3 py-1.5 text-sm hover:bg-sand-100">{{ $section->is_visible ? 'Verbergen' : 'Tonen' }}</button>
                        </form>
                        <form method="POST" action="{{ route('admin.sections.destroy', $section) }}" data-confirm="Weet je zeker dat je ‘{{ $section->title }}’ en alles wat erin staat wilt verwijderen?">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="rounded-lg border border-red-200 px-3 py-1.5 text-sm text-red-800 hover:bg-red-50">Verwijderen</button>
                        </form>
                    </div>
                </li>
            @endforeach
        </ol>
    @endif

    <form method="GET" action="{{ route('admin.sections.create') }}" class="flex flex-wrap items-end gap-3 rounded-xl border border-stone-200 bg-white p-4">
        <div>
            <label for="new-section-type" class="mb-1 block font-bold">Nieuwe sectie toevoegen</label>
            <select id="new-section-type" name="type" class="rounded-lg border border-stone-300 bg-white px-3 py-2">
                @foreach ($availableTypes as $type)
                    <option value="{{ $type->value }}">{{ $type->label() }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="rounded-lg bg-rust-700 px-4 py-2 font-bold text-white hover:bg-rust-800">Toevoegen</button>
    </form>
</x-layouts.admin>
