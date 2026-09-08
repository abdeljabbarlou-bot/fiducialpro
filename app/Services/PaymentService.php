<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Invoice;
use App\Models\Notification;
use App\Models\Payment;
use Exception;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    /**
     * Enregistrer un règlement sur facture avec validation stricte RG08
     */
    public function recordPayment(Invoice $invoice, array $data, int $userId): Payment
    {
        $amount = (float) $data['amount'];

        if ($amount <= 0) {
            throw new Exception("Le montant du paiement doit être supérieur à 0.");
        }

        return DB::transaction(function () use ($invoice, $data, $amount, $userId) {
            // Verrou pessimiste pour empêcher toute race condition en concurrence
            $lockedInvoice = Invoice::whereKey($invoice->id)->lockForUpdate()->firstOrFail();

            // Règle RG08 : Le total des paiements ne doit pas dépasser le total TTC
            $currentPaid = $lockedInvoice->payments()->sum('amount');
            $maxAllowed = round($lockedInvoice->total_ttc - $currentPaid, 2);

            if ($amount > ($maxAllowed + 0.01)) {
                throw new Exception("Le montant ({$amount} DH) dépasse le solde restant à payer ({$maxAllowed} DH).");
            }

            $payment = Payment::create([
                'invoice_id' => $lockedInvoice->id,
                'client_id' => $lockedInvoice->client_id,
                'payment_date' => $data['payment_date'],
                'amount' => $amount,
                'payment_method' => $data['payment_method'],
                'reference' => $data['reference'] ?? null,
                'bank' => $data['bank'] ?? null,
                'comments' => $data['comments'] ?? null,
                'created_by' => $userId,
            ]);

            // Recalculer l'état de la facture
            $lockedInvoice->recalculatePaymentStatus();

            // Journal d'audit (RG14)
            ActivityLog::log(
                action: 'paiement',
                module: 'payments',
                description: "Enregistrement d'un règlement de " . number_format($amount, 2, ',', ' ') . " DH sur la facture {$lockedInvoice->reference} (Reste: {$lockedInvoice->remaining_amount} DH)",
                targetId: $payment->id,
                targetLabel: $lockedInvoice->reference
            );

            // Notification interne
            Notification::create([
                'title' => 'Règlement enregistré',
                'message' => "Un paiement de " . number_format($amount, 2, ',', ' ') . " DH a été encaissé pour la facture {$lockedInvoice->reference}.",
                'type' => 'success',
                'link' => route('invoices.show', $lockedInvoice->id),
            ]);

            return $payment;
        });
    }
}
