@extends('reports.pdf.layout')

@section('doc_title', 'Rapport Portefeuille Clients')
@section('report_name', 'Répertoire du Portefeuille Clients')

@section('report_meta')
    Total entreprises clientes : <strong>{{ $clients->count() }}</strong>
@endsection

@section('content')

    @if(!empty($byStatus) || !empty($byForm))
        <div class="summary-box">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    @foreach($byStatus as $status => $count)
                        <td>{{ ucfirst($status) }}<br><strong>{{ $count }}</strong></td>
                    @endforeach
                    @foreach($byForm as $form => $count)
                        <td>{{ $form }}<br><strong>{{ $count }}</strong></td>
                    @endforeach
                </tr>
            </table>
        </div>
    @endif

    @if($clients->isEmpty())
        <div class="empty-state">Aucun client ne correspond aux critères sélectionnés.</div>
    @else
        <table class="data">
            <thead>
                <tr>
                    <th>Raison Sociale</th>
                    <th>Forme</th>
                    <th>ICE</th>
                    <th>IF</th>
                    <th>RC</th>
                    <th>Ville</th>
                    <th>Téléphone</th>
                    <th>Représentant Légal</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                @foreach($clients as $c)
                    <tr>
                        <td class="font-bold">{{ $c->company_name }}</td>
                        <td>{{ $c->legal_form ?? '-' }}</td>
                        <td class="mono">{{ $c->ice ?? '-' }}</td>
                        <td class="mono">{{ $c->if_number ?? '-' }}</td>
                        <td class="mono">{{ $c->rc_number ?? '-' }}</td>
                        <td>{{ $c->city ?? '-' }}</td>
                        <td>{{ $c->phone ?? '-' }}</td>
                        <td>{{ $c->primaryContact?->full_name ?? '-' }}</td>
                        <td>{{ ucfirst($c->status) }}</td>
                    </tr>
                @endforeach

                <tr class="totals">
                    <td colspan="9">TOTAL : {{ $clients->count() }} client{{ $clients->count() > 1 ? 's' : '' }} au portefeuille</td>
                </tr>
            </tbody>
        </table>
    @endif

@endsection
