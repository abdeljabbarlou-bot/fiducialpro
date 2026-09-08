@extends('reports.pdf.layout')

@section('doc_title', 'Rapport Déclarations Fiscales')
@section('report_name', 'Déclarations Fiscales & Télétransmissions SIMPL')

@section('report_meta')
    Période : <strong>Du {{ $dateStart ? \Carbon\Carbon::parse($dateStart)->format('d/m/Y') : 'Début' }}
    au {{ $dateEnd ? \Carbon\Carbon::parse($dateEnd)->format('d/m/Y') : 'Ce jour' }}</strong>
@endsection

@section('content')

    <div class="summary-box">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td>Total déclarations<br><strong>{{ $declarations->count() }}</strong></td>
                @foreach($byStatus as $status => $count)
                    <td>
                        {{ str_replace('_', ' ', ucfirst($status)) }}<br>
                        <strong style="{{ $status === 'en_retard' ? 'color: #b91c1c;' : '' }}">{{ $count }}</strong>
                    </td>
                @endforeach
                <td>Montant total<br><strong>{{ number_format($totalAmount, 2, ',', ' ') }} DH</strong></td>
            </tr>
        </table>
    </div>

    @if($declarations->isEmpty())
        <div class="empty-state">Aucune déclaration fiscale sur la période sélectionnée.</div>
    @else
        <table class="data">
            <thead>
                <tr>
                    <th>Client</th>
                    <th>Impôt / Taxe</th>
                    <th>Période</th>
                    <th>Échéance légale</th>
                    <th>Date de dépôt</th>
                    <th class="text-right">Montant (DH)</th>
                    <th>Réf. SIMPL</th>
                    <th>Statut</th>
                    <th>Responsable</th>
                </tr>
            </thead>
            <tbody>
                @foreach($declarations as $decl)
                    <tr>
                        <td class="font-bold">{{ $decl->client->company_name }}</td>
                        <td>{{ $decl->type->code }}</td>
                        <td>{{ $decl->period }}</td>
                        <td style="{{ $decl->status === 'en_retard' ? 'color: #b91c1c; font-weight: bold;' : '' }}">{{ $decl->due_date->format('d/m/Y') }}</td>
                        <td>{{ $decl->filing_date ? $decl->filing_date->format('d/m/Y') : '—' }}</td>
                        <td class="text-right font-bold">{{ number_format($decl->amount, 2, ',', ' ') }}</td>
                        <td class="mono">{{ $decl->filing_reference ?? '—' }}</td>
                        <td>{{ $decl->status_label }}</td>
                        <td>{{ $decl->responsible?->full_name ?? 'Non assigné' }}</td>
                    </tr>
                @endforeach

                <tr class="totals">
                    <td colspan="5">TOTAL ({{ $declarations->count() }} déclaration{{ $declarations->count() > 1 ? 's' : '' }})</td>
                    <td class="text-right">{{ number_format($totalAmount, 2, ',', ' ') }}</td>
                    <td colspan="3">DH</td>
                </tr>
            </tbody>
        </table>
    @endif

@endsection
