<x-layouts.admin title="Tweestapsverificatie">
    <p class="-mt-4 mb-6"><a href="{{ route('admin.account.edit') }}" class="underline underline-offset-2">← Terug naar account</a></p>

    <div class="max-w-2xl rounded-xl border border-stone-200 bg-white p-6">
        @if (! $user->two_factor_secret)
            <p class="mb-4 text-stone-700">Tweestapsverificatie staat <strong>uit</strong>. Zet je het aan, dan heb je bij het inloggen naast je wachtwoord een code nodig uit een authenticator-app op je telefoon, zoals Microsoft Authenticator, Google Authenticator of 2FAS.</p>
            @if (session('status') === 'two-factor-authentication-disabled')
                <p class="mb-4 rounded-lg bg-brand-100 px-3 py-2 font-bold text-brand-900" role="status">Tweestapsverificatie is uitgezet.</p>
            @endif
            <form method="POST" action="{{ route('two-factor.enable') }}">
                @csrf
                <button type="submit" class="rounded-lg bg-rust-700 px-5 py-2.5 font-bold text-white hover:bg-rust-800">Tweestapsverificatie aanzetten</button>
            </form>
        @elseif (! $user->hasEnabledTwoFactorAuthentication())
            <h2 class="mb-2 text-lg font-bold text-brand-900">Stap 1: scan de QR-code</h2>
            <p class="mb-4 text-stone-700">Scan deze code met je authenticator-app. Lukt scannen niet? Voer dan de sleutel eronder handmatig in.</p>
            <div class="mb-4 inline-block rounded-lg border border-stone-200 bg-white p-3">{!! $user->twoFactorQrCodeSvg() !!}</div>
            <p class="mb-6 text-sm">Sleutel: <code class="rounded bg-sand-100 px-2 py-1 font-mono">{{ decrypt($user->two_factor_secret) }}</code></p>

            <h2 class="mb-2 text-lg font-bold text-brand-900">Stap 2: bevestig met een code</h2>
            <form method="POST" action="{{ route('two-factor.confirm') }}" class="mb-6 max-w-xs">
                @csrf
                <x-admin.input name="code" label="Code uit de app" bag="confirmTwoFactorAuthentication" inputmode="numeric" autocomplete="one-time-code" pattern="[0-9]*" maxlength="6" required autofocus />
                <button type="submit" class="rounded-lg bg-rust-700 px-5 py-2.5 font-bold text-white hover:bg-rust-800">Bevestigen</button>
            </form>

            <form method="POST" action="{{ route('two-factor.disable') }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-sm underline underline-offset-2">Annuleren</button>
            </form>
        @else
            @if (session('status') === 'two-factor-authentication-confirmed')
                <p class="mb-4 rounded-lg bg-brand-100 px-3 py-2 font-bold text-brand-900" role="status">Tweestapsverificatie staat aan. Bewaar je herstelcodes op een veilige plek.</p>
            @elseif (session('status') === 'recovery-codes-generated')
                <p class="mb-4 rounded-lg bg-brand-100 px-3 py-2 font-bold text-brand-900" role="status">Er zijn nieuwe herstelcodes gemaakt. De oude codes werken niet meer.</p>
            @endif

            <p class="mb-4 text-stone-700">Tweestapsverificatie staat <strong>aan</strong>.</p>

            <details class="mb-6 rounded-lg border border-stone-200 p-4" @if (in_array(session('status'), ['two-factor-authentication-confirmed', 'recovery-codes-generated'], true)) open @endif>
                <summary class="cursor-pointer font-bold">Herstelcodes</summary>
                <p class="my-3 text-sm text-stone-700">Ben je je telefoon kwijt? Dan kun je met één van deze codes inloggen. Elke code werkt één keer.</p>
                <ul class="mb-4 grid grid-cols-2 gap-1 font-mono text-sm">
                    @foreach ($user->recoveryCodes() as $recoveryCode)
                        <li>{{ $recoveryCode }}</li>
                    @endforeach
                </ul>
                <form method="POST" action="{{ route('two-factor.regenerate-recovery-codes') }}">
                    @csrf
                    <button type="submit" class="rounded-lg border border-stone-300 px-4 py-2 text-sm font-bold hover:bg-sand-100">Nieuwe herstelcodes maken</button>
                </form>
            </details>

            <form method="POST" action="{{ route('two-factor.disable') }}" data-confirm="Weet je zeker dat je tweestapsverificatie wilt uitzetten?">
                @csrf
                @method('DELETE')
                <button type="submit" class="rounded-lg border border-red-200 px-4 py-2 font-bold text-red-800 hover:bg-red-50">Tweestapsverificatie uitzetten</button>
            </form>
        @endif
    </div>
</x-layouts.admin>
