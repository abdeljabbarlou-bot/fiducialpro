@extends('layouts.app')

@section('title', 'Nouveau Dossier')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    
    <!-- Top breadcrumb -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center space-x-2 text-xs text-slate-400 mb-1">
                <a href="{{ route('dossiers.index') }}" class="hover:text-sky-600">Dossiers</a>
                <span>&bull;</span>
                <span class="text-slate-600 font-medium">Ouverture de mission</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Ouvrir un Nouveau Dossier Client</h1>
        </div>
        <a href="{{ route('dossiers.index') }}" class="px-3.5 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold transition-colors">
            Annuler
        </a>
    </div>

    <!-- Form -->
    <form action="{{ route('dossiers.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-5">
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                
                <!-- Client -->
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Client concerné *</label>
                    <select name="client_id" required class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                        <option value="">Sélectionnez un client...</option>
                        @foreach($clients as $c)
                            <option value="{{ $c->id }}" {{ (old('client_id', $selectedClientId) == $c->id) ? 'selected' : '' }}>
                                {{ $c->company_name }} (ICE: {{ $c->ice ?? 'N/A' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Type de dossier -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Type de mission / dossier *</label>
                    <select name="type" required class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                        <option value="comptabilite" {{ old('type') === 'comptabilite' ? 'selected' : '' }}>Comptabilité & Tenue annuelle</option>
                        <option value="fiscalite" {{ old('type') === 'fiscalite' ? 'selected' : '' }}>Fiscalité & Déclarations TVA/IS</option>
                        <option value="conseil" {{ old('type') === 'conseil' ? 'selected' : '' }}>Conseil & Audit de gestion</option>
                        <option value="formation" {{ old('type') === 'formation' ? 'selected' : '' }}>Formation & Accompagnement</option>
                        <option value="social_rh" {{ old('type') === 'social_rh' ? 'selected' : '' }}>Social, Paie & RH</option>
                        <option value="juridique" {{ old('type') === 'juridique' ? 'selected' : '' }}>Juridique & Secrétariat juridique</option>
                        <option value="creation_entreprise" {{ old('type') === 'creation_entreprise' ? 'selected' : '' }}>Création & Constitution d'entreprise</option>
                    </select>
                </div>

                <!-- Priorité -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Niveau de priorité *</label>
                    <select name="priority" required class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                        <option value="faible" {{ old('priority') === 'faible' ? 'selected' : '' }}>Faible</option>
                        <option value="moyenne" {{ old('priority', 'moyenne') === 'moyenne' ? 'selected' : '' }}>Moyenne</option>
                        <option value="haute" {{ old('priority') === 'haute' ? 'selected' : '' }}>Haute</option>
                        <option value="urgente" {{ old('priority') === 'urgente' ? 'selected' : '' }}>Urgente</option>
                    </select>
                </div>

                <!-- Date début -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Date d'ouverture / début *</label>
                    <input type="date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <!-- Date fin -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Date d'échéance / clôture prévue</label>
                    <input type="date" name="end_date" value="{{ old('end_date') }}" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <!-- Statut -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Statut initial *</label>
                    <select name="status" required class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                        <option value="nouveau" {{ old('status', 'nouveau') === 'nouveau' ? 'selected' : '' }}>Nouveau</option>
                        <option value="en_cours" {{ old('status') === 'en_cours' ? 'selected' : '' }}>En cours</option>
                        <option value="en_attente" {{ old('status') === 'en_attente' ? 'selected' : '' }}>En attente de pièces</option>
                        <option value="termine" {{ old('status') === 'termine' ? 'selected' : '' }}>Terminé</option>
                    </select>
                </div>

                <!-- Responsable de mission -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Superviseur / Responsable du dossier</label>
                    <select name="responsible_id" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                        <option value="">Sélectionnez un collaborateur...</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ old('responsible_id') == $emp->id ? 'selected' : '' }}>
                                {{ $emp->full_name }} ({{ $emp->position }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Affectation directe optionnelle -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Collaborateur à affecter au dossier</label>
                    <select name="assigned_employee_id" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                        <option value="">Aucune affectation immédiate</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ old('assigned_employee_id') == $emp->id ? 'selected' : '' }}>
                                {{ $emp->full_name }} ({{ $emp->position }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Rôle du collaborateur dans la mission</label>
                    <input type="text" name="role_in_dossier" value="{{ old('role_in_dossier', 'Comptable référent') }}" placeholder="Ex: Saisie comptable, Suivi TVA..." class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <!-- Description -->
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Description de la mission</label>
                    <textarea name="description" rows="3" placeholder="Périmètre de la mission, objectifs comptables ou légaux..." class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">{{ old('description') }}</textarea>
                </div>

                <!-- Notes internes -->
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Notes internes</label>
                    <textarea name="notes" rows="2" placeholder="Consignes particulières, antécédents fiscaux..." class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">{{ old('notes') }}</textarea>
                </div>

            </div>

        </div>

        <!-- Submit -->
        <div class="flex items-center justify-end space-x-3">
            <a href="{{ route('dossiers.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-semibold transition-colors">
                Annuler
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-xs font-semibold shadow-lg shadow-sky-500/25 transition-all">
                Créer le dossier
            </button>
        </div>

    </form>

</div>
@endsection
