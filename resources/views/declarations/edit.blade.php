@extends('layouts.app')

@section('title', 'Modifier Déclaration ' . $declaration->period)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    
    <!-- Breadcrumb -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center space-x-2 text-xs text-slate-400 mb-1">
                <a href="{{ route('declarations.index') }}" class="hover:text-sky-600">Déclarations</a>
                <span>&bull;</span>
                <span class="text-slate-600 font-medium">{{ $declaration->type->name }}</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Modifier la Déclaration</h1>
        </div>
        <a href="{{ route('declarations.show', $declaration) }}" class="px-3.5 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold transition-colors">
            Annuler
        </a>
    </div>

    <!-- Form -->
    <form action="{{ route('declarations.update', $declaration) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-5">
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                
                <!-- Client -->
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Client concerné *</label>
                    <select name="client_id" required class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                        @foreach($clients as $c)
                            <option value="{{ $c->id }}" {{ (old('client_id', $declaration->client_id) == $c->id) ? 'selected' : '' }}>
                                {{ $c->company_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Dossier optionnel -->
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Dossier client</label>
                    <select name="dossier_id" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                        <option value="">Aucun dossier particulier</option>
                        @foreach($dossiers as $dos)
                            <option value="{{ $dos->id }}" {{ (old('dossier_id', $declaration->dossier_id) == $dos->id) ? 'selected' : '' }}>
                                {{ $dos->reference }} ({{ $dos->type_label }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Type de déclaration -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Type d'impôt *</label>
                    <select name="declaration_type_id" required class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                        @foreach($types as $t)
                            <option value="{{ $t->id }}" {{ old('declaration_type_id', $declaration->declaration_type_id) == $t->id ? 'selected' : '' }}>
                                {{ $t->name }} ({{ $t->code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Période fiscale -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Période concernée *</label>
                    <input type="text" name="period" required value="{{ old('period', $declaration->period) }}" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <!-- Date échéance -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Date limite d'échéance *</label>
                    <input type="date" name="due_date" required value="{{ old('due_date', $declaration->due_date->format('Y-m-d')) }}" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <!-- Montant de l'impôt -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Montant de l'impôt (DH)</label>
                    <input type="number" step="0.01" name="amount" value="{{ old('amount', $declaration->amount) }}" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <!-- Statut -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Statut *</label>
                    <select name="status" required class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                        <option value="a_preparer" {{ old('status', $declaration->status) === 'a_preparer' ? 'selected' : '' }}>À préparer</option>
                        <option value="en_preparation" {{ old('status', $declaration->status) === 'en_preparation' ? 'selected' : '' }}>En préparation</option>
                        <option value="prete" {{ old('status', $declaration->status) === 'prete' ? 'selected' : '' }}>Prête</option>
                        <option value="deposee" {{ old('status', $declaration->status) === 'deposee' ? 'selected' : '' }}>Déposée</option>
                        <option value="payee" {{ old('status', $declaration->status) === 'payee' ? 'selected' : '' }}>Payée</option>
                        <option value="en_retard" {{ old('status', $declaration->status) === 'en_retard' ? 'selected' : '' }}>En retard</option>
                        <option value="annulee" {{ old('status', $declaration->status) === 'annulee' ? 'selected' : '' }}>Annulée</option>
                    </select>
                </div>

                <!-- Responsable -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Responsable de traitement</label>
                    <select name="responsible_id" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                        <option value="">Sélectionnez...</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ old('responsible_id', $declaration->responsible_id) == $emp->id ? 'selected' : '' }}>
                                {{ $emp->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Date dépôt -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Date effective de dépôt</label>
                    <input type="date" name="filing_date" value="{{ old('filing_date', $declaration->filing_date ? $declaration->filing_date->format('Y-m-d') : '') }}" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <!-- Réf dépôt -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Référence télédéclaration (SIMPL)</label>
                    <input type="text" name="filing_reference" value="{{ old('filing_reference', $declaration->filing_reference) }}" class="w-full py-2.5 px-3 bg-slate-50 font-mono text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <!-- Commentaires -->
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Commentaires ou justificatifs</label>
                    <textarea name="comments" rows="2" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">{{ old('comments', $declaration->comments) }}</textarea>
                </div>

            </div>

        </div>

        <!-- Submit -->
        <div class="flex items-center justify-end space-x-3">
            <a href="{{ route('declarations.show', $declaration) }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-semibold transition-colors">
                Annuler
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-xs font-semibold shadow-lg shadow-sky-500/25 transition-all">
                Mettre à jour la déclaration
            </button>
        </div>

    </form>

</div>
@endsection
