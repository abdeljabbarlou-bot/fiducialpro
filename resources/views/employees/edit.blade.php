@extends('layouts.app')

@section('title', 'Modifier ' . $employee->full_name)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    
    <!-- Top breadcrumb -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center space-x-2 text-xs text-slate-400 mb-1">
                <a href="{{ route('employees.index') }}" class="hover:text-sky-600">Collaborateurs</a>
                <span>&bull;</span>
                <a href="{{ route('employees.show', $employee) }}" class="hover:text-sky-600">{{ $employee->full_name }}</a>
                <span>&bull;</span>
                <span class="text-slate-600 font-medium">Modification</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Modifier la Fiche Collaborateur</h1>
        </div>
        <a href="{{ route('employees.show', $employee) }}" class="px-3.5 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold transition-colors">
            Annuler
        </a>
    </div>

    <!-- Form -->
    <form action="{{ route('employees.update', $employee) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-5">
            <div class="border-b border-slate-100 pb-3">
                <h2 class="text-base font-bold text-slate-900 flex items-center">
                    <i class="fa-solid fa-id-badge text-sky-600 mr-2"></i> État Civil & Identité
                </h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Prénom *</label>
                    <input type="text" name="first_name" value="{{ old('first_name', $employee->first_name) }}" required class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nom *</label>
                    <input type="text" name="last_name" value="{{ old('last_name', $employee->last_name) }}" required class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">CIN *</label>
                    <input type="text" name="cin" value="{{ old('cin', $employee->cin) }}" required class="w-full py-2.5 px-3 bg-slate-50 font-mono text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Matricule interne *</label>
                    <input type="text" name="matricule" value="{{ old('matricule', $employee->matricule) }}" required class="w-full py-2.5 px-3 bg-slate-50 font-mono text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Email professionnel *</label>
                    <input type="email" name="email" value="{{ old('email', $employee->email) }}" required class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Téléphone *</label>
                    <input type="text" name="phone" value="{{ old('phone', $employee->phone) }}" required class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Fonction *</label>
                    <input type="text" name="position" value="{{ old('position', $employee->position) }}" required class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Date d'embauche *</label>
                    <input type="date" name="hire_date" value="{{ old('hire_date', $employee->hire_date->format('Y-m-d')) }}" required class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Salaire (DH)</label>
                    <input type="number" step="0.01" name="salary" value="{{ old('salary', $employee->salary) }}" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Statut *</label>
                    <select name="status" required class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                        <option value="actif" {{ old('status', $employee->status) === 'actif' ? 'selected' : '' }}>Actif</option>
                        <option value="conge" {{ old('status', $employee->status) === 'conge' ? 'selected' : '' }}>En congé</option>
                        <option value="inactif" {{ old('status', $employee->status) === 'inactif' ? 'selected' : '' }}>Inactif</option>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Adresse</label>
                    <input type="text" name="address" value="{{ old('address', $employee->address) }}" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Notes RH</label>
                    <textarea name="notes" rows="2" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">{{ old('notes', $employee->notes) }}</textarea>
                </div>

            </div>
        </div>

        <!-- Submit -->
        <div class="flex items-center justify-end space-x-3">
            <a href="{{ route('employees.show', $employee) }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-semibold transition-colors">
                Annuler
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-xs font-semibold shadow-lg shadow-sky-500/25 transition-all">
                Mettre à jour
            </button>
        </div>

    </form>

</div>
@endsection
