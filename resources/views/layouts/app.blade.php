<!DOCTYPE html>
<html lang="fr" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Gestion Cabinet Fiduciaire') - FiducialPro</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('images/logo.jpg') }}">

    <!-- Police (chargée si connexion disponible, repli système sinon) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- CSS (Tailwind + Font Awesome) et JS (Alpine + Chart.js) compilés localement par Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif; }
        [x-cloak] { display: none !important; }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
    @stack('styles')
</head>
<body class="h-full antialiased text-slate-800" x-data="{ sidebarOpen: false, profileDropdown: false, notifDropdown: false }">
    <div class="min-h-full flex flex-col lg:flex-row">
        
        <!-- Mobile Sidebar Backdrop -->
        <div x-show="sidebarOpen" 
             x-cloak
             @click="sidebarOpen = false" 
             class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm lg:hidden"></div>

        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
               class="fixed inset-y-0 left-0 z-50 w-72 bg-slate-900 text-slate-300 transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 flex flex-col shadow-xl">
            
            <!-- Logo & Cabinet Name -->
            <div class="h-20 flex items-center justify-between px-5 border-b border-slate-800 bg-slate-950/70">
                <a href="{{ route('dashboard') }}" class="flex items-center flex-1 pr-2">
                    <img src="{{ asset('images/logo.jpg') }}?v=2" alt="FIDUCIALPRO - Cabinet d'Expertise & Conseil" class="h-12 w-auto max-w-[225px] object-contain rounded shadow-sm">
                </a>
                <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white p-1">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 overflow-y-auto px-4 py-5 space-y-1.5 custom-scrollbar text-sm font-medium">
                
                <div class="px-3 pb-2 text-xs font-semibold text-slate-400 uppercase tracking-wider">Pilotage</div>
                
                <a href="{{ route('dashboard') }}" 
                   class="flex items-center px-3.5 py-2.5 rounded-lg transition-colors {{ request()->routeIs('dashboard') || request()->routeIs('home') ? 'bg-sky-600 text-white shadow-sm' : 'hover:bg-slate-800/80 hover:text-white' }}">
                    <i class="fa-solid fa-chart-pie w-6 text-base"></i>
                    <span>Tableau de bord</span>
                </a>

                <div class="pt-4 px-3 pb-2 text-xs font-semibold text-slate-400 uppercase tracking-wider">Gestion Métier</div>

                <a href="{{ route('clients.index') }}" 
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-lg transition-colors {{ request()->routeIs('clients.*') ? 'bg-sky-600 text-white shadow-sm' : 'hover:bg-slate-800/80 hover:text-white' }}">
                    <div class="flex items-center">
                        <i class="fa-solid fa-building-user w-6 text-base"></i>
                        <span>Clients</span>
                    </div>
                    <span class="text-xs px-2 py-0.5 rounded-full {{ request()->routeIs('clients.*') ? 'bg-white/20 text-white' : 'bg-slate-800 text-slate-400' }}">
                        {{ \App\Models\Client::count() }}
                    </span>
                </a>

                <a href="{{ route('dossiers.index') }}" 
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-lg transition-colors {{ request()->routeIs('dossiers.*') ? 'bg-sky-600 text-white shadow-sm' : 'hover:bg-slate-800/80 hover:text-white' }}">
                    <div class="flex items-center">
                        <i class="fa-solid fa-folder-open w-6 text-base"></i>
                        <span>Dossiers clients</span>
                    </div>
                    <span class="text-xs px-2 py-0.5 rounded-full {{ request()->routeIs('dossiers.*') ? 'bg-white/20 text-white' : 'bg-slate-800 text-slate-400' }}">
                        {{ \App\Models\Dossier::whereIn('status', ['nouveau', 'en_cours'])->count() }}
                    </span>
                </a>

                <a href="{{ route('declarations.index') }}" 
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-lg transition-colors {{ request()->routeIs('declarations.*') ? 'bg-sky-600 text-white shadow-sm' : 'hover:bg-slate-800/80 hover:text-white' }}">
                    <div class="flex items-center">
                        <i class="fa-solid fa-file-invoice-dollar w-6 text-base"></i>
                        <span>Déclarations fiscales</span>
                    </div>
                    @php
                        $overdueCount = \App\Models\Declaration::where('status', 'en_retard')->count();
                    @endphp
                    @if($overdueCount > 0)
                        <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-rose-500 text-white animate-pulse">
                            {{ $overdueCount }}
                        </span>
                    @endif
                </a>

                <a href="{{ route('deadlines.index') }}" 
                   class="flex items-center px-3.5 py-2.5 rounded-lg transition-colors {{ request()->routeIs('deadlines.*') ? 'bg-sky-600 text-white shadow-sm' : 'hover:bg-slate-800/80 hover:text-white' }}">
                    <i class="fa-solid fa-calendar-days w-6 text-base"></i>
                    <span>Échéances & Agenda</span>
                </a>

                <div class="pt-4 px-3 pb-2 text-xs font-semibold text-slate-400 uppercase tracking-wider">Comptabilité & GED</div>

                <a href="{{ route('invoices.index') }}" 
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-lg transition-colors {{ request()->routeIs('invoices.*') ? 'bg-sky-600 text-white shadow-sm' : 'hover:bg-slate-800/80 hover:text-white' }}">
                    <div class="flex items-center">
                        <i class="fa-solid fa-receipt w-6 text-base"></i>
                        <span>Factures</span>
                    </div>
                    @php
                        $unpaidCount = \App\Models\Invoice::whereNotIn('status', ['payee', 'annulee'])->count();
                    @endphp
                    @if($unpaidCount > 0)
                        <span class="text-xs px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300">
                            {{ $unpaidCount }}
                        </span>
                    @endif
                </a>

                <a href="{{ route('payments.index') }}" 
                   class="flex items-center px-3.5 py-2.5 rounded-lg transition-colors {{ request()->routeIs('payments.*') ? 'bg-sky-600 text-white shadow-sm' : 'hover:bg-slate-800/80 hover:text-white' }}">
                    <i class="fa-solid fa-hand-holding-dollar w-6 text-base"></i>
                    <span>Paiements</span>
                </a>

                <a href="{{ route('documents.index') }}" 
                   class="flex items-center px-3.5 py-2.5 rounded-lg transition-colors {{ request()->routeIs('documents.*') ? 'bg-sky-600 text-white shadow-sm' : 'hover:bg-slate-800/80 hover:text-white' }}">
                    <i class="fa-solid fa-box-archive w-6 text-base"></i>
                    <span>Documents (GED)</span>
                </a>

                <div class="pt-4 px-3 pb-2 text-xs font-semibold text-slate-400 uppercase tracking-wider">Cabinet & Rapports</div>

                <a href="{{ route('employees.index') }}" 
                   class="flex items-center px-3.5 py-2.5 rounded-lg transition-colors {{ request()->routeIs('employees.*') ? 'bg-sky-600 text-white shadow-sm' : 'hover:bg-slate-800/80 hover:text-white' }}">
                    <i class="fa-solid fa-user-group w-6 text-base"></i>
                    <span>Collaborateurs</span>
                </a>

                @unless(auth()->user()->hasRole('secretaire'))
                <a href="{{ route('reports.index') }}"
                   class="flex items-center px-3.5 py-2.5 rounded-lg transition-colors {{ request()->routeIs('reports.*') ? 'bg-sky-600 text-white shadow-sm' : 'hover:bg-slate-800/80 hover:text-white' }}">
                    <i class="fa-solid fa-chart-line w-6 text-base"></i>
                    <span>Rapports & Exports</span>
                </a>
                @endunless

                @if(auth()->user()->isAdmin() || auth()->user()->isGerant())
                <a href="{{ route('activity-logs.index') }}" 
                   class="flex items-center px-3.5 py-2.5 rounded-lg transition-colors {{ request()->routeIs('activity-logs.*') ? 'bg-sky-600 text-white shadow-sm' : 'hover:bg-slate-800/80 hover:text-white' }}">
                    <i class="fa-solid fa-clock-rotate-left w-6 text-base"></i>
                    <span>Journal d'audit (Logs)</span>
                </a>

                <a href="{{ route('trash.index') }}"
                   class="flex items-center px-3.5 py-2.5 rounded-lg transition-colors {{ request()->routeIs('trash.*') ? 'bg-sky-600 text-white shadow-sm' : 'hover:bg-slate-800/80 hover:text-white' }}">
                    <i class="fa-solid fa-trash-can-arrow-up w-6 text-base"></i>
                    <span>Corbeille</span>
                </a>
                @endif

                @if(auth()->user()->isAdmin())
                <a href="{{ route('users.index') }}" 
                   class="flex items-center px-3.5 py-2.5 rounded-lg transition-colors {{ request()->routeIs('users.*') ? 'bg-sky-600 text-white shadow-sm' : 'hover:bg-slate-800/80 hover:text-white' }}">
                    <i class="fa-solid fa-users-gear w-6 text-base"></i>
                    <span>Utilisateurs & Rôles</span>
                </a>
                @endif

            </nav>

            <!-- User bottom card -->
            <div class="p-4 border-t border-slate-800 bg-slate-950/30">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3 overflow-hidden">
                        <div class="w-9 h-9 rounded-full bg-slate-700 flex items-center justify-center font-bold text-white text-sm">
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        </div>
                        <div class="truncate">
                            <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->name }}</p>
                            <span class="inline-flex items-center text-xs font-medium px-2 py-0.5 rounded bg-sky-900/60 text-sky-300">
                                {{ auth()->user()->role?->name ?? 'Utilisateur' }}
                            </span>
                        </div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" title="Se déconnecter" class="text-slate-400 hover:text-rose-400 p-2 rounded-lg hover:bg-slate-800 transition-colors">
                            <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        </button>
                    </form>
                </div>
            </div>

        </aside>

        <!-- Main Wrapper -->
        <div class="flex-1 flex flex-col min-w-0 bg-slate-50">
            
            <!-- Top Navbar Header -->
            <header class="h-20 bg-white border-b border-slate-200 sticky top-0 z-30 flex items-center justify-between px-4 sm:px-8">
                
                <!-- Left: Hamburger + Global Search -->
                <div class="flex items-center space-x-4 flex-1 max-w-xl">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-lg text-slate-600 hover:bg-slate-100">
                        <i class="fa-solid fa-bars text-xl"></i>
                    </button>
                    
                    <form action="{{ route('search') }}" method="GET" class="w-full">
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-solid fa-magnifying-glass text-sm"></i>
                            </span>
                            <input type="search" 
                                   name="q" 
                                   value="{{ request('q') }}"
                                   placeholder="Rechercher un client, ICE, dossier, facture (ex: ABC, FAC, 0021...)" 
                                   class="w-full pl-10 pr-4 py-2 bg-slate-100 hover:bg-slate-50 focus:bg-white text-sm text-slate-800 placeholder-slate-400 rounded-xl border border-transparent focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10 transition-all outline-none">
                        </div>
                    </form>
                </div>

                <!-- Right: Notifications, Quick actions & Profile -->
                <div class="flex items-center space-x-3 sm:space-x-4">
                    
                    <!-- Fast Action Add Dropdown -->
                    <div class="hidden sm:flex items-center space-x-2">
                        <a href="{{ route('clients.create') }}" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-sky-50 text-sky-700 hover:bg-sky-100 transition-colors">
                            <i class="fa-solid fa-plus mr-1"></i> Client
                        </a>
                        <a href="{{ route('invoices.create') }}" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 transition-colors">
                            <i class="fa-solid fa-plus mr-1"></i> Facture
                        </a>
                    </div>

                    <!-- Notifications Dropdown -->
                    @php
                        $unreadNotifs = \App\Models\Notification::where(function($q) {
                            $q->whereNull('user_id')->orWhere('user_id', auth()->id());
                        })->where('is_read', false)->orderByDesc('created_at')->take(5)->get();
                    @endphp
                    <div class="relative" x-data="{ notifOpen: false }">
                        <button @click="notifOpen = !notifOpen" 
                                class="relative p-2.5 rounded-xl text-slate-600 hover:text-sky-600 hover:bg-slate-100 transition-colors">
                            <i class="fa-regular fa-bell text-lg"></i>
                            @if($unreadNotifs->count() > 0)
                                <span class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-rose-500 rounded-full ring-2 ring-white animate-pulse"></span>
                            @endif
                        </button>

                        <!-- Dropdown Panel -->
                        <div x-show="notifOpen" 
                             x-cloak
                             @click.outside="notifOpen = false" 
                             class="absolute right-0 mt-3 w-80 sm:w-96 bg-white rounded-2xl shadow-2xl border border-slate-100 py-3 z-50">
                            
                            <div class="px-4 py-2 border-b border-slate-100 flex items-center justify-between">
                                <h4 class="font-semibold text-slate-800 text-sm">Notifications</h4>
                                @if($unreadNotifs->count() > 0)
                                    <form action="{{ route('notifications.read-all') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-xs text-sky-600 hover:underline">Tout marquer comme lu</button>
                                    </form>
                                @endif
                            </div>

                            <div class="max-h-80 overflow-y-auto divide-y divide-slate-100">
                                @forelse($unreadNotifs as $notif)
                                    <div class="p-3.5 hover:bg-slate-50 transition-colors flex items-start space-x-3">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 
                                            {{ $notif->type === 'danger' ? 'bg-rose-100 text-rose-600' : ($notif->type === 'warning' ? 'bg-amber-100 text-amber-600' : 'bg-sky-100 text-sky-600') }}">
                                            <i class="fa-solid {{ $notif->type === 'danger' ? 'fa-triangle-exclamation' : ($notif->type === 'warning' ? 'fa-bell' : 'fa-circle-info') }} text-xs"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-semibold text-slate-800">{{ $notif->title }}</p>
                                            <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">{{ $notif->message }}</p>
                                            <span class="text-[10px] text-slate-400 mt-1 block">{{ $notif->created_at->diffForHumans() }}</span>
                                        </div>
                                        <form action="{{ route('notifications.read', $notif) }}" method="POST">
                                            @csrf
                                            <button type="submit" title="Marquer comme lu" class="text-slate-400 hover:text-slate-600 text-xs p-1">
                                                <i class="fa-solid fa-check"></i>
                                            </button>
                                        </form>
                                    </div>
                                @empty
                                    <div class="py-8 text-center text-slate-400 text-xs">
                                        <i class="fa-regular fa-bell-slash text-2xl mb-2 text-slate-300"></i>
                                        <p>Aucune notification en attente</p>
                                    </div>
                                @endforelse
                            </div>

                            <div class="px-4 pt-2 border-t border-slate-100 text-center">
                                <a href="{{ route('notifications.index') }}" class="text-xs font-medium text-sky-600 hover:underline">
                                    Voir toutes les alertes &rarr;
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Profile Dropdown -->
                    <div class="relative" x-data="{ profileOpen: false }">
                        <button @click="profileOpen = !profileOpen" 
                                class="flex items-center space-x-3 p-1.5 rounded-xl hover:bg-slate-100 transition-colors">
                            <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-sky-600 to-indigo-600 text-white font-bold flex items-center justify-center shadow-sm text-sm">
                                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                            </div>
                            <div class="hidden md:block text-left">
                                <p class="text-xs font-bold text-slate-800 leading-tight">{{ auth()->user()->name }}</p>
                                <p class="text-[11px] text-slate-400">{{ auth()->user()->role?->name }}</p>
                            </div>
                            <i class="fa-solid fa-chevron-down text-xs text-slate-400"></i>
                        </button>

                        <div x-show="profileOpen" 
                             x-cloak
                             @click.outside="profileOpen = false" 
                             class="absolute right-0 mt-3 w-56 bg-white rounded-2xl shadow-xl border border-slate-100 py-2 z-50">
                            
                            <div class="px-4 py-2 border-b border-slate-100">
                                <p class="text-xs font-bold text-slate-800">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-slate-400 truncate">{{ auth()->user()->email }}</p>
                            </div>

                            <a href="{{ route('profile.show') }}" class="flex items-center px-4 py-2.5 text-xs text-slate-700 hover:bg-slate-50 transition-colors">
                                <i class="fa-regular fa-user mr-2.5 text-slate-400"></i> Mon Profil & Sécurité
                            </a>

                            @if(auth()->user()->isAdmin())
                            <a href="{{ route('users.index') }}" class="flex items-center px-4 py-2.5 text-xs text-slate-700 hover:bg-slate-50 transition-colors">
                                <i class="fa-solid fa-gear mr-2.5 text-slate-400"></i> Administration
                            </a>
                            @endif

                            <div class="border-t border-slate-100 my-1"></div>

                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full flex items-center px-4 py-2.5 text-xs text-rose-600 hover:bg-rose-50 transition-colors text-left">
                                    <i class="fa-solid fa-arrow-right-from-bracket mr-2.5"></i> Déconnexion
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 p-4 sm:p-8 max-w-7xl w-full mx-auto">
                
                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between shadow-sm animate-fade-in" x-data="{ show: true }" x-show="show">
                        <div class="flex items-center space-x-3">
                            <i class="fa-solid fa-circle-check text-emerald-500 text-lg"></i>
                            <span class="text-sm font-medium">{{ session('success') }}</span>
                        </div>
                        <button @click="show = false" class="text-emerald-500 hover:text-emerald-700"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center justify-between shadow-sm" x-data="{ show: true }" x-show="show">
                        <div class="flex items-center space-x-3">
                            <i class="fa-solid fa-circle-exclamation text-rose-500 text-lg"></i>
                            <span class="text-sm font-medium">{{ session('error') }}</span>
                        </div>
                        <button @click="show = false" class="text-rose-500 hover:text-rose-700"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                @endif

                @if(session('info'))
                    <div class="mb-6 p-4 rounded-xl bg-sky-50 border border-sky-200 text-sky-800 flex items-center justify-between shadow-sm" x-data="{ show: true }" x-show="show">
                        <div class="flex items-center space-x-3">
                            <i class="fa-solid fa-circle-info text-sky-500 text-lg"></i>
                            <span class="text-sm font-medium">{{ session('info') }}</span>
                        </div>
                        <button @click="show = false" class="text-sky-500 hover:text-sky-700"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 shadow-sm">
                        <div class="flex items-center space-x-2 font-semibold text-sm mb-1">
                            <i class="fa-solid fa-triangle-exclamation text-rose-500"></i>
                            <span>Veuillez corriger les erreurs suivantes :</span>
                        </div>
                        <ul class="list-disc list-inside text-xs space-y-1 ml-5">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')

            </main>

            <!-- Footer -->
            <footer class="bg-white border-t border-slate-200 py-4 px-8 text-center text-xs text-slate-400 flex flex-col sm:flex-row items-center justify-between">
                <span>&copy; {{ date('Y') }} <strong>FiducialPro</strong> — Système Intégré de Gestion de Cabinet Fiduciaire.</span>
                <span class="mt-1 sm:mt-0 text-slate-400 font-medium">Conçu pour l'Expertise Comptable, Fiscale & Juridique</span>
            </footer>

        </div>
    </div>

    @stack('scripts')
</body>
</html>
