@extends('layouts.app')

@section('title', 'Créer un Utilisateur')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    
    <!-- Top breadcrumb -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center space-x-2 text-xs text-slate-400 mb-1">
                <a href="{{ route('users.index') }}" class="hover:text-sky-600">Utilisateurs</a>
                <span>&bull;</span>
                <span class="text-slate-600 font-medium">Création de compte</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Ajouter un Utilisateur au Système</h1>
        </div>
        <a href="{{ route('users.index') }}" class="px-3.5 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold transition-colors">
            Annuler
        </a>
    </div>

    <!-- Form -->
    <form action="{{ route('users.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-5">
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nom complet *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="Ex: Sara Alami" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Adresse Email (Login) *</label>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="sara@cabinet.ma" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Téléphone</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="+212 6 ..." class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Mot de passe provisoire *</label>
                    <input type="password" name="password" required placeholder="Minimum 8 caractères" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Rôle dans l'application *</label>
                    <select name="role_id" required class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                {{ $role->name }} ({{ $role->slug }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Statut du compte *</label>
                    <select name="status" required class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                        <option value="actif" {{ old('status', 'actif') === 'actif' ? 'selected' : '' }}>Actif</option>
                        <option value="inactif" {{ old('status') === 'inactif' ? 'selected' : '' }}>Inactif</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Lier à un collaborateur existant</label>
                    <select name="employee_id" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                        <option value="">Aucun lien employé</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ old('employee_id') == $emp->id ? 'selected' : '' }}>
                                {{ $emp->full_name }} ({{ $emp->matricule }})
                            </option>
                        @endforeach
                    </select>
                </div>

            </div>

        </div>

        <!-- Submit -->
        <div class="flex items-center justify-end space-x-3">
            <a href="{{ route('users.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-semibold transition-colors">
                Annuler
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-xs font-semibold shadow-lg shadow-sky-500/25 transition-all">
                Créer l'utilisateur
            </button>
        </div>

    </form>

</div>
@endsection
