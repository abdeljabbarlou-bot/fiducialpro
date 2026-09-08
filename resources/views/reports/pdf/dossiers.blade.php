@extends('reports.pdf.layout')

@section('doc_title', 'Rapport Dossiers & Missions')
@section('report_name', 'Suivi des Dossiers & Missions du Cabinet')

@section('report_meta')
    Total missions : <strong>{{ $dossiers->count() }}</strong>
@endsection

@section('content')

    @if(!empty($byStatus) || !empty($byType))
        <div class="summary-box">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    @foreach($byStatus as $status => $count)
                        <td>{{ str_replace('_', ' ', ucfirst($status)) }}<br><strong>{{ $count }}</strong></td>
                    @endforeach
                </tr>
            </table>
        </div>
    @endif

    @if($dossiers->isEmpty())
        <div class="empty-state">Aucun dossier ne correspond aux critères sélectionnés.</div>
    @else
        <table class="data">
            <thead>
                <tr>
                    <th>Réf Dossier</th>
                    <th>Client</th>
                    <th>Type de Mission</th>
                    <th>Ouverture</th>
                    <th>Clôture</th>
                    <th>Responsable</th>
                    <th class="text-center">Équipe</th>
                    <th>Priorité</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dossiers as $dos)
                    <tr>
                        <td class="font-bold mono">{{ $dos->reference }}</td>
                        <td>{{ $dos->client->company_name }}</td>
                        <td>{{ $dos->type_label }}</td>
                        <td>{{ $dos->start_date->format('d/m/Y') }}</td>
                        <td>{{ $dos->end_date ? $dos->end_date->format('d/m/Y') : 'En cours' }}</td>
                        <td>{{ $dos->responsible?->full_name ?? 'Cabinet' }}</td>
                        <td class="text-center">{{ $dos->employees->count() }}</td>
                        <td style="{{ $dos->priority === 'urgente' ? 'color: #b91c1c; font-weight: bold;' : '' }}">{{ ucfirst($dos->priority) }}</td>
                        <td>{{ $dos->status_label }}</td>
                    </tr>
                @endforeach

                <tr class="totals">
                    <td colspan="9">TOTAL : {{ $dossiers->count() }} mission{{ $dossiers->count() > 1 ? 's' : '' }} au portefeuille du cabinet</td>
                </tr>
            </tbody>
        </table>
    @endif

@endsection
