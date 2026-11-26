<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') | {{ config('app.name') }}</title>
    @include('layouts.styles')
    @stack('styles')
</head>
<body>
<header class="topbar">
    <a class="brand" href="{{ route('home') }}">SmilePro</a>

    @auth
        <nav aria-label="Hoofdmenu">
            <a href="{{ route('dashboard') }}" @class(['active' => request()->routeIs('dashboard')])>Dashboard</a>
            @if (auth()->user()->isStaff() && Route::has('patients.index'))
                <a href="{{ route('patients.index') }}" @class(['active' => request()->routeIs('patients.*')])>Patiënten</a>
            @endif
            @if (auth()->user()->isStaff() && Route::has('invoices.index'))
                <a href="{{ route('invoices.index') }}" @class(['active' => request()->routeIs('invoices.*')])>Facturen</a>
            @endif
            @if (auth()->user()->hasRole('admin') && Route::has('employees.index'))
                <a href="{{ route('employees.index') }}" @class(['active' => request()->routeIs('employees.*')])>Medewerkers</a>
            @endif
            @if (auth()->user()->employee && Route::has('agenda.index'))
                <a href="{{ route('agenda.index') }}" @class(['active' => request()->routeIs('agenda.*')])>Mijn agenda</a>
            @endif
        </nav>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn btn-light" type="submit">Uitloggen</button>
        </form>
    @else
        <nav aria-label="Account">
            <a href="{{ route('login') }}">Inloggen</a>
            @if (Route::has('register'))
                <a href="{{ route('register') }}">Registreren</a>
            @endif
        </nav>
    @endauth
</header>

<main class="page">
    @if (session('status'))
        <div class="alert alert-success" role="status">{{ session('status') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-error" role="alert">{{ session('error') }}</div>
    @endif

    @yield('content')
</main>
</body>
</html>
