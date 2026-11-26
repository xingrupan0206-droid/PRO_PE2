<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard | {{ config('app.name') }}</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; background: #f1f5f9; color: #0f172a; font: 16px/1.5 system-ui, sans-serif; }
        header { display: flex; justify-content: space-between; align-items: center; gap: 16px; padding: 16px max(24px, calc((100% - 1040px) / 2)); background: white; border-bottom: 1px solid #e2e8f0; }
        header strong { color: #0369a1; font-size: 1.2rem; }
        header form { margin: 0; }
        button { padding: 9px 14px; border: 1px solid #cbd5e1; border-radius: 8px; background: white; font: inherit; cursor: pointer; }
        main { max-width: 1040px; margin: 48px auto; padding: 0 24px; }
        section { padding: 28px; border: 1px solid #e2e8f0; border-radius: 14px; background: white; }
        h1 { margin: 0 0 8px; font-size: 1.8rem; }
        p { margin: 0; color: #475569; }
    </style>
</head>
<body>
<header>
    <strong>SmilePro</strong>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Uitloggen</button>
    </form>
</header>
<main>
    <section>
        <h1>Welkom, {{ auth()->user()->name }}</h1>
        <p>Je bent ingelogd als {{ auth()->user()->role?->name ?? 'gebruiker' }}.</p>
        @can('view-appointment-overview')
            <p><a href="{{ route('afspraken.overzicht') }}">Afsprakenoverzicht</a></p>
        @endcan
    </section>
</main>
</body>
</html>
