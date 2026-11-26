<x-layouts.admin :title="$section->title">
    <p class="-mt-4 mb-6 text-stone-600">{{ $section->type->label() }} · <a href="{{ route('admin.sections.index') }}" class="underline underline-offset-2">terug naar alle secties</a></p>

    <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_minmax(0,1fr)]">
        <form method="POST" action="{{ route('admin.sections.update', $section) }}" class="rounded-xl border border-stone-200 bg-white p-6">
            @csrf
            @method('PUT')
            <h2 class="mb-4 text-lg font-bold text-brand-900">Sectie</h2>

            @include('admin.sections.fields', ['type' => $section->type])

            <button type="submit" class="mt-4 rounded-lg bg-rust-700 px-5 py-2.5 font-bold text-white hover:bg-rust-800">Opslaan</button>
        </form>

        <div class="space-y-8">
            @forelse ($section->type->itemGroups() as $group => $groupLabel)
                @php($items = $section->itemsInGroup($group))
                <section class="rounded-xl border border-stone-200 bg-white p-6" aria-labelledby="group-{{ $group }}">
                    <h2 id="group-{{ $group }}" class="mb-4 text-lg font-bold text-brand-900">{{ $groupLabel }}</h2>

                    @if ($items->isEmpty())
                        <p class="mb-4 text-stone-600">Nog niets toegevoegd.</p>
                    @else
                        <ol class="mb-4 divide-y divide-stone-200 rounded-lg border border-stone-200">
                            @foreach ($items as $item)
                                <li class="flex items-center gap-2 px-3 py-2">
                                    <form method="POST" action="{{ route('admin.sections.items.move', [$section, $item, 'up']) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="rounded border border-stone-300 px-2 py-0.5 text-sm hover:bg-sand-100 disabled:opacity-30" @disabled($loop->first) aria-label="‘{{ $item->title }}’ omhoog">↑</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.sections.items.move', [$section, $item, 'down']) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="rounded border border-stone-300 px-2 py-0.5 text-sm hover:bg-sand-100 disabled:opacity-30" @disabled($loop->last) aria-label="‘{{ $item->title }}’ omlaag">↓</button>
                                    </form>
                                    <a href="{{ route('admin.sections.items.edit', [$section, $item]) }}" class="min-w-0 flex-1 truncate underline-offset-2 hover:underline">{{ $item->title }}</a>
                                    <form method="POST" action="{{ route('admin.sections.items.destroy', [$section, $item]) }}" data-confirm="Weet je zeker dat je ‘{{ $item->title }}’ wilt verwijderen?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded px-2 py-0.5 text-sm text-red-800 hover:bg-red-50">Verwijderen</button>
                                    </form>
                                </li>
                            @endforeach
                        </ol>
                    @endif

                    <a href="{{ route('admin.sections.items.create', [$section, 'group' => $group]) }}" class="inline-block rounded-lg border border-stone-300 px-4 py-2 font-bold hover:bg-sand-100">+ Toevoegen</a>
                </section>
            @empty
                <p class="rounded-xl border border-dashed border-stone-300 p-6 text-stone-600">Dit type sectie heeft geen losse onderdelen.</p>
            @endforelse
        </div>
    </div>
</x-layouts.admin>
