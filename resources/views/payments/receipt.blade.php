<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Reçu de Règlement - {{ $payment->invoice->reference }} - FiducialPro</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('images/logo.jpg') }}">
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; font-size: 12px; }
            .receipt-card { box-shadow: none !important; border: 1px solid #cbd5e1 !important; border-radius: 0 !important; }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen p-4 sm:p-8 font-sans antialiased text-slate-800">

    <!-- Top Action Bar -->
    <div class="max-w-2xl mx-auto mb-5 flex items-center justify-between no-print">
        <button onclick="window.history.back()" class="inline-flex items-center text-xs text-slate-600 hover:text-slate-900 font-semibold transition-colors">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>Revenir en arrière</span>
        </button>
        <button onclick="window.print()" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-semibold shadow-md transition-all flex items-center space-x-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            <span>Imprimer le Reçu</span>
        </button>
    </div>

    <!-- Receipt Card -->
    <div class="receipt-card max-w-2xl mx-auto bg-white p-8 sm:p-12 rounded-3xl shadow-xl border border-slate-200/80 space-y-7">
        
        <!-- Header -->
        <div class="flex justify-between items-start border-b border-slate-200 pb-6">
            <div>
                <a href="{{ route('dashboard') }}" class="block mb-2">
                    <img src="{{ asset('images/logo.jpg') }}?v=2" alt="FIDUCIALPRO" class="h-10 w-auto max-w-[210px] object-contain rounded">
                </a>
                <p class="text-xs text-slate-500 font-medium">{{ config('cabinet.tagline') }}</p>
                <p class="text-[11px] text-slate-400 mt-0.5">{{ config('cabinet.city') }}, {{ config('cabinet.country') }} &bull; ICE: {{ config('cabinet.ice') }} &bull; IF: {{ config('cabinet.if') }}</p>
            </div>
            <div class="text-right">
                <span class="inline-block text-[11px] font-bold text-slate-800 uppercase tracking-wider bg-slate-100 px-3 py-1 rounded-full border border-slate-300">
                    REÇU DE RÈGLEMENT
                </span>
                <p class="text-xs text-slate-400 mt-2.5">Reçu N° : <strong class="font-mono text-slate-800 font-bold">REC-{{ $payment->id }}-{{ $payment->payment_date->format('Y') }}</strong></p>
                <p class="text-xs text-slate-400">Date : <strong class="text-slate-800">{{ $payment->payment_date->format('d/m/Y') }}</strong></p>
            </div>
        </div>

        <!-- Receipt Body -->
        <div class="space-y-5 text-xs leading-relaxed text-slate-700">
            <div>
                <span class="text-[11px] uppercase tracking-wider text-slate-400 font-bold">Client Bénéficiaire :</span>
                <div class="mt-1.5 p-4 bg-slate-50 rounded-2xl border border-slate-200/80">
                    <div class="font-bold text-slate-900 text-sm">
                        {{ $payment->client->company_name }}
                    </div>
                    <div class="text-xs text-slate-500 mt-1 flex flex-wrap gap-x-4 gap-y-1">
                        <span>ICE : <strong class="font-mono text-slate-700">{{ $payment->client->ice ?? 'N/A' }}</strong></span>
                        @if($payment->client->if_number)
                            <span>IF : <strong class="font-mono text-slate-700">{{ $payment->client->if_number }}</strong></span>
                        @endif
                        <span>Ville : <strong class="text-slate-700">{{ $payment->client->city ?? 'Casablanca' }}</strong></span>
                    </div>
                </div>
            </div>

            <!-- Payment Details Grid -->
            <div class="grid grid-cols-2 gap-4 py-2 border-y border-slate-100">
                <div>
                    <span class="text-slate-400 font-bold uppercase text-[10px] tracking-wider">Facture Référencée</span>
                    <p class="font-mono font-bold text-sky-800 text-sm mt-0.5">{{ $payment->invoice->reference }}</p>
                </div>
                <div>
                    <span class="text-slate-400 font-bold uppercase text-[10px] tracking-wider">Mode de Paiement</span>
                    <p class="font-bold text-slate-900 text-sm mt-0.5">{{ $payment->method_label }}</p>
                </div>
                @if($payment->reference)
                <div>
                    <span class="text-slate-400 font-bold uppercase text-[10px] tracking-wider">Référence Pièce / Chèque</span>
                    <p class="font-mono font-bold text-slate-800 mt-0.5">{{ $payment->reference }}</p>
                </div>
                @endif
                @if($payment->bank)
                <div>
                    <span class="text-slate-400 font-bold uppercase text-[10px] tracking-wider">Banque Émettrice</span>
                    <p class="font-bold text-slate-800 mt-0.5">{{ $payment->bank }}</p>
                </div>
                @endif
            </div>

            <!-- Executive Amount Banner (Sober Navy / Slate Corporate) -->
            <div class="p-6 bg-slate-900 text-white rounded-2xl flex justify-between items-center shadow-lg shadow-slate-900/10">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-sky-400">Montant Encaissé</span>
                    <p class="text-xs text-slate-300 mt-0.5">En règlement de la facture précitée</p>
                </div>
                <div class="text-3xl font-black text-white tracking-tight">
                    {{ number_format($payment->amount, 2, ',', ' ') }} <span class="text-sm font-semibold text-sky-400">MAD</span>
                </div>
            </div>

            <!-- Balance remaining status -->
            <div class="flex justify-between items-center px-3 py-2 bg-slate-50 rounded-xl text-xs text-slate-600 border border-slate-200/60">
                <span>Total Facture : <strong class="text-slate-900">{{ number_format($payment->invoice->total_ttc, 2, ',', ' ') }} DH</strong></span>
                <span>Solde Restant Dû : <strong class="{{ $payment->invoice->remaining_amount > 0 ? 'text-amber-700' : 'text-slate-900' }}">{{ number_format($payment->invoice->remaining_amount, 2, ',', ' ') }} DH</strong></span>
            </div>

            @if($payment->comments)
                <div class="p-3.5 bg-slate-50 rounded-xl text-slate-600 text-xs italic border border-slate-200/60">
                    <strong>Observations :</strong> {{ $payment->comments }}
                </div>
            @endif
        </div>

        <!-- Stamp & Signature -->
        <div class="pt-6 border-t border-slate-200 flex justify-between items-end text-xs">
            <div class="text-slate-400 max-w-xs space-y-1">
                <p>Émis par le cabinet pour valoir quittance de paiement.</p>
                <p class="text-[11px] text-slate-400">Opérateur : <strong class="text-slate-600">{{ $payment->creator?->name ?? 'Direction Financière' }}</strong></p>
            </div>
            <div class="text-center w-56 border-2 border-dashed border-slate-300 p-4 rounded-xl bg-slate-50/60">
                <span class="text-[10px] font-bold uppercase text-slate-400 block mb-6">Cachet & Signature de la Fiduciaire</span>
                <span class="text-slate-900 font-extrabold text-xs uppercase tracking-widest border-2 border-slate-800 px-3 py-1 rounded bg-white shadow-sm inline-block">
                    RÈGLEMENT VALIDÉ
                </span>
            </div>
        </div>

    </div>

</body>
</html>
