@use('App\Enums\SettingKey')
@use('App\Support\Markdown')
<x-layouts.site title="Privacyverklaring" noindex class="page">
    <div class="container narrow">
        <a class="back-link" href="{{ route('home') }}">← Terug naar de homepage</a>
        <h1>Privacyverklaring</h1>
        <div class="rich-text">
            {{ Markdown::block($settings->get(SettingKey::PrivacyStatement)) }}
        </div>
    </div>
</x-layouts.site>
