{{-- Plain-text e-mail: values are output raw on purpose, because HTML escaping would show up as entities in the text. --}}
Nieuw bericht via het contactformulier op de website.

Onderwerp: {!! $onderwerp ?? '(geen onderwerp)' !!}
Naam: {!! $naam !!}
Bereikbaar via: {!! $contact !!}

Bericht:
{!! filled($bericht) ? $bericht : '(geen bericht)' !!}
