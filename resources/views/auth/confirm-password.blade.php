<x-layouts.admin title="Bevestig je wachtwoord">
    <form method="POST" action="{{ route('password.confirm') }}" class="max-w-md rounded-xl border border-stone-200 bg-white p-6">
        @csrf
        <p class="mb-4 text-stone-700">Dit is een beveiligd deel van het beheer. Bevestig eerst je wachtwoord.</p>

        <x-admin.input name="password" label="Wachtwoord" type="password" autocomplete="current-password" required autofocus />

        <button type="submit" class="rounded-lg bg-rust-700 px-5 py-2.5 font-bold text-white hover:bg-rust-800">Bevestigen</button>
    </form>
</x-layouts.admin>
