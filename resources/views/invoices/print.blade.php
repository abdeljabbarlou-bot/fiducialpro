<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture {{ $invoice->reference }}</title>
    <!-- CSS compilé localement (impression possible même hors ligne) -->
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; font-size: 12px; }
            .print-shadow-none { box-shadow: none !important; border: none !important; }
        }
    </style>
</head>
<body class="bg-slate-100 p-4 sm:p-8 font-sans antialiased text-slate-800">

    <!-- Top print bar -->
    <div class="max-w-4xl mx-auto mb-4 flex items-center justify-between no-print">
        <a href="{{ route('invoices.show', $invoice) }}" class="text-xs text-slate-600 hover:text-slate-900 font-semibold">
            &larr; Revenir à la facture
        </a>
        <button onclick="window.print()" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-semibold shadow-md transition-all flex items-center space-x-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            <span>Lancer l'impression</span>
        </button>
    </div>

    <!-- Sheet -->
    <div class="max-w-4xl mx-auto bg-white p-8 sm:p-12 rounded-3xl shadow-lg border border-slate-200 print-shadow-none space-y-8">
        
        <div class="flex justify-between items-start border-b border-slate-100 pb-6">
            <div>
                <a href="{{ route('dashboard') }}" class="block mb-2">
                    <img src="{{ asset('images/logo.jpg') }}?v=2" alt="FIDUCIALPRO" class="h-10 w-auto max-w-[210px] object-contain rounded">
                </a>
                <p class="text-xs text-slate-500 font-medium">{{ config('cabinet.tagline') }}</p>
                <p class="text-xs text-slate-400">{{ config('cabinet.address') }}, {{ config('cabinet.city') }} - {{ config('cabinet.country') }}</p>
                <p class="text-[11px] text-slate-400 font-mono mt-0.5">ICE: {{ config('cabinet.ice') }} &bull; IF: {{ config('cabinet.if') }} &bull; RC: {{ config('cabinet.rc') }}</p>
            </div>
            <div class="text-right">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Facture d'Honoraires</span>
                <p class="text-2xl font-black font-mono text-sky-700 mt-1">{{ $invoice->reference }}</p>
                <p class="text-xs text-slate-500 mt-2">Date émission : <strong>{{ $invoice->invoice_date->format('d/m/Y') }}</strong></p>
                <p class="text-xs text-slate-500">Date échéance : <strong>{{ $invoice->due_date->format('d/m/Y') }}</strong></p>
            </div>
        </div>

        <div class="bg-slate-50 p-6 rounded-2xl grid grid-cols-2 gap-4 text-xs">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase">Facturé à :</span>
                <h2 class="text-sm font-bold text-slate-900 mt-0.5">{{ $invoice->client->company_name }}</h2>
                <p class="text-slate-600 mt-1">{{ $invoice->client->address ?? 'Siège social' }}</p>
                <p class="text-slate-600">{{ $invoice->client->city ?? 'Casablanca' }} &bull; Tél: {{ $invoice->client->phone ?? 'N/A' }}</p>
            </div>
            <div class="text-right space-y-0.5">
                <span class="text-[10px] font-bold text-slate-400 uppercase">Identifiants</span>
                <p>ICE : <strong class="font-mono">{{ $invoice->client->ice ?? 'N/A' }}</strong></p>
                <p>IF : <strong class="font-mono">{{ $invoice->client->if_number ?? 'N/A' }}</strong></p>
                <p>RC : <strong class="font-mono">{{ $invoice->client->rc_number ?? 'N/A' }}</strong></p>
            </div>
        </div>

        @if($invoice->description)
            <div class="text-xs bg-sky-50/60 p-3 rounded-xl border border-sky-100 text-sky-950">
                <strong>Objet :</strong> {{ $invoice->description }}
            </div>
        @endif

        <table class="w-full text-left text-xs">
            <thead>
                <tr class="border-b-2 border-slate-200 text-slate-500 uppercase text-[10px] font-bold">
                    <th class="py-2.5">Désignation des prestations</th>
                    <th class="py-2.5 text-right">Qté</th>
                    <th class="py-2.5 text-right">P.U. HT</th>
                    <th class="py-2.5 text-right">TVA</th>
                    <th class="py-2.5 text-right">Total TTC</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($invoice->items as $item)
                    <tr>
                        <td class="py-3 font-semibold text-slate-800">{{ $item->description }}</td>
                        <td class="py-3 text-right text-slate-600">{{ number_format($item->quantity, 2, ',', ' ') }}</td>
                        <td class="py-3 text-right text-slate-600">{{ number_format($item->unit_price, 2, ',', ' ') }} DH</td>
                        <td class="py-3 text-right text-slate-600">{{ number_format($item->tax_rate, 0) }}%</td>
                        <td class="py-3 text-right font-bold text-slate-900">{{ number_format($item->total_ttc, 2, ',', ' ') }} DH</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="flex justify-between items-start pt-4 border-t border-slate-100">
            <div class="text-xs text-slate-500 max-w-sm space-y-1">
                <p><strong>Modalités :</strong> {{ $invoice->payment_conditions }}</p>
                <p><strong>Virement :</strong> {{ config('cabinet.bank_name') }} RIB: {{ config('cabinet.bank_rib') }}</p>
            </div>
            <div class="w-72 bg-slate-50 p-4 rounded-2xl space-y-1.5 text-xs">
                <div class="flex justify-between text-slate-600">
                    <span>Total HT :</span>
                    <strong>{{ number_format($invoice->subtotal_ht, 2, ',', ' ') }} DH</strong>
                </div>
                @foreach($invoice->tax_breakdown as $tb)
                    <div class="flex justify-between text-slate-600">
                        <span>TVA ({{ number_format($tb['rate'], 0) }}%) :</span>
                        <strong>{{ number_format($tb['tax_amount'], 2, ',', ' ') }} DH</strong>
                    </div>
                @endforeach
                <div class="flex justify-between text-sm font-black text-slate-900 pt-2 border-t border-slate-200">
                    <span>Total TTC :</span>
                    <span class="text-sky-700">{{ number_format($invoice->total_ttc, 2, ',', ' ') }} DH</span>
                </div>
                <div class="flex justify-between text-xs text-emerald-700 font-semibold pt-1 border-t border-slate-100">
                    <span>Payé :</span>
                    <span>{{ number_format($invoice->paid_amount, 2, ',', ' ') }} DH</span>
                </div>
                <div class="flex justify-between text-xs font-bold pt-1 border-t border-slate-100 text-amber-600">
                    <span>Reste Dû :</span>
                    <span>{{ number_format($invoice->remaining_amount, 2, ',', ' ') }} DH</span>
                </div>
            </div>
        </div>

    </div>

</body>
</html>
