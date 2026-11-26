<x-layouts.admin title="Account">
    <div class="grid max-w-4xl gap-8 md:grid-cols-2">
        <form method="POST" action="{{ route('user-profile-information.update') }}" class="rounded-xl border border-stone-200 bg-white p-6">
            @csrf
            @method('PUT')
            <h2 class="mb-4 text-lg font-bold text-brand-900">Naam en e-mailadres</h2>
            @if (session('status') === 'profile-information-updated')
                <p class="mb-4 rounded-lg bg-brand-100 px-3 py-2 font-bold text-brand-900" role="status">Opgeslagen.</p>
            @endif

            <x-admin.input name="name" label="Naam" :value="$user->name" bag="updateProfileInformation" autocomplete="name" required />
            <x-admin.input name="email" label="E-mailadres" type="email" :value="$user->email" bag="updateProfileInformation" autocomplete="email" required help="Hier komen links om je wachtwoord opnieuw in te stellen." />
            <x-admin.input name="current_password" id="f-profile-current-password" label="Huidig wachtwoord" type="password" bag="updateProfileInformation" autocomplete="current-password" required />

            <button type="submit" class="rounded-lg bg-rust-700 px-5 py-2.5 font-bold text-white hover:bg-rust-800">Opslaan</button>
        </form>

        <form method="POST" action="{{ route('user-password.update') }}" class="rounded-xl border border-stone-200 bg-white p-6">
            @csrf
            @method('PUT')
            <h2 class="mb-4 text-lg font-bold text-brand-900">Wachtwoord wijzigen</h2>
            @if (session('status') === 'password-updated')
                <p class="mb-4 rounded-lg bg-brand-100 px-3 py-2 font-bold text-brand-900" role="status">Je wachtwoord is gewijzigd.</p>
            @endif

            <x-admin.input name="current_password" label="Huidig wachtwoord" type="password" bag="updatePassword" autocomplete="current-password" required />
            <x-admin.input name="password" label="Nieuw wachtwoord" type="password" bag="updatePassword" autocomplete="new-password" required help="Minimaal 12 tekens." />
            <x-admin.input name="password_confirmation" label="Nieuw wachtwoord nogmaals" type="password" bag="updatePassword" autocomplete="new-password" required />

            <button type="submit" class="rounded-lg bg-rust-700 px-5 py-2.5 font-bold text-white hover:bg-rust-800">Wachtwoord wijzigen</button>
        </form>

        <section class="rounded-xl border border-stone-200 bg-white p-6 md:col-span-2" aria-labelledby="two-factor-heading">
            <h2 id="two-factor-heading" class="mb-2 text-lg font-bold text-brand-900">Tweestapsverificatie</h2>
            <p class="mb-4 text-stone-700">
                @if ($user->hasEnabledTwoFactorAuthentication())
                    <strong>Aan.</strong> Bij het inloggen vragen we om een code uit je authenticator-app.
                @else
                    <strong>Uit.</strong> Met tweestapsverificatie heb je naast je wachtwoord een code uit een authenticator-app nodig om in te loggen.
                @endif
            </p>
            <a href="{{ route('admin.account.two-factor') }}" class="inline-block rounded-lg border border-stone-300 px-4 py-2 font-bold hover:bg-sand-100">Tweestapsverificatie beheren</a>
        </section>
    </div>
</x-layouts.admin>
