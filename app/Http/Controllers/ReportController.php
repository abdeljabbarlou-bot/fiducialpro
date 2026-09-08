<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Client;
use App\Models\Declaration;
use App\Models\Dossier;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\ExcelReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function __construct(protected ExcelReportService $excelReportService)
    {
    }

    public function index()
    {
        $stats = [
            'total_clients' => Client::count(),
            'active_clients' => Client::where('status', 'actif')->count(),
            'total_dossiers' => Dossier::count(),
            'active_dossiers' => Dossier::whereIn('status', ['nouveau', 'en_cours', 'en_attente'])->count(),
            'total_declarations' => Declaration::count(),
            'overdue_declarations' => Declaration::where('status', 'en_retard')->count(),
            'total_invoiced' => Invoice::where('status', '!=', 'annulee')->sum('total_ttc'),
            'total_paid' => Payment::sum('amount'),
            'total_unpaid' => Invoice::whereNotIn('status', ['payee', 'annulee'])->sum('remaining_amount'),
        ];

        return view('reports.index', compact('stats'));
    }

    public function generate(Request $request)
    {
        $type = $request->input('report_type', 'financial');
        $format = $request->input('format', $request->input('export', 'pdf'));
        // 'csv' est conservé comme alias historique du format tableur (désormais un vrai .xlsx)
        if ($format === 'csv') {
            $format = 'excel';
        }
        if (!in_array($format, ['pdf', 'excel'])) {
            $format = 'pdf';
        }

        $startDate = $request->input('start_date', $request->input('date_start', Carbon::now()->startOfYear()->toDateString()));
        $endDate = $request->input('end_date', $request->input('date_end', Carbon::now()->endOfYear()->toDateString()));

        ActivityLog::log(
            action: 'export',
            module: 'reports',
            description: "Génération du rapport '{$type}' (format {$format}) pour la période du {$startDate} au {$endDate}"
        );

        if ($type === 'financial') {
            return $this->financialReport($startDate, $endDate, $format);
        } elseif ($type === 'clients') {
            return $this->clientsReport($format, $request->input('status'), $request->input('legal_form'));
        } elseif ($type === 'declarations') {
            return $this->declarationsReport($startDate, $endDate, $format, $request->input('type_id'), $request->input('status'));
        } elseif ($type === 'dossiers') {
            return $this->dossiersReport($format, $request->input('type'), $request->input('status'));
        }

        return redirect()->route('reports.index');
    }

    protected function financialReport(string $startDate, string $endDate, string $format)
    {
        $invoices = Invoice::with(['client', 'payments'])
            ->whereBetween('invoice_date', [$startDate, $endDate])
            ->orderBy('invoice_date')
            ->get();

        $payments = Payment::with(['client', 'invoice'])
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->orderBy('payment_date')
            ->get();

        $totalHt = $invoices->where('status', '!=', 'annulee')->sum('subtotal_ht');
        $totalTtc = $invoices->where('status', '!=', 'annulee')->sum('total_ttc');
        $totalPaid = $payments->sum('amount');
        $totalRemaining = $invoices->whereNotIn('status', ['payee', 'annulee'])->sum('remaining_amount');
        $dateStart = $startDate;
        $dateEnd = $endDate;

        $data = compact('invoices', 'payments', 'totalHt', 'totalTtc', 'totalPaid', 'totalRemaining', 'dateStart', 'dateEnd', 'startDate', 'endDate');

        if ($format === 'excel') {
            return $this->exportInvoicesExcel($invoices, $startDate, $endDate, $totalHt, $totalTtc, $totalPaid, $totalRemaining);
        }

        $pdf = Pdf::loadView('reports.pdf.financial', $data)->setPaper('a4', 'landscape');
        return $pdf->download("Rapport_Financier_{$startDate}_{$endDate}.pdf");
    }

    protected function clientsReport(string $format, ?string $status = null, ?string $legalForm = null)
    {
        $query = Client::with(['primaryContact', 'dossiers'])->orderBy('company_name');

        if (!empty($status)) {
            $query->where('status', $status);
        }

        if (!empty($legalForm)) {
            $query->where('legal_form', $legalForm);
        }

        $clients = $query->get();

        $byStatus = Client::selectRaw('status, count(*) as count')->groupBy('status')->pluck('count', 'status')->toArray();
        $byForm = Client::selectRaw('legal_form, count(*) as count')->whereNotNull('legal_form')->groupBy('legal_form')->pluck('count', 'legal_form')->toArray();

        $data = compact('clients', 'byStatus', 'byForm');

        if ($format === 'excel') {
            return $this->exportClientsExcel($clients);
        }

        $pdf = Pdf::loadView('reports.pdf.clients', $data)->setPaper('a4', 'portrait');
        return $pdf->download("Rapport_Portefeuille_Clients.pdf");
    }

    protected function declarationsReport(string $startDate, string $endDate, string $format, ?string $typeId = null, ?string $status = null)
    {
        $query = Declaration::with(['client', 'type', 'responsible'])
            ->whereBetween('due_date', [$startDate, $endDate])
            ->orderBy('due_date');

        if (!empty($typeId)) {
            $query->where('declaration_type_id', $typeId);
        }

        if (!empty($status)) {
            $query->where('status', $status);
        }

        $declarations = $query->get();
        $byStatus = $declarations->groupBy('status')->map->count();
        $totalAmount = $declarations->sum('amount');
        $dateStart = $startDate;
        $dateEnd = $endDate;

        $data = compact('declarations', 'byStatus', 'totalAmount', 'dateStart', 'dateEnd', 'startDate', 'endDate');

        if ($format === 'excel') {
            return $this->exportDeclarationsExcel($declarations, $startDate, $endDate, $totalAmount);
        }

        $pdf = Pdf::loadView('reports.pdf.declarations', $data)->setPaper('a4', 'landscape');
        return $pdf->download("Rapport_Declarations_Fiscales.pdf");
    }

    protected function dossiersReport(string $format, ?string $type = null, ?string $status = null)
    {
        $query = Dossier::with(['client', 'responsible', 'employees'])->orderByDesc('id');

        if (!empty($type)) {
            $query->where('type', $type);
        }

        if (!empty($status)) {
            $query->where('status', $status);
        }

        $dossiers = $query->get();
        $byType = $dossiers->groupBy('type')->map->count();
        $byStatus = $dossiers->groupBy('status')->map->count();

        $data = compact('dossiers', 'byType', 'byStatus');

        if ($format === 'excel') {
            return $this->exportDossiersExcel($dossiers);
        }

        $pdf = Pdf::loadView('reports.pdf.dossiers', $data)->setPaper('a4', 'landscape');
        return $pdf->download("Rapport_Dossiers_Cabinet.pdf");
    }

    // Export Excel (.xlsx) — classeurs professionnels générés via PhpSpreadsheet (ExcelReportService)
    protected function exportInvoicesExcel($invoices, string $startDate, string $endDate, $totalHt, $totalTtc, $totalPaid, $totalRemaining): StreamedResponse
    {
        $columns = [
            ['label' => 'Référence', 'width' => 15],
            ['label' => 'Client', 'width' => 32],
            ['label' => 'Date Facture', 'width' => 14, 'format' => 'date'],
            ['label' => 'Date Échéance', 'width' => 14, 'format' => 'date'],
            ['label' => 'Sous-total HT (DH)', 'width' => 17, 'format' => 'currency'],
            ['label' => 'Montant TVA (DH)', 'width' => 16, 'format' => 'currency'],
            ['label' => 'Total TTC (DH)', 'width' => 16, 'format' => 'currency'],
            ['label' => 'Montant Encaissé (DH)', 'width' => 18, 'format' => 'currency'],
            ['label' => 'Solde Restant Dû (DH)', 'width' => 18, 'format' => 'currency'],
            ['label' => 'Statut du Règlement', 'width' => 20],
        ];

        $rows = $invoices->map(fn ($inv) => [
            $inv->reference,
            $inv->client->company_name,
            $inv->invoice_date,
            $inv->due_date,
            (float) $inv->subtotal_ht,
            (float) $inv->tax_amount,
            (float) $inv->total_ttc,
            (float) $inv->paid_amount,
            (float) $inv->remaining_amount,
            $inv->status_label,
        ])->all();

        $totalsRow = ['', '', '', 'TOTAUX', (float) $totalHt, (float) ($totalTtc - $totalHt), (float) $totalTtc, (float) $totalPaid, (float) $totalRemaining, ''];

        return $this->excelReportService->download(
            title: 'Rapport Financier & Facturation',
            subtitle: 'Période du ' . Carbon::parse($startDate)->format('d/m/Y') . ' au ' . Carbon::parse($endDate)->format('d/m/Y'),
            columns: $columns,
            rows: $rows,
            filename: "Rapport_Financier_{$startDate}_{$endDate}.xlsx",
            totalsRow: $totalsRow,
        );
    }

    protected function exportClientsExcel($clients): StreamedResponse
    {
        $columns = [
            ['label' => 'Raison Sociale', 'width' => 32],
            ['label' => 'Forme Juridique', 'width' => 16],
            ['label' => 'ICE', 'width' => 18],
            ['label' => 'Identifiant Fiscal (IF)', 'width' => 18],
            ['label' => 'Registre de Commerce (RC)', 'width' => 20],
            ['label' => 'Ville', 'width' => 16],
            ['label' => 'Téléphone', 'width' => 16],
            ['label' => 'Adresse Email', 'width' => 26],
            ['label' => 'Représentant Légal', 'width' => 24],
            ['label' => 'Statut', 'width' => 14],
        ];

        $rows = $clients->map(fn ($c) => [
            $c->company_name,
            $c->legal_form,
            $c->ice,
            $c->if_number,
            $c->rc_number,
            $c->city,
            $c->phone,
            $c->email,
            $c->primaryContact?->full_name,
            ucfirst($c->status),
        ])->all();

        return $this->excelReportService->download(
            title: 'Rapport du Portefeuille Clients',
            subtitle: 'Situation au ' . now()->format('d/m/Y') . ' — ' . $clients->count() . ' client(s)',
            columns: $columns,
            rows: $rows,
            filename: 'Rapport_Portefeuille_Clients.xlsx',
        );
    }

    protected function exportDeclarationsExcel($declarations, string $startDate, string $endDate, $totalAmount): StreamedResponse
    {
        $columns = [
            ['label' => 'Client', 'width' => 30],
            ['label' => 'Impôt / Taxe', 'width' => 22],
            ['label' => 'Période Fiscale', 'width' => 16],
            ["label" => "Date d'Échéance", 'width' => 14, 'format' => 'date'],
            ['label' => 'Date Effective de Dépôt', 'width' => 16, 'format' => 'date'],
            ['label' => 'Montant à Payer (DH)', 'width' => 17, 'format' => 'currency'],
            ['label' => 'Référence Télédéclaration SIMPL', 'width' => 24],
            ['label' => 'Statut', 'width' => 16],
            ['label' => 'Collaborateur Responsable', 'width' => 22],
        ];

        $rows = $declarations->map(fn ($d) => [
            $d->client->company_name,
            $d->type->name,
            $d->period,
            $d->due_date,
            $d->filing_date ?: null,
            (float) $d->amount,
            $d->filing_reference ?? '-',
            $d->status_label,
            $d->responsible?->full_name ?? 'Non assigné',
        ])->all();

        $totalsRow = ['', '', '', '', 'TOTAL', (float) $totalAmount, '', '', ''];

        return $this->excelReportService->download(
            title: 'Rapport des Déclarations Fiscales',
            subtitle: 'Période du ' . Carbon::parse($startDate)->format('d/m/Y') . ' au ' . Carbon::parse($endDate)->format('d/m/Y'),
            columns: $columns,
            rows: $rows,
            filename: 'Rapport_Declarations_Fiscales.xlsx',
            totalsRow: $totalsRow,
        );
    }

    protected function exportDossiersExcel($dossiers): StreamedResponse
    {
        $columns = [
            ['label' => 'Réf Dossier', 'width' => 16],
            ['label' => 'Client', 'width' => 32],
            ['label' => 'Type de Mission', 'width' => 20],
            ["label" => "Date d'Ouverture", 'width' => 14, 'format' => 'date'],
            ['label' => 'Date de Clôture', 'width' => 14, 'format' => 'date'],
            ['label' => 'Degré de Priorité', 'width' => 15],
            ['label' => 'Statut de la Mission', 'width' => 18],
            ['label' => 'Superviseur Responsable', 'width' => 22],
        ];

        $rows = $dossiers->map(fn ($dos) => [
            $dos->reference,
            $dos->client->company_name,
            $dos->type_label,
            $dos->start_date,
            $dos->end_date ?: null,
            ucfirst($dos->priority),
            $dos->status_label,
            $dos->responsible?->full_name ?? 'Cabinet',
        ])->all();

        return $this->excelReportService->download(
            title: 'Rapport des Dossiers du Cabinet',
            subtitle: 'Situation au ' . now()->format('d/m/Y') . ' — ' . $dossiers->count() . ' dossier(s)',
            columns: $columns,
            rows: $rows,
            filename: 'Rapport_Dossiers_Cabinet.xlsx',
        );
    }
}
