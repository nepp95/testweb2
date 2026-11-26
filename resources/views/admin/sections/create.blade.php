<x-layouts.admin :title="'Nieuwe sectie: '.$type->label()">
    <form method="POST" action="{{ route('admin.sections.store') }}" class="max-w-2xl rounded-xl border border-stone-200 bg-white p-6">
        @csrf
        <input type="hidden" name="type" value="{{ $type->value }}">
        @error('type')
            <p class="mb-5 rounded-lg bg-red-50 px-4 py-3 font-bold text-red-800" role="alert">{{ $message }}</p>
        @enderror

        @include('admin.sections.fields', ['section' => null])

        <p class="mt-6 text-sm text-stone-600">Na het opslaan kun je de onderdelen (zoals kaarten of vragen) toevoegen.</p>
        <div class="mt-4 flex gap-3">
            <button type="submit" class="rounded-lg bg-rust-700 px-5 py-2.5 font-bold text-white hover:bg-rust-800">Opslaan</button>
            <a href="{{ route('admin.sections.index') }}" class="rounded-lg px-5 py-2.5 hover:bg-sand-100">Annuleren</a>
        </div>
    </form>
</x-layouts.admin>
