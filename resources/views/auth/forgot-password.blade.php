<x-layouts.admin title="Wachtwoord vergeten">
    <form method="POST" action="{{ route('password.email') }}" class="rounded-xl border border-stone-200 bg-white p-6">
        @csrf
        @if (session('status'))
            <p class="mb-4 rounded-lg bg-brand-100 px-3 py-2 font-bold text-brand-900" role="status">{{ session('status') }}</p>
        @endif
        <p class="mb-4 text-stone-700">Vul het e-mailadres van het beheerdersaccount in. Je krijgt dan een link om een nieuw wachtwoord in te stellen.</p>

        <x-admin.input name="email" label="E-mailadres" type="email" autocomplete="username" required autofocus />

        <button type="submit" class="w-full rounded-lg bg-rust-700 px-5 py-2.5 font-bold text-white hover:bg-rust-800">Stuur de link</button>
        <p class="mt-4 text-center text-sm"><a href="{{ route('login') }}" class="underline underline-offset-2">Terug naar inloggen</a></p>
    </form>
</x-layouts.admin>
