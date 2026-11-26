{{-- Shared fields of the create and edit forms. Expects: $type, $section (nullable), $sectionOptions. --}}
@php
    $commonField = fn (string $name, string $label, bool $required = false, ?string $help = null) => new App\Support\FormField($name, $label, required: $required, help: $help);
@endphp

@include('admin.partials.field', ['field' => $commonField('title', 'Titel', true), 'name' => 'title', 'errorKey' => 'title', 'value' => $section?->title])

@foreach ($type->fields() as $field)
    @include('admin.partials.field', [
        'field' => $field,
        'name' => "content[{$field->name}]",
        'errorKey' => "content.{$field->name}",
        'value' => $section?->field($field->name),
        'sectionOptions' => collect($sectionOptions)->except($section?->id)->all(),
    ])
@endforeach

<fieldset class="mt-8 rounded-xl border border-stone-200 p-4">
    <legend class="px-2 font-bold">Plaats op de pagina</legend>
    @include('admin.partials.field', ['field' => $commonField('nav_label', 'Naam in het menu', help: 'Laat leeg als deze sectie niet in het menu hoeft. Bij het contactblok wordt dit de knop rechts in het menu.'), 'name' => 'nav_label', 'errorKey' => 'nav_label', 'value' => $section?->nav_label])
    @include('admin.partials.field', ['field' => $commonField('anchor', 'Ankernaam', help: 'Het deel achter de # in de link, bijvoorbeeld “over-ons”. Laat leeg om het uit de titel te maken.'), 'name' => 'anchor', 'errorKey' => 'anchor', 'value' => $section?->anchor])
    @include('admin.partials.field', ['field' => new App\Support\FormField('is_visible', 'Toon deze sectie op de website', App\Enums\FieldType::Checkbox, default: true), 'name' => 'is_visible', 'errorKey' => 'is_visible', 'value' => $section?->is_visible])
</fieldset>
