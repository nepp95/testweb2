<x-layouts.admin title="Inloggen">
    <form method="POST" action="{{ route('login') }}" class="rounded-xl border border-stone-200 bg-white p-6">
        @csrf
        @if (session('status'))
            <p class="mb-4 rounded-lg bg-brand-100 px-3 py-2 font-bold text-brand-900" role="status">{{ session('status') }}</p>
        @endif

        <x-admin.input name="email" label="E-mailadres" type="email" autocomplete="username" required autofocus />
        <x-admin.input name="password" label="Wachtwoord" type="password" autocomplete="current-password" required />

        <label class="mb-5 flex items-center gap-2">
            <input type="checkbox" name="remember" value="1" class="size-5 accent-brand-700">
            Ingelogd blijven op dit apparaat
        </label>

        <button type="submit" class="w-full rounded-lg bg-rust-700 px-5 py-2.5 font-bold text-white hover:bg-rust-800">Inloggen</button>
        <p class="mt-4 text-center text-sm"><a href="{{ route('password.request') }}" class="underline underline-offset-2">Wachtwoord vergeten?</a></p>
    </form>
</x-layouts.admin>
