<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture {{ $invoice->reference }}</title>
    <style>
        @page { margin: 25px 30px; }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #333333;
            line-height: 1.4;
        }
        .header-table, .meta-table, .items-table, .totals-table {
            width: 100%;
            border-collapse: collapse;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .text-muted { color: #666666; font-size: 10px; }
        
        .brand-name {
            font-size: 20px;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: -0.5px;
        }
        .brand-accent { color: #0284c7; }

        .invoice-title {
            font-size: 18px;
            font-weight: 900;
            color: #0284c7;
        }

        .client-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 12px;
            border-radius: 6px;
            margin-top: 15px;
            margin-bottom: 20px;
        }

        .items-table {
            margin-top: 10px;
            margin-bottom: 20px;
        }
        .items-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 8px 10px;
            letter-spacing: 0.5px;
        }
        .items-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 10px;
        }
        .items-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }

        .totals-table {
            width: 260px;
            float: right;
            margin-bottom: 20px;
        }
        .totals-table td {
            padding: 5px 8px;
            font-size: 10px;
        }
        .totals-table tr.total-ttc td {
            font-size: 12px;
            font-weight: bold;
            color: #0f172a;
            border-top: 1px solid #cbd5e1;
            border-bottom: 2px solid #0f172a;
            padding-top: 8px;
            padding-bottom: 8px;
        }

        .clear { clear: both; }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            font-size: 9px;
            font-weight: bold;
            border-radius: 4px;
            text-transform: uppercase;
        }
        .badge-paid { background-color: #dcfce7; color: #166534; }
        .badge-unpaid { background-color: #fef3c7; color: #92400e; }
        .badge-overdue { background-color: #ffe4e6; color: #9f1239; }
    </style>
</head>
<body>

    <!-- Header -->
    <table class="header-table">
        <tr>
            <td style="width: 55%; vertical-align: top;">
                @if(file_exists(public_path('images/logo.jpg')))
                    <div style="background: #0b0f19; display: inline-block; padding: 4px 8px; border-radius: 4px; margin-bottom: 6px;">
                        <img src="{{ public_path('images/logo.jpg') }}" style="height: 30px; width: auto;">
                    </div>
                @else
                    <div class="brand-name">{{ config('cabinet.name') }}</div>
                @endif
                <div class="text-muted" style="margin-top: 3px;">{{ config('cabinet.tagline') }}</div>
                <div class="text-muted">{{ config('cabinet.address') }}, {{ config('cabinet.city') }} - {{ config('cabinet.country') }}</div>
                <div class="text-muted" style="margin-top: 4px;">
                    ICE : <strong>{{ config('cabinet.ice') }}</strong> &bull; IF : <strong>{{ config('cabinet.if') }}</strong> &bull; RC : <strong>{{ config('cabinet.rc') }}</strong> &bull; Patente : <strong>{{ config('cabinet.patente') }}</strong>
                </div>
            </td>
            <td style="width: 45%; vertical-align: top;" class="text-right">
                <div class="invoice-title">FACTURE {{ $invoice->reference }}</div>
                <div style="margin-top: 4px;">
                    @if($invoice->status === 'payee')
                        <span class="badge badge-paid">Facture Réglée</span>
                    @elseif($invoice->status === 'en_retard')
                        <span class="badge badge-overdue">En retard de paiement</span>
                    @else
                        <span class="badge badge-unpaid">{{ $invoice->status_label }}</span>
                    @endif
                </div>
                <div class="text-muted" style="margin-top: 6px;">
                    Date d'émission : <strong>{{ $invoice->invoice_date->format('d/m/Y') }}</strong><br>
                    Date d'échéance : <strong>{{ $invoice->due_date->format('d/m/Y') }}</strong>
                </div>
            </td>
        </tr>
    </table>

    <!-- Client Box -->
    <div class="client-box">
        <table style="width: 100%;">
            <tr>
                <td style="width: 55%; vertical-align: top;">
                    <span class="text-muted" style="text-transform: uppercase; font-weight: bold;">Facturé à :</span>
                    <div style="font-size: 13px; font-weight: bold; color: #0f172a; margin-top: 2px;">
                        {{ $invoice->client->company_name }}
                    </div>
                    @if($invoice->client->trade_name)
                        <div class="text-muted">Enseigne : {{ $invoice->client->trade_name }}</div>
                    @endif
                    <div style="margin-top: 3px; font-size: 10px; color: #475569;">
                        {{ $invoice->client->address ?? 'Siège social' }}<br>
                        {{ $invoice->client->city ?? 'Casablanca' }}, {{ $invoice->client->country ?? 'Maroc' }}<br>
                        Tél : {{ $invoice->client->phone ?? 'N/A' }}
                    </div>
                </td>
                <td style="width: 45%; vertical-align: top;" class="text-right">
                    <span class="text-muted" style="text-transform: uppercase; font-weight: bold;">Identifiants Fiscaux :</span>
                    <div style="margin-top: 3px; font-size: 10px; color: #334155;">
                        <strong>ICE :</strong> {{ $invoice->client->ice ?? 'N/A' }}<br>
                        <strong>Identifiant Fiscal (IF) :</strong> {{ $invoice->client->if_number ?? 'N/A' }}<br>
                        <strong>Registre Commerce (RC) :</strong> {{ $invoice->client->rc_number ?? 'N/A' }}<br>
                        <strong>Forme Juridique :</strong> {{ $invoice->client->legal_form ?? 'SARL' }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    @if($invoice->description)
        <div style="font-size: 10px; background-color: #f1f5f9; padding: 6px 10px; border-radius: 4px; margin-bottom: 12px;">
            <strong>Objet :</strong> {{ $invoice->description }}
        </div>
    @endif

    <!-- Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 45%;">Désignation des Prestations</th>
                <th class="text-right" style="width: 10%;">Qté</th>
                <th class="text-right" style="width: 15%;">P.U. HT</th>
                <th class="text-right" style="width: 10%;">TVA</th>
                <th class="text-right" style="width: 20%;">Total TTC</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
                <tr>
                    <td class="font-bold">{{ $item->description }}</td>
                    <td class="text-right">{{ number_format($item->quantity, 2, ',', ' ') }}</td>
                    <td class="text-right">{{ number_format($item->unit_price, 2, ',', ' ') }} DH</td>
                    <td class="text-right">{{ number_format($item->tax_rate, 0) }}%</td>
                    <td class="text-right font-bold">{{ number_format($item->total_ttc, 2, ',', ' ') }} DH</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totals -->
    <table class="totals-table">
        <tr>
            <td class="text-muted">Total Net Hors Taxes (HT) :</td>
            <td class="text-right font-bold">{{ number_format($invoice->subtotal_ht, 2, ',', ' ') }} DH</td>
        </tr>
        @foreach($invoice->tax_breakdown as $tb)
            <tr>
                <td class="text-muted">TVA ({{ number_format($tb['rate'], 0) }}%) sur {{ number_format($tb['base_ht'], 2, ',', ' ') }} DH :</td>
                <td class="text-right font-bold">{{ number_format($tb['tax_amount'], 2, ',', ' ') }} DH</td>
            </tr>
        @endforeach
        <tr class="total-ttc">
            <td>TOTAL TTC (MAD) :</td>
            <td class="text-right">{{ number_format($invoice->total_ttc, 2, ',', ' ') }} DH</td>
        </tr>
        <tr>
            <td class="text-muted">Montant Déjà Réglé :</td>
            <td class="text-right font-bold" style="color: #166534;">{{ number_format($invoice->paid_amount, 2, ',', ' ') }} DH</td>
        </tr>
        <tr>
            <td class="text-muted">Net Restant à Payer :</td>
            <td class="text-right font-bold" style="color: #b91c1c;">{{ number_format($invoice->remaining_amount, 2, ',', ' ') }} DH</td>
        </tr>
    </table>

    <div class="clear"></div>

    <!-- Payment conditions & Bank details -->
    <div style="margin-top: 25px; border-top: 1px dashed #cbd5e1; padding-top: 10px; font-size: 10px; color: #475569;">
        <strong>Modalités de paiement :</strong> {{ $invoice->payment_conditions }}<br>
        <strong>Règlement par virement :</strong> {{ config('cabinet.bank_name') }} &bull; RIB : <strong>{{ config('cabinet.bank_rib') }}</strong><br>
        @if($invoice->notes)
            <div style="margin-top: 4px; font-style: italic;">{{ $invoice->notes }}</div>
        @endif
    </div>

    <!-- Footer -->
    <div class="footer">
        {{ config('cabinet.name') }} &bull; {{ config('cabinet.footer_mention') }} &bull; {{ config('cabinet.city') }}, {{ config('cabinet.country') }}
    </div>

</body>
</html>
