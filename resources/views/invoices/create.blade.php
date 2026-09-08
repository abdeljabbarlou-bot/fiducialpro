@extends('layouts.app')

@section('title', 'Émettre une Facture')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="invoiceForm()">
    
    <!-- Top breadcrumb -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center space-x-2 text-xs text-slate-400 mb-1">
                <a href="{{ route('invoices.index') }}" class="hover:text-sky-600">Facturation</a>
                <span>&bull;</span>
                <span class="text-slate-600 font-medium">Nouvelle facture</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Créer et Émettre une Facture d'Honoraires</h1>
        </div>
        <a href="{{ route('invoices.index') }}" class="px-3.5 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold transition-colors">
            Annuler
        </a>
    </div>

    <!-- Form -->
    <form action="{{ route('invoices.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Card 1 : Entête & Client -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-5">
            <div class="border-b border-slate-100 pb-3">
                <h2 class="text-base font-bold text-slate-900 flex items-center">
                    <i class="fa-solid fa-file-invoice text-sky-600 mr-2"></i> Informations de Facturation
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Désignation du client débiteur et conditions de règlement.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                
                <!-- Client -->
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Client à facturer *</label>
                    <select name="client_id" required class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                        <option value="">Sélectionnez le client...</option>
                        @foreach($clients as $c)
                            <option value="{{ $c->id }}" {{ (old('client_id', $selectedClientId) == $c->id) ? 'selected' : '' }}>
                                {{ $c->company_name }} (ICE: {{ $c->ice ?? 'N/A' }} | IF: {{ $c->if_number ?? 'N/A' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Date facture -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Date d'émission *</label>
                    <input type="date" name="invoice_date" value="{{ old('invoice_date', date('Y-m-d')) }}" required class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <!-- Date échéance -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Date d'échéance de paiement *</label>
                    <input type="date" name="due_date" value="{{ old('due_date', date('Y-m-d', strtotime('+30 days'))) }}" required class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <!-- Statut initial -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Statut initial *</label>
                    <select name="status" required class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                        <option value="emise" {{ old('status', 'emise') === 'emise' ? 'selected' : '' }}>Émise (Validée et transmise au client)</option>
                        <option value="brouillon" {{ old('status') === 'brouillon' ? 'selected' : '' }}>Brouillon (En cours d'élaboration)</option>
                    </select>
                </div>

                <!-- Conditions -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Conditions de règlement</label>
                    <input type="text" name="payment_conditions" value="{{ old('payment_conditions', 'Paiement à réception ou virement sous 30 jours') }}" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <!-- Objet / Description -->
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Objet / Description générale de la facture</label>
                    <input type="text" name="description" value="{{ old('description') }}" placeholder="Ex: Honoraires de tenue comptable et bilan fiscal exercice 2025/2026..." class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

            </div>
        </div>

        <!-- Card 2 : Lignes de Facture Dynamiques (Alpine.js) -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <h2 class="text-base font-bold text-slate-900 flex items-center">
                        <i class="fa-solid fa-list-check text-sky-600 mr-2"></i> Lignes de Prestations
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">Détaillez les honoraires, débours et vacations.</p>
                </div>
                <button type="button" @click="addItem()" class="px-3 py-1.5 bg-sky-50 text-sky-700 hover:bg-sky-100 rounded-xl text-xs font-semibold transition-colors flex items-center space-x-1.5">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>Ajouter une ligne</span>
                </button>
            </div>

            <!-- Table of Items -->
            <div class="space-y-3">
                <template x-for="(item, index) in items" :key="index">
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 grid grid-cols-12 gap-3 items-center">
                        
                        <!-- Description -->
                        <div class="col-span-12 sm:col-span-5">
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Désignation de la prestation *</label>
                            <input type="text" 
                                   :name="'items[' + index + '][description]'" 
                                   x-model="item.description" 
                                   required 
                                   placeholder="Ex: Tenue de comptabilité mensuelle..." 
                                   class="w-full py-2 px-3 bg-white text-xs rounded-xl border border-slate-200 focus:border-sky-500 outline-none">
                        </div>

                        <!-- Quantité -->
                        <div class="col-span-4 sm:col-span-2">
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Qté *</label>
                            <input type="number" 
                                   step="0.01" 
                                   min="0.01" 
                                   :name="'items[' + index + '][quantity]'" 
                                   x-model.number="item.quantity" 
                                   required 
                                   class="w-full py-2 px-3 bg-white text-xs rounded-xl border border-slate-200 focus:border-sky-500 outline-none text-right">
                        </div>

                        <!-- Prix Unitaire HT -->
                        <div class="col-span-4 sm:col-span-2">
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Prix U. HT *</label>
                            <input type="number" 
                                   step="0.01" 
                                   min="0" 
                                   :name="'items[' + index + '][unit_price]'" 
                                   x-model.number="item.unit_price" 
                                   required 
                                   class="w-full py-2 px-3 bg-white text-xs rounded-xl border border-slate-200 focus:border-sky-500 outline-none text-right">
                        </div>

                        <!-- Taux TVA -->
                        <div class="col-span-3 sm:col-span-2">
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">TVA (%)</label>
                            <select :name="'items[' + index + '][tax_rate]'" 
                                    x-model.number="item.tax_rate" 
                                    class="w-full py-2 px-2 bg-white text-xs rounded-xl border border-slate-200 focus:border-sky-500 outline-none">
                                <option value="20">20% (Standard)</option>
                                <option value="14">14%</option>
                                <option value="10">10%</option>
                                <option value="7">7%</option>
                                <option value="0">0% (Exonéré)</option>
                            </select>
                        </div>

                        <!-- Remove Button -->
                        <div class="col-span-1 flex justify-center pt-4 sm:pt-4">
                            <button type="button" 
                                    @click="removeItem(index)" 
                                    :disabled="items.length === 1"
                                    class="text-slate-400 hover:text-rose-600 disabled:opacity-30 p-1">
                                <i class="fa-solid fa-trash-can text-sm"></i>
                            </button>
                        </div>

                    </div>
                </template>
            </div>

            <!-- Calculated Totals Box -->
            <div class="mt-6 pt-4 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-end">
                <div class="w-full sm:w-80 bg-slate-50 p-4 rounded-2xl border border-slate-200/80 space-y-2 text-xs">
                    <div class="flex justify-between text-slate-600">
                        <span>Total Hors Taxes (HT) :</span>
                        <strong class="font-bold text-slate-800" x-text="formatMoney(calculateSubtotal()) + ' DH'"></strong>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>Montant TVA :</span>
                        <strong class="font-bold text-slate-800" x-text="formatMoney(calculateTax()) + ' DH'"></strong>
                    </div>
                    <div class="flex justify-between text-base font-black text-slate-900 pt-2 border-t border-slate-200">
                        <span>Total TTC :</span>
                        <span class="text-sky-700" x-text="formatMoney(calculateTotal()) + ' DH'"></span>
                    </div>
                </div>
            </div>

        </div>

        <!-- Card 3 : Notes & Coordonnées Bancaires -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
            <h2 class="text-base font-bold text-slate-900 flex items-center">
                <i class="fa-solid fa-building-columns text-slate-600 mr-2"></i> Mentions & Coordonnées Bancaires
            </h2>
            <textarea name="notes" rows="2" placeholder="Ex: Règlement par virement bancaire sur notre compte Attijariwafa Bank RIB : 007 780 0001234567890123 45..." class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">{{ old('notes') }}</textarea>
        </div>

        <!-- Submit -->
        <div class="flex items-center justify-end space-x-3">
            <a href="{{ route('invoices.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-semibold transition-colors">
                Annuler
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-xs font-semibold shadow-lg shadow-sky-500/25 transition-all">
                Émettre la facture
            </button>
        </div>

    </form>

</div>

<script>
function invoiceForm() {
    return {
        items: [
            { description: 'Honoraires de tenue comptable et déclarations fiscales', quantity: 1, unit_price: 2500, tax_rate: 20 }
        ],
        addItem() {
            this.items.push({ description: '', quantity: 1, unit_price: 0, tax_rate: 20 });
        },
        removeItem(index) {
            if (this.items.length > 1) {
                this.items.splice(index, 1);
            }
        },
        calculateSubtotal() {
            return this.items.reduce((sum, item) => {
                const q = parseFloat(item.quantity) || 0;
                const p = parseFloat(item.unit_price) || 0;
                return sum + (q * p);
            }, 0);
        },
        calculateTax() {
            return this.items.reduce((sum, item) => {
                const q = parseFloat(item.quantity) || 0;
                const p = parseFloat(item.unit_price) || 0;
                const r = parseFloat(item.tax_rate) || 0;
                return sum + (q * p * (r / 100));
            }, 0);
        },
        calculateTotal() {
            return this.calculateSubtotal() + this.calculateTax();
        },
        formatMoney(val) {
            return (Math.round(val * 100) / 100).toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
    };
}
</script>
@endsection
