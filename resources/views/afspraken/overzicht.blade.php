<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Afsprakenoverzicht | {{ config('app.name') }}</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; background: #f1f5f9; color: #0f172a; font: 16px/1.5 system-ui, sans-serif; }
        header { display: flex; justify-content: space-between; align-items: center; gap: 16px; padding: 16px max(24px, calc((100% - 1040px) / 2)); background: white; border-bottom: 1px solid #e2e8f0; }
        header strong { color: #0369a1; font-size: 1.2rem; }
        header form { margin: 0; }
        a { color: #0369a1; }
        button { padding: 9px 14px; border: 1px solid #cbd5e1; border-radius: 8px; background: white; font: inherit; cursor: pointer; }
        main { max-width: 1040px; margin: 48px auto; padding: 0 24px; }
        section { padding: 28px; border: 1px solid #e2e8f0; border-radius: 14px; background: white; }
        h1 { margin: 0 0 8px; font-size: 1.8rem; }
        p { margin: 0; color: #475569; }
        .overzicht-kop { display: flex; align-items: center; gap: 16px; margin-bottom: 8px; }
        .overzicht-kop h1 { margin: 0; }
        .status-filter { display: flex; align-items: center; gap: 8px; }
        .status-filter label { color: #475569; font-weight: 600; }
        select { padding: 8px 10px; border: 1px solid #cbd5e1; border-radius: 8px; background: white; font: inherit; }
        .fout { margin-top: 16px; padding: 14px 16px; border: 1px solid #fecaca; border-radius: 8px; background: #fef2f2; color: #991b1b; }
        table { width: 100%; margin-top: 20px; border-collapse: collapse; }
        th, td { padding: 10px 12px; border-bottom: 1px solid #e2e8f0; text-align: left; }
        th { background: #f8fafc; color: #475569; font-size: 0.85rem; letter-spacing: 0.04em; text-transform: uppercase; }
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
        <div class="overzicht-kop">
            <form method="GET" action="{{ route('afspraken.overzicht') }}" class="status-filter">
                <label for="status">Status</label>
                <select id="status" name="status" onchange="this.form.submit()">
                    <option value="">Alle statussen</option>
                    @foreach ($statusLabels as $waarde => $label)
                        <option value="{{ $waarde }}" @selected($statusFilter === $waarde)>{{ $label }}</option>
                    @endforeach
                </select>
                <button type="submit">Filter</button>
            </form>
            <h1>Afsprakenoverzicht</h1>
        </div>

        @if ($foutmelding ?? null)
            <p class="fout" role="alert">{{ $foutmelding }}</p>
        @else
            <table>
                <thead>
                <tr>
                    <th>Datum</th>
                    <th>Tijdstip</th>
                    <th>Medewerker</th>
                    <th>Klant</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($afspraken as $afspraak)
                    <tr>
                        <td>{{ $afspraak['datum'] }}</td>
                        <td>{{ $afspraak['tijdstip'] }}</td>
                        <td>{{ $afspraak['medewerker'] }}</td>
                        <td>{{ $afspraak['klant'] }}</td>
                        <td>{{ $afspraak['status'] }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </section>
</main>
</body>
</html>
