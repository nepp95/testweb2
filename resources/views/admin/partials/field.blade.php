{{--
    Renders one App\Support\FormField.
    Expects: $field, $name (input name), $errorKey (validation key), $value.
    Optional: $sectionOptions (id => title), $subjectOptions (id => title).
--}}
@use('App\Enums\FieldType')
@php
    $inputId = 'f-'.str_replace(['[', ']', '.'], ['-', '', '-'], $name);
    $inputClass = 'block w-full rounded-lg border border-stone-300 bg-white px-3 py-2 focus:border-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-100';
    $current = old($errorKey, $value ?? $field->default);
    $selectOptions = match ($field->type) {
        FieldType::Select => $field->options,
        FieldType::SectionLink => $sectionOptions ?? [],
        FieldType::ContactSubject => $subjectOptions ?? [],
        default => [],
    };
    $describedBy = collect([
        $field->help || $field->type === FieldType::Markdown ? $inputId.'-help' : null,
        $errors->has($errorKey) ? $inputId.'-error' : null,
    ])->filter()->implode(' ');
@endphp
<div class="mb-5">
    @if ($field->type === FieldType::Checkbox)
        <input type="hidden" name="{{ $name }}" value="0">
        <label class="flex items-start gap-3">
            <input type="checkbox" id="{{ $inputId }}" name="{{ $name }}" value="1" class="mt-1 size-5 accent-brand-700" @checked((bool) $current) @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif>
            <span class="font-bold">{{ $field->label }}</span>
        </label>
    @else
        <label for="{{ $inputId }}" class="mb-1 block font-bold">
            {{ $field->label }}
            @unless ($field->required)
                <span class="font-normal text-stone-500">(niet verplicht)</span>
            @endunless
        </label>

        @if (in_array($field->type, [FieldType::Select, FieldType::SectionLink, FieldType::ContactSubject], true))
            <select id="{{ $inputId }}" name="{{ $name }}" class="{{ $inputClass }}" @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif @error($errorKey) aria-invalid="true" @enderror>
                @unless ($field->required)
                    <option value="">— geen —</option>
                @endunless
                @foreach ($selectOptions as $optionValue => $optionLabel)
                    <option value="{{ $optionValue }}" @selected((string) $current === (string) $optionValue)>{{ $optionLabel }}</option>
                @endforeach
            </select>
        @elseif (in_array($field->type, [FieldType::Textarea, FieldType::Markdown], true))
            <textarea id="{{ $inputId }}" name="{{ $name }}" rows="{{ $field->type === FieldType::Markdown ? 8 : 3 }}" class="{{ $inputClass }}" @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif @error($errorKey) aria-invalid="true" @enderror>{{ $current }}</textarea>
        @else
            <input type="{{ $field->type === FieldType::Email ? 'email' : 'text' }}" id="{{ $inputId }}" name="{{ $name }}" value="{{ $current }}" class="{{ $inputClass }}" @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif @error($errorKey) aria-invalid="true" @enderror>
        @endif
    @endif

    @if ($field->help || $field->type === FieldType::Markdown)
        <p id="{{ $inputId }}-help" class="mt-1 text-sm text-stone-600">
            {{ $field->help }}
            @if ($field->type === FieldType::Markdown)
                Opmaak: **vet**, *schuin*, [linktekst](https://…), een lege regel voor een nieuwe alinea, “- ” voor een opsomming.
            @endif
        </p>
    @endif
    @error($errorKey)
        <p id="{{ $inputId }}-error" class="mt-1 text-sm font-bold text-red-700">{{ $message }}</p>
    @enderror
</div>
