@extends('layouts.app')

@section('title', 'Enregistrer un Règlement')

@section('content')
<div class="max-w-2xl mx-auto space-y-6" x-data="{ selectedRemaining: {{ $invoice ? $invoice->remaining_amount : 0 }} }">
    
    <!-- Top breadcrumb -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center space-x-2 text-xs text-slate-400 mb-1">
                <a href="{{ route('payments.index') }}" class="hover:text-sky-600">Paiements</a>
                <span>&bull;</span>
                <span class="text-slate-600 font-medium">Nouvel encaissement</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Enregistrer un Règlement Client</h1>
        </div>
        <a href="{{ route('payments.index') }}" class="px-3.5 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold transition-colors">
            Annuler
        </a>
    </div>

    <!-- Form -->
    <form action="{{ route('payments.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-5">
            
            <!-- Facture concernée -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Facture impayée concernée *</label>
                @if($invoice)
                    <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-xs flex justify-between items-center">
                        <div>
                            <span class="font-mono font-bold text-sky-700">{{ $invoice->reference }}</span> &bull; 
                            <strong class="text-slate-800">{{ $invoice->client->company_name }}</strong>
                        </div>
                        <span class="font-bold text-amber-600">Reste dû : {{ number_format($invoice->remaining_amount, 2, ',', ' ') }} DH</span>
                    </div>
                @else
                    <select name="invoice_id" 
                            required 
                            @change="const opt = $event.target.selectedOptions[0]; selectedRemaining = opt ? opt.getAttribute('data-remaining') : 0"
                            class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                        <option value="">Sélectionnez la facture à régler...</option>
                        @foreach($unpaidInvoices as $inv)
                            <option value="{{ $inv->id }}" data-remaining="{{ $inv->remaining_amount }}" {{ old('invoice_id') == $inv->id ? 'selected' : '' }}>
                                {{ $inv->reference }} &bull; {{ $inv->client->company_name }} (Reste : {{ number_format($inv->remaining_amount, 2, ',', ' ') }} DH)
                            </option>
                        @endforeach
                    </select>
                @endif
            </div>

            <!-- Montant -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Montant encaissé (DH) *</label>
                <input type="number" 
                       step="0.01" 
                       min="0.01" 
                       name="amount" 
                       required 
                       value="{{ old('amount', $invoice ? $invoice->remaining_amount : '') }}" 
                       placeholder="0.00" 
                       class="w-full py-2.5 px-3 bg-slate-50 font-bold text-slate-900 text-sm rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                <p class="text-[10px] text-slate-400 mt-1">Le montant ne peut pas excéder le solde restant dû.</p>
            </div>

            <!-- Date paiement -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Date d'encaissement *</label>
                <input type="date" name="payment_date" required value="{{ old('payment_date', date('Y-m-d')) }}" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
            </div>

            <!-- Mode de règlement -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Mode de règlement *</label>
                <select name="payment_method" required class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                    <option value="virement" {{ old('payment_method') === 'virement' ? 'selected' : '' }}>Virement bancaire</option>
                    <option value="cheque" {{ old('payment_method') === 'cheque' ? 'selected' : '' }}>Chèque bancaire</option>
                    <option value="especes" {{ old('payment_method') === 'especes' ? 'selected' : '' }}>Espèces (Reçu de caisse)</option>
                </select>
            </div>

            <!-- Banque & Référence -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Banque émettrice</label>
                    <input type="text" name="bank" value="{{ old('bank') }}" placeholder="Ex: Attijariwafa, BCP, BMCI..." class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Référence (N° Chèque / Bordereau)</label>
                    <input type="text" name="reference" value="{{ old('reference') }}" placeholder="Ex: CHQ-554123..." class="w-full py-2.5 px-3 bg-slate-50 font-mono text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Commentaires ou mentions</label>
                <textarea name="comments" rows="2" placeholder="Informations complémentaires..." class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">{{ old('comments') }}</textarea>
            </div>

        </div>

        <!-- Submit -->
        <div class="flex items-center justify-end space-x-3">
            <a href="{{ route('payments.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-semibold transition-colors">
                Annuler
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-xs font-semibold shadow-lg shadow-sky-500/25 transition-all">
                Enregistrer l'encaissement
            </button>
        </div>

    </form>

</div>
@endsection
