@php
    $groupLabel = $section->type->itemGroups()[$group];
    $isNew = ! $item->exists;
@endphp
<x-layouts.admin :title="($isNew ? 'Toevoegen aan ' : 'Bewerken in ').mb_strtolower($groupLabel)">
    <p class="-mt-4 mb-6 text-stone-600">Sectie: <a href="{{ route('admin.sections.edit', $section) }}" class="underline underline-offset-2">{{ $section->title }}</a></p>

    <form method="POST" action="{{ $isNew ? route('admin.sections.items.store', $section) : route('admin.sections.items.update', [$section, $item]) }}" class="max-w-2xl rounded-xl border border-stone-200 bg-white p-6">
        @csrf
        @unless ($isNew)
            @method('PUT')
        @else
            <input type="hidden" name="group" value="{{ $group }}">
        @endunless

        @foreach ($section->type->itemFields($group) as $field)
            @php($isTitle = $field->name === 'title')
            @include('admin.partials.field', [
                'field' => $field,
                'name' => $isTitle ? 'title' : "content[{$field->name}]",
                'errorKey' => $isTitle ? 'title' : "content.{$field->name}",
                'value' => $isTitle ? $item->title : $item->field($field->name),
            ])
        @endforeach

        <div class="mt-4 flex gap-3">
            <button type="submit" class="rounded-lg bg-rust-700 px-5 py-2.5 font-bold text-white hover:bg-rust-800">Opslaan</button>
            <a href="{{ route('admin.sections.edit', $section) }}" class="rounded-lg px-5 py-2.5 hover:bg-sand-100">Annuleren</a>
        </div>
    </form>
</x-layouts.admin>
