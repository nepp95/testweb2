{{-- A labelled text input for the account and login forms. --}}
@props(['name', 'label', 'type' => 'text', 'value' => null, 'bag' => 'default', 'help' => null])
@php
    $inputId = $attributes->get('id', 'f-'.$name);
    $error = $errors->getBag($bag)->first($name);
    $describedBy = collect([$help ? $inputId.'-help' : null, $error ? $inputId.'-error' : null])->filter()->implode(' ');
@endphp
<div class="mb-5">
    <label for="{{ $inputId }}" class="mb-1 block font-bold">{{ $label }}</label>
    <input
        id="{{ $inputId }}"
        name="{{ $name }}"
        type="{{ $type }}"
        @if ($type !== 'password') value="{{ old($name, $value) }}" @endif
        @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
        @if ($error) aria-invalid="true" @endif
        {{ $attributes->except('id')->merge(['class' => 'block w-full rounded-lg border border-stone-300 bg-white px-3 py-2 focus:border-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-100']) }}
    >
    @if ($help)
        <p id="{{ $inputId }}-help" class="mt-1 text-sm text-stone-600">{{ $help }}</p>
    @endif
    @if ($error)
        <p id="{{ $inputId }}-error" class="mt-1 text-sm font-bold text-red-700">{{ $error }}</p>
    @endif
</div>
