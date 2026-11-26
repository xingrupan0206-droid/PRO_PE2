<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inloggen | {{ config('app.name') }}</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; padding: 24px; background: #f1f5f9; color: #0f172a; font: 16px/1.5 system-ui, sans-serif; }
        main { width: min(100%, 420px); padding: 36px; border: 1px solid #e2e8f0; border-radius: 16px; background: white; box-shadow: 0 16px 40px rgb(15 23 42 / 8%); }
        h1 { margin: 0 0 8px; font-size: 1.75rem; }
        p { margin: 0 0 24px; color: #475569; }
        label { display: block; margin: 16px 0 6px; font-weight: 600; }
        input[type="email"], input[type="password"] { width: 100%; padding: 11px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font: inherit; }
        input:focus { outline: 3px solid #bae6fd; border-color: #0284c7; }
        .remember { display: flex; align-items: center; gap: 8px; margin: 16px 0; color: #475569; }
        button { width: 100%; padding: 12px; border: 0; border-radius: 8px; background: #0369a1; color: white; font: inherit; font-weight: 700; cursor: pointer; }
        button:hover { background: #075985; }
        .error { margin-top: 6px; color: #b91c1c; font-size: .9rem; }
        .alert { margin-bottom: 20px; padding: 12px 16px; border-radius: 10px; border: 1px solid #bbf7d0; background: #f0fdf4; color: #166534; }
        .switch { margin: 20px 0 0; text-align: center; }
        .switch a { color: #0369a1; font-weight: 600; }
    </style>
</head>
<body>
<main>
    <h1>Welkom bij SmilePro</h1>
    <p>Log in met je praktijkaccount.</p>

    @if (session('status'))
        <div class="alert" role="status">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login.store') }}">
        @csrf
        <label for="email">E-mailadres</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="username" required autofocus>
        @error('email')
            <div class="error">{{ $message }}</div>
        @enderror

        <label for="password">Wachtwoord</label>
        <input id="password" name="password" type="password" autocomplete="current-password" required>
        @error('password')
            <div class="error">{{ $message }}</div>
        @enderror

        <label class="remember">
            <input type="checkbox" name="remember" value="1">
            Onthoud mij
        </label>

        <button type="submit">Inloggen</button>
    </form>

    @if (Route::has('register'))
        <p class="switch">Nog geen account? <a href="{{ route('register') }}">Registreer hier</a></p>
    @endif
</main>
</body>
</html>
