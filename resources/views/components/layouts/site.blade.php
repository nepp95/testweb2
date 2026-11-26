@props(['title' => null, 'navSections' => null, 'noindex' => false])
@use('App\Enums\SectionType')
@use('App\Enums\SettingKey')
@use('App\Support\Markdown')
@php
    $name = $settings->get(SettingKey::Name);
    $navItems = collect($navSections)->filter(fn ($section) => filled($section->nav_label));
    $isHome = request()->routeIs('home');
    $footerFacts = collect([
        'KvK' => $settings->get(SettingKey::Kvk),
        'RSIN' => $settings->get(SettingKey::Rsin),
        'ANBI-status' => $settings->get(SettingKey::Anbi),
        'IBAN' => $settings->get(SettingKey::Iban),
    ])->filter(fn (string $value) => filled($value));
@endphp
<!doctype html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ? $title.' — '.$name : collect([$name, $settings->get(SettingKey::Tagline)])->filter()->implode(' — ') }}</title>
    @if (filled($settings->get(SettingKey::MetaDescription)))
        <meta name="description" content="{{ $settings->get(SettingKey::MetaDescription) }}">
    @endif
    @if ($noindex)
        <meta name="robots" content="noindex">
    @endif
    <meta name="theme-color" content="#082a2a">
    <link rel="icon" href="{{ asset('logo.svg') }}" type="image/svg+xml">
    @fonts
    @vite(['resources/css/site.css', 'resources/js/site.js'])
</head>
<body id="top">
    <a class="skip-link" href="#main">Direct naar de inhoud</a>

    @if (filled($settings->get(SettingKey::CrisisBar)))
        <div class="crisis-bar" role="note">
            <div class="container">
                <p>{{ Markdown::inline($settings->get(SettingKey::CrisisBar)) }}</p>
            </div>
        </div>
    @endif

    <header class="site-header">
        <div class="container header-inner">
            <a class="brand" href="{{ $isHome ? '#top' : route('home') }}" aria-label="{{ $isHome ? 'Naar de bovenkant van de pagina' : 'Naar de homepage' }}">
                <img src="{{ asset('logo.svg') }}" alt="" width="40" height="40">
                <span class="brand-name">{{ $name }}</span>
            </a>
            @if ($navItems->isNotEmpty())
                <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="site-nav">
                    <span class="nav-toggle-bar" aria-hidden="true"></span>
                    <span class="sr-only">Menu</span>
                </button>
                <nav id="site-nav" class="site-nav" aria-label="Hoofdmenu">
                    <ul>
                        @foreach ($navItems as $navSection)
                            <li>
                                @if ($navSection->type === SectionType::Contact)
                                    <a class="btn btn-small" href="#{{ $navSection->anchor }}">{{ $navSection->nav_label }}</a>
                                @else
                                    <a href="#{{ $navSection->anchor }}">{{ $navSection->nav_label }}</a>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </nav>
            @endif
        </div>
    </header>

    <main id="main" {{ $attributes }}>
        {{ $slot }}
    </main>

    <footer class="site-footer">
        <div class="container footer-inner">
            <div>
                <a class="brand brand-footer" href="{{ $isHome ? '#top' : route('home') }}">
                    <img src="{{ asset('logo.svg') }}" alt="" width="36" height="36">
                    <span>{{ $name }}</span>
                </a>
                @if (filled($settings->get(SettingKey::FooterText)))
                    <p>{{ $settings->get(SettingKey::FooterText) }}</p>
                @endif
            </div>
            @if ($footerFacts->isNotEmpty())
                <div>
                    <h2 class="footer-title">Gegevens</h2>
                    <ul class="footer-list">
                        @foreach ($footerFacts as $label => $value)
                            <li>{{ $label }}: {{ $value }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div>
                <h2 class="footer-title">Meer</h2>
                <ul class="footer-list">
                    <li><a href="{{ route('privacy') }}">Privacyverklaring</a></li>
                    @foreach ($navItems as $navSection)
                        <li><a href="#{{ $navSection->anchor }}">{{ $navSection->nav_label }}</a></li>
                    @endforeach
                    @unless ($isHome)
                        <li><a href="{{ route('home') }}">Naar de homepage</a></li>
                    @endunless
                </ul>
            </div>
        </div>
        <div class="container footer-bottom">
            <p>© {{ now()->year }} {{ $name }}</p>
        </div>
    </footer>
</body>
</html>
