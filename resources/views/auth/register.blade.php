<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registreren | {{ config('app.name') }}</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; padding: 24px; background: #f1f5f9; color: #0f172a; font: 16px/1.5 system-ui, sans-serif; }
        main { width: min(100%, 480px); padding: 36px; border: 1px solid #e2e8f0; border-radius: 16px; background: white; box-shadow: 0 16px 40px rgb(15 23 42 / 8%); }
        h1 { margin: 0 0 8px; font-size: 1.75rem; }
        p { margin: 0 0 24px; color: #475569; }
        label { display: block; margin: 16px 0 6px; font-weight: 600; }
        input { width: 100%; padding: 11px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font: inherit; }
        input:focus { outline: 3px solid #bae6fd; border-color: #0284c7; }
        input[aria-invalid="true"] { border-color: #dc2626; }
        .row { display: grid; grid-template-columns: 2fr 1fr 2fr; gap: 12px; }
        .row label { margin-top: 16px; }
        button { width: 100%; margin-top: 24px; padding: 12px; border: 0; border-radius: 8px; background: #0369a1; color: white; font: inherit; font-weight: 700; cursor: pointer; }
        button:hover { background: #075985; }
        .error { margin-top: 6px; color: #b91c1c; font-size: .9rem; }
        .alert { margin-bottom: 20px; padding: 12px 16px; border-radius: 10px; border: 1px solid #fecaca; background: #fef2f2; color: #991b1b; }
        .switch { margin: 20px 0 0; text-align: center; }
        .switch a { color: #0369a1; font-weight: 600; }
        @media (max-width: 520px) { .row { grid-template-columns: 1fr; gap: 0; } }
    </style>
</head>
<body>
<main>
    <h1>Account aanmaken</h1>
    <p>Maak een account aan bij SmilePro.</p>

    @if ($errors->any())
        <div class="alert" role="alert">Je account is nog niet aangemaakt. Controleer de velden hieronder.</div>
    @endif

    <form method="POST" action="{{ route('register.store') }}" novalidate>
        @csrf
        <div class="row">
            <div>
                <label for="first_name">Voornaam</label>
                <input id="first_name" name="first_name" type="text" value="{{ old('first_name') }}" autocomplete="given-name" required autofocus @error('first_name') aria-invalid="true" @enderror>
                @error('first_name')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <label for="prefix">Tussenv.</label>
                <input id="prefix" name="prefix" type="text" value="{{ old('prefix') }}">
            </div>
            <div>
                <label for="last_name">Achternaam</label>
                <input id="last_name" name="last_name" type="text" value="{{ old('last_name') }}" autocomplete="family-name" required @error('last_name') aria-invalid="true" @enderror>
                @error('last_name')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <label for="email">E-mailadres</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required @error('email') aria-invalid="true" @enderror>
        @error('email')
            <div class="error">{{ $message }}</div>
        @enderror

        <label for="password">Wachtwoord</label>
        <input id="password" name="password" type="password" autocomplete="new-password" required @error('password') aria-invalid="true" @enderror>
        @error('password')
            <div class="error">{{ $message }}</div>
        @enderror

        <label for="password_confirmation">Herhaal wachtwoord</label>
        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>

        <button type="submit">Registreren</button>
    </form>

    <p class="switch">Heb je al een account? <a href="{{ route('login') }}">Log hier in</a></p>
</main>
</body>
</html>
