<x-layouts.admin title="Tweestapsverificatie">
    <form method="POST" action="{{ route('two-factor.login') }}" class="rounded-xl border border-stone-200 bg-white p-6">
        @csrf
        <p class="mb-4 text-stone-700">Vul de code in uit je authenticator-app.</p>

        <x-admin.input name="code" label="Code" inputmode="numeric" autocomplete="one-time-code" pattern="[0-9]*" maxlength="6" autofocus />

        <details class="mb-5" @if (old('recovery_code') || $errors->has('recovery_code')) open @endif>
            <summary class="cursor-pointer text-sm underline underline-offset-2">Telefoon kwijt? Gebruik een herstelcode</summary>
            <div class="mt-3">
                <x-admin.input name="recovery_code" label="Herstelcode" autocomplete="off" />
            </div>
        </details>

        <button type="submit" class="w-full rounded-lg bg-rust-700 px-5 py-2.5 font-bold text-white hover:bg-rust-800">Inloggen</button>
    </form>
</x-layouts.admin>
