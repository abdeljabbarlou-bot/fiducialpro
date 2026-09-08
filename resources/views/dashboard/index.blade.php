@extends('layouts.app')

@section('title', 'Tableau de bord de gestion')

@section('content')
<div class="space-y-8">
    
    <!-- Header with Welcome & Date -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
        <div>
            <div class="flex items-center space-x-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></span>
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Cabinet en activité</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 mt-1">Bonjour, {{ auth()->user()->name }} 👋</h1>
            <p class="text-sm text-slate-500">Voici la synthèse en temps réel de votre portefeuille fiduciaire.</p>
        </div>

        <div class="flex items-center space-x-3">
            @unless(auth()->user()->hasRole('secretaire'))
            <a href="{{ route('reports.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-semibold transition-colors flex items-center space-x-2">
                <i class="fa-solid fa-file-export text-slate-400"></i>
                <span>Générer un rapport</span>
            </a>
            @endunless
            <a href="{{ route('clients.create') }}" class="px-4 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-xs font-semibold shadow-md shadow-sky-500/20 transition-all flex items-center space-x-2">
                <i class="fa-solid fa-plus"></i>
                <span>Nouveau Client</span>
            </a>
        </div>
    </div>

    <!-- 4 Main KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <!-- Chiffre d'Affaires Facturé -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm relative overflow-hidden group hover:border-sky-300 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Chiffre d'Affaires</span>
                <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                </div>
            </div>
            <p class="text-2xl font-black text-slate-900 mt-3">{{ number_format($totalRevenue, 2, ',', ' ') }} <span class="text-xs font-normal text-slate-400">DH</span></p>
            <div class="flex items-center justify-between text-xs text-slate-500 mt-3 pt-3 border-t border-slate-100">
                <span>Encaissé : <strong class="text-emerald-600">{{ number_format($totalCollected, 0, ',', ' ') }} DH</strong></span>
                <span class="text-sky-600 font-semibold">{{ $totalRevenue > 0 ? round(($totalCollected / $totalRevenue) * 100) : 0 }}%</span>
            </div>
        </div>

        <!-- Reste à Recouvrer -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm relative overflow-hidden group hover:border-amber-300 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Reste à Recouvrer</span>
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                </div>
            </div>
            <p class="text-2xl font-black text-amber-600 mt-3">{{ number_format($totalRemaining, 2, ',', ' ') }} <span class="text-xs font-normal text-slate-400">DH</span></p>
            <div class="flex items-center justify-between text-xs text-slate-500 mt-3 pt-3 border-t border-slate-100">
                <span>Factures échues</span>
                <span class="px-2 py-0.5 rounded-full font-bold {{ $overdueInvoices > 0 ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-600' }}">
                    {{ $overdueInvoices }} impayée(s)
                </span>
            </div>
        </div>

        <!-- Portefeuille Clients -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm relative overflow-hidden group hover:border-indigo-300 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Portefeuille Clients</span>
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-building"></i>
                </div>
            </div>
            <p class="text-2xl font-black text-slate-900 mt-3">{{ $totalClients }} <span class="text-xs font-normal text-slate-400">entreprises</span></p>
            <div class="flex items-center justify-between text-xs text-slate-500 mt-3 pt-3 border-t border-slate-100">
                <span class="text-emerald-600 font-semibold">{{ $activeClients }} actifs</span>
                <span class="text-indigo-600">{{ $prospectClients }} prospects</span>
            </div>
        </div>

        <!-- Déclarations Fiscales & Alertes -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm relative overflow-hidden group hover:border-rose-300 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Déclarations Fiscales</span>
                <div class="w-10 h-10 rounded-xl {{ $overdueDeclarations > 0 ? 'bg-rose-50 text-rose-600 animate-pulse' : 'bg-emerald-50 text-emerald-600' }} flex items-center justify-center text-lg">
                    <i class="fa-solid fa-landmark"></i>
                </div>
            </div>
            <div class="flex items-baseline space-x-2 mt-3">
                <p class="text-2xl font-black {{ $overdueDeclarations > 0 ? 'text-rose-600' : 'text-slate-900' }}">{{ $overdueDeclarations }}</p>
                <span class="text-xs font-bold text-rose-600 uppercase">en retard</span>
            </div>
            <div class="flex items-center justify-between text-xs text-slate-500 mt-3 pt-3 border-t border-slate-100">
                <span>À échéance proche</span>
                <span class="font-bold text-sky-600">{{ $upcomingDeclarations }} à traiter</span>
            </div>
        </div>

    </div>

    <!-- Secondary operational badges -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-slate-100/70 p-3.5 rounded-xl flex items-center space-x-3">
            <i class="fa-solid fa-folder-tree text-sky-600 text-lg"></i>
            <div>
                <p class="text-xs text-slate-500 font-medium">Dossiers en cours</p>
                <p class="text-sm font-bold text-slate-800">{{ $activeDossiers }} / {{ $totalDossiers }} dossiers</p>
            </div>
        </div>
        <div class="bg-slate-100/70 p-3.5 rounded-xl flex items-center space-x-3">
            <i class="fa-solid fa-users text-indigo-600 text-lg"></i>
            <div>
                <p class="text-xs text-slate-500 font-medium">Équipe cabinet</p>
                <p class="text-sm font-bold text-slate-800">{{ $totalEmployees }} collaborateurs</p>
            </div>
        </div>
        <div class="bg-slate-100/70 p-3.5 rounded-xl flex items-center space-x-3">
            <i class="fa-solid fa-check-double text-emerald-600 text-lg"></i>
            <div>
                <p class="text-xs text-slate-500 font-medium">Missions clôturées</p>
                <p class="text-sm font-bold text-slate-800">{{ $completedDossiers }} terminées</p>
            </div>
        </div>
        <div class="bg-slate-100/70 p-3.5 rounded-xl flex items-center space-x-3">
            <i class="fa-solid fa-calendar-check text-purple-600 text-lg"></i>
            <div>
                <p class="text-xs text-slate-500 font-medium">Échéances urgentes</p>
                <p class="text-sm font-bold text-slate-800">{{ $urgentDeadlines->count() }} cette semaine</p>
            </div>
        </div>
    </div>

    <!-- Charts Section (Interactive Chart.js) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Monthly Revenue Chart (2 Cols) -->
        <div class="lg:col-span-2 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="font-bold text-slate-900 text-base">Chiffre d'Affaires Mensuel (MAD)</h3>
                    <p class="text-xs text-slate-400">Évolution de la facturation sur les 6 derniers mois</p>
                </div>
                <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-600 text-xs font-semibold">TTC</span>
            </div>
            <div class="h-64 relative">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Dossiers Distribution by Type (1 Col) -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
            <div class="mb-4">
                <h3 class="font-bold text-slate-900 text-base">Répartition des Dossiers</h3>
                <p class="text-xs text-slate-400">Ventilation par discipline métier</p>
            </div>
            <div class="h-64 relative flex items-center justify-center">
                <canvas id="dossiersChart"></canvas>
            </div>
        </div>

        <!-- Statut du portefeuille de facturation -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
            <div class="mb-4">
                <h3 class="font-bold text-slate-900 text-base">Portefeuille de Facturation</h3>
                <p class="text-xs text-slate-400">État de recouvrement des factures émises</p>
            </div>
            <div class="h-64 relative flex items-center justify-center">
                <canvas id="invoicesChart"></canvas>
            </div>
        </div>

        <!-- Typologie des obligations fiscales (2 Cols) -->
        <div class="lg:col-span-2 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="font-bold text-slate-900 text-base">Obligations Fiscales par Statut</h3>
                    <p class="text-xs text-slate-400">Avancement des déclarations SIMPL du cabinet</p>
                </div>
                <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-600 text-xs font-semibold">DGI</span>
            </div>
            <div class="h-64 relative">
                <canvas id="declarationsChart"></canvas>
            </div>
        </div>

    </div>

    <!-- Bottom Section: Urgent Deadlines, Declarations & Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left: Deadlines & Tax Declarations (2 cols) -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Declarations alert card -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-slate-900 text-sm">Déclarations Fiscales Récentes & Échéances</h3>
                        <p class="text-xs text-slate-400">Obligations TVA, IS, IR et CNSS sous surveillance</p>
                    </div>
                    <a href="{{ route('declarations.index') }}" class="text-xs font-semibold text-sky-600 hover:underline">
                        Tout afficher &rarr;
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-100">
                            <tr>
                                <th class="py-3 px-4">Client</th>
                                <th class="py-3 px-4">Type & Période</th>
                                <th class="py-3 px-4">Échéance</th>
                                <th class="py-3 px-4">Montant</th>
                                <th class="py-3 px-4">Statut</th>
                                <th class="py-3 px-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($recentDeclarations as $decl)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="py-3 px-4 font-semibold text-slate-800">
                                        <a href="{{ route('clients.show', $decl->client_id) }}" class="hover:text-sky-600">
                                            {{ $decl->client->company_name }}
                                        </a>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="font-bold text-slate-700">{{ $decl->type->code }}</span>
                                        <span class="text-slate-400 block text-[11px]">{{ $decl->period }}</span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="{{ $decl->status === 'en_retard' ? 'text-rose-600 font-bold' : 'text-slate-600' }}">
                                            {{ $decl->due_date->format('d/m/Y') }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 font-semibold text-slate-700">
                                        {{ number_format($decl->amount, 2, ',', ' ') }} DH
                                    </td>
                                    <td class="py-3 px-4">
                                        @if($decl->status === 'deposee' || $decl->status === 'payee')
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Déposée</span>
                                        @elseif($decl->status === 'en_retard')
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700">En retard</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">{{ $decl->status_label }}</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        <a href="{{ route('declarations.show', $decl) }}" class="p-1.5 text-slate-400 hover:text-sky-600 rounded-lg hover:bg-slate-100 transition-colors">
                                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-6 text-center text-slate-400">Aucune déclaration enregistrée</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Urgent deadlines card -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-5">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="font-bold text-slate-900 text-sm">Échéances & Tâches à Venir</h3>
                        <p class="text-xs text-slate-400">Calendrier des obligations prioritaires</p>
                    </div>
                    <a href="{{ route('deadlines.index') }}" class="text-xs font-semibold text-sky-600 hover:underline">
                        Voir le calendrier &rarr;
                    </a>
                </div>

                <div class="space-y-2.5">
                    @forelse($urgentDeadlines as $dead)
                        <div class="p-3 rounded-2xl border border-slate-100 hover:border-slate-200 bg-slate-50/50 flex items-center justify-between transition-all">
                            <div class="flex items-center space-x-3 min-w-0">
                                <form action="{{ route('deadlines.toggle', $dead) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-5 h-5 rounded-md border {{ $dead->status === 'terminee' ? 'bg-emerald-500 border-emerald-500 text-white' : 'border-slate-300 hover:border-sky-500' }} flex items-center justify-center text-xs">
                                        @if($dead->status === 'terminee')
                                            <i class="fa-solid fa-check"></i>
                                        @endif
                                    </button>
                                </form>
                                <div class="truncate">
                                    <p class="text-xs font-bold text-slate-800 truncate">{{ $dead->title }}</p>
                                    <p class="text-[11px] text-slate-400">
                                        {{ $dead->client?->company_name ?? 'Interne' }} &bull; Échéance : 
                                        <span class="font-semibold text-slate-600">{{ $dead->due_date->format('d/m/Y') }}</span>
                                    </p>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $dead->priority_badge_class }}">
                                {{ ucfirst($dead->priority) }}
                            </span>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 text-center py-4">Toutes les échéances sont à jour.</p>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- Right: Activity Log & Quick Facts (1 col) -->
        <div class="space-y-6">
            
            <!-- Audit Activity Log -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-slate-900 text-sm">Journal d'Activité Récent</h3>
                    @if(auth()->user()->isAdmin() || auth()->user()->isGerant())
                    <a href="{{ route('activity-logs.index') }}" class="text-xs text-sky-600 hover:underline">Tous les logs</a>
                    @endif
                </div>

                <div class="relative pl-6 space-y-4 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-100">
                    @forelse($recentActivities as $act)
                        <div class="relative group">
                            <span class="absolute -left-6 top-1 w-2.5 h-2.5 rounded-full ring-4 ring-white 
                                {{ $act->action === 'creation' ? 'bg-emerald-500' : ($act->action === 'modification' ? 'bg-sky-500' : ($act->action === 'paiement' ? 'bg-indigo-500' : 'bg-slate-400')) }}"></span>
                            <div>
                                <p class="text-xs text-slate-700 leading-relaxed">{{ $act->description }}</p>
                                <span class="text-[10px] text-slate-400 mt-0.5 block">
                                    Par <strong>{{ $act->user_name }}</strong> &bull; {{ $act->created_at->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 text-center py-4">Aucune activité enregistrée</p>
                    @endforelse
                </div>
            </div>


        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // 1. Chart CA Mensuel
        const ctxRevenue = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctxRevenue, {
            type: 'bar',
            data: {
                labels: {!! json_encode($monthLabels) !!},
                datasets: [{
                    label: 'Chiffre d\'Affaires (DH)',
                    data: {!! json_encode($monthlyRevenue) !!},
                    backgroundColor: 'rgba(2, 132, 199, 0.85)',
                    hoverBackgroundColor: 'rgba(3, 105, 161, 1)',
                    borderRadius: 8,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.raw.toLocaleString('fr-FR') + ' DH';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            callback: function(value) { return value.toLocaleString('fr-FR') + ' DH'; },
                            font: { size: 10 }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 } }
                    }
                }
            }
        });

        // 2. Chart Répartition des Dossiers
        const dossiersData = {!! json_encode($dossiersByType) !!};
        const ctxDossiers = document.getElementById('dossiersChart').getContext('2d');
        
        const labelsMap = {
            'comptabilite': 'Comptabilité',
            'fiscalite': 'Fiscalité',
            'conseil': 'Conseil',
            'formation': 'Formation',
            'social_rh': 'Social & RH',
            'juridique': 'Juridique',
            'creation_entreprise': 'Création Ste'
        };

        const chartLabels = Object.keys(dossiersData).map(key => labelsMap[key] || key);
        const chartValues = Object.values(dossiersData);

        new Chart(ctxDossiers, {
            type: 'doughnut',
            data: {
                labels: chartLabels.length ? chartLabels : ['Aucun dossier'],
                datasets: [{
                    data: chartValues.length ? chartValues : [1],
                    backgroundColor: [
                        '#0284c7', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899', '#06b6d4', '#64748b'
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 10, font: { size: 10 }, padding: 12 }
                    }
                },
                cutout: '68%'
            }
        });

        // 3. Statut du portefeuille de facturation (RG07)
        const invoicesData = @json($invoicesByStatus);
        const invoiceLabels = {
            'brouillon': 'Brouillon',
            'emise': 'Émise',
            'partiellement_payee': 'Partiellement payée',
            'payee': 'Payée',
            'en_retard': 'En retard',
            'annulee': 'Annulée'
        };
        const invoiceColors = {
            'brouillon': '#94a3b8',
            'emise': '#0284c7',
            'partiellement_payee': '#f59e0b',
            'payee': '#10b981',
            'en_retard': '#e11d48',
            'annulee': '#cbd5e1'
        };
        const invoiceKeys = Object.keys(invoicesData);

        new Chart(document.getElementById('invoicesChart').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: invoiceKeys.length ? invoiceKeys.map(k => invoiceLabels[k] || k) : ['Aucune facture'],
                datasets: [{
                    data: invoiceKeys.length ? Object.values(invoicesData) : [1],
                    backgroundColor: invoiceKeys.length ? invoiceKeys.map(k => invoiceColors[k] || '#64748b') : ['#e2e8f0'],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 10, font: { size: 10 }, padding: 12 }
                    }
                },
                cutout: '68%'
            }
        });

        // 4. Typologie des obligations fiscales par statut (RG09 / RG11)
        const declarationsData = @json($declarationsByStatus);
        const declarationLabels = {
            'a_preparer': 'À préparer',
            'en_preparation': 'En préparation',
            'prete': 'Prête',
            'deposee': 'Déposée',
            'payee': 'Payée',
            'en_retard': 'En retard',
            'annulee': 'Annulée'
        };
        const declarationKeys = Object.keys(declarationsData);

        new Chart(document.getElementById('declarationsChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: declarationKeys.length ? declarationKeys.map(k => declarationLabels[k] || k) : ['Aucune déclaration'],
                datasets: [{
                    label: 'Déclarations',
                    data: declarationKeys.length ? Object.values(declarationsData) : [0],
                    backgroundColor: declarationKeys.map(k => k === 'en_retard' ? '#e11d48' : '#0284c7'),
                    borderRadius: 6,
                    maxBarThickness: 48,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0, font: { size: 11 } },
                        grid: { color: '#f1f5f9' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 } }
                    }
                }
            }
        });

    });
</script>
@endpush
