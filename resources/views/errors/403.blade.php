@extends('layouts.app')

@section('title', 'Geen toegang')

@section('content')
    <section class="card">
        <h1>Geen toegang (403)</h1>
        <div class="alert alert-error" role="alert">
            {{ $exception->getMessage() ?: 'Je hebt onvoldoende rechten om deze pagina te bekijken.' }}
        </div>
        <p class="lead">Neem contact op met de praktijkbeheerder als je denkt dat dit niet klopt.</p>
        <a class="btn" href="{{ auth()->check() ? route('dashboard') : route('home') }}">Terug naar het overzicht</a>
    </section>
@endsection
