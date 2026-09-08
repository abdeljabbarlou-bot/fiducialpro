@extends('layouts.app')

@section('title', 'Nouveau Collaborateur')

@section('content')
<div class="max-w-3xl mx-auto space-y-6" x-data="{ createUser: false }">
    
    <!-- Top breadcrumb -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center space-x-2 text-xs text-slate-400 mb-1">
                <a href="{{ route('employees.index') }}" class="hover:text-sky-600">Collaborateurs</a>
                <span>&bull;</span>
                <span class="text-slate-600 font-medium">Fiche collaborateur</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Ajouter un Collaborateur au Cabinet</h1>
        </div>
        <a href="{{ route('employees.index') }}" class="px-3.5 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold transition-colors">
            Annuler
        </a>
    </div>

    <!-- Form -->
    <form action="{{ route('employees.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-5">
            <div class="border-b border-slate-100 pb-3">
                <h2 class="text-base font-bold text-slate-900 flex items-center">
                    <i class="fa-solid fa-id-badge text-sky-600 mr-2"></i> État Civil & Identité
                </h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Prénom *</label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}" required placeholder="Ex: Sara" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nom *</label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}" required placeholder="Ex: Alami" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">CIN (Carte d'Identité Nationale) *</label>
                    <input type="text" name="cin" value="{{ old('cin') }}" required placeholder="Ex: BK123456" class="w-full py-2.5 px-3 bg-slate-50 font-mono text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Matricule interne *</label>
                    <input type="text" name="matricule" value="{{ old('matricule', 'EMP-' . sprintf('%03d', \App\Models\Employee::count() + 1)) }}" required class="w-full py-2.5 px-3 bg-slate-50 font-mono text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Email professionnel *</label>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="collaborateur@cabinet.ma" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Téléphone *</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" required placeholder="+212 6 ..." class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Fonction / Intitulé du poste *</label>
                    <input type="text" name="position" value="{{ old('position', 'Comptable') }}" required placeholder="Ex: Comptable Senior, Fiscaliste, Juriste..." class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Date d'embauche *</label>
                    <input type="date" name="hire_date" value="{{ old('hire_date', date('Y-m-d')) }}" required class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Salaire mensuel (DH)</label>
                    <input type="number" step="0.01" name="salary" value="{{ old('salary') }}" placeholder="0.00" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Statut *</label>
                    <select name="status" required class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                        <option value="actif" {{ old('status', 'actif') === 'actif' ? 'selected' : '' }}>Actif</option>
                        <option value="conge" {{ old('status') === 'conge' ? 'selected' : '' }}>En congé</option>
                        <option value="inactif" {{ old('status') === 'inactif' ? 'selected' : '' }}>Inactif</option>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Adresse de résidence</label>
                    <input type="text" name="address" value="{{ old('address') }}" placeholder="Quartier, ville..." class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

            </div>
        </div>

        <!-- Section 2 : Création de compte d'accès utilisateur (Optionnelle) -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center space-x-2">
                    <input type="checkbox" id="createUserCheckbox" name="create_user_account" value="1" x-model="createUser" class="w-4 h-4 text-sky-600 rounded border-slate-300 focus:ring-sky-500">
                    <label for="createUserCheckbox" class="text-xs font-bold text-slate-900 cursor-pointer">
                        Créer un compte d'accès pour ce collaborateur à l'application
                    </label>
                </div>
            </div>

            <div x-show="createUser" x-cloak class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Rôle d'accès au système *</label>
                    <select name="role_id" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }} ({{ $role->slug }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Mot de passe de connexion initial *</label>
                    <input type="password" name="user_password" placeholder="Minimum 8 caractères" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>
            </div>
        </div>

        <!-- Section 3 : Notes -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
            <h2 class="text-base font-bold text-slate-900 flex items-center">
                <i class="fa-regular fa-note-sticky text-amber-500 mr-2"></i> Notes Internes RH
            </h2>
            <textarea name="notes" rows="2" placeholder="Compétences, diplômes, missions antérieures..." class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">{{ old('notes') }}</textarea>
        </div>

        <!-- Submit -->
        <div class="flex items-center justify-end space-x-3">
            <a href="{{ route('employees.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-semibold transition-colors">
                Annuler
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-xs font-semibold shadow-lg shadow-sky-500/25 transition-all">
                Enregistrer le collaborateur
            </button>
        </div>

    </form>

</div>
@endsection
