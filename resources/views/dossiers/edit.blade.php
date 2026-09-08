@extends('layouts.app')

@section('title', 'Modifier ' . $dossier->reference)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    
    <!-- Top breadcrumb -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center space-x-2 text-xs text-slate-400 mb-1">
                <a href="{{ route('dossiers.index') }}" class="hover:text-sky-600">Dossiers</a>
                <span>&bull;</span>
                <a href="{{ route('dossiers.show', $dossier) }}" class="hover:text-sky-600">{{ $dossier->reference }}</a>
                <span>&bull;</span>
                <span class="text-slate-600 font-medium">Modification</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Modifier le Dossier {{ $dossier->reference }}</h1>
        </div>
        <a href="{{ route('dossiers.show', $dossier) }}" class="px-3.5 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold transition-colors">
            Annuler
        </a>
    </div>

    <!-- Form -->
    <form action="{{ route('dossiers.update', $dossier) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-5">
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                
                <!-- Client -->
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Client concerné *</label>
                    <select name="client_id" required class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                        @foreach($clients as $c)
                            <option value="{{ $c->id }}" {{ (old('client_id', $dossier->client_id) == $c->id) ? 'selected' : '' }}>
                                {{ $c->company_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Type de dossier -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Type de mission *</label>
                    <select name="type" required class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                        <option value="comptabilite" {{ old('type', $dossier->type) === 'comptabilite' ? 'selected' : '' }}>Comptabilité & Tenue annuelle</option>
                        <option value="fiscalite" {{ old('type', $dossier->type) === 'fiscalite' ? 'selected' : '' }}>Fiscalité & Déclarations</option>
                        <option value="conseil" {{ old('type', $dossier->type) === 'conseil' ? 'selected' : '' }}>Conseil & Audit</option>
                        <option value="formation" {{ old('type', $dossier->type) === 'formation' ? 'selected' : '' }}>Formation</option>
                        <option value="social_rh" {{ old('type', $dossier->type) === 'social_rh' ? 'selected' : '' }}>Social & RH</option>
                        <option value="juridique" {{ old('type', $dossier->type) === 'juridique' ? 'selected' : '' }}>Juridique</option>
                        <option value="creation_entreprise" {{ old('type', $dossier->type) === 'creation_entreprise' ? 'selected' : '' }}>Création Sté</option>
                    </select>
                </div>

                <!-- Priorité -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Niveau de priorité *</label>
                    <select name="priority" required class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                        <option value="faible" {{ old('priority', $dossier->priority) === 'faible' ? 'selected' : '' }}>Faible</option>
                        <option value="moyenne" {{ old('priority', $dossier->priority) === 'moyenne' ? 'selected' : '' }}>Moyenne</option>
                        <option value="haute" {{ old('priority', $dossier->priority) === 'haute' ? 'selected' : '' }}>Haute</option>
                        <option value="urgente" {{ old('priority', $dossier->priority) === 'urgente' ? 'selected' : '' }}>Urgente</option>
                    </select>
                </div>

                <!-- Date début -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Date d'ouverture *</label>
                    <input type="date" name="start_date" value="{{ old('start_date', $dossier->start_date->format('Y-m-d')) }}" required class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <!-- Date fin -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Date de clôture prévue</label>
                    <input type="date" name="end_date" value="{{ old('end_date', $dossier->end_date ? $dossier->end_date->format('Y-m-d') : '') }}" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <!-- Statut -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Statut du dossier *</label>
                    <select name="status" required class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                        <option value="nouveau" {{ old('status', $dossier->status) === 'nouveau' ? 'selected' : '' }}>Nouveau</option>
                        <option value="en_cours" {{ old('status', $dossier->status) === 'en_cours' ? 'selected' : '' }}>En cours</option>
                        <option value="en_attente" {{ old('status', $dossier->status) === 'en_attente' ? 'selected' : '' }}>En attente de pièces</option>
                        <option value="termine" {{ old('status', $dossier->status) === 'termine' ? 'selected' : '' }}>Terminé</option>
                        <option value="suspendu" {{ old('status', $dossier->status) === 'suspendu' ? 'selected' : '' }}>Suspendu</option>
                        <option value="archive" {{ old('status', $dossier->status) === 'archive' ? 'selected' : '' }}>Archivé</option>
                    </select>
                </div>

                <!-- Responsable -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Superviseur / Responsable</label>
                    <select name="responsible_id" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                        <option value="">Sélectionnez...</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ old('responsible_id', $dossier->responsible_id) == $emp->id ? 'selected' : '' }}>
                                {{ $emp->full_name }} ({{ $emp->position }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Description -->
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Description de la mission</label>
                    <textarea name="description" rows="3" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">{{ old('description', $dossier->description) }}</textarea>
                </div>

                <!-- Notes internes -->
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Notes internes</label>
                    <textarea name="notes" rows="2" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">{{ old('notes', $dossier->notes) }}</textarea>
                </div>

            </div>

        </div>

        <!-- Submit -->
        <div class="flex items-center justify-end space-x-3">
            <a href="{{ route('dossiers.show', $dossier) }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-semibold transition-colors">
                Annuler
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-xs font-semibold shadow-lg shadow-sky-500/25 transition-all">
                Enregistrer les modifications
            </button>
        </div>

    </form>

</div>
@endsection
