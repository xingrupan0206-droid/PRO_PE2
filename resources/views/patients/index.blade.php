@extends('layouts.app')

@section('title', 'Patiëntenoverzicht')

@section('content')
    <section class="card">
        <h1>Patiëntenoverzicht</h1>
        <p class="lead">Alle patiënten van de praktijk. Zoek op naam, e-mailadres of telefoonnummer.</p>

        <div class="toolbar">
            <form class="search" method="GET" action="{{ route('patients.index') }}" role="search">
                <label class="visually-hidden" for="zoek">Zoek patiënt</label>
                <input id="zoek" name="zoek" type="search" value="{{ $search }}" placeholder="Zoek patiënt...">
                <button class="btn" type="submit">Zoeken</button>
                @if ($search !== '')
                    <a class="btn btn-light" href="{{ route('patients.index') }}">Wissen</a>
                @endif
            </form>
            <span>{{ $patients->total() }} {{ $patients->total() === 1 ? 'patiënt' : 'patiënten' }}</span>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th scope="col">Naam</th>
                        <th scope="col">Geboortedatum</th>
                        <th scope="col">Telefoonnummer</th>
                        <th scope="col">E-mailadres</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($patients as $patient)
                        <tr>
                            <td>{{ $patient->user->name }}</td>
                            <td>{{ $patient->birth_date->format('d-m-Y') }}</td>
                            <td>{{ $patient->user->phone ?? '-' }}</td>
                            <td>{{ $patient->user->email }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td class="empty" colspan="4">
                                @if ($search !== '')
                                    Geen patiënten gevonden voor "{{ $search }}".
                                @else
                                    Er zijn nog geen patiënten geregistreerd.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $patients->links('partials.pagination') }}
    </section>
@endsection
