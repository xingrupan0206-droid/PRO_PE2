@extends('layouts.app')

@section('title', 'Facturatieoverzicht')

@section('content')
    <section class="card">
        <h1>Facturatieoverzicht</h1>
        <p class="lead">Alle facturen van de praktijk. Zoek op factuurnummer of patiëntnaam.</p>

        @if ($loadError)
            <div class="alert alert-error" role="alert">{{ $loadError }}</div>
        @else
            <div class="toolbar">
                <form class="search" method="GET" action="{{ route('invoices.index') }}" role="search">
                    <label class="visually-hidden" for="zoek">Zoek factuur</label>
                    <input id="zoek" name="zoek" type="search" value="{{ $search }}" placeholder="Factuurnummer of patiënt...">

                    <label class="visually-hidden" for="status">Betaalstatus</label>
                    <select id="status" name="status">
                        <option value="">Alle statussen</option>
                        @foreach ($statusLabels as $statusValue => $statusLabel)
                            <option value="{{ $statusValue }}" @selected($status === $statusValue)>{{ $statusLabel }}</option>
                        @endforeach
                    </select>

                    <button class="btn" type="submit">Zoeken</button>
                    @if ($search !== '' || $status !== '')
                        <a class="btn btn-light" href="{{ route('invoices.index') }}">Wissen</a>
                    @endif
                </form>
                <span>{{ $invoices->total() }} {{ $invoices->total() === 1 ? 'factuur' : 'facturen' }}</span>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th scope="col">Factuurnummer</th>
                            <th scope="col">Patiënt</th>
                            <th scope="col">Factuurdatum</th>
                            <th scope="col" class="amount">Bedrag</th>
                            <th scope="col">Betaalstatus</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($invoices as $invoice)
                            <tr>
                                <td>{{ $invoice->invoice_number }}</td>
                                <td>{{ $invoice->patient->user->name }}</td>
                                <td>{{ $invoice->invoice_date->format('d-m-Y') }}</td>
                                <td class="amount">€ {{ number_format((float) $invoice->total_amount, 2, ',', '.') }}</td>
                                <td><span class="badge badge-{{ $invoice->status }}">{{ $statusLabels[$invoice->status] ?? $invoice->status }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td class="empty" colspan="5">
                                    @if ($search !== '' || $status !== '')
                                        Geen facturen gevonden met deze zoekopdracht.
                                    @else
                                        Er zijn nog geen facturen.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $invoices->links('partials.pagination') }}
        @endif
    </section>
@endsection
