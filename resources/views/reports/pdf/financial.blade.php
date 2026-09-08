@extends('reports.pdf.layout')

@section('doc_title', 'Rapport Financier')
@section('report_name', 'Rapport Financier & État de Recouvrement')

@section('report_meta')
    Période : <strong>Du {{ $dateStart ? \Carbon\Carbon::parse($dateStart)->format('d/m/Y') : 'Début' }}
    au {{ $dateEnd ? \Carbon\Carbon::parse($dateEnd)->format('d/m/Y') : 'Ce jour' }}</strong>
@endsection

@section('content')

    <div class="summary-box">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td>Total Facturé HT<br><strong>{{ number_format($totalHt, 2, ',', ' ') }} DH</strong></td>
                <td>Total Facturé TTC<br><strong>{{ number_format($totalTtc, 2, ',', ' ') }} DH</strong></td>
                <td>Total Encaissé<br><strong style="color: #047857;">{{ number_format($totalPaid, 2, ',', ' ') }} DH</strong></td>
                <td>Reste à Recouvrer<br><strong style="color: #b91c1c;">{{ number_format($totalRemaining, 2, ',', ' ') }} DH</strong></td>
                <td>Taux de Recouvrement<br><strong>{{ $totalTtc > 0 ? number_format(($totalPaid / $totalTtc) * 100, 1, ',', ' ') : '0,0' }} %</strong></td>
            </tr>
        </table>
    </div>

    @if($invoices->isEmpty())
        <div class="empty-state">Aucune facture émise sur la période sélectionnée.</div>
    @else
        <table class="data">
            <thead>
                <tr>
                    <th>Réf Facture</th>
                    <th>Client</th>
                    <th>Date</th>
                    <th>Échéance</th>
                    <th class="text-right">Total HT</th>
                    <th class="text-right">TVA</th>
                    <th class="text-right">Total TTC</th>
                    <th class="text-right">Encaissé</th>
                    <th class="text-right">Reste Dû</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoices as $inv)
                    <tr>
                        <td class="font-bold mono">{{ $inv->reference }}</td>
                        <td>{{ $inv->client->company_name }}</td>
                        <td>{{ $inv->invoice_date->format('d/m/Y') }}</td>
                        <td style="{{ $inv->status === 'en_retard' ? 'color: #b91c1c; font-weight: bold;' : '' }}">{{ $inv->due_date->format('d/m/Y') }}</td>
                        <td class="text-right">{{ number_format($inv->subtotal_ht, 2, ',', ' ') }}</td>
                        <td class="text-right">{{ number_format($inv->tax_amount, 2, ',', ' ') }}</td>
                        <td class="text-right font-bold">{{ number_format($inv->total_ttc, 2, ',', ' ') }}</td>
                        <td class="text-right" style="color: #047857;">{{ number_format($inv->paid_amount, 2, ',', ' ') }}</td>
                        <td class="text-right" style="{{ $inv->remaining_amount > 0 ? 'color: #b91c1c;' : '' }}">{{ number_format($inv->remaining_amount, 2, ',', ' ') }}</td>
                        <td>{{ $inv->status_label }}</td>
                    </tr>
                @endforeach

                <tr class="totals">
                    <td colspan="4">TOTAUX ({{ $invoices->count() }} facture{{ $invoices->count() > 1 ? 's' : '' }})</td>
                    <td class="text-right">{{ number_format($invoices->where('status', '!=', 'annulee')->sum('subtotal_ht'), 2, ',', ' ') }}</td>
                    <td class="text-right">{{ number_format($invoices->where('status', '!=', 'annulee')->sum('tax_amount'), 2, ',', ' ') }}</td>
                    <td class="text-right">{{ number_format($totalTtc, 2, ',', ' ') }}</td>
                    <td class="text-right">{{ number_format($invoices->sum('paid_amount'), 2, ',', ' ') }}</td>
                    <td class="text-right">{{ number_format($totalRemaining, 2, ',', ' ') }}</td>
                    <td>DH</td>
                </tr>
            </tbody>
        </table>
    @endif

@endsection
