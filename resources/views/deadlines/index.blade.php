@extends('layouts.app')

@section('title', 'Échéances & Calendrier des Obligations')

@section('content')
<div class="space-y-6" x-data="{ newDeadlineModal: false }">

    <!-- Top header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Calendrier & Échéances Légales</h1>
            <p class="text-xs sm:text-sm text-slate-500">Planning des déclarations TVA, acomptes IS, bordereaux CNSS et jalons clients.</p>
        </div>

        <div class="flex items-center space-x-3">
            <!-- View Mode Switcher -->
            <div class="bg-slate-200/80 p-1 rounded-xl flex items-center text-xs font-semibold">
                <a href="{{ route('deadlines.index', ['view' => 'calendar', 'month' => $currentMonth]) }}" 
                   class="px-3 py-1.5 rounded-lg transition-colors {{ $viewMode === 'calendar' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                    <i class="fa-solid fa-calendar mr-1"></i> Calendrier
                </a>
                <a href="{{ route('deadlines.index', ['view' => 'list']) }}" 
                   class="px-3 py-1.5 rounded-lg transition-colors {{ $viewMode === 'list' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                    <i class="fa-solid fa-list mr-1"></i> Liste
                </a>
            </div>

            <button type="button" @click="newDeadlineModal = true" class="px-4 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-xs font-semibold shadow-md shadow-sky-500/20 transition-all flex items-center space-x-1.5">
                <i class="fa-solid fa-plus"></i>
                <span>Planifier Échéance</span>
            </button>
        </div>
    </div>

    @if($viewMode === 'calendar')
        <!-- Calendar Month View -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 space-y-6">
            
            <!-- Month Navigation -->
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div class="flex items-center space-x-3">
                    <h2 class="text-lg font-bold text-slate-900 capitalize">
                        {{ $monthDate->translatedFormat('F Y') }}
                    </h2>
                    <span class="text-xs font-semibold text-slate-400">
                        {{ $deadlines->count() }} échéance(s)
                    </span>
                </div>

                <div class="flex items-center space-x-1">
                    <a href="{{ route('deadlines.index', ['view' => 'calendar', 'month' => $monthDate->copy()->subMonth()->format('Y-m')]) }}" class="p-2 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-600 text-xs">
                        <i class="fa-solid fa-chevron-left"></i>
                    </a>
                    <a href="{{ route('deadlines.index', ['view' => 'calendar', 'month' => now()->format('Y-m')]) }}" class="px-3 py-1.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-600 text-xs font-semibold">
                        Aujourd'hui
                    </a>
                    <a href="{{ route('deadlines.index', ['view' => 'calendar', 'month' => $monthDate->copy()->addMonth()->format('Y-m')]) }}" class="p-2 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-600 text-xs">
                        <i class="fa-solid fa-chevron-right"></i>
                    </a>
                </div>
            </div>

            <!-- Calendar Days Grid -->
            @php
                $daysInMonth = $monthDate->daysInMonth;
                $firstDayOfWeek = $monthDate->dayOfWeekIso; // 1 (Lundi) à 7 (Dimanche)
            @endphp

            <div class="grid grid-cols-7 gap-px bg-slate-200 rounded-2xl overflow-hidden border border-slate-200">
                <!-- Day names -->
                @foreach(['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'] as $dayName)
                    <div class="bg-slate-50 p-2.5 text-center text-xs font-bold text-slate-500 uppercase">
                        {{ $dayName }}
                    </div>
                @endforeach

                <!-- Pre-padding empty cells -->
                @for($i = 1; $i < $firstDayOfWeek; $i++)
                    <div class="bg-slate-100/50 min-h-[100px] p-2 text-slate-300 text-xs"></div>
                @endfor

                <!-- Days of current month -->
                @for($day = 1; $day <= $daysInMonth; $day++)
                    @php
                        $currentDate = $monthDate->copy()->day($day);
                        $isToday = $currentDate->isToday();
                        $dayDeadlines = $deadlines->filter(function($d) use ($currentDate) {
                            return $d->due_date->isSameDay($currentDate);
                        });
                    @endphp

                    <div class="bg-white min-h-[110px] p-2 hover:bg-slate-50 transition-colors flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <span class="inline-flex w-6 h-6 rounded-full text-xs font-bold items-center justify-center {{ $isToday ? 'bg-sky-600 text-white shadow-sm' : 'text-slate-700' }}">
                                {{ $day }}
                            </span>
                            @if($dayDeadlines->count() > 0)
                                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            @endif
                        </div>

                        <!-- Deadlines tags for this day -->
                        <div class="mt-1 space-y-1 flex-1">
                            @foreach($dayDeadlines as $dead)
                                <div class="p-1 rounded-md text-[10px] font-semibold truncate border {{ $dead->priority_badge_class }}" title="{{ $dead->title }} ({{ $dead->client?->company_name ?? 'Cabinet' }})">
                                    {{ $dead->title }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endfor

                <!-- Post-padding empty cells -->
                @php
                    $totalCells = ($firstDayOfWeek - 1) + $daysInMonth;
                    $remainingCells = (7 - ($totalCells % 7)) % 7;
                @endphp
                @for($j = 0; $j < $remainingCells; $j++)
                    <div class="bg-slate-100/50 min-h-[100px] p-2 text-slate-300 text-xs"></div>
                @endfor
            </div>

        </div>

    @else
        <!-- List View -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-100">
                        <tr>
                            <th class="py-3.5 px-5">Statut</th>
                            <th class="py-3.5 px-5">Titre de l'Échéance</th>
                            <th class="py-3.5 px-5">Client / Dossier</th>
                            <th class="py-3.5 px-5">Date Limite</th>
                            <th class="py-3.5 px-5">Priorité</th>
                            <th class="py-3.5 px-5">Responsable</th>
                            <th class="py-3.5 px-5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($deadlines as $dead)
                            <tr class="hover:bg-slate-50/70 transition-colors {{ $dead->status === 'terminee' ? 'opacity-60 bg-slate-50/40' : '' }}">
                                <td class="py-4 px-5">
                                    <form action="{{ route('deadlines.toggle', $dead) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-5 h-5 rounded-md border {{ $dead->status === 'terminee' ? 'bg-emerald-500 border-emerald-500 text-white' : 'border-slate-300 hover:border-sky-500' }} flex items-center justify-center text-xs">
                                            @if($dead->status === 'terminee')
                                                <i class="fa-solid fa-check"></i>
                                            @endif
                                        </button>
                                    </form>
                                </td>

                                <td class="py-4 px-5">
                                    <span class="font-bold text-slate-900 {{ $dead->status === 'terminee' ? 'line-through text-slate-400' : '' }}">{{ $dead->title }}</span>
                                    <span class="block text-[11px] text-slate-400 capitalize">{{ $dead->type }}</span>
                                </td>

                                <td class="py-4 px-5">
                                    @if($dead->client)
                                        <a href="{{ route('clients.show', $dead->client_id) }}" class="font-semibold text-slate-800 hover:text-sky-600">
                                            {{ $dead->client->company_name }}
                                        </a>
                                    @else
                                        <span class="text-slate-400 italic">Interne cabinet</span>
                                    @endif
                                </td>

                                <td class="py-4 px-5">
                                    <span class="{{ $dead->due_date->isPast() && $dead->status !== 'terminee' ? 'text-rose-600 font-bold' : 'text-slate-800' }}">
                                        {{ $dead->due_date->format('d/m/Y') }}
                                    </span>
                                    <span class="block text-[10px] text-slate-400">{{ $dead->due_date->diffForHumans() }}</span>
                                </td>

                                <td class="py-4 px-5">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $dead->priority_badge_class }}">
                                        {{ ucfirst($dead->priority) }}
                                    </span>
                                </td>

                                <td class="py-4 px-5 text-slate-700">
                                    {{ $dead->responsible?->full_name ?? 'Non assigné' }}
                                </td>

                                <td class="py-4 px-5 text-right">
                                    <form action="{{ route('deadlines.destroy', $dead) }}" method="POST" onsubmit="return confirm('Supprimer cette échéance ?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-slate-100 transition-colors">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-400">
                                    <i class="fa-solid fa-calendar-check text-3xl mb-2 text-slate-300"></i>
                                    <p class="text-sm font-medium">Aucune échéance planifiée</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Modal Planifier une échéance -->
    <div x-show="newDeadlineModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div @click.outside="newDeadlineModal = false" class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl border border-slate-100 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="font-bold text-slate-900 text-sm">Planifier une Nouvelle Échéance</h3>
                <button @click="newDeadlineModal = false" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form action="{{ route('deadlines.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Titre de l'échéance *</label>
                    <input type="text" name="title" required placeholder="Ex: Déclaration TVA T1, Dépôt liasse..." class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Type d'obligation *</label>
                        <select name="type" required class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                            <option value="fiscal">Fiscale (TVA/IS/IR)</option>
                            <option value="social">Sociale (CNSS)</option>
                            <option value="comptable">Comptable / Bilan</option>
                            <option value="paiement">Paiement client</option>
                            <option value="juridique">Juridique / PV</option>
                            <option value="autre">Autre tâche</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Priorité *</label>
                        <select name="priority" required class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                            <option value="moyenne">Moyenne</option>
                            <option value="haute">Haute</option>
                            <option value="urgente">Urgente</option>
                            <option value="faible">Faible</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Date d'échéance *</label>
                    <input type="date" name="due_date" required value="{{ date('Y-m-d', strtotime('+7 days')) }}" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Client associé</label>
                    <select name="client_id" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                        <option value="">Aucun (Tâche interne)</option>
                        @foreach($clients as $c)
                            <option value="{{ $c->id }}">{{ $c->company_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Responsable assigné</label>
                    <select name="responsible_id" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                        <option value="">Sélectionner...</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center justify-end space-x-2 pt-2">
                    <button type="button" @click="newDeadlineModal = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl">Annuler</button>
                    <button type="submit" class="px-5 py-2 text-xs font-semibold text-white bg-sky-600 hover:bg-sky-700 rounded-xl shadow-md shadow-sky-500/20">Ajouter l'échéance</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
