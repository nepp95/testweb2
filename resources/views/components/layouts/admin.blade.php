@props(['title'])
<!doctype html>
<html lang="nl" class="bg-sand-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $title }} — Beheer</title>
    <link rel="icon" href="{{ asset('logo.svg') }}" type="image/svg+xml">
    @fonts('body')
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
</head>
<body class="min-h-screen font-sans text-stone-900 antialiased">
    <a href="#inhoud" class="sr-only focus:not-sr-only focus:absolute focus:left-3 focus:top-3 focus:rounded focus:bg-brand-900 focus:px-4 focus:py-2 focus:text-white">Direct naar de inhoud</a>

    @auth
        <header class="bg-brand-900 text-white">
            <div class="mx-auto flex max-w-5xl flex-wrap items-center gap-x-6 gap-y-2 px-4 py-3">
                <a href="{{ route('admin.sections.index') }}" class="flex items-center gap-2 font-bold">
                    <img src="{{ asset('logo.svg') }}" alt="" width="28" height="28">
                    Beheer
                </a>
                <nav aria-label="Beheermenu" class="flex flex-wrap gap-1 text-sm">
                    @foreach ([
                        'admin.sections.index' => ['Secties', 'admin.sections.*'],
                        'admin.settings.edit' => ['Gegevens', 'admin.settings.*'],
                        'admin.account.edit' => ['Account', 'admin.account.*'],
                    ] as $route => [$label, $pattern])
                        <a href="{{ route($route) }}" @class([
                            'rounded px-3 py-1.5 hover:bg-white/10',
                            'bg-white/15 font-bold' => request()->routeIs($pattern),
                        ]) @if (request()->routeIs($pattern)) aria-current="page" @endif>{{ $label }}</a>
                    @endforeach
                    <a href="{{ route('home') }}" class="rounded px-3 py-1.5 hover:bg-white/10" target="_blank" rel="noopener">Bekijk website ↗</a>
                </nav>
                <form method="POST" action="{{ route('logout') }}" class="ml-auto">
                    @csrf
                    <button type="submit" class="rounded px-3 py-1.5 text-sm hover:bg-white/10">Uitloggen</button>
                </form>
            </div>
        </header>
    @endauth

    <main id="inhoud" @class(['mx-auto px-4 py-8', 'max-w-5xl' => auth()->check(), 'max-w-md pt-16' => ! auth()->check()])>
        <h1 class="mb-6 text-2xl font-bold text-brand-900">{{ $title }}</h1>

        @if (session('notice'))
            <p class="mb-6 rounded-lg bg-brand-100 px-4 py-3 font-bold text-brand-900" role="status">{{ session('notice') }}</p>
        @endif

        {{ $slot }}
    </main>
</body>
</html>
