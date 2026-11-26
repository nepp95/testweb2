<x-layouts.admin title="Gegevens van de stichting">
    <form method="POST" action="{{ route('admin.settings.update') }}" class="max-w-2xl space-y-8">
        @csrf
        @method('PUT')

        @foreach ($groups as $groupLabel => $keys)
            <fieldset class="rounded-xl border border-stone-200 bg-white p-6">
                <legend class="px-2 text-lg font-bold text-brand-900">{{ $groupLabel }}</legend>
                @foreach ($keys as $key)
                    @include('admin.partials.field', [
                        'field' => $key->field(),
                        'name' => $key->value,
                        'errorKey' => $key->value,
                        'value' => $settings->get($key),
                    ])
                @endforeach
            </fieldset>
        @endforeach

        <button type="submit" class="rounded-lg bg-rust-700 px-5 py-2.5 font-bold text-white hover:bg-rust-800">Opslaan</button>
    </form>
</x-layouts.admin>
