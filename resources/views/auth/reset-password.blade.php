<x-layouts.admin title="Nieuw wachtwoord instellen">
    <form method="POST" action="{{ route('password.update') }}" class="rounded-xl border border-stone-200 bg-white p-6">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <x-admin.input name="email" label="E-mailadres" type="email" :value="$request->query('email')" autocomplete="username" required />
        <x-admin.input name="password" label="Nieuw wachtwoord" type="password" autocomplete="new-password" required help="Minimaal 12 tekens." />
        <x-admin.input name="password_confirmation" label="Nieuw wachtwoord nogmaals" type="password" autocomplete="new-password" required />

        <button type="submit" class="w-full rounded-lg bg-rust-700 px-5 py-2.5 font-bold text-white hover:bg-rust-800">Wachtwoord opslaan</button>
    </form>
</x-layouts.admin>
