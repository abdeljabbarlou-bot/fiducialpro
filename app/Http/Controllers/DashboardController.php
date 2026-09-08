<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Client;
use App\Models\Deadline;
use App\Models\Declaration;
use App\Models\Dossier;
use App\Models\Employee;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\DeclarationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(DeclarationService $declarationService)
    {
        // Règle RG11 : synchronisation proactive des retards
        $declarationService->syncOverdueDeclarations();

        // 1. Indicateurs Clients
        $totalClients = Client::count();
        $activeClients = Client::where('status', 'actif')->count();
        $prospectClients = Client::where('status', 'prospect')->count();

        // 2. Indicateurs Dossiers
        $totalDossiers = Dossier::count();
        $activeDossiers = Dossier::whereIn('status', ['nouveau', 'en_cours', 'en_attente'])->count();
        $completedDossiers = Dossier::where('status', 'termine')->count();

        // 3. Indicateurs Déclarations
        $upcomingDeclarations = Declaration::where('due_date', '>=', Carbon::today())
            ->whereNotIn('status', ['deposee', 'payee', 'annulee'])
            ->count();
        $overdueDeclarations = Declaration::where('status', 'en_retard')->count();

        // 4. Indicateurs Facturation & Encaissements
        $totalRevenue = Invoice::where('status', '!=', 'annulee')->sum('total_ttc');
        $totalCollected = Payment::sum('amount');
        $totalRemaining = Invoice::whereNotIn('status', ['payee', 'annulee'])->sum('remaining_amount');
        $overdueInvoices = Invoice::where('status', 'en_retard')->count();

        // 5. Collaborateurs
        $totalEmployees = Employee::where('status', 'actif')->count();

        // 6. Données graphiques : CA des 6 derniers mois
        $monthlyRevenue = [];
        $monthLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthLabels[] = $date->translatedFormat('M Y');
            $revenue = Invoice::whereYear('invoice_date', $date->year)
                ->whereMonth('invoice_date', $date->month)
                ->where('status', '!=', 'annulee')
                ->sum('total_ttc');
            $monthlyRevenue[] = (float) $revenue;
        }

        // 7. Données graphiques : Répartition des dossiers par type
        $dossiersByType = Dossier::select('type', DB::raw('count(*) as total'))
            ->groupBy('type')
            ->pluck('total', 'type')
            ->toArray();

        // 8. Données graphiques : Déclarations par statut
        $declarationsByStatus = Declaration::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // 9. Données graphiques : Factures payées vs impayées
        $invoicesByStatus = Invoice::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // 10. Listes récentes
        $urgentDeadlines = Deadline::with('client', 'responsible')
            ->whereIn('status', ['en_attente', 'en_cours', 'en_retard'])
            ->orderBy('due_date')
            ->take(5)
            ->get();

        $recentDeclarations = Declaration::with('client', 'type', 'responsible')
            ->orderBy('due_date')
            ->take(5)
            ->get();

        $recentInvoices = Invoice::with('client')
            ->orderByDesc('id')
            ->take(5)
            ->get();

        $recentActivities = ActivityLog::orderByDesc('id')
            ->take(6)
            ->get();

        return view('dashboard.index', compact(
            'totalClients',
            'activeClients',
            'prospectClients',
            'totalDossiers',
            'activeDossiers',
            'completedDossiers',
            'upcomingDeclarations',
            'overdueDeclarations',
            'totalRevenue',
            'totalCollected',
            'totalRemaining',
            'overdueInvoices',
            'totalEmployees',
            'monthLabels',
            'monthlyRevenue',
            'dossiersByType',
            'declarationsByStatus',
            'invoicesByStatus',
            'urgentDeadlines',
            'recentDeclarations',
            'recentInvoices',
            'recentActivities'
        ));
    }
}
