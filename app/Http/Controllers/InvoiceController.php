<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Client;
use App\Models\Invoice;
use App\Services\InvoiceService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with(['client', 'payments']);

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->filled('filter') && $request->filter === 'unpaid') {
            $query->unpaid();
        } elseif ($request->filled('filter') && $request->filter === 'overdue') {
            $query->overdue();
        }

        $invoices = $query->orderByDesc('id')->paginate(10)->withQueryString();
        $clients = Client::orderBy('company_name')->get();

        return view('invoices.index', compact('invoices', 'clients'));
    }

    public function create(Request $request)
    {
        $clients = Client::where('status', '!=', 'archive')->orderBy('company_name')->get();
        $selectedClientId = $request->query('client_id');

        return view('invoices.create', compact('clients', 'selectedClientId'));
    }

    public function store(Request $request, InvoiceService $invoiceService)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'description' => 'nullable|string|max:255',
            'payment_conditions' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'status' => 'required|in:brouillon,emise',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            // RG06 : seuls les taux de TVA légaux marocains sont acceptés (CGI : 20, 14, 10, 7, 0 %)
            'items.*.tax_rate' => ['required', 'numeric', Rule::in(config('cabinet.tva_rates'))],
        ]);

        $invoice = $invoiceService->createInvoice($validated, $validated['items'], auth()->id());

        return redirect()->route('invoices.show', $invoice)->with('success', "La facture {$invoice->reference} a été émise avec succès.");
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['client.contacts', 'items', 'payments.creator', 'creator']);
        return view('invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        if ($invoice->status === 'payee') {
            return redirect()->route('invoices.show', $invoice)->with('error', "Une facture entièrement réglée ne peut plus être modifiée.");
        }

        $clients = Client::orderBy('company_name')->get();
        $invoice->load('items');

        return view('invoices.edit', compact('invoice', 'clients'));
    }

    public function update(Request $request, Invoice $invoice, InvoiceService $invoiceService)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'description' => 'nullable|string|max:255',
            'payment_conditions' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'status' => 'required|in:brouillon,emise,annulee',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            // RG06 : seuls les taux de TVA légaux marocains sont acceptés (CGI : 20, 14, 10, 7, 0 %)
            'items.*.tax_rate' => ['required', 'numeric', Rule::in(config('cabinet.tva_rates'))],
        ]);

        $invoice = $invoiceService->updateInvoice($invoice, $validated, $validated['items']);

        return redirect()->route('invoices.show', $invoice)->with('success', "La facture {$invoice->reference} a été mise à jour.");
    }

    public function downloadPdf(Invoice $invoice, InvoiceService $invoiceService)
    {
        $pdf = $invoiceService->generatePdf($invoice);
        return $pdf->download("Facture_{$invoice->reference}.pdf");
    }

    public function print(Invoice $invoice)
    {
        $invoice->load(['client.contacts', 'items', 'payments']);
        return view('invoices.print', compact('invoice'));
    }

    public function destroy(Invoice $invoice)
    {
        if ($invoice->payments()->exists()) {
            return back()->with('error', "Impossible de supprimer une facture ayant des paiements enregistrés. Veuillez annuler la facture.");
        }

        $ref = $invoice->reference;
        $invoice->delete();

        ActivityLog::log(
            action: 'suppression',
            module: 'invoices',
            description: "Suppression de la facture {$ref}",
            targetId: $invoice->id,
            targetLabel: $ref
        );

        return redirect()->route('invoices.index')->with('success', "La facture {$ref} a été supprimée.");
    }
}
