<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\PaymentService;
use Exception;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['invoice.client', 'client', 'creator']);

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('reference', 'like', "%{$term}%")
                  ->orWhere('bank', 'like', "%{$term}%")
                  ->orWhereHas('client', function ($cq) use ($term) {
                      $cq->where('company_name', 'like', "%{$term}%");
                  })
                  ->orWhereHas('invoice', function ($iq) use ($term) {
                      $iq->where('reference', 'like', "%{$term}%");
                  });
            });
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        $payments = $query->orderByDesc('payment_date')->paginate(10)->withQueryString();
        $totalCollected = Payment::sum('amount');
        $clients = Client::orderBy('company_name')->get();

        return view('payments.index', compact('payments', 'totalCollected', 'clients'));
    }

    public function create(Request $request)
    {
        $invoiceId = $request->query('invoice_id');
        $invoice = null;

        if ($invoiceId) {
            $invoice = Invoice::with('client')->findOrFail($invoiceId);
        }

        $unpaidInvoices = Invoice::with('client')
            ->whereNotIn('status', ['payee', 'annulee'])
            ->where('remaining_amount', '>', 0)
            ->orderByDesc('id')
            ->get();

        return view('payments.create', compact('invoice', 'unpaidInvoices'));
    }

    public function store(Request $request, PaymentService $paymentService)
    {
        $validated = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            // Un encaissement ne peut être ni postdaté ni antérieur à l'émission de la facture
            'payment_date' => 'required|date|before_or_equal:today',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:especes,virement,cheque,carte,autre',
            'reference' => 'nullable|string|max:100',
            'bank' => 'nullable|string|max:100',
            'comments' => 'nullable|string',
        ], [
            'payment_date.before_or_equal' => "La date d'encaissement ne peut pas être postérieure à aujourd'hui.",
        ]);

        $invoice = Invoice::findOrFail($validated['invoice_id']);

        if ($invoice->invoice_date->gt($validated['payment_date'])) {
            return back()
                ->withErrors(['payment_date' => "La date d'encaissement ne peut pas être antérieure à la date d'émission de la facture ({$invoice->invoice_date->format('d/m/Y')})."])
                ->withInput();
        }

        try {
            $payment = $paymentService->recordPayment($invoice, $validated, auth()->id());
            return redirect()->route('invoices.show', $invoice)
                ->with('success', "Le paiement de " . number_format($payment->amount, 2, ',', ' ') . " DH a été enregistré avec succès.");
        } catch (Exception $e) {
            return back()->withErrors(['amount' => $e->getMessage()])->withInput();
        }
    }

    public function receipt(Payment $payment)
    {
        $payment->load(['invoice.client.contacts', 'client', 'creator']);
        return view('payments.receipt', compact('payment'));
    }

    public function destroy(Payment $payment)
    {
        $invoice = $payment->invoice;
        $amount = $payment->amount;

        $payment->delete();

        ActivityLog::log(
            action: 'suppression',
            module: 'payments',
            description: "Suppression du paiement de " . number_format($amount, 2, ',', ' ') . " DH sur la facture {$invoice->reference}",
            targetId: $invoice->id,
            targetLabel: $invoice->reference
        );

        return back()->with('info', "Le paiement a été supprimé et le solde de la facture a été réajusté.");
    }
}
